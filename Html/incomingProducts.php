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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>InvenTra</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../Css/systemNav.css" />
  <link rel="stylesheet" href="../Css/incomingProducts.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet" />
</head>
<body class="incoming-page">
  <div class="systemNav-container">
    <?php include("systemNav.php"); ?>
    <div class="main-content">
      <header class="topbar">
        <button class="hamburger" id="menuBtn">
          <i data-lucide="menu"></i>
        </button>
      </header>

      <div class="container-fluid mt-4">
        <h2 class="text-center mb-4">Incoming Products</h2>

        <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
          <div class="w-50">
            <input id="incomingSearch" type="text" class="form-control" placeholder="Search incoming products..." />
          </div>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#incomingModal">
            <i data-lucide="plus"></i> Add New Incoming
          </button>
        </div>

        <div class="table-responsive">
          <table id="incomingTable" class="table table-bordered table-striped table-hover align-middle text-center">
            <thead class="table-primary">
              <tr>
                <th>Product Code</th>
                <th>Type</th>
                <th>Size</th>
                <th>Department</th>
                <th>Current Qty</th>
                <th>Incoming Qty</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="modal fade" id="incomingModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="incomingModalTitle">Add Incoming Quantity</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="incomingForm">
              <div class="mb-3">
                <label for="productCode" class="form-label">Product Code</label>
                <div class="input-group">
                  <input list="productCodeList" type="text" id="productCode" class="form-control" placeholder="Enter or select product code">
                  <button type="button" class="btn btn-outline-secondary" onclick="loadProductDetails()">Load</button>
                </div>
                <datalist id="productCodeList"></datalist>
                <small id="productCodeError" class="text-danger"></small>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Type</label>
                  <input type="text" id="incomingType" class="form-control" disabled>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Size</label>
                  <input type="text" id="incomingSize" class="form-control" disabled>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Department</label>
                  <input type="text" id="incomingDept" class="form-control" disabled>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Current Quantity</label>
                  <input type="number" id="incomingCurrentQty" class="form-control" disabled>
                </div>
              </div>
              <div class="mb-3">
                <label for="incomingQty" class="form-label">Incoming Quantity</label>
                <input type="number" id="incomingQty" class="form-control" placeholder="Enter incoming quantity">
                <small id="incomingQtyError" class="text-danger"></small>
              </div>
              <div class="text-center">
                <button type="button" class="btn btn-primary w-100" onclick="submitIncoming()">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="../Script/systemNav.js"></script>
  <script src="../Script/incomingProducts.js"></script>
</body>
</html>
