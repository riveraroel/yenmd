<?php
include('../../connection/conn.php');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if(!IS_AJAX)
{
	die('<h1>404 Not Found</h1>');
}
else
{
    $set_as_inactive = '0';
    $acc_id = urldecode($_POST['acc_id']);
    $s = $conn->prepare("UPDATE tbl_login SET is_active = ? WHERE information_id = ?");
    $s->bind_param("si", $set_as_inactive, $acc_id);
    $s->execute();
    $s->store_result();
    if ($s->affected_rows == 1)
    {
        $s = $conn->prepare("UPDATE tbl_information SET information_is_active = ? WHERE information_id = ?");
        $s->bind_param("si", $set_as_inactive, $acc_id);
        $s->execute();
        echo "Account has been deleted";
        $s->close();
    }
    else
    {
        echo "Something went wrong. Please try again.";
        $s->close();
    }
}
$conn->close();
?>