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

// 📥 Fetch uploaded files
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

// ❌ No uploads
if (empty($uploadImages)) {
    echo '<div class="d-md-none"><div class="alert alert-info text-center mb-0">No uploads yet</div></div>';
    echo '<div class="d-none d-md-flex justify-content-center align-items-center flex-column" style="height: 300px; background-color: #f8f9fa; border: 2px dashed #ccc;">
          <i class="bi bi-image fs-1 text-muted mb-3"></i>
          <p class="text-muted fs-5 mb-0">No uploads yet</p>
          </div>';
    exit;
}

// 🔄 Group by lab type
$grouped = [];
foreach ($uploadImages as $img) {
    $grouped[$img['lab_type']][] = $img;
}

// 🔖 Card rendering helper
function renderCard($img, $basePath) {
    $isUntagged = ($img['lab_type'] === 'Untagged' || empty($img['lab_type']));
    $cardClass = $isUntagged ? 'bg-warning-subtle border border-warning' : '';

    // ✅ Full path for PDF and image
    $fullPath = $basePath . '/main/php/' . $img['path'];

    // Check if PDF
    $isPDF = preg_match('/\.pdf$/i', $img['path']);
    ?>
    <div class="card shadow-sm h-100 <?= $cardClass ?>">
        <?php if ($isPDF): ?>
            <!-- 📄 PDF Thumbnail -->
            <div class="d-flex align-items-center justify-content-center bg-light open-pdf-modal"
                 style="height: 120px; cursor: pointer;"
                 data-pdf="<?= $fullPath ?>"
                 data-bs-toggle="modal"
                 data-bs-target="#pdfModal">
                <i class="bi bi-file-earmark-pdf fs-1 text-danger"></i>
            </div>
        <?php else: ?>
            <!-- 🖼 Image Thumbnail -->
            <img
                src="<?= $fullPath ?>"
                class="card-img-top img-thumbnail open-image-modal"
                alt="Lab Image"
                data-img="<?= $fullPath ?>"
                data-bs-toggle="modal"
                data-bs-target="#imageModal"
                style="height: 120px; object-fit: cover; cursor: pointer;"
            >
        <?php endif; ?>

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

// -----------------------------
// Render all grouped sections
// -----------------------------
echo "<div class='d-flex flex-wrap gap-4 justify-content-center'>";

foreach ($grouped as $group => $images) {
    echo "<div class='border rounded p-3 bg-light text-center' style='min-width: 180px;'>";
    echo "<div class='fw-bold mb-2' style='font-size: 1rem;'>" . htmlspecialchars($group) . "</div>";

    echo "<div class='d-flex flex-wrap justify-content-center gap-3'>";
    foreach ($images as $img) {
        echo "<div style='width: 150px;'>";
        renderCard($img, $basePath);
        echo "</div>";
    }
    echo "</div></div>";
}

echo "</div>";
?>
