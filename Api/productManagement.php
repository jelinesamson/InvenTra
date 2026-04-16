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

$action = $_POST['action'];
$account_id = $_SESSION['account_id'] ?? null;

if (in_array($action, ['store','update','drop','receive','restore','permanentDelete','addIncoming','editIncoming']) && !$account_id) {
    jsonResponse("error", "User not logged in");
}

if ($action == "store") {
    $payload = json_decode($_POST['payload']);
    if (!$payload) {
        jsonResponse("error", "Invalid payload.");
    }

    $incoming_qty = isset($payload->incoming_qty) ? (int)$payload->incoming_qty : 0;

    $stmt = $conn->prepare("CALL add_product(?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssiidsi",
        $payload->code,
        $payload->product_type,
        $payload->size,
        $payload->department,
        $payload->quantity,
        $incoming_qty,
        $payload->price,
        $payload->status,
        $account_id
    );

    if ($stmt->execute()) {
        while ($conn->more_results() && $conn->next_result()) {}
        $stmt->close();

        $stmt2 = $conn->prepare("SELECT product_id, quantity, incoming_qty FROM products WHERE product_code = ? LIMIT 1");
        $stmt2->bind_param("s", $payload->code);
        $stmt2->execute();
        $product = $stmt2->get_result()->fetch_assoc();
        $stmt2->close();

        if ($product && isset($product['product_id'])) {
            $stmt3 = $conn->prepare("SELECT COUNT(*) AS cnt FROM product_journal WHERE product_id = ? AND notes = 'Add'");
            $stmt3->bind_param("i", $product['product_id']);
            $stmt3->execute();
            $count = $stmt3->get_result()->fetch_assoc()['cnt'] ?? 0;
            $stmt3->close();

            if ((int)$count === 0) {
                logJournal(
                    $conn,
                    $product['product_id'],
                    $product['incoming_qty'],
                    0,
                    "Add",
                    $product['quantity'],
                    $account_id
                );
            }
        }

        jsonResponse("success", "Product added successfully.");
    }

    $stmt->close();
    jsonResponse("error", $stmt->error);
}

if ($action == "get") {
    $result = $conn->query("SELECT * FROM v_products");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            $row['product_code'],
            $row['product_type'],
            $row['size'],
            $row['department'],
            $row['price'],
            $row['status'],
            '<button class="btn btn-sm btn-warning" onclick="update(this)">Edit</button> '
            . '<button class="btn btn-sm btn-danger" onclick="deleteRow(this)">Delete</button>'
        ];
    }
    echo json_encode(["data" => $data]);
    exit;
}

if ($action == "getDeleted") {
    $result = $conn->query("SELECT * FROM products WHERE is_deleted = 1");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    jsonResponse("success", "Deleted products loaded.", $data);
}

if ($action == "getProduct") {
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

if ($action == "getProductOptions") {
    $result = $conn->query("SELECT product_code, product_type, size, department, quantity, incoming_qty, price, status FROM products WHERE is_deleted = 0 ORDER BY product_code ASC");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    jsonResponse("success", "Product options loaded.", $data);
}

if ($action == "getIncoming") {
    $result = $conn->query("SELECT product_code, product_type, size, department, quantity, incoming_qty, price FROM products WHERE is_deleted = 0 ORDER BY product_code ASC");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['incoming_status'] = ((int)$row['incoming_qty'] > 0) ? 'On the Way' : 'Received';
        $data[] = $row;
    }
    jsonResponse("success", "Incoming products loaded.", $data);
}

if ($action == "addIncoming") {
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

if ($action == "editIncoming") {
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

if ($action == "update") {
    $payload = json_decode($_POST['payload']);
    if (!$payload) {
        jsonResponse("error", "Invalid payload.");
    }

    $stmtId = $conn->prepare("SELECT product_id, quantity FROM products WHERE product_code = ?");
    $stmtId->bind_param("s", $payload->code);
    $stmtId->execute();
    $result = $stmtId->get_result()->fetch_assoc();
    $stmtId->close();

    $product_id = $result['product_id'] ?? null;
    $quantity = $result['quantity'] ?? 0;
    if (!$product_id) {
        jsonResponse("error", "Product not found.");
    }

    $stmt = $conn->prepare("UPDATE products SET product_type = ?, size = ?, department = ?, quantity = ?, price = ?, status = ? WHERE product_code = ?");
    $stmt->bind_param(
        "sssiiss",
        $payload->product_type,
        $payload->size,
        $payload->department,
        $payload->quantity,
        $payload->price,
        $payload->status,
        $payload->code
    );

    if ($stmt->execute()) {
        logJournal(
            $conn,
            $product_id,
            0,
            0,
            "Edit",
            (int)$payload->quantity,
            $account_id
        );
        $stmt->close();
        jsonResponse("success", "Product updated successfully.");
    }

    $stmt->close();
    jsonResponse("error", $stmt->error);
}
	
if ($action == "drop") {
    $code = $_POST['code'] ?? '';

    $stmt = $conn->prepare("CALL delete_product(?,?)");
    $stmt->bind_param("si", $code, $account_id);

    if ($stmt->execute()) {
        while ($conn->more_results() && $conn->next_result()) {}
        $stmt->close();

        $stmt2 = $conn->prepare("SELECT product_id, quantity FROM products WHERE product_code = ? LIMIT 1");
        $stmt2->bind_param("s", $code);
        $stmt2->execute();
        $product = $stmt2->get_result()->fetch_assoc();
        $stmt2->close();

        if ($product && isset($product['product_id'])) {
            $stmt3 = $conn->prepare("SELECT COUNT(*) AS cnt FROM product_journal WHERE product_id = ? AND notes = 'Delete'");
            $stmt3->bind_param("i", $product['product_id']);
            $stmt3->execute();
            $count = $stmt3->get_result()->fetch_assoc()['cnt'] ?? 0;
            $stmt3->close();

            if ((int)$count === 0) {
                logJournal(
                    $conn,
                    $product['product_id'],
                    0,
                    0,
                    "Delete",
                    $product['quantity'],
                    $account_id
                );
            }
        }

        jsonResponse("success", "Product archived successfully.");
    }

    $stmt->close();
    jsonResponse("error", $stmt->error);
}

if ($action == "restore") {
    $code = $_POST['code'] ?? '';
    $stmt = $conn->prepare("UPDATE products SET is_deleted = 0 WHERE product_code = ?");
    $stmt->bind_param("s", $code);

    if ($stmt->execute()) {
        $stmt->close();

        $stmt2 = $conn->prepare("SELECT product_id, quantity FROM products WHERE product_code = ? LIMIT 1");
        $stmt2->bind_param("s", $code);
        $stmt2->execute();
        $product = $stmt2->get_result()->fetch_assoc();
        $stmt2->close();

        if ($product && isset($product['product_id'])) {
            logJournal(
                $conn,
                $product['product_id'],
                0,
                0,
                "Restore",
                $product['quantity'],
                $account_id
            );
        }

        jsonResponse("success", "Product restored successfully.");
    }

    $stmt->close();
    jsonResponse("error", $stmt->error);
}

if ($action == "permanentDelete") {
    $code = $_POST['code'] ?? '';
    $stmt = $conn->prepare("SELECT product_id FROM products WHERE product_code = ? LIMIT 1");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $product_id = $result['product_id'] ?? null;
    if (!$product_id) {
        jsonResponse("error", "Product not found.");
    }

    $conn->begin_transaction();
    $stmt = $conn->prepare("DELETE FROM product_journal WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();
    $conn->commit();

    jsonResponse("success", "Product permanently deleted.");
}

if ($action == "receive") {
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
?>