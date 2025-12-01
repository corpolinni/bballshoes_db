<?php
include '../config.php';

$playstyle = $_GET['playstyle'] ?? '';
$valid = ['Guard','Bigs','Power','All-Around'];
if (!in_array($playstyle, $valid)) {
    die("Invalid category.");
}

// Fetch shoes
$query = "
SELECT id, brand, model, image_path,
       ROUND((traction + cushion + bounce + support + durability) / 5, 1) AS overall
FROM shoes
WHERE playstyle = ?
ORDER BY release_date DESC
";
$stmt = $db->prepare($query);
$stmt->bind_param('s', $playstyle);
$stmt->execute();
$result = $stmt->get_result();

$shoes = [];
while ($row = $result->fetch_assoc()) {
    $shoes[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($playstyle) ?> Shoes</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <!-- Optional: SweetAlert (if used elsewhere) -->
  <link rel="stylesheet" href="../plugins/sweetalert2/sweetalert2.min.css">

  <style>
    .shoe-card-img {
      height: 180px;
      object-fit: cover;
    }
    .shoe-model-link {
      color: #495057;
      font-weight: 600;
      text-decoration: none;
    }
    .shoe-model-link:hover {
      color: #007bff;
      text-decoration: none;
    }
    .badge-tag {
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
    }
    .tag-Guard      { background-color: #007bff; color: #fff; }
    .tag-Bigs       { background-color: #28a745; color: #fff; }
    .tag-Power      { background-color: #dc3545; color: #fff; }
    .tag-All-Around { background-color: #ffc107; color: #212529; }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card mt-3">
            <div class="card-header bg-primary text-white">
              <h3 class="card-title">
                <i class="fas fa-shoe-prints"></i>
                <?= htmlspecialchars($playstyle) ?> Shoes
                <span class="badge badge-light ml-2"><?= count($shoes) ?></span>
              </h3>
            </div>
            <div class="card-body">
              <?php if (empty($shoes)): ?>
                <p class="text-center text-muted">No shoes found in this category.</p>
              <?php else: ?>
                <div class="row">
                  <?php foreach ($shoes as $s):
                    $img = $s['image_path'] ? $s['image_path'] : "../dist/img/default-shoe.jpg";
                    $modelSlug = strtolower(str_replace([' ', '/'], '-', $s['model']));
                    $detailUrl = "shoe.php?model=" . urlencode($modelSlug);
                  ?>
                    <div class="col-lg-2 col-md-4 col-12 mb-4">
                      <div class="card h-100 shadow-sm">
                        <img src="<?= htmlspecialchars($img) ?>" class="card-img-top shoe-card-img" alt="<?= htmlspecialchars($s['brand'].' '.$s['model']) ?>">
                        <div class="card-body text-center d-flex flex-column">
                          <h6 class="card-title mb-2">
                            <a href="<?= $detailUrl ?>" class="shoe-model-link">
                              <?= htmlspecialchars($s['brand']) ?> <?= htmlspecialchars($s['model']) ?>
                            </a>
                          </h6>
                          <p class="mt-auto">
                            <strong>Overall: <?= $s['overall'] ?></strong>
                          </p>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

</div>

<!-- Scripts (same as dashboard) -->
<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../dist/js/adminlte.min.js"></script>
</body>
</html>