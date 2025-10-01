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
    $apt_id = base64_decode($_GET['apt_id']);
    $s = $conn->prepare("SELECT apt_date, TIME_FORMAT(apt_start, '%h:%i %p') as apt_start, TIME_FORMAT(apt_end, '%h:%i %p') as apt_end, apt_reason FROM tbl_appointment WHERE apt_id = ? AND apt_is_active = ?");
    $s->bind_param("ii", $apt_id, $is_active);
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