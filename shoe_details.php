<style>
.score-list { margin:0; padding:0; list-style:none; }
.score-list li { 
    display:flex; align-items:flex-start; gap:10px; padding:10px 0; 
    font-size:0.95rem; border:none !important; background:transparent !important;
}

/* Main category icon */
.score-list li i:first-child {
    font-size:1.8rem; 
    width:32px; 
    flex-shrink:0; 
    text-align:center;
    margin-top:4px; 
    text-shadow:1px 1px 2px rgba(0,0,0,0.3);
}

/* Tiny info icon – super tight now */
.info-icon {
    font-size: 0.75rem !important;   /* even smaller */
    opacity: 0.7;
    cursor: help;
    margin-left: 0px !important;     /* was 0.25rem → now only 2px */
    position: relative;
    top: -1px;                       /* nudges it down 1px so it aligns perfectly with text */
}
.info-icon:hover { opacity: 1; }
</style>

<?php
echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>';

if (!isset($shoe) || !isset($db)) return;
$shoeId = (int)$shoe['id'];

// [Your warnings + justifications code – unchanged]

$warnings = []; 
$result = $db->query("SELECT warning_text FROM warnings WHERE shoe_id = $shoeId ORDER BY created_at DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $warnings[] = htmlspecialchars($row['warning_text'], ENT_QUOTES, 'UTF-8');
    }
}

$justifications = [];
$result = $db->query("SELECT category, just_text FROM score_details WHERE shoe_id = $shoeId");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $justifications[strtolower($row['category'])] = htmlspecialchars($row['just_text'], ENT_QUOTES, 'UTF-8');
    }
}

$icons = ['Traction'=>'fa-shoe-prints','Cushion'=>'fa-feather-alt','Bounce'=>'fa-chart-line','Support'=>'fa-hands','Durability'=>'fa-shield-alt','Lockdown'=>'fa-lock','Ventilation'=>'fa-wind','Mobility'=>'fa-running'];
$iconColors = ['Traction'=>'#0099ff','Cushion'=>'#2dc000','Bounce'=>'#ffd000','Support'=>'#ff0037','Durability'=>'#9900ff','Lockdown'=>'#ff9900','Ventilation'=>'#00cccc','Mobility'=>'#000000'];
$statCategories = ['traction'=>'Traction','cushion'=>'Cushion','bounce'=>'Bounce','support'=>'Support','durability'=>'Durability','lockdown'=>'Lockdown','ventilation'=>'Ventilation','mobility'=>'Mobility'];

$tooltips = [
    'Traction'     => 'Grip on court — stops, cuts, and slides',
    'Cushion'      => 'Impact protection for joints',
    'Bounce'       => 'Energy return — how springy the shoe feels',
    'Support'      => 'Stability and foot containment',
    'Durability'   => 'How long the shoe lasts under heavy use',
    'Lockdown'     => 'How securely your foot stays in place',
    'Ventilation'  => 'Breathability and temperature control',
    'Mobility'     => 'Natural foot movement and agility'
];

echo '<div class="row mt-5 mb-5 gx-5">';

if (!empty($justifications)) {
    echo '<div class="col-lg-7 col-md-12">';
    echo '<h4 class="mb-4"><i class="fas fa-search me-2"></i>Score Breakdown</h4>';
    echo '<ul class="score-list">';

    foreach ($statCategories as $dbCol => $displayName) {
        $key = strtolower($dbCol);
        if (!isset($shoe[$dbCol]) || !isset($justifications[$key])) continue;

        $color = $iconColors[$displayName] ?? '#666';
        $iconClass = $icons[$displayName] ?? 'fa-circle';

        echo '<li>';
        echo '  <i class="fas ' . $iconClass . '" style="color:' . $color . ';" aria-hidden="true"></i>';
        echo '  <div>';

        // INFO ICON BEFORE THE NAME
        if (isset($tooltips[$displayName])) {
            echo '<i class="fas fa-info-circle info-icon me-1" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top" 
                       title="' . htmlspecialchars($tooltips[$displayName], ENT_QUOTES) . '"></i>';
        }

        echo '<strong>' . $displayName . ':</strong> ' . $justifications[$key];
        echo '  </div>';
        echo '</li>';
    }
    echo '</ul></div>';
}

// Warnings column (unchanged)
if (!empty($warnings)) {
    echo '<div class="col-lg-5 col-md-12 mt-4 mt-lg-0">';
    echo '<div class="alert alert-warning" role="alert">';
    echo '<h4 class="alert-heading">Important Warnings</h4><hr>';
    echo '<ul class="mb-0">';
    foreach ($warnings as $w) echo '<li>' . $w . '</li>';
    echo '</ul></div></div>';
}

echo '</div>';

// CRITICAL: Initialize tooltips AFTER Bootstrap JS is loaded
?>
<script>
// Only run when DOM is fully ready and Bootstrap is available
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>