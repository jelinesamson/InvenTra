<?php
require_once "config.php";
require_once "inventoryManagement.php";
header('Content-Type: application/json');

function jsonResponse($status, $message, $data = null) {
    $payload = ["status" => $status, "message" => $message];
    if ($data !== null) {
        $payload["data"] = $data;
    }
    echo json_encode($payload);
    exit;
}

if (!isset($_POST['action'])) {
    jsonResponse("error", "No action specified.");
}

action:
$action = $_POST['action'];
$account_id = $_SESSION['account_id'] ?? null;

if (in_array($action, ['addIncoming', 'editIncoming', 'receive']) && !$account_id) {
    jsonResponse("error", "User not logged in.");
}

if ($action == 'getProductOptions') {
    $result = $conn->query("SELECT product_code, product_type, size, department, quantity, incoming_qty, price, status FROM products WHERE is_deleted = 0 ORDER BY product_code ASC");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    jsonResponse("success", "Product options loaded.", $data);
}

if ($action == 'getIncoming') {
    $result = $conn->query("SELECT product_code, product_type, size, department, quantity, incoming_qty, price FROM products WHERE is_deleted = 0 ORDER BY product_code ASC");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['incoming_status'] = ((int)$row['incoming_qty'] > 0) ? 'On the Way' : 'Received';
        $data[] = $row;
    }
    jsonResponse("success", "Incoming products loaded.", $data);
}

if ($action == 'getProduct') {
    $code = $_POST['code'] ?? '';
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_code = ? LIMIT 1");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$result) {
        jsonResponse("error", "Product not found.");
    }
    jsonResponse("success", "Product found.", $result);
}

if ($action == 'addIncoming') {
    $code = $_POST['code'] ?? '';
    $amount = isset($_POST['amount']) ? (int)$_POST['amount'] : 0;

    if ($amount <= 0) {
        jsonResponse("error", "Please enter a valid incoming quantity.");
    }

    $stmt = $conn->prepare("SELECT product_id, quantity, incoming_qty FROM products WHERE product_code = ? LIMIT 1");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$product) {
        jsonResponse("error", "Product not found.");
    }

    $newIncoming = (int)$product['incoming_qty'] + $amount;
    $stmt = $conn->prepare("UPDATE products SET incoming_qty = ? WHERE product_id = ?");
    $stmt->bind_param("ii", $newIncoming, $product['product_id']);

    if ($stmt->execute()) {
        $stmt->close();
        logJournal(
            $conn,
            $product['product_id'],
            $amount,
            0,
            "Incoming Add",
            $product['quantity'],
            $account_id
        );
        jsonResponse("success", "Incoming quantity added successfully.");
    }

    $stmt->close();
    jsonResponse("error", $stmt->error);
}

if ($action == 'editIncoming') {
    $code = $_POST['code'] ?? '';
    $amount = isset($_POST['amount']) ? (int)$_POST['amount'] : 0;

    if ($amount < 0) {
        jsonResponse("error", "Please enter a valid incoming quantity.");
    }

    $stmt = $conn->prepare("SELECT product_id, quantity FROM products WHERE product_code = ? LIMIT 1");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$product) {
        jsonResponse("error", "Product not found.");
    }

    $stmt = $conn->prepare("UPDATE products SET incoming_qty = ? WHERE product_id = ?");
    $stmt->bind_param("ii", $amount, $product['product_id']);

    if ($stmt->execute()) {
        $stmt->close();
        logJournal(
            $conn,
            $product['product_id'],
            $amount,
            0,
            "Incoming Edit",
            $product['quantity'],
            $account_id
        );
        jsonResponse("success", "Incoming quantity updated successfully.");
    }

    $stmt->close();
    jsonResponse("error", $stmt->error);
}

if ($action == 'receive') {
    $code = $_POST['code'] ?? '';
    $stmt = $conn->prepare("SELECT product_id, quantity, incoming_qty FROM products WHERE product_code = ? LIMIT 1");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$product) {
        jsonResponse("error", "Product not found.");
    }

    $incoming_qty = (int)$product['incoming_qty'];
    if ($incoming_qty <= 0) {
        jsonResponse("error", "No incoming stock to receive.");
    }

    $new_quantity = (int)$product['quantity'] + $incoming_qty;
    $stmt = $conn->prepare("UPDATE products SET quantity = ?, incoming_qty = 0 WHERE product_id = ?");
    $stmt->bind_param("ii", $new_quantity, $product['product_id']);

    if ($stmt->execute()) {
        $stmt->close();
        logJournal(
            $conn,
            $product['product_id'],
            0,
            0,
            "Receive",
            $new_quantity,
            $account_id
        );
        jsonResponse("success", "Stock received successfully.");
    }

    $stmt->close();
    jsonResponse("error", $stmt->error);
}

jsonResponse("error", "Unknown action.");
