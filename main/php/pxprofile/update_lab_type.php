<?php
include('../../check_session.php');
include('../../connection/conn.php');

header('Content-Type: application/json');

$response = ['success' => false];

$data = json_decode(file_get_contents('php://input'), true);
$uploadId = $data['upload_id'] ?? null;
$labType = $data['lab_type'] ?? '';

if ($uploadId && $labType) {
  $stmt = $conn->prepare("UPDATE tbl_uploads SET lab_type = ? WHERE upload_id = ?");
  $stmt->bind_param("si", $labType, $uploadId);

  if ($stmt->execute()) {
    $response['success'] = true;
  } else {
    $response['message'] = 'Database update failed.';
  }

  $stmt->close();
} else {
  $response['message'] = 'Invalid data submitted.';
}

echo json_encode($response);
