<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shoe Playstyle Categories</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="datatables-responsive/css/responsive.bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="sweetalert2/sweetalert2.min.css">
</head>
<?php include "navbar.php"?>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Main Content -->
  <section class="content p-4">
    <div class="container-fluid">
      <div class="row">
      
        <div class="col-12 mb-4">
    <?php include "fresh_kicks.php"?>
  </div>

        <!-- Guards -->
        <div class="col-md-6 col-sm-6 col-xs-12 col-lg-3 col-12">
          <a href="category.php?playstyle=Guard" class="info-box-link d-block text-decoration-none">
          <div class="info-box bg-primary">
            <span class="info-box-icon"><i class="fas fa-bolt"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Guards</span>
              <span class="info-box-number">Ground combat in breakneck speeds</span>
            </div>
          </div>
        </div>

        <!-- Bigs -->
        <div class="col-md-6 col-sm-6 col-xs-12 col-lg-3 col-12">
          <a href="category.php?playstyle=Bigs" class="info-box-link d-block text-decoration-none">
          <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fas fa-shield-alt"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Bigs</span>
              <span class="info-box-number">Stability under intense pressure</span>
            </div>
          </div>
        </div>

        <!-- Power -->
        <div class="col-md-6 col-sm-6 col-xs-12 col-lg-3 col-12">
          <a href="category.php?playstyle=Power" class="info-box-link d-block text-decoration-none">
          <div class="info-box bg-danger">
            <span class="info-box-icon"><i class="fas fa-fire"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Power</span>
              <span class="info-box-number">Explosive energy is top priority</span>
            </div>
          </div>
          </a>
        </div>

        <!-- All-Around -->
<div class="col-md-6 col-sm-6 col-xs-12 col-12 col-lg-3 mb-3">
  <a href="category.php?playstyle=All-Around" class="info-box-link d-block text-decoration-none">
    <div class="info-box bg-warning text-dark h-100">
      <span class="info-box-icon"><i class="fas fa-sync-alt"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">All-Around</span>
        <span class="info-box-number">Change & adapt like water</span>
      </div>
    </div>
  </a>
</div>

      </div>
    </div>
  </section>

</div>

<div>
  <?php include "leaderboard.php" ?>
</div>

<!-- AdminLTE Scripts -->
<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../dist/js/adminlte.min.js"></script>
</body>
</html>

