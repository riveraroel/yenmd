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
    $s = $conn->prepare("SELECT info.information_contact AS info_cont, info.information_name AS info_name, info.information_emailadd AS info_emailadd, info.information_id AS info_id, lgn.username AS uname, lgn.pword AS pword, info.role_id AS role, info.information_licno AS licno, info.information_ptr AS ptr, info.information_s2 AS s2, info.information_address AS addr FROM tbl_information AS info INNER JOIN tbl_login AS lgn ON info.information_id = lgn.information_id WHERE info.information_id = ? AND info.information_is_active = ?");
    $s->bind_param("is", $id, $is_active);
    $s->execute();
    $res = $s->get_result();
    while ($row = $res->fetch_assoc())
    {
        $row['pword'] = encrypt_decrypt('decrypt', $row['pword']);
        $row['uname'] = encrypt_decrypt('decrypt', $row['uname']);
        $data[] = $row;
    }
    $res->free_result();
    $s->close();
    header('Content-Type: application/json');
    echo json_encode($data);
}
$conn->close();
?>