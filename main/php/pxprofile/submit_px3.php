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
    $issued_by = $_SESSION['USER_ID'];
    $fname = strtoupper(urldecode($_POST['fname']));
    $mname = strtoupper(urldecode($_POST['mname']));
    $lname = strtoupper(urldecode($_POST['lname']));
    $suffix = strtoupper(urldecode($_POST['suffix']));
    $addr = urldecode($_POST['addr']);
    $dob = urldecode($_POST['dob']);
    if (empty($dob) || $dob == "")
    {
        $dob = null;
    }
    $gender = urldecode($_POST['gender']);
    $civstat = urldecode($_POST['civstat']);
    $cellnum = urldecode($_POST['cellnum']);
    $emailadd = urldecode($_POST['emailadd']);
    $hmo = urldecode($_POST['hmo']);
    $company = urldecode($_POST['company']);
    $px_is_pwd = isset($_POST['px_is_pwd']) ? intval($_POST['px_is_pwd']) : 0;
    $is_active = '1';
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
    else
    {
        $s = $conn->prepare("INSERT INTO tbl_px_details (px_firstname, px_midname, px_lastname, px_suffix, px_dob, px_gender, px_civilstatus, px_cellnumber, px_emailadd, px_hmo, px_company, issued_by, px_is_active, px_address, px_is_pwd) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $s->bind_param("ssssssssssssssi", $fname, $mname, $lname, $suffix, $dob, $gender, $civstat, $cellnum, $emailadd, $hmo, $company, $issued_by, $is_active, $addr, $px_is_pwd);
        $s->execute();
        $s->store_result();
        if ($s->affected_rows == 1)
        {
            echo "Patient successfully added||1";
            $s->close();
        }
    }
}
$conn->close();
?>