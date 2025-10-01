<?php
include('../../connection/conn.php');
include('../../check_session.php');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if(!IS_AJAX)
{
	die('<h1>404 Not Found</h1>');
}
else
{
    $px_id = urldecode($_POST['px_id']);
    $fname = urldecode($_POST['fname']);
    $mname = urldecode($_POST['mname']);
    $lname = urldecode($_POST['lname']);
    $suffix = urldecode($_POST['suffix']);
    $s = $conn->prepare("SELECT px_id FROM tbl_px_details WHERE px_firstname = ? AND px_lastname = ? AND px_is_active = ?");
    $s->bind_param("sss", $fname, $lname, $is_active);
    $s->execute();
    $s->store_result();
    if ($s->num_rows > 0)
    {
        echo "Patient already existing and active";
        return false;
        $s->close();
    }
    $dob = urldecode($_POST['dob']);
    if (empty($dob) || $dob == "")
    {
        $dob = null;
    }
    $addr = urldecode($_POST['addr']);
    $gender = urldecode($_POST['gender']);
    $civstat = urldecode($_POST['civstat']);
    $cellnum = urldecode($_POST['cellnum']);
    $emailadd = urldecode($_POST['emailadd']);
    $hmo = urldecode($_POST['hmo']);
    $company = urldecode($_POST['company']);
    $s = $conn->prepare("UPDATE tbl_px_details SET px_firstname = ?, px_midname = ?, px_lastname = ?, px_suffix = ?, px_dob = ?, px_gender = ?, px_civilstatus = ?, px_cellnumber = ?, px_emailadd = ?, px_hmo = ?, px_company = ?, px_address = ? WHERE px_id = ?");
    $s->bind_param("ssssssssssssi", $fname, $mname, $lname, $suffix, $dob, $gender, $civstat, $cellnum, $emailadd, $hmo, $company, $addr, $px_id);
    $s->execute();
    $s->store_result();
    if ($s->affected_rows == 1)
    {
        echo "Patient details successfully updated";
        $s->close();
    }
    else
    {
        echo "No patient records updated";
        $s->close();
    }
}
$conn->close();
?>