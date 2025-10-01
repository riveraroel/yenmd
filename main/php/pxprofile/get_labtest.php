<?php
include('../../connection/conn.php');

// Force JSON output and show errors for debugging
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Validate AJAX request
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if (!IS_AJAX) {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
    exit;
}

$labtest_id = isset($_GET['labtest_id']) ? base64_decode($_GET['labtest_id']) : null;
if (!$labtest_id) {
    echo json_encode(['error' => 'Invalid labtest_id']);
    exit;
}

$data = [];

$s = $conn->prepare("
    SELECT 
        labtest_selected, 
        labtest_others, 
        labtest_request_date, 
        labtest_clinical_impression, 
        labtest_remarks,
        labtest_folldate, 
        pdf_filename, 
        info.information_name 
    FROM tbl_lab_requests AS lab 
    INNER JOIN tbl_information AS info 
    ON lab.issued_by = info.information_id 
    WHERE lab.labtest_id = ?
");
$s->bind_param("i", $labtest_id);
$s->execute();
$res = $s->get_result();

while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

$s->close();
$conn->close();

echo json_encode($data);
