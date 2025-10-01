<?php
include('../../connection/conn.php');
include('../../check_session.php');

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
if (!IS_AJAX) {
    die('<h1>404 Not Found</h1>');
}

$issued_by = $_SESSION['USER_ID'] ?? null;

$fname     = strtoupper(trim(urldecode($_POST['fname'] ?? '')));
$mname     = strtoupper(trim(urldecode($_POST['mname'] ?? '')));
$lname     = strtoupper(trim(urldecode($_POST['lname'] ?? '')));
$suffix = trim(urldecode($_POST['suffix'] ?? ''));
$suffix = ($suffix === '' || strtolower($suffix) === 'null') ? null : strtoupper($suffix);

$addr      = trim(urldecode($_POST['addr'] ?? ''));
$dob       = trim(urldecode($_POST['dob'] ?? ''));
$dob       = empty($dob) ? null : $dob;

$gender    = urldecode($_POST['gender'] ?? '');
$civstat   = urldecode($_POST['civstat'] ?? '');
$cellnum   = urldecode($_POST['cellnum'] ?? '');
$emailadd  = urldecode($_POST['emailadd'] ?? '');
$hmo       = urldecode($_POST['hmo'] ?? 'None');
$company   = urldecode($_POST['company'] ?? '');
$px_is_pwd = isset($_POST['px_is_pwd']) ? intval($_POST['px_is_pwd']) : 0;

$is_active = 1;

// Check if patient already exists (active)
$check = $conn->prepare("SELECT px_id FROM tbl_px_details WHERE px_firstname = ? AND px_lastname = ? AND px_is_active = ?");
$check->bind_param("ssi", $fname, $lname, $is_active);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "Patient already existing and active";
    $check->close();
    $conn->close();
    exit;
}
$check->close();

// Insert new patient
$insert = $conn->prepare("
    INSERT INTO tbl_px_details (
        px_firstname, px_midname, px_lastname, px_suffix,
        px_dob, px_gender, px_civilstatus, px_cellnumber,
        px_emailadd, px_hmo, px_company, issued_by,
        px_is_active, px_address, px_is_pwd
    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
");

$insert->bind_param(
    "ssssssssssssssi",
    $fname, $mname, $lname, $suffix,
    $dob, $gender, $civstat, $cellnum,
    $emailadd, $hmo, $company, $issued_by,
    $is_active, $addr, $px_is_pwd
);

if ($insert->execute()) {
    echo "Patient successfully added||1";
} else {
    echo "Error adding patient: " . $insert->error . "||0";
}
$insert->close();
$conn->close();
?>
