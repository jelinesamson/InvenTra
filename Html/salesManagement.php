<?php
    include("../Api/config.php");
    requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InvenTra</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Css/systemNav.css" />
    <link rel="stylesheet" href="../Css/salesManagement.css">
</head>
<body>
    <div class="systemNav-container">
        <?php include("systemNav.php"); ?>
        <div class="main-content">

        <header class="topbar">
            <button class="hamburger" id="menuBtn">
                <i data-lucide="menu"></i>
            </button>
        </header>

        <div class="pos-wrapper">
            <div class="pos-products">
                <h2>Available Products</h2>
                <div class="search-bar">
                    <input type="text" id="productSearch" placeholder="Search products..." onkeyup="filterProducts()">
                </div>
                <div id="productGrid" class="product-grid"></div>
            </div>

            <div class="pos-cart">
                <h2>Current Transaction</h2>
                <div class="cart-table-container">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th style="width: 60px;">Qty</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cartBody"></tbody>
                    </table>
                </div>
                <div class="checkout-panel">
                    <div class="summary-row">
                        <span>Total Due:</span>
                        <span id="cartTotal" class="total-amt" data-raw-total="0">₱0.00</span>
                    </div>
                    <div class="payment-row">
                        <label for="amountPaid">Amount Paid:</label>
                        <input type="number" id="amountPaid" placeholder="Enter Cash" min="0" step="0.01">
                    </div>
                    <button class="checkout-btn" onclick="processCheckout()">Complete Sale</button>
                </div>
            </div>
        </div>
    </div>

    <!-- for receipt (show after a successfull transaction)-->
    <div id="receiptModal" class="modal" style="display: none;">
        <div class="modal-content receipt-content">
            <span class="close" onclick="closeReceipt()">&times;</span>
            <div id="printableReceipt">
                <h2 style="text-align: center; margin-bottom: 5px;">EduTrack POS</h2>
                <p style="text-align: center; margin-top: 0; color: #666;">Official Receipt</p>
                <hr>
                <p><strong>Txn ID:</strong> <span id="rTxnId"></span></p>
                <p><strong>Date:</strong> <span id="rDate"></span></p>
                <hr>
                <table width="100%" class="receipt-table">
                    <tbody id="rItems"></tbody>
                </table>
                <hr>
                <div style="text-align: right; font-size: 16px;">
                    <p><strong>Total Due:</strong> ₱<span id="rTotal"></span></p>
                    <p><strong>Cash Paid:</strong> ₱<span id="rPaid"></span></p>
                    <p><strong>Change:</strong> ₱<span id="rChange"></span></p>
                </div>
                <hr>
                <p style="text-align: center; font-style: italic;">Thank you for your purchase!</p>
            </div>
            <div class="receipt-buttons">
                <button class="checkout-btn" onclick="printReceipt()" style="margin-bottom: 10px;">🖨️ Print Receipt</button>
                <button class="checkout-btn" onclick="exportReceiptCSV()" style="background: #17a2b8;">📊 Save as CSV</button>
            </div>
        </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="../Script/systemNav.js"></script>
    <script src="../Script/salesManagement.js"></script>
</body>
</html>