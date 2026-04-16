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

                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

                <link rel="stylesheet" href="../Css/systemNav.css" />
                <link rel="stylesheet" href="../Css/productManagement.css">

                <link
                    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap"
                    rel="stylesheet"
                    />
                
                <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
                <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
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

                <div class="container-fluid mt-4">
                <h2 class="text-center mb-4">Product Management</h2>

                <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
    
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                        <i data-lucide="plus"></i> Add Product
                    </button>

                    <button type="button" class="btn btn-outline-danger" onclick="viewDeleted()">
                        <i data-lucide="trash-2"></i> Deleted Products
                    </button>

                </div>

                <div class="table-responsive">
                    <table id="myTable" class="table table-bordered table-striped table-hover align-middle text-center">
                        <thead class="table-primary">
                            <tr >
                                <th style="text-align:center; vertical-align:middle;">Product Code</th>
                                <th style="text-align:center; vertical-align:middle;">Type</th>
                                <th style="text-align:center; vertical-align:middle;">Size</th>
                                <th style="text-align:center; vertical-align:middle;">Dept</th>
                                <th style="text-align:center; vertical-align:middle;">Price</th>
                                <th style="text-align:center; vertical-align:middle;">Status</th>
                                <th style="text-align:center; vertical-align:middle;">Action</th>
                            </tr>
                        </thead>
                          <tbody></tbody>
                    </table>
                </div>
              </div>

      <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
              <h5 class="modal-title">Product</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
      <div class="modal-body">
        <form id="productForm">

            <div class="mb-3">
              <label for="code" class="form-label">Product Code</label>
              <input type="text" class="form-control" id="code" placeholder="Enter product code">
              <small id="codeError" class="text-danger"></small>
            </div>

            <div class="mb-3">
              <label for="type" class="form-label">Type</label>
              <select class="form-select" id="type" onchange="checkType(this); checkSizeByType();">
                <option value="">Select Type</option>
                <option>ID Lace</option>
                <option>Book</option>
                <option>Uniform</option>
                <option>Merchandise</option>
                <option value="Other">Other</option>
              </select>
              <small id="typeError" class="text-danger"></small>
            </div>

            <div class="mb-3" id="otherTypeContainer" style="display:none;">
              <label for="otherType" class="form-label">Other Type</label>
              <input type="text" class="form-control" id="otherType" placeholder="Enter type">
              <small id="otherTypeError" class="text-danger"></small>
            </div>
            

            <div class="mb-3">
              <label for="size" class="form-label">Size</label>
              <select class="form-select" id="size">
                <option value="">Select Size</option>
                <option>Small</option>
                <option>Medium</option>
                <option>Large</option>
                <option value="None">None</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="dept" class="form-label">Department</label>
              <select class="form-select" id="dept">
                <option value="">Select Department</option>
                <option>CICT</option>
                <option>CBEA</option>
                <option>CAFA</option>
                <option>CAL</option>
                <option>COE</option>
                <option>COED</option>
                <option>CS</option>
                <option>CIT</option>
              </select>
              <small id="deptError" class="text-danger"></small>
            </div>

            <div class="mb-3">
              <label for="status" class="form-label">Status</label>
              <select class="form-select" id="status">
                <option value="">Select Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <small id="statusError" class="text-danger"></small>
            </div>

            <div class="mb-3">
              <label for="price" class="form-label">Price</label>
              <input type="number" class="form-control" id="price" placeholder="0.00">
              <small id="priceError" class="text-danger"></small>
            </div>

            <div class="text-center">
              <button id="saveProductBtn" type="button" class="btn btn-primary w-100" onclick="store()">Save</button>
            </div>

        </form>
 
      </div>
    </div>
  </div>
</div>

      <div class="modal fade" id="deletedModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Deleted Products</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <input type="text" id="deletedSearch" class="form-control" placeholder="Search deleted products..." oninput="filterDeletedProducts()">
              </div>
              <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center" id="deletedTable">
                  <thead class="table-secondary">
                    <tr>
                      <th>Product Code</th>
                      <th>Type</th>
                      <th>Size</th>
                      <th>Dept</th>
                      <th>Price</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

            <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

            <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

            <script src="https://unpkg.com/lucide@latest"></script>



            <script src="../Script/systemNav.js"></script>
            <script src="../Script/productManagement.js"></script>

            </body>
        </html>