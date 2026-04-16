<?php
// Inventory Management API
// Handles journal logging and retrieval for inventory tracking
require_once "config.php";

function logJournal($conn, $product_id, $incoming_qty, $sales, $notes, $journal_qty, $account_id) {

    $stmt = $conn->prepare("
        INSERT INTO product_journal
        (product_id, incoming_quantity, sales, notes, journal_qty, account_id, date_time)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param(
        "iiisii",
        $product_id,
        $incoming_qty,
        $sales,
        $notes,
        $journal_qty,
        $account_id
    );

    if (!$stmt->execute()) {
        error_log("Journal insert failed: " . $stmt->error);
        return false;
    }

    $stmt->close();
    return true;
}

// ─── API Actions (GET requests) ──────────────────────────────────────────────

if (isset($_GET['action'])) {

    header('Content-Type: application/json');

    if ($_GET['action'] == "getProducts") {

        $stmt = $conn->prepare("
            SELECT p.product_id AS id, p.product_code AS name
            FROM products p
            ORDER BY p.product_code ASC
        ");

        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];

        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        echo json_encode([
            "status" => "success",
            "data" => $products
        ]);

        exit;
    }

    // Returns journal entries for a specific product, with optional date range
 if ($_GET['action'] == "getJournal") {

    header('Content-Type: application/json');

    $prod_id = trim($_GET['prod_id'] ?? '');

    if (empty($prod_id)) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid product ID."
        ]);
        exit;
    }

    $date_from = $_GET['date_from'] ?? '';
    $date_to   = $_GET['date_to'] ?? '';

    $params = [$prod_id];
    $types = "i";

    $date_filter = "";

    if (!empty($date_from) && !empty($date_to)) {

        $df = DateTime::createFromFormat('Y-m-d', $date_from);
        $dt = DateTime::createFromFormat('Y-m-d', $date_to);

        if (!$df || !$dt) {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid date format."
            ]);
            exit;
        }

        $date_filter = " AND pj.date_time BETWEEN ? AND ?";

        $params[] = $date_from . ' 00:00:00';
        $params[] = $date_to . ' 23:59:59';

        $types .= "ss";
    }

    $query = "
        SELECT pj.journal_id,
               CONCAT(p.product_code, ' - ', p.product_type) AS prod_name,
               pj.notes,
               pj.incoming_quantity,
               pj.sales,
               pj.journal_qty AS total_qty,
               pj.date_time,
               CASE
                   WHEN a.role IS NOT NULL AND a.role != '' THEN CONCAT(CONCAT_WS(' ', a.firstName, a.lastName), ' - ', UPPER(a.role))
                   ELSE CONCAT_WS(' ', a.firstName, a.lastName)
               END AS account_name,
               p.incoming_qty AS current_incoming_qty
        FROM product_journal pj
        LEFT JOIN products p ON p.product_id = pj.product_id
        LEFT JOIN accounts a ON a.account_id = pj.account_id
        WHERE pj.product_id = ?
        $date_filter
        ORDER BY pj.date_time DESC
    ";

    $stmt = $conn->prepare($query);

    $stmt->bind_param($types, ...$params);

    $stmt->execute();

    $result = $stmt->get_result();

    $entries = [];

    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "data" => $entries
    ]);

    exit;
}



    // Fallback for unknown actions
    echo json_encode(["status" => "error", "message" => "Unknown action."]);
    exit;
}
