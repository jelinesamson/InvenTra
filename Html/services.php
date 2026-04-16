<?php include("navbar.php");?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>InvenTra - Services</title>

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />

    <link rel="stylesheet" href="../Css/nav.css" />
    <link rel="stylesheet" href="../Css/services.css" />

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  </head>
  <body>
    <div class="container mt-5">
      <h1 class="brand-text display-6 text-center fw-bold text-shadow">
        InvenTra
      </h1>
      <h1 class="display-4 text-center fw-bold text-shadow">
        Services / Features
      </h1>

      <p class="text-center mb-4 lead">
        Manages and monitors school-related products to ensure organized
        inventory records.
      </p>

      <div class="row g-4 align-items-stretch">
        <div class="col-md-6">
          <div class="card service-card p-3 h-100">
            <h5 class="feature-view text-shadow">Product Management</h5>
            <p class="lead">
              Allow users to view, add, update and remove school-related
              products, including merchandise, books, ID laces and uniforms.
            </p>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card service-card p-3 h-100">
            <h5 class="feature-sales text-shadow">Inventory Management</h5>
            <p class="lead">
              Maintains accurate stock levels by monitoring incoming supplies
              and outgoing sales.
            </p>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card service-card p-3 h-100">
            <h5 class="feature-report text-shadow">Sales Report</h5>
            <p class="lead">
              Creates sales reports to monitor total sales and product
              performance, helping users to understand sales trends over time.
            </p>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card service-card p-3 h-100">
            <h5 class="feature-alert text-shadow">Low Stock Alert</h5>
            <p class="lead">
              Displays summary of daily sales and notifies users of low
              inventory levels, ensuring that restocking can be done on time.
            </p>
          </div>
        </div>

        <div class="col-md-6 col-lg-6 mx-auto">
          <div class="card service-card p-3 h-100">
            <h5 class="feature-alert text-shadow">Sales Management</h5>
            <p class="lead">
              Records all sales transactions, such as product details, date of
              sale and quantity sold, to ensure accurate monitoring of daily and
              overall sales.
            </p>
          </div>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-4.0.0.js" integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U=" crossorigin="anonymous"></script>
    <script src="../Script/nav.js"></script>
    <script src="../Script/logscript.js"></script>
    <script src="../Script/regscript.js"></script>
  </body>
</html>
