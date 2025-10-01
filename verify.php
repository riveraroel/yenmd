<?php
include('connection/conn.php');

$medhis_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$token = isset($_GET['token']) ? $_GET['token'] : '';

if ($medhis_id === 0 || empty($token)) {
    die("Invalid parameters.");
}

$s = $conn->prepare("SELECT 
        px.px_firstname, px.px_lastname, px.px_suffix,
        mh.medhis_diagnosis, mh.medhis_recommendation, 
        mh.medhis_issuance_date, mh.medhis_token, 
        i.information_name,
        mh.medhis_is_active
    FROM tbl_medical_history mh
    INNER JOIN tbl_px_details px ON mh.px_id = px.px_id
    INNER JOIN tbl_information i ON mh.issued_by = i.information_id
    WHERE mh.medhis_id = ? AND mh.medhis_token = ?");
$s->bind_param("is", $medhis_id, $token);
$s->execute();
$s->store_result();

if ($s->num_rows === 1) {
    $s->bind_result($fname, $lname, $suffix, $diag, $recom, $issued_date, $db_token, $physician, $is_active);
    $s->fetch();

    if ($is_active == 0) {
        // Certificate is inactive – treat as invalid
        echo "<!DOCTYPE html>
<html>
<head>
    <title>Medical Certificate Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff3f3;
            text-align: center;
            padding: 50px;
        }
        .card {
            background: white;
            padding: 30px;
            margin: auto;
            max-width: 500px;
            border: 2px solid red;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: red;
        }
    </style>
</head>
<body>
    <div class='card'>
        <h1>❌ Invalid or Expired Certificate</h1>
        <p>This certificate has been marked as inactive and is no longer valid.</p>
    </div>
</body>
</html>";
    } else {
        // Valid certificate
        $fullname = strtoupper($fname . ' ' . $lname . ($suffix ? ' ' . $suffix : ''));
        $formatted_date = date('F j, Y', strtotime($issued_date));
        $diagnosis = strtoupper(encrypt_decrypt('decrypt', $diag));
        $recommendation = strtoupper(encrypt_decrypt('decrypt', $recom));
        $pdf_filename = "{$fullname}_MedCert_" . date('Y_m_d', strtotime($issued_date)) . ".pdf";

        echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Medical Certificate Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            text-align: center;
            padding: 50px;
        }
        .card {
            background: white;
            padding: 30px;
            margin: auto;
            max-width: 600px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        h1 {
            color: green;
        }
        .info {
            text-align: left;
            margin-top: 20px;
        }
        .info p {
            font-size: 16px;
            line-height: 1.5;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class='card'>
        <h1>✅ Valid Medical Certificate</h1>
        <div class='info'>
            <p><strong>Certificate No.:</strong> {$medhis_id}</p>
            <p><strong>Issued To:</strong> {$fullname}</p>
            <p><strong>Date Issued:</strong> {$formatted_date}</p>
            <p><strong>Diagnosis:</strong><br>{$diagnosis}</p>
            <p><strong>Recommendation:</strong><br>{$recommendation}</p>
            <p><strong>Physician:</strong> {$physician}</p>
        </div>
        <a class='btn' href='main/php/MedCertPDFs/{$pdf_filename}' target='_blank'>View Certificate</a>
    </div>
</body>
</html>";
    }
} else {
    // No matching record found
    echo "<!DOCTYPE html>
<html>
<head>
    <title>Medical Certificate Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff3f3;
            text-align: center;
            padding: 50px;
        }
        .card {
            background: white;
            padding: 30px;
            margin: auto;
            max-width: 500px;
            border: 2px solid red;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: red;
        }
    </style>
</head>
<body>
    <div class='card'>
        <h1>❌ Invalid or Expired Certificate</h1>
        <p>The certificate you're trying to verify is not found or the verification link has expired.</p>
    </div>
</body>
</html>";
}
$conn->close();
?>
