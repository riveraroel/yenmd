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
    $s = $conn->prepare("SELECT information_name AS info_name, information_id AS info_id FROM tbl_information WHERE information_is_active = ? ORDER by information_cre_datetime DESC");
    $s->bind_param("i", $is_active);
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