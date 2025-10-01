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
    $px_id = urldecode($_POST['px_id']);
    $s = $conn->prepare("UPDATE tbl_px_details SET px_is_active = ? WHERE px_id = ?");
    $s->bind_param("si", $set_as_inactive, $px_id);
    $s->execute();
    $s->store_result();
    if ($s->affected_rows > 0)
    {
        $x = $conn->prepare("UPDATE tbl_appointment SET apt_is_active = ? WHERE px_id = ?");
        $x->bind_param("si", $set_as_inactive, $px_id);
        $x->execute();
        $x->close();
        $x = $conn->prepare("UPDATE tbl_prescrip SET prescription_is_active = ? WHERE prescription_for = ?");
        $x->bind_param("si", $set_as_inactive, $px_id);
        $x->execute();
        $x->close();
        $x = $conn->prepare("UPDATE tbl_medical_history SET medhis_is_active = ? WHERE px_id = ?");
        $x->bind_param("si", $set_as_inactive, $px_id);
        $x->execute();
        $x->close();
        echo "Patient has been deleted";
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