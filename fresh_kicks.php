<?php
include('config.php');

// Fetch sneakers ordered by newest releases
$query = "
SELECT 
  id, brand, model, playstyle, availability, release_date, image_path,
  ROUND((traction + cushion + bounce + support + durability) / 5, 1) AS overall
FROM shoes
ORDER BY release_date DESC
LIMIT 30
";
$result = $db->query($query);

$sneakers = [];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $sneakers[] = $row;
  }
}
?>

<div class="fresh-kicks-container mb-4">
  <h4 class="mb-3"><i class="fas fa-shoe-prints text-primary"></i> Fresh Kicks</h4>

  <div class="carousel-container position-relative">
    <button class="carousel-btn left" onclick="prevPage()"><i class="fas fa-chevron-left"></i></button>

    <div class="carousel-row" id="carouselRow"></div>

    <button class="carousel-btn right" onclick="nextPage()"><i class="fas fa-chevron-right"></i></button>
  </div>

  <div class="carousel-indicator text-center mt-2 text-muted" id="pageIndicator"></div>
</div>

<style>
  .carousel-container {
    overflow: hidden;
    width: 100%;
    position: relative;
  }

  .carousel-row {
    display: flex;
    justify-content: center;
    gap: 1rem;
    transition: transform 0.5s ease;
    flex-wrap: nowrap;
  }

  .sneaker-card {
    flex: 0 0 18%;
    border-radius: 10px;
    overflow: hidden;
    color: white;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    background: #fff;
  }

  .sneaker-header {
    padding: 8px;
    font-weight: bold;
  }

  .sneaker-body {
    color: #333;
    padding: 10px;
  }

  .sneaker-body img {
    width: 100%;
    height: 130px;
    object-fit: cover;
    border-radius: 6px;
  }

  .carousel-btn {
    position: absolute;
    top: 40%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.95);
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.25);
    z-index: 10;
  }

  .carousel-btn.left { left: 10px; }
  .carousel-btn.right { right: 10px; }
  .carousel-btn:hover { background: #f0f0f0; }

  .badge-tag {
    padding: 3px 6px;
    border-radius: 4px;
    font-size: 0.75rem;
  }

  .tag-Guard { background-color: #007bff; color: white; }
  .tag-Bigs { background-color: #28a745; color: white; }
  .tag-Power { background-color: #dc3545; color: white; }
  .tag-All-Around { background-color: #ffc107; color: #212529; }
</style>

<script>
  const sneakers = <?php echo json_encode($sneakers); ?>;
  const perPage = 5;
  let currentPage = 0;

  // Map playstyle → color class for the header
  function getPlaystyleColor(playstyle) {
    switch (playstyle) {
      case "Guard": return "bg-primary";
      case "Bigs": return "bg-success";
      case "Power": return "bg-danger";
      case "All-Around": return "bg-warning";
      default: return "bg-secondary";
    }
  }

  // Render page
  //<img src="${imageSrc}" alt="${s.model}" paste this under sneaker-body then add a greater than
  function renderPage() {
    const row = document.getElementById("carouselRow");
    const start = currentPage * perPage;
    const end = start + perPage;
    const pageSneakers = sneakers.slice(start, end);

    row.innerHTML = pageSneakers.map(s => {
        const colorClass = getPlaystyleColor(s.playstyle);
        const imageSrc = s.image_path ? s.image_path : "../dist/img/default-shoe.jpg";

        return `
            <div class="sneaker-card col-md-4 col-sm-12 col-xs-12 col-lg-2 col-12 m-2">
                <div class="sneaker-body">
                
                    <h6 class="mt-2 mb-0">
                        <a href="shoe.php?model=${s.model}" class=" fw-bold">
                            ${s.brand} ${s.model}
                        </a>
                    </h6>
                    <small>${new Date(s.release_date).toLocaleDateString()}</small><br>
                    <strong>⭐ ${s.overall}</strong><br>
                    <span class="badge-tag tag-${s.playstyle.replace(/\s+/g, '-')} mt-1">${s.playstyle}</span><br>
                    <span class="badge badge-light mt-1">${s.availability}</span>
                </div>
            </div>
        `;
    }).join("");

    document.getElementById("pageIndicator").textContent =
        `Page ${currentPage + 1} of ${Math.ceil(sneakers.length / perPage)}`;
}
  function nextPage() {
    if ((currentPage + 1) * perPage < sneakers.length) {
      currentPage++;
      renderPage();
    }
  }

  function prevPage() {
    if (currentPage > 0) {
      currentPage--;
      renderPage();
    }
  }

  renderPage();
</script>
