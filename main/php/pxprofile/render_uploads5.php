<?php
include('../../check_session.php');
include_once('../../connection/conn.php');

$id = $_GET['px_id'] ?? 0;
if (!$id) {
  echo "<div class='text-muted'>No patient ID provided.</div>";
  exit;
}

// ✅ Base path for localhost vs production
$basePath = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) ? '/onlineclinic' : '';

// 📥 Fetch uploaded images
$uploadImages = [];

$stmt = $conn->prepare("
  SELECT upload_id, image_path, upload_datetime, lab_type 
  FROM tbl_uploads 
  WHERE px_id = ? 
  ORDER BY upload_datetime DESC
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $uploadImages[] = [
    'id' => $row['upload_id'],
    'path' => htmlspecialchars($row['image_path']),
    'datetime' => date('Y-m-d', strtotime($row['upload_datetime'])),
    'lab_type' => $row['lab_type'] ?: 'Untagged'
  ];
}
$stmt->close();

if (empty($uploadImages)) {      
    echo '<div class="d-md-none"><div class="alert alert-info text-center mb-0">No images uploaded yet</div></div>';
    echo '
<div class="d-none d-md-flex justify-content-center align-items-center flex-column" style="height: 300px; background-color: #f8f9fa; border: 2px dashed #ccc;">
  <i class="bi bi-image fs-1 text-muted mb-3"></i>
  <p class="text-muted fs-5 mb-0">No images uploaded yet</p>
</div>';
  exit;
}

// 🔄 Group by lab type
$grouped = [];
foreach ($uploadImages as $img) {
  $group = $img['lab_type'];
  $grouped[$group][] = $img;
}

// 🔖 Card rendering helper
function renderCard($img, $basePath) {
  $isUntagged = ($img['lab_type'] === 'Untagged' || empty($img['lab_type']));
  $cardClass = $isUntagged ? 'bg-warning-subtle border border-warning' : '';
  ?>
  <div class="card shadow-sm h-100 <?= $cardClass ?>">
    <img
      src="<?= $basePath ?>/main/php/<?= $img['path'] ?>"
      class="card-img-top img-thumbnail open-image-modal"
      alt="Lab Image"
      data-img="<?= $basePath ?>/main/php/<?= $img['path'] ?>"
      data-bs-toggle="modal"
      data-bs-target="#imageModal"
      style="height: 120px; object-fit: cover; cursor: pointer;"
    >

    <div class="card-body p-2 d-flex flex-column">
      <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted text-truncate mb-0"><?= $img['datetime'] ?></small>

          <div class="dropdown">
            <button class="btn btn-sm btn-light border px-2 py-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              ⋮
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <button
                  class="dropdown-item tag-lab-btn"
                  data-upload-id="<?= $img['id'] ?>"
                  data-lab-type="<?= htmlspecialchars($img['lab_type']) ?>"
                >🏷 <?= $isUntagged ? 'Tag Lab Type' : 'Change Lab Type' ?></button>
              </li>
              <li>
                <button
                  class="dropdown-item text-danger delete-image-btn"
                  data-upload-id="<?= $img['id'] ?>"
                >🗑 Delete</button>
              </li>
            </ul>
          </div>
      </div>

      <?php if ($isUntagged): ?>
        <button
          class="btn btn-sm btn-warning w-100 mt-2 tag-lab-btn"
          data-upload-id="<?= $img['id'] ?>"
        >⚠️ Tag Lab Type</button>
      <?php endif; ?>
    </div>
  </div>
  <?php
}

// ✅ Render all grouped sections in one flex row
echo "<div class='d-flex flex-wrap gap-4 justify-content-center'>";

foreach ($grouped as $group => $images) {
  echo "<div class='border rounded p-3 bg-light text-center' style='min-width: 180px;'>";

  // Group label
  echo "<div class='fw-bold mb-2' style='font-size: 1rem;'>" . htmlspecialchars($group) . "</div>";

  // Image cards
  echo "<div class='d-flex flex-wrap justify-content-center gap-3'>";
  foreach ($images as $img) {
    echo "<div style='width: 150px;'>";
    renderCard($img, $basePath);
    echo "</div>";
  }
  echo "</div>"; // cards

  echo "</div>"; // group box
}

echo "</div>"; // wrapper
?>
