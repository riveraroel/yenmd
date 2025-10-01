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
    $s = $conn->prepare("SELECT CONCAT(px.px_firstname, ' ', px.px_midname, ' ', px.px_lastname, ' ', px.px_suffix) AS fullname, apt.px_id AS aptid, apt.apt_reason AS aptreason, apt.apt_date AS aptdate FROM tbl_appointment AS apt INNER JOIN tbl_px_details AS px ON apt.px_id = px.px_id WHERE apt.issued_by = ? AND apt.apt_is_active = ? ORDER BY apt.apt_cre_datetime DESC");
    $s->bind_param("ii", $id, $is_active);
    $s->execute();
    $res = $s->get_result();
    while ($row = $res->fetch_assoc())
    {
        $data[] = $row;
    }
    $res->free_result();
    $s->close();
    echo json_encode($data);
}
$conn->close();
?>