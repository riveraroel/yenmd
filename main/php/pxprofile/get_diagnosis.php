<?php
include('../../connection/conn.php');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if(!IS_AJAX)
{
	die('<h1>404 Not Found</h1>');
}
else
{
    $is_active = '1';
    $medhis_id = base64_decode($_GET['medhis_id']);
    $s = $conn->prepare("SELECT medhis_labresult, medhis_diagnosis, medhis_medication, medhis_recommendation, medhis_diagnosis_date, medhis_issuance_date, medhis_attachment FROM tbl_medical_history WHERE medhis_id = ? AND medhis_is_active = ?");
    $s->bind_param("ii", $medhis_id, $is_active);
    $s->execute();
    $res = $s->get_result();
    while ($row = $res->fetch_assoc())
    {
        $row['medhis_labresult'] = encrypt_decrypt('decrypt', $row['medhis_labresult']);
        $row['medhis_diagnosis'] = encrypt_decrypt('decrypt', $row['medhis_diagnosis']);
        $row['medhis_medication'] = encrypt_decrypt('decrypt', $row['medhis_medication']);
        $row['medhis_recommendation'] = encrypt_decrypt('decrypt', $row['medhis_recommendation']);
        $data[] = $row;
    }
    $res->free_result();
    $s->close();
    echo json_encode($data);
}
$conn->close();
?>