<?php
// Api/salesManagement.php

require_once 'config.php';
require_once 'inventoryManagement.php';

// check for db if connected
    if (!$conn) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database connection failed. Check config.php']);
        exit;
    }

    if (isset($_GET['api'])) {
        header('Content-Type: application/json');

        // get product
        if ($_GET['api'] === 'products') {
            $query = "SELECT product_id, product_code, CONCAT(product_code, ' - ', product_type) AS name, price, quantity AS stock FROM products WHERE quantity > 0 AND is_deleted = 0 AND status = 'active'";
            $result = mysqli_query($conn, $query);

            // error if sql fails
            if (!$result) {
                echo json_encode(['error' => 'SQL Error: ' . mysqli_error($conn)]);
                exit;
            }

            $products = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $row['product_id'] = intval($row['product_id']);
                $row['price'] = floatval($row['price']);
                $row['stock'] = intval($row['stock']);
                $products[] = $row;
            }
            echo json_encode($products);
            exit;
        }

    //  checkout
     if ($_GET['api'] === 'checkout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $body = json_decode(file_get_contents('php://input'), true);
        $cart = $body['cart'] ?? [];
        $paid = floatval($body['paid'] ?? 0);

        if (empty($cart)) { echo json_encode(['error' => 'Cart is empty.']); exit; }

        $total = 0;
        $productsToBuy = [];

        foreach ($cart as $item) {
            $pid = intval($item['product_id']);
            $qty_needed = intval($item['qty']);

            $res = mysqli_query($conn, "SELECT product_code, incoming_qty, product_type, price, quantity FROM products WHERE product_id = $pid");
            $prod = mysqli_fetch_assoc($res);
          

            if (!$prod || $prod['quantity'] < $qty_needed) {
                echo json_encode(['error' => "Insufficient stock"]); 
                exit;
            }
            $vat = $total * 0.12;
            $productsToBuy[$pid] = $prod;
            $total += ($prod['price'] * $qty_needed);
}

        // if ($paid < $total) { echo json_encode(['error' => 'Insufficient payment. Total is ₱' . number_format($total, 2)]); exit; }
            if ($paid < $total) {
                echo json_encode(['error' => 'Insufficient payment.']);
                exit;
            }

            $change = $paid - $total;
            $txnId = 'TXN-' . strtoupper(substr(uniqid(), -7));

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $account_id = $_SESSION['account_id'] ?? 0;
    $insertTxn = mysqli_query($conn, "
    INSERT INTO transactions (transaction_id, total, paid, change_amount, vat, status, account_id, date_time)
    VALUES (
        '$txnId',
        $total,
        $paid,
        $change,
        " . ($total * 0.12) . ",  -- VAT
        'completed',
        $account_id,
        NOW()
    )
");

if (!$insertTxn) {
    echo json_encode(['error' => 'Transaction insert failed: ' . mysqli_error($conn)]);
    exit;
}

   foreach ($cart as $item) {
    $pid = intval($item['product_id']);
    $qty_sold = intval($item['qty']);

    $prod = $productsToBuy[$pid];
    $price = $prod['price'];

    $total_price = $price * $qty_sold;
    $new_stock = $prod['quantity'] - $qty_sold;

    $product_code = mysqli_real_escape_string($conn, $prod['product_code']);
    $product_type = mysqli_real_escape_string($conn, $prod['product_type']);

    // Insert transaction item
    $insertItem = mysqli_query($conn, "
        INSERT INTO transaction_items 
        (transaction_id, product_id, product_name, category, qty, unit_price, total_price)
        VALUES
        ('$txnId', $pid, '$product_code', '$product_type', $qty_sold, $price, $total_price)
    ");
    if (!$insertItem) {
        echo json_encode(['error' => 'Item insert failed: ' . mysqli_error($conn)]);
        exit;
    }

    // Update stock
    mysqli_query($conn, "UPDATE products SET quantity = $new_stock WHERE product_id = $pid");

    //  Log journal: incoming = current stock BEFORE sale, sales = qty_sold, journal_qty = stock AFTER sale
    logJournal(
        $conn,
        $pid,
        $prod['incoming_qty'],  // current stock before sale
        $qty_sold,
        "Sale $txnId",
        $new_stock,         // stock after sale
        $account_id
    );
}
        $receipt = ['id' => $txnId, 'date' => date('Y-m-d H:i:s'), 'items' => $cart, 'total' => $total, 'paid' => $paid, 'change' => $change];
        echo json_encode(['success' => true, 'receipt' => $receipt]); exit;
    }
    
    echo json_encode(['error' => 'Unknown endpoint']); exit;
}
?>