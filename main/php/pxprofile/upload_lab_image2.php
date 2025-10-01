<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../../connection/conn.php');
include('../../check_session.php');

$uploaded_by = $_SESSION['USER_ID'];
$uploadDir = __DIR__ . '/../uploads/';
$logFile = $uploadDir . 'upload_debug.log';

function logUpload($message) {
    global $logFile;
    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] $message\n", FILE_APPEND);
}

// Make sure the uploads folder exists
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
    logUpload("Created uploads folder: $uploadDir");
}

$response = ['success' => false, 'uploaded' => [], 'errors' => []];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
    $px_id = $_POST['px_id'] ?? 0;

    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        if (!is_uploaded_file($tmp)) {
            logUpload("Invalid temp file: $tmp");
            continue;
        }

        $orig = $_FILES['images']['name'][$i];
        $safe = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $orig);
        $dest = $uploadDir . $safe;

        if (move_uploaded_file($tmp, $dest)) {
            $rel_path = 'uploads/' . $safe;
            logUpload("Saved file: $safe");

            // Insert into DB
            $stmt = $conn->prepare("INSERT INTO tbl_uploads (px_id, image_path, uploaded_by) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $px_id, $rel_path, $uploaded_by);
            if ($stmt->execute()) {
                $response['uploaded'][] = $rel_path;
            } else {
                logUpload("DB Insert error: " . $stmt->error);
                $response['errors'][] = "$orig: DB insert failed";
            }
        } else {
            logUpload("Failed to move: $orig");
            $response['errors'][] = "$orig: move failed";
        }
    }

    if (!empty($response['uploaded'])) {
        $response['success'] = true;
    } else {
        $response['message'] = 'No files uploaded';
    }
} else {
    logUpload("Invalid request or no files.");
    $response['message'] = 'No image uploaded.';
}

echo json_encode($response);
?>
