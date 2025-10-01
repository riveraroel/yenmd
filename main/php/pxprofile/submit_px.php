<?php
include('../../connection/conn.php');
include('../../check_session.php');

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
if (!IS_AJAX) {
    die('<h1>404 Not Found</h1>');
}

$issued_by = $_SESSION['USER_ID'] ?? null;

// ------------------ Helper Functions ------------------

// Uppercase with ñ support
function safe_upper($string) {
    return mb_strtoupper(trim(urldecode($string ?? '')), 'UTF-8');
}

// Smart address capitalization
function smart_address($addr) {
    $addr = trim(urldecode($addr ?? ''));

    // Words to keep lowercase
    $exceptions = ['corner', 'of', 'and', 'the', 'unit', 'phase']; 

    // Split into words including spaces/punctuation
    $words = preg_split('/(\s+)/u', $addr, -1, PREG_SPLIT_DELIM_CAPTURE);

    foreach ($words as &$word) {
        if (trim($word) === '') continue;

        // Retain fully uppercase words
        if ($word === mb_strtoupper($word, 'UTF-8')) continue;

        // Lowercase for exceptions
        $lower = mb_strtolower($word, 'UTF-8');
        if (in_array($lower, $exceptions)) {
            $word = $lower;
        } else {
            // Capitalize first letter only
            $firstChar = mb_substr($lower, 0, 1, 'UTF-8');
            $rest = mb_substr($lower, 1, null, 'UTF-8');
            $word = mb_strtoupper($firstChar, 'UTF-8') . $rest;
        }
    }

    return implode('', $words);
}

// ------------------ Input Processing ------------------

$fname     = safe_upper($_POST['fname'] ?? '');
$mname     = safe_upper($_POST['mname'] ?? '');
$lname     = safe_upper($_POST['lname'] ?? '');
$suffix    = trim(urldecode($_POST['suffix'] ?? ''));
$suffix    = ($suffix === '' || strtolower($suffix) === 'null') ? null : safe_upper($suffix);

$addr      = smart_address($_POST['addr'] ?? '');
$dob       = trim(urldecode($_POST['dob'] ?? ''));
$dob       = empty($dob) ? null : $dob;

$gender    = urldecode($_POST['gender'] ?? '');
$civstat   = urldecode($_POST['civstat'] ?? '');
$civstat   = (trim($civstat) === '' || strtolower($civstat) === 'null') ? null : $civstat; 
$cellnum   = urldecode($_POST['cellnum'] ?? '');
$emailadd  = urldecode($_POST['emailadd'] ?? '');
$hmo       = urldecode($_POST['hmo'] ?? 'None');
$company   = safe_upper($_POST['company'] ?? '');
$px_is_pwd = isset($_POST['px_is_pwd']) ? intval($_POST['px_is_pwd']) : 0;

$is_active = 1;

// ------------------ Check existing patient ------------------

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

// ------------------ Insert patient ------------------

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
    echo "Patient has been successfully added.||1";
} else {
    echo "Error adding patient: " . $insert->error . "||0";
}
$insert->close();
$conn->close();
?>
