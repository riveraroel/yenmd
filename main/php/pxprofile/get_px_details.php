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
    $s = $conn->prepare("SELECT CONCAT(px_firstname, ' ', px_midname, ' ', px_lastname, ' ', px_suffix) AS fullname,
       px_address, px_id, px_firstname, px_midname, px_lastname, px_suffix, px_dob, px_gender,
       px_civilstatus, px_cellnumber, px_emailadd, px_hmo, px_company, px_is_pwd FROM tbl_px_details WHERE px_id = ? AND px_is_active = ?");
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