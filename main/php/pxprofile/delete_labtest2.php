<?php
include('../../connection/conn.php');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

if (!IS_AJAX) {
    http_response_code(404);
    die('<h1>404 Not Found</h1>');
}

// Validate and sanitize input
$labtest_id = isset($_POST['labtest_id']) ? intval($_POST['labtest_id']) : 0;
if ($labtest_id <= 0) {
    http_response_code(400);
    echo "Invalid lab test ID.";
    exit;
}

$set_as_inactive = '0';

// Prepare statement
if (!($s = $conn->prepare("UPDATE tbl_lab_requests SET labtest_is_active = ? WHERE labtest_id = ?"))) {
    http_response_code(500);
    die("Prepare failed: " . $conn->error);
}

$s->bind_param("si", $set_as_inactive, $labtest_id);

// Execute
if (! $s->execute()) {
    http_response_code(500);
    die("Execute failed: " . $s->error);
}

// Check result
if ($s->affected_rows === 1) {
    echo "Lab test has been deleted";
} else {
    echo "Something went wrong. Please try again.";
}

$s->close();
$conn->close();
?>
