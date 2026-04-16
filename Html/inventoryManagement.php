<?php
    include("../Api/config.php");
    requireLogin();
    
    if ($_SESSION['role'] === 'cashier') {
    header("Location: salesManagement.php");
    exit;
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InvenTra</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <link rel="stylesheet" href="../Css/systemNav.css" />
    <link rel="stylesheet" href="../Css/inventoryManagement.css" />
</head>

<body>
    <div class="systemNav-container">

        <!-- Sidebar navigation (shared component) -->
        <?php include("systemNav.php"); ?>

        <div class="main-content">

        <!-- Hamburger toggle for mobile -->
        <header class="topbar">
            <button class="hamburger" id="menuBtn">
                <i data-lucide="menu"></i>
            </button>
        </header>

        <div class="container">

            <!-- Top bar: heading and action buttons -->
            <div class="top-bar">
                <h2>Inventory History</h2>
                <div class="action-buttons">
                    <button class="btn-print" onclick="printPage()" id="btnPrint">
                        <i data-lucide="printer"></i> Print
                    </button>
                    <button class="btn-csv" onclick="exportCSV()" id="btnCsv">
                        <i data-lucide="download"></i> Download CSV
                    </button>
                </div>
            </div>

            <!-- Print-only header (hidden on screen, visible when printing) -->
            <div class="print-header" id="printHeader">
                <h3 id="printTitle">Inventory Journal</h3>
                <p id="printDateRange"></p>
            </div>

            <!-- Filter bar: product selector + date range -->
            <div class="filter-bar">
                <label for="productSelect">Product:</label>
                <select id="productSelect">
                    <option value="">-- Select Product --</option>
                </select>

                <label for="dateFrom">From:</label>
                <input type="date" id="dateFrom" />

                <label for="dateTo">To:</label>
                <input type="date" id="dateTo" />

                <button class="btn-search" onclick="searchJournal()" id="btnSearch">
                    Search
                </button>
            </div>

            <!-- Summary cards (hidden until a search is performed) -->
            <div class="summary-cards" id="summaryCards">
                <div class="summary-card card-incoming">
                    <div class="card-label">Total Incoming</div>
                    <div class="card-value" id="cardIncoming">0</div>
                </div>
                <div class="summary-card card-sales">
                    <div class="card-label">Total Sales</div>
                    <div class="card-value" id="cardSales">0</div>
                </div>
                <div class="summary-card card-stock">
                    <div class="card-label">Current Stock</div>
                    <div class="card-value" id="cardStock">0</div>
                </div>
            </div>

            <!-- Journal table -->
            <table class="journal-table" id="journalTable">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Incoming</th>
                        <th>Sales</th>
                        <th>Notes</th>
                        <th>T. Qty</th>
                        <th>Date / Time</th>
                        <th>By</th>
                    </tr>
                </thead>
                <tbody id="journalBody">
                    <!-- Default state message -->
                    <tr class="status-row">
                        <td colspan="8">Select a product above to view its inventory history.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="../Script/systemNav.js"></script>
    <script src="../Script/inventoryManagement.js"></script>
</body>
</html>
