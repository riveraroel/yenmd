<?php
session_start();
include('../../connection/conn.php');

header('Content-Type: application/json');

if (!isset($_SESSION['USER_ID'])) {
    echo json_encode(['error' => 'Doctor not logged in.']);
    exit;
}

$user_id = $_SESSION['USER_ID'];

try {
    // 1. Get information_id from tbl_login where login_id == USER_ID
    $stmt = $conn->prepare("SELECT information_id FROM tbl_login WHERE login_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($information_id);

    if ($stmt->fetch()) {
        $stmt->close();

        // 2. Use information_id to get PTR and S2 from tbl_information
        $stmt2 = $conn->prepare("SELECT information_ptr, information_s2 FROM tbl_information WHERE information_id = ?");
        $stmt2->bind_param("i", $information_id);
        $stmt2->execute();
        $stmt2->bind_result($ptr, $s2);

        if ($stmt2->fetch()) {
            echo json_encode(['ptr' => $ptr, 's2' => $s2]);
        } else {
            echo json_encode(['error' => 'PTR and S2 not found.']);
        }
        $stmt2->close();
    } else {
        echo json_encode(['error' => 'No matching doctor record found.']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
$conn->close();
