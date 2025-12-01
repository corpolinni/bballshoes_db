<?php
// ==========================================
// 📂 recommendations.php
// Shows similar shoes based on performance
// ==========================================

if (!isset($db) || !isset($shoe)) {
    die("Missing context for recommendations.");
}

$currentShoeId = $shoe['id'];

// Optionally limit by same playstyle for better accuracy
$playstyle = $db->real_escape_string($shoe['playstyle']);

$query = "
  SELECT 
    id, brand, model, image_path,
    traction, cushion, bounce, support, durability, lockdown, ventilation, mobility
  FROM shoes
  WHERE id != $currentShoeId
    AND playstyle = '$playstyle'
";

$result = $db->query($query);
$recommendations = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Euclidean distance
        $distance = sqrt(
            pow($row['traction'] - $shoe['traction'], 2) +
            pow($row['cushion'] - $shoe['cushion'], 2) +
            pow($row['bounce'] - $shoe['bounce'], 2) +
            pow($row['support'] - $shoe['support'], 2) +
            pow($row['durability'] - $shoe['durability'], 2) +
            pow($row['lockdown'] - $shoe['lockdown'], 2) +
            pow($row['ventilation'] - $shoe['ventilation'], 2) +
            pow($row['mobility'] - $shoe['mobility'], 2)
        );

        $row['similarity'] = $distance;
        $recommendations[] = $row;
    }

    // Sort by similarity (ascending)
    usort($recommendations, fn($a, $b) => $a['similarity'] <=> $b['similarity']);
    $recommendations = array_slice($recommendations, 0, 4);
}
?>

<?php if (!empty($recommendations)): ?>
<div class="container mt-5">
  <h4><i class="fas fa-lightbulb text-warning"></i> Similar Sneakers You Might Like</h4>
  <div class="row">
    <?php foreach ($recommendations as $rec): ?>
      <div class="col-md-3 col-sm-6 mt-3">
        <div class="card h-100 shadow-sm border-0 hover-shadow">
          <img src="<?php //echo htmlspecialchars($rec['image_path']); ?>" 
               class="card-img-top" 
               alt="<?php echo htmlspecialchars($rec['model']); ?>">
          <div class="card-body text-center">
            <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($rec['brand']); ?></h6>
            <p class="text-muted mb-2"><?php echo htmlspecialchars($rec['model']); ?></p>
            <a href="shoe.php?model=<?php echo urlencode($rec['model']); ?>" 
               class="btn btn-outline-primary btn-sm">
              View Details
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<style>
.hover-shadow:hover {
  box-shadow: 0 6px 20px rgba(0,0,0,0.15);
  transform: translateY(-3px);
  transition: all 0.2s ease-in-out;
}
</style>
