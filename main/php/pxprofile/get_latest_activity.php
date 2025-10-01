<?php
include('../../connection/conn.php');
header('Content-Type: application/json');

$is_active = 1;
$px_id = isset($_GET['px_id']) ? base64_decode($_GET['px_id']) : 0;

$data = [
  'last_medhist' => null,
  'last_prescription' => null,
  'last_lab' => null,
  'last_appointment' => null
];

// 🔹 Safe function to run each query
function getLatestDate($conn, $sql, $px_id, $is_active, $column_alias) {
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    error_log("SQL Error: " . $conn->error);
    return null;
  }
  $stmt->bind_param("ii", $px_id, $is_active);
  $stmt->execute();
  $res = $stmt->get_result();
  $latest = null;
  if ($row = $res->fetch_assoc()) {
    $latest = $row[$column_alias];
  }
  $stmt->close();
  return $latest;
}

// 🔹 Run each query safely
$data['last_medhist'] = getLatestDate($conn,
  "SELECT MAX(mh.medhis_diagnosis_date) AS last_med FROM tbl_medical_history mh WHERE mh.px_id = ? AND mh.medhis_is_active = ?",
  $px_id, $is_active, 'last_med');

$data['last_prescription'] = getLatestDate($conn,
  "SELECT MAX(prescription_date) AS last_presc FROM tbl_prescrip WHERE prescription_for = ? AND prescription_is_active = ?",
  $px_id, $is_active, 'last_presc');

$data['last_lab'] = getLatestDate($conn,
  "SELECT MAX(labtest_request_date) AS last_lab FROM tbl_lab_requests WHERE px_id = ? AND labtest_is_active = ?",
  $px_id, $is_active, 'last_lab');

$data['last_appointment'] = getLatestDate($conn,
  "SELECT MAX(apt_date) AS last_appt FROM tbl_appointment WHERE px_id = ? AND apt_is_active = ?",
  $px_id, $is_active, 'last_appt');

$conn->close();
echo json_encode($data);
