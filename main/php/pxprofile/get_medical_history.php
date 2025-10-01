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
    $id = base64_decode($_GET['px_id']);
    $s = $conn->prepare("SELECT info.information_name, mh.medhis_id, mh.medhis_diagnosis, mh.medhis_diagnosis_date FROM tbl_medical_history AS mh INNER JOIN tbl_information AS info ON mh.issued_by = info.information_id WHERE mh.px_id = ? AND mh.medhis_is_active = ? ORDER BY mh.medhis_cre_datetime DESC");
    $s->bind_param("ii", $id, $is_active);
    $s->execute();
    $res = $s->get_result();
    while ($row = $res->fetch_assoc())
    {
        $row['medhis_diagnosis'] = encrypt_decrypt('decrypt', $row['medhis_diagnosis']);
        $data[] = $row;
    }
    $res->free_result();
    $s->close();
    echo json_encode($data);
}
$conn->close();
?>