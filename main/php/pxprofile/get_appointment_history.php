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
    $s = $conn->prepare("SELECT info.information_name, apt.apt_id, apt.apt_reason, apt.apt_date FROM tbl_appointment AS apt INNER JOIN tbl_information AS info ON apt.issued_by = info.information_id WHERE apt.px_id = ? AND apt.apt_is_active = ? ORDER BY apt.apt_cre_datetime DESC");
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