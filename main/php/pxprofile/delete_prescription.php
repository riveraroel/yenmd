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
    $presc_id = urldecode($_POST['id']);
    $s = $conn->prepare("UPDATE tbl_prescrip SET prescription_is_active = ? WHERE presc_id = ?");
    $s->bind_param("si", $set_as_inactive, $presc_id);
    $s->execute();
    $s->store_result();
    if ($s->affected_rows == 1)
    {
        echo "Prescription has been successfully deleted.";
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