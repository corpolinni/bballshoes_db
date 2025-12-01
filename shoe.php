<?php
include('config.php'); // inside /dashboard/

$model = isset($_GET['model']) ? trim($_GET['model']) : '';

$shoe = null;

if ($model !== '') {
  // Escape the model name safely for SQL
  $modelSafe = $db->real_escape_string($model);

  $query = "
    SELECT 
      id, brand, model, playstyle, availability, release_date, image_path, fit, weight, squeak_profile, weight_profile, 
      traction, cushion, bounce, support, durability, lockdown, ventilation, mobility,
      ROUND((traction + cushion + bounce + support + durability + lockdown + ventilation + mobility)/8, 1) AS overall
    FROM shoes
    WHERE model = '$modelSafe'
    LIMIT 1
  ";

  $result = $db->query($query);
  if ($result && $result->num_rows > 0) {
    $shoe = $result->fetch_assoc();
  }
}

// fallback if nothing found
if (!$shoe) {
  die("<div style='padding:2rem; font-family:sans-serif; color:#555;'>
        <h3>⚠️ Shoe not found.</h3>
      </div>");
}

// ----------------------------------------------------------------------
// 💡 CRITICAL FIX: Determine image path (Removed file_exists check)
// ----------------------------------------------------------------------
$dbImagePath = $shoe['image_path'];
$defaultFallback = "../dist/img/default-shoe.jpg";

$imageSrc = (!empty($dbImagePath))
  ? $dbImagePath // Use the path from the database directly
  : $defaultFallback; // Only fallback if the database path is empty
// ----------------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($shoe['brand'] . ' ' . $shoe['model']); ?></title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="plugins/sweetalert2/sweetalert2.min.css">

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    body {
      background: #f8f9fa;
    }
    .shoe-container {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 2rem; /* The space between the image and the stats box */
      padding: 2rem;
      flex-wrap: wrap;
    }
    .shoe-container i {
    color: #007bff; /* or whatever accent color you prefer */
    margin-right: 8px;
    width: 20px; /* keeps all icons aligned */
    font-size: 1.2rem;
}
    .shoe-img {
      flex: 0 0 40%;
      text-align: left;
    }
    .shoe-img img {
      width: 100%;
      max-width: 350px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    /* 🚀 CRITICAL FIX HERE */
    .stats-container {
      /* Change flex: 0 0 50% to flex: 1 1 50% */
      flex: 1 1 50%; 
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      padding: 1.5rem;
      /* Also ensure canvas content fits within the padding */
      min-width: 0; /* Prevents flex children from overflowing */
    }
    /* 🚀 OPTIONAL: Force canvas to always take full width of its parent */
    #statsChart {
        width: 100% !important;
        /* height: auto !important; /* Let Chart.js handle the height based on its aspect ratio */
    }
    
    h3 {
      margin-bottom: 1rem;
    }
    .badge-tag {
      background: #333;
      color: #fff;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 0.85rem;
      margin-right: 5px;
    }
    .badge-tag {
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 500;
    display: inline-block;
    margin-top: 6px;
  }

  .chart {
  height: 380px !important;   /* You control the height here */
}
.chart canvas {
  width: 100% !important;
  height: 100% !important;
}

  /* Color-coded Playstyle Tags */
  .tag-Guard { background-color: #007bff; color: white; }
  .tag-Bigs { background-color: #28a745; color: white; }
  .tag-Power { background-color: #dc3545; color: white; }
  .tag-All-Around { background-color: #ffc107; color: #212529; }
</style>
</head>
<body>

<div class="container-fluid mt-4">
  <a href="index.php" class="btn btn-light mb-3"><i class="fas fa-arrow-left"></i> Back</a>

  <div class="shoe-container">
    <div class="shoe-img">
      <img src="<?php //echo htmlspecialchars($imageSrc); ?>" alt="<?php //echo htmlspecialchars($shoe['model']); ?>">
      <h4 class="mt-3"><?php //echo htmlspecialchars($shoe['brand'] . ' ' . $shoe['model']); ?></h4>

      <!--Colored badge for every play style-->
      <?php 
      $playstyle = htmlspecialchars($shoe['playstyle']);
      $playstyleClass = 'tag-' . preg_replace('/\s+/', '-', $playstyle); // Replace spaces with hyphens
      ?>
      <span class="badge-tag <?php echo $playstyleClass; ?>"><?php echo $playstyle; ?></span>
      <span class="badge bg-light text-dark">
        <i class="fas fa-shopping-cart"></i>
        <strong>Availablity: </strong>
        <?php echo htmlspecialchars($shoe['availability']); ?>
      </span>
<!--Date format turned into MM-YYYY-->
<p class="text-muted mt-2">Released: 
  <?php 
    if (!empty($shoe['release_date']) && $shoe['release_date'] !== '0000-00-00') {
      $parts = explode('-', $shoe['release_date']);
      if (count($parts) >= 2) {
        $year = strlen($parts[0]) == 4 ? $parts[0] : $parts[1];
        $month = strlen($parts[0]) == 4 ? $parts[1] : $parts[0];
        echo date('M Y', mktime(0, 0, 0, $month, 1, $year));
      } else {
        echo "TBA";
      }
    } else {
      echo "TBA";
    }
  ?>
</p>
      <h5>⭐ Overall: <?php echo htmlspecialchars($shoe['overall']); ?></h5>

          <!-- New Attributes -->
    <ul class="list-unstyled mt-3">
        <li><i class="fas fa-ruler-horizontal"></i><strong> Fit:</strong> <?php echo htmlspecialchars($shoe['fit']); ?></li>
        <li><i class="fas fa-weight-hanging"></i><strong> Weight:</strong> <?php echo htmlspecialchars($shoe['weight']); ?></li>
        <li><i class="fas fa-balance-scale-left"></i><strong> Weight Profile:</strong> <?php echo htmlspecialchars($shoe['weight_profile']); ?></li>
        <li><i class="fas fa-volume-up"></i><strong> Squeak Profile:</strong> <?php echo htmlspecialchars($shoe['squeak_profile']); ?></li>
    </ul>
    </div>

    <div class="stats-container">
  <h3><i class="fas fa-chart-bar text-primary"></i> Performance Stats</h3>

  <!-- FIXED: No more stretching -->
  <div style="position: relative; height: 380px; width: 100%;">
    <canvas id="statsChart"></canvas>
  </div>
</div>
    </div>
  </div>
  <div>
  <?php include('shoe_details.php'); ?>
</div>
  <?php include('recomm.php');?>
</div>



<script>
    // Descriptions of the chart
    const chartIcons = [
        '\uf54b', // Traction - fa-shoe-prints
        '\uf381', // Cushion - fa-feather-alt
        '\uf201', // Bounce - fa-toggle-on
        '\uf4c2', // Support - fa-hands-helping
        '\uf3ed', // Durability - fa-cogs
        '\uf023', // Lockdown - fa-lock
        '\uf72e', // Ventilation - fa-wind
        '\uf0fb'  // mobility - fa-plane
    ];

    const ctx = document.getElementById('statsChart');

    const data = {
        labels: [
            'Traction', 
            'Cushion', 
            'Bounce', 
            'Support', 
            'Durability', 
            'Lockdown',      // 🚀 NEW Label
            'Ventilation',   // 🚀 NEW Label
            'mobility'   // 🚀 NEW Label
        ],
        datasets: [{
            label: 'Rating',
            data: [
                <?php echo $shoe['traction']; ?>,
                <?php echo $shoe['cushion']; ?>,
                <?php echo $shoe['bounce']; ?>,
                <?php echo $shoe['support']; ?>,
                <?php echo $shoe['durability']; ?>,
                <?php echo $shoe['lockdown']; ?>,    // 🚀 NEW Data Point
                <?php echo $shoe['ventilation']; ?>, // 🚀 NEW Data Point
                <?php echo $shoe['mobility']; ?> // 🚀 NEW Data Point
            ],
            backgroundColor: [
                'rgba(0, 153, 255, 1)',
                'rgba(45, 192, 0, 1)',
                'rgba(255, 208, 0, 1)',
                'rgba(255, 0, 55, 1)',
                'rgba(153, 0, 255, 0.8)',
                'rgba(255, 153, 0, 1)', // Orange (Lockdown)
                'rgba(0, 204, 204, 1)',  // Lime Green (Ventilation)
                'rgba(0, 0, 0, 1)'  // Cyan/Teal (mobility)
            ],
            borderRadius: 6
        }]
    };

    // 🧩 Custom Plugin to draw ONLY the numeric scores inside the bars
    const scoreDisplayPlugin = {
        id: 'scoreDisplay',
        afterDatasetsDraw(chart) {
            const { ctx, scales: { x, y } } = chart;
            const meta = chart.getDatasetMeta(0);
            
            ctx.save();
            
            meta.data.forEach((bar, index) => {
                // Get the score for the current bar
                const scoreValue = chart.data.datasets[0].data[index];
                
                // Set font style for the score text
                ctx.font = 'bold 16px sans-serif'; 
                ctx.fillStyle = '#fff'; // White color for visibility inside the bar
                ctx.textAlign = 'center';
                ctx.textBaseline = 'top'; 
                
                // Calculate position: 20 pixels down from the top edge of the bar (bar.y)
                const scoreYPos = bar.y + 20; 
                
                // Draw the score
                ctx.fillText(scoreValue, bar.x, scoreYPos);
            });
            
            ctx.restore();
        }
    };

new Chart(ctx, {
    type: 'bar',
    data: data,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
            padding: {
                right: 20   // Prevents the last bar from being cut off
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: { stepSize: 20 }
            },
            x: {
                offset: true,                    // THIS IS THE MAGIC LINE
                grid: { 
                    offset: true 
                },
                ticks: {
                    callback: function(val, index) {
                        return chartIcons[index];
                    },
                    font: {
                        family: '"Font Awesome 5 Free"',
                        size: 24, 
                        weight: 900
                    },
                    color: '#333',
                    maxRotation: 0,
                    minRotation: 0
                }
            }
        },
        plugins: {
            legend: { display: false },
            tooltip: { enabled: true }
        }
    },
    plugins: [scoreDisplayPlugin]
});
</script>
</body>
</html>