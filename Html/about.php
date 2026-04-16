<?php include("navbar.php");?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>InvenTra - About</title>

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    
    <link rel="stylesheet" href="../Css/nav.css" />
    <link rel="stylesheet" href="../Css/about.css" />

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  </head>
  <body>
    <!-- Hero -->
    <section class="hero-sec" id="home">
      <div class="container">
        <div class="row justify-content-center text-center">
          <div class="col-lg-10">
            <h1 class="hero-title">
              About <span style="color: #48cae4">InvenTra</span>
            </h1>
            <p class="hero-tagline">
              Never Lose Track of School Supplies Again
            </p>
            <p class="hero-description">
              We're revolutionizing how schools manage their inventory—making it
              simple, efficient, and stress-free for educators everywhere.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Background -->
    <section class="background-section py-5" id="about">
      <div class="container">
        <div class="text-center mb-5">
          <p class="section-subtitle">Our Story</p>
          <h2 class="section-title">Project Background</h2>
          <p class="section-intro mx-auto">
            EduTrack was born from a simple frustration: watching school
            administrators and staff waste precious time manually tracking
            inventory instead of focusing on more important tasks.
          </p>
        </div>
        <div class="row g-4 mb-5">
          <div class="col-lg-6">
            <div class="background-card h-100 fade-in">
              <h3>How It Started</h3>
              <p>
                EduTrack was developed to address the growing need for efficient
                school inventory management. Schools across the country struggle
                with tracking their supplies, merchandise, books, uniforms, and
                ID laces using outdated manual methods.
              </p>
              <p>
                The vision was clear: create a centralized digital platform that
                would streamline inventory tracking, reduce administrative
                burden, and ensure that school resources are properly monitored
                and maintained.
              </p>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="background-card h-100 fade-in">
              <h3>The Problem</h3>
              <p>
                Traditional inventory management in schools is inefficient and
                error-prone. Administrators and staff waste countless hours
                manually recording stock levels, searching for items, and
                reconciling discrepancies. Without a proper tracking system,
                schools face challenges in monitoring supply quantity and
                product availability.
              </p>
              <p>
                This leads to overstocking, understocking, misplaced items, and
                difficulty in maintaining accurate records of merchandise,
                books, uniforms, and ID laces. EduTrack solves these problems by
                providing a comprehensive digital solution for all
                school-related inventory needs.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Objectives -->
    <section class="objectives-section py-5" id="services">
      <div class="container">
        <div class="text-center mb-5">
          <p class="section-subtitle">Our Mission</p>
          <h2 class="section-title">What We're Building</h2>
          <p class="section-intro mx-auto">
            Our goal is simple: give school administrators and staff their time
            back so they can focus on what matters most—managing school
            operations efficiently.
          </p>
        </div>
        <div class="row g-4 justify-content-center">
          <div class="col-lg-4">
            <div class="objective-card text-center fade-in">
              <div class="objective-number mx-auto">1</div>
              <h3>Simplify Inventory</h3>
              <p>
                Create a platform anyone can use—no technical training required.
                Track items with a simple search.
              </p>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="objective-card text-center fade-in">
              <div class="objective-number mx-auto">2</div>
              <h3>Save Time & Money</h3>
              <p>
                Reduce time spent on tracking by 80%. Eliminate duplicate
                purchases and prevent supply losses.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Users -->
    <section class="users-section py-5" id="gallery">
      <div class="container">
        <div class="text-center mb-5">
          <p class="section-subtitle">Who We Serve</p>
          <h2 class="section-title">Target Users</h2>
          <p class="section-intro mx-auto">
            EduTrack is specifically designed for school administrators and
            staff members who manage and track school inventory.
          </p>
        </div>
        <div class="row g-4 justify-content-center">
          <div class="col-lg-8">
            <div class="user-card d-flex align-items-center fade-in">
              <div class="user-icon">👥</div>
              <div class="user-info">
                <h4>Admin/Staffs</h4>
                <p>
                  School administrators and staff members responsible for
                  managing school inventory including merchandise, books,
                  uniforms, and ID laces. Track supply quantities, monitor
                  product availability, and maintain accurate records of all
                  school-related items.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-4.0.0.js" integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U=" crossorigin="anonymous"></script>
    <script src="../Script/nav.js"></script>
    <script src="../Script/logscript.js"></script>
    <script src="../Script/regscript.js"></script>
  </body>
</html>
