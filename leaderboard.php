<?php
include('config.php');

// Default filter: current year
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// ------------------------------------------------------------------
// Extract year from any reasonable date format
// ------------------------------------------------------------------
function extractYear($date) {
    if (empty($date) || $date === '0000-00-00') return null;

    // Try standard formats first
    $dt = DateTime::createFromFormat('Y-m-d', $date) ?:
          DateTime::createFromFormat('m-Y', $date) ?:
          DateTime::createFromFormat('m/Y', $date) ?:
          DateTime::createFromFormat('Y-m', $date);

    if ($dt) return (int)$dt->format('Y');

    // Fallback: look for a 4-digit year anywhere
    if (preg_match('/\b(19|20)\d{2}\b/', $date, $m)) {
        return (int)$m[0];
    }
    return null;
}

// Fetch top 10 sneakers (by overall score)
$query = "SELECT 
    brand, model, playstyle, availability, release_date,
    ROUND((traction + cushion + bounce + support + durability) / 5, 1) AS overall
  FROM shoes 
  WHERE release_date IS NOT NULL 
    AND release_date != '' 
    AND release_date != '0000-00-00'
  ORDER BY overall DESC, release_date DESC 
  LIMIT 10";

$result = $db->query($query);
$shoes = [];
while ($row = $result->fetch_assoc()) {
    $row['release_year'] = extractYear($row['release_date']);
    // Show shoe if it matches selected year OR if we're viewing current year (show everything from this year + upcoming)
    if ($row['release_year'] == $year || ($year == date('Y') && $row['release_year'] >= $year)) {
        $shoes[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sneaker of the Year <?= $year ?> Leaderboard</title>
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <section class="content mt-4">
    <div class="container-fluid">

      <div class="row mb-3">
        <div class="col-md-8">
          <h2>Sneaker of the Year Leaderboard <?= $year ?></h2>
        </div>
        <div class="col-md-4 text-right">
          <form method="GET" class="form-inline justify-content-end">
            <label for="year" class="mr-2">Year:</label>
            <select name="year" id="year" class="form-control" onchange="this.form.submit()">
              <?php for ($y = date('Y')+1; $y >= 2020; $y--): ?>
                <option value="<?= $y ?>" <?= ($y == $year) ? 'selected' : '' ?>><?= $y ?></option>
              <?php endfor; ?>
            </select>
          </form>
        </div>
      </div>

      <div class="card card-outline card-warning shadow">
        <div class="card-body table-responsive p-0">
          <table class="table table-hover text-center">
            <thead class="bg-warning text-dark">
              <tr>
                <th>Rank</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Playstyle</th>
                <th>Score</th>
                <th>Status</th>
                <th>Released</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($shoes) > 0):
                $rank = 1;
                foreach ($shoes as $shoe):
                  // Clean date display: always show as "Mar 2025"
                  $dateDisplay = "TBA";
                  if (!empty($shoe['release_date']) && $shoe['release_date'] !== '0000-00-00') {
                      $dt = DateTime::createFromFormat('Y-m-d', $shoe['release_date'])
                         ?: DateTime::createFromFormat('m-Y', $shoe['release_date'])
                         ?: DateTime::createFromFormat('m/Y', $shoe['release_date'])
                         ?: DateTime::createFromFormat('Y-m', $shoe['release_date']);

                      if ($dt) {
                          $dateDisplay = $dt->format('M Y'); // ← This is what you wanted
                      }
                  }
              ?>
                <tr>
                  <td><?= $rank == 1 ? '1st Place Medal ' : '' ?><?= $rank ?></td>
                  <td><?= htmlspecialchars($shoe['brand']) ?></td>
                  <td>
                    <a href="shoe.php?model=<?= urlencode($shoe['model']) ?>" class="text-primary font-weight-bold">
                      <?= htmlspecialchars($shoe['model']) ?>
                    </a>
                  </td>
                  <td><?= htmlspecialchars($shoe['playstyle']) ?></td>
                  <td><strong><?= $shoe['overall'] ?></strong></td>
                  <td>
                    <span class="badge badge-<?= 
                      in_array($shoe['availability'], ['Available', 'Widely Available']) ? 'success' : 'secondary'
                    ?>">
                      <?= htmlspecialchars($shoe['availability']) ?>
                    </span>
                  </td>
                  <td><?= $dateDisplay ?></td>
                </tr>
              <?php 
                  $rank++;
                endforeach;
              else: ?>
                <tr><td colspan="7">No shoes found for <?= $year ?>.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="card-footer text-center small text-muted">
          Rankings based on traction • cushion • bounce • support • durability
        </div>
      </div>

    </div>
  </section>

</div>
</body>
</html>