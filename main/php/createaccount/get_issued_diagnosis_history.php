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
    $id = base64_decode($_GET['id']);
    $s = $conn->prepare("SELECT CONCAT(px.px_firstname, ' ', px.px_midname, ' ', px.px_lastname, ' ', px.px_suffix) AS fullname, mh.px_id AS mhid, mh.medhis_diagnosis AS mhdiag, mh.medhis_diagnosis_date AS mhdate FROM tbl_medical_history AS mh INNER JOIN tbl_px_details AS px ON mh.px_id = px.px_id WHERE mh.issued_by = ? AND mh.medhis_is_active = ? ORDER BY medhis_cre_datetime DESC");
    $s->bind_param("ii", $id, $is_active);
    $s->execute();
    $res = $s->get_result();
    while ($row = $res->fetch_assoc())
    {
        $row['mhdiag'] = encrypt_decrypt('decrypt', $row['mhdiag']);
        $data[] = $row;
    }
    $res->free_result();
    $s->close();
    echo json_encode($data);
}
$conn->close();
?>