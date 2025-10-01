<?php
include('../../check_session.php');
include('../../connection/conn.php');

header('Content-Type: application/json');
$response = ['success' => false];

$data = json_decode(file_get_contents("php://input"), true);
$upload_id = isset($data['upload_id']) ? intval($data['upload_id']) : 0;

if ($upload_id) {
  // Step 1: Get image path
  $stmt = $conn->prepare("SELECT image_path FROM tbl_uploads WHERE upload_id = ?");
  $stmt->bind_param("i", $upload_id);
  $stmt->execute();
  $stmt->bind_result($image_path);
  $stmt->fetch();
  $stmt->close();

  if ($image_path) {
    // Step 2: Delete DB record
    $stmt = $conn->prepare("DELETE FROM tbl_uploads WHERE upload_id = ?");
    $stmt->bind_param("i", $upload_id);

    if ($stmt->execute()) {
      $response['success'] = true;

      // Step 3: Delete actual file
      $fullPath = __DIR__ . '/' . $image_path;
      if (file_exists($fullPath)) {
        unlink($fullPath);
      }
    }
    $stmt->close();
  }
} else {
  $response['message'] = 'Invalid upload ID.';
}

echo json_encode($response);
