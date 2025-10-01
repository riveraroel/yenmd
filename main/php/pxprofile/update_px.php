<?php
include('../../connection/conn.php');
include('../../check_session.php');

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
if (!IS_AJAX) {
    die('<h1>404 Not Found</h1>');
}

// ------------------ Helper Functions ------------------

function safe_upper($string) {
    return mb_strtoupper(trim(urldecode($string ?? '')), 'UTF-8');
}

function smart_address($addr) {
    $addr = trim(urldecode($addr ?? ''));
    $exceptions = ['corner', 'of', 'and', 'the', 'unit', 'phase']; 
    $words = preg_split('/(\s+)/u', $addr, -1, PREG_SPLIT_DELIM_CAPTURE);
    foreach ($words as &$word) {
        if (trim($word) === '') continue;
        if ($word === mb_strtoupper($word, 'UTF-8')) continue;
        $lower = mb_strtolower($word, 'UTF-8');
        if (in_array($lower, $exceptions)) {
            $word = $lower;
        } else {
            $firstChar = mb_substr($lower, 0, 1, 'UTF-8');
            $rest = mb_substr($lower, 1, null, 'UTF-8');
            $word = mb_strtoupper($firstChar, 'UTF-8') . $rest;
        }
    }
    return implode('', $words);
}

// ------------------ Input Processing ------------------

$px_id   = urldecode($_POST['px_id'] ?? '');
$fname   = safe_upper($_POST['fname'] ?? '');
$mname   = safe_upper($_POST['mname'] ?? '');
$lname   = safe_upper($_POST['lname'] ?? '');
$suffix  = trim(urldecode($_POST['suffix'] ?? ''));
$suffix  = ($suffix === '' || strtolower($suffix) === 'null') ? null : safe_upper($suffix);

$dob     = urldecode($_POST['dob'] ?? '');
$dob     = empty($dob) ? null : $dob;

$addr     = smart_address($_POST['addr'] ?? '');
$gender   = urldecode($_POST['gender'] ?? '');
$civstat  = urldecode($_POST['civstat'] ?? '');
$cellnum  = urldecode($_POST['cellnum'] ?? '');
$emailadd = urldecode($_POST['emailadd'] ?? '');
$hmo      = urldecode($_POST['hmo'] ?? 'None');
$company  = safe_upper($_POST['company'] ?? '');
$is_pwd   = isset($_POST['is_pwd']) ? intval($_POST['is_pwd']) : 0;

// ------------------ Check existing patient ------------------

$s = $conn->prepare("SELECT px_id FROM tbl_px_details WHERE px_firstname = ? AND px_lastname = ? AND px_is_active = ?");
$s->bind_param("ssi", $fname, $lname, $is_active);
$s->execute();
$s->store_result();
if ($s->num_rows > 0) {
    echo "Patient already existing and active";
    return false;
    $s->close();
}

// ------------------ Update patient ------------------

$s = $conn->prepare("UPDATE tbl_px_details 
    SET px_firstname = ?, px_midname = ?, px_lastname = ?, px_suffix = ?, 
        px_dob = ?, px_gender = ?, px_civilstatus = ?, px_cellnumber = ?, 
        px_emailadd = ?, px_hmo = ?, px_company = ?, px_address = ?, px_is_pwd = ? 
    WHERE px_id = ?");
$s->bind_param(
    "ssssssssssssii", 
    $fname, $mname, $lname, $suffix, $dob, $gender, $civstat, 
    $cellnum, $emailadd, $hmo, $company, $addr, $is_pwd, $px_id
);
$s->execute();
$s->store_result();
if ($s->affected_rows == 1) {
    echo "Patient details successfully updated";
    $s->close();
} else {
    echo "No patient records updated";
    $s->close();
}

$conn->close();
?>
