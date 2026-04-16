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
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>InvenTra</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../Css/systemNav.css"/>
  <link rel="stylesheet" href="../Css/salesReport.css"/>
</head>
<body>
<div class="systemNav-container">

  <?php include('systemNav.php'); ?>

  <main class="main">
    <div class="main-content">
    <!-- Top bar -->
    <header class="topbar">
      <button class="hamburger" id="menuBtn"><i data-lucide="menu"></i></button>
      <h2>Sales Report</h2>
    </header>

    <!-- Filters -->
    <div class="filter-bar">
      <select id="selMonth"></select>
      <select id="selType">
        <option value="">All Categories</option>
      </select>
      
      <input type="text" id="searchTxn" placeholder="Search TXN ID..." style="padding: 6px 10px; border: 1px solid #ddd; border-radius: 4px; outline: none; flex: 1; max-width: 250px;">
      
      <button class="btn-export" id="btnCSV">
        <i data-lucide="download" style="width:14px;height:14px;"></i> Export CSV
      </button>
      <span class="filter-label" id="lblGenerated"></span>
    </div>

    <!-- KPI -->
    <div class="kpi-grid">
      <div class="kpi-card kpi-blue">
        <div class="kpi-label">Total Revenue</div>
        <div class="kpi-value" id="kTotal">—</div>
        <div class="kpi-sub" id="kTotalChg">—</div>
      </div>
      <div class="kpi-card kpi-green">
        <div class="kpi-label">Total Qty Sold</div>
        <div class="kpi-value" id="kQty">—</div>
        <div class="kpi-sub" id="kQtyChg">—</div>
      </div>
      <div class="kpi-card kpi-orange">
        <div class="kpi-label">Top Category</div>
        <div class="kpi-value" id="kTop" style="font-size:1.1rem;line-height:1.3">—</div>
        <div class="kpi-sub neu">by revenue</div>
      </div>
      <div class="kpi-card kpi-teal">
        <div class="kpi-label">VAT Collected (12%)</div>
        <div class="kpi-value" id="kVat">—</div>
        <div class="kpi-sub neu">this month</div>
      </div>
    </div>

    <!-- Tables -->
    <div class="section-card">
      <div class="tab-bar">
        <button class="tab-btn active" data-tab="summary">
          Product Summary
        </button>
        <button class="tab-btn" data-tab="history">
          Transaction History
        </button>
      </div>

      <!-- Product Summary -->
      <div class="tab-pane active" id="tab-summary">
        <div class="table-wrap">
          <table id="tblSummary">
            <thead>
              <tr>
                <th>#</th>
                <th>Product</th>
                <th>Category</th>
                <th>Unit Price</th>
                <th>Total Qty</th>
                <th>Total Amount</th>
                <th>VAT (12%)</th>
              </tr>
            </thead>
            <tbody id="bodySummary"></tbody>
            <tfoot id="footSummary"></tfoot>
          </table>
        </div>
      </div>

      <!-- Transaction History -->
      <div class="tab-pane" id="tab-history">
        <div class="table-wrap">
          <table id="tblHistory">
            <thead>
              <tr>
                <th>#</th>
                <th>TXN ID</th>
                <th>Product</th>
                <th>Category</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total Price</th>
                <th>Paid</th>
                <th>Change</th>
                <th>VAT (12%)</th>
                <th>Date & Time</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="bodyHistory"></tbody>
            <tfoot id="footHistory"></tfoot>
          </table>
        </div>
      </div>
    </div>

    <div class="page-footer">
      EduTrack School Inventory System &nbsp;·&nbsp; Sales Report &nbsp;·&nbsp; Internal Use Only
    </div>

  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script src="../Script/systemNav.js"></script>
<script src="../Script/salesReport.js"></script>
</body>
</html>
