<?php
include('../../connection/conn.php');
include('../../check_session.php');
require '../PHPMailer/PHPMailerAutoload.php';

date_default_timezone_set('Asia/Manila');

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if (!IS_AJAX) {
    http_response_code(404);
    exit('<h1>404 Not Found</h1>');
}

$px_id = intval($_POST['px_id']);
$issued_by = $_SESSION['USER_ID'] ?? 0;

$appointmentDateRaw = $_POST['appointmentDate'] ?? '';
$appointmentStartRaw = $_POST['appointmentStart'] ?? '';
$appointmentEndRaw = $_POST['appointmentEnd'] ?? '';
$reasonRaw = $_POST['reason'] ?? '';

$appointmentDate = date('Y-m-d', strtotime($appointmentDateRaw));
$appointmentStart = date('H:i', strtotime($appointmentStartRaw));
$appointmentEnd = date('H:i', strtotime($appointmentEndRaw));
$reason = trim(urldecode($reasonRaw));

$startTime = strtotime($appointmentStartRaw);
$endTime = strtotime($appointmentEndRaw);
$durationHours = ($endTime - $startTime) / 3600;

if ($durationHours <= 0) {
    http_response_code(400);
    echo json_encode([
        'message' => 'Invalid time. Please try again.',
        'email_sent' => false
    ]);
    exit;
}

$is_attended = '2';
$is_active = '1';

$checkStmt = $conn->prepare("SELECT apt_id FROM tbl_appointment WHERE apt_date = ? AND apt_start = ? AND apt_is_active = ?");
$checkStmt->bind_param("ssi", $appointmentDate, $appointmentStart, $is_active);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    $checkStmt->close();
    http_response_code(409);
    echo json_encode([
        'message' => 'This appointment slot is already taken. Please choose another time.',
        'email_sent' => false
    ]);
    exit;
}
$checkStmt->close();

$insertStmt = $conn->prepare("
    INSERT INTO tbl_appointment (px_id, issued_by, apt_date, apt_start, apt_end, apt_reason, apt_is_attended, apt_is_active)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
$insertStmt->bind_param("iissssss", $px_id, $issued_by, $appointmentDate, $appointmentStart, $appointmentEnd, $reason, $is_attended, $is_active);
$insertStmt->execute();

if ($insertStmt->affected_rows !== 1) {
    $insertStmt->close();
    http_response_code(500);
    echo json_encode([
        'message' => 'Failed to add appointment. Please try again.',
        'email_sent' => false
    ]);
    exit;
}
$apt_id = $insertStmt->insert_id;
$insertStmt->close();

$infoStmt = $conn->prepare("
    SELECT px.px_emailadd AS email,
           CONCAT_WS(' ', px.px_firstname, px.px_midname, px.px_lastname, px.px_suffix) AS fullname,
           apt.apt_date AS aptdate,
           TIME_FORMAT(apt.apt_start, '%h:%i %p') AS aptstart,
           TIME_FORMAT(apt.apt_end, '%h:%i %p') AS aptend,
           apt.apt_reason AS aptreason
    FROM tbl_appointment AS apt
    INNER JOIN tbl_px_details AS px ON apt.px_id = px.px_id
    WHERE apt.apt_id = ?
");
$infoStmt->bind_param("i", $apt_id);
$infoStmt->execute();
$infoStmt->bind_result($email, $fullname, $aptdate, $aptstart, $aptend, $aptreason);
$infoStmt->fetch();
$infoStmt->close();

if (empty($email)) {
    echo json_encode([
        'message' => 'Appointment added, but no email address found.',
        'email_sent' => false
    ]);
    exit;
}

$formattedDate = (new DateTime($aptdate))->format('F j, Y');

$smtpStmt = $conn->prepare("
    SELECT smtp_host, smtp_username, smtp_password, smtp_senderfrom, smtp_sendername
    FROM tbl_smtp_service WHERE smtp_is_active = '1' LIMIT 1
");
$smtpStmt->execute();
$smtpStmt->bind_result($host, $username, $password, $senderFrom, $senderName);
$smtpStmt->fetch();
$smtpStmt->close();

if (empty($host) || empty($username) || empty($password)) {
    echo json_encode([
        'message' => 'Appointment added, but email was not sent due to incomplete SMTP settings.',
        'email_sent' => false
    ]);
    exit;
}

try {
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom($senderFrom, $senderName);
    $mail->addAddress($email, $fullname);
    // $mail->addEmbeddedImage('banner.png', 'image_cid');

    $mail->isHTML(true);
    $mail->Subject = "HealthLab Appointment for: $fullname";

    $mail->Body = <<<HTML
<html>
<head>
  <style>
    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
    .header, .footer { text-align: center; }
    .details-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    .details-table th, .details-table td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: center;
    }
    .details-table th {
      background-color: #f5f5f5;
    }
    .greeting, .message, .signature {
      margin: 20px 0;
    }
  </style>
</head>
<body>
  <!-- <div class="header">
    <img src="cid:image_cid" alt="Clinic Logo" width="350" height="100" />
    <p>Suite 1601, Medical Plaza Makati, Amorsolo Corner Dela Rosa Sts., Legaspi Village, Makati City</p>
    <p>Tel. No: 8-867-1140</p>
    <hr />
  </div> -->

  <div class="greeting">
    <p>Dear <strong>$fullname</strong>,</p>
  </div>

  <div class="message">
    <p>We are pleased to confirm your appointment at <strong>HealthLab</strong>.</p>
    <p>Below are the details of your scheduled appointment:</p>
  </div>

  <table class="details-table">
    <tr>
      <th>Date</th>
      <th>Start Time</th>
      <th>End Time</th>
      <th>Reason for Visit</th>
    </tr>
    <tr>
      <td>$formattedDate</td>
      <td>$aptstart</td>
      <td>$aptend</td>
      <td>{$aptreason}</td>
    </tr>
  </table>

  <div class="message">    
    <p>Please arrive at least 10 minutes before your scheduled time and bring any relevant documents or prior test results, if available.</p>
    <p>If you have any questions or need to reschedule, feel free to contact us at Tel. No: 8867-1140.</p>
  </div>

  <div class="signature">
    <p>Sincerely,</p>
    <p><strong>HealthLab Team</strong></p>
  </div>

  <div class="footer">
    <hr />
    <p>This is an automated message. Please do not reply directly to this email.</p>
  </div>
</body>
</html>
HTML;

    $mail->send();
    echo json_encode([
        'message' => 'Appointment has been added.',
        'email_sent' => true
    ]);
} catch (Exception $e) {
    echo json_encode([
        'message' => 'Appointment saved, but email failed to send.',
        'email_sent' => false
    ]);
}

$conn->close();
