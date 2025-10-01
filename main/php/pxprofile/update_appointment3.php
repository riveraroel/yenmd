<?php
include('../../connection/conn.php');
require '../PHPMailer/PHPMailerAutoload.php';

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
if (!IS_AJAX) {
    die('<h1>404 Not Found</h1>');
}

$apt_date = date('Y-m-d', strtotime($_POST['apt_date']));
$apt_start = date('H:i', strtotime($_POST['apt_start']));
$apt_end = date('H:i', strtotime($_POST['apt_end']));
$apt_reason = urldecode($_POST['apt_reason']);
$apt_id = urldecode($_POST['apt_id']);

$a = strtotime($_POST['apt_start']);
$b = strtotime($_POST['apt_end']);
$seconds_diff = ($b - $a) / 3600;

if ($seconds_diff <= 0) {
    echo "Invalid time. Please try again. Thanks";
    exit;
}

// Update appointment
$update = $conn->prepare("UPDATE tbl_appointment SET apt_date = ?, apt_start = ?, apt_end = ?, apt_reason = ? WHERE apt_id = ?");
$update->bind_param("ssssi", $apt_date, $apt_start, $apt_end, $apt_reason, $apt_id);
$update->execute();
$update->store_result();

if ($update->affected_rows == 1) {
    $stmt = $conn->prepare("
        SELECT px.px_emailadd AS email,
               CONCAT(px.px_firstname, ' ', px.px_midname, ' ', px.px_lastname, ' ', px.px_suffix) AS fullname,
               apt.apt_date, TIME_FORMAT(apt.apt_start, '%h:%i %p') AS aptstart,
               TIME_FORMAT(apt.apt_end, '%h:%i %p') AS aptend, apt.apt_reason
        FROM tbl_appointment AS apt
        INNER JOIN tbl_px_details AS px ON apt.px_id = px.px_id
        WHERE apt.apt_id = ?
    ");
    $stmt->bind_param("i", $apt_id);
    $stmt->execute();
    $stmt->bind_result($email, $fullname, $aptdate, $aptstart, $aptend, $aptreason);
    $stmt->fetch();
    $stmt->close();

    if (empty($email)) {
        echo "Appointment has been updated. No email address on file.";
        exit;
    }

    $formattedDate = (new DateTime($aptdate))->format('F j, Y');
    $image_path = 'banner.png';

    $smtp = $conn->prepare("SELECT smtp_host, smtp_username, smtp_password, smtp_senderfrom, smtp_sendername FROM tbl_smtp_service WHERE smtp_is_active = 1 LIMIT 1");
    $smtp->execute();
    $smtp->bind_result($host, $user, $pass, $from, $from_name);
    $smtp->fetch();
    $smtp->close();

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $user;
        $mail->Password = $pass;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom($from, $from_name);
        $mail->addAddress($email, $fullname);
        $mail->addEmbeddedImage($image_path, 'image_cid');

        $mail->isHTML(true);
        $mail->Subject = "HealthLab Appointment Update for: $fullname";

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
  <div class="header">
    <img src="cid:image_cid" alt="Clinic Logo" width="350" height="100" />
    <p>Suite 1601, Medical Plaza Makati, Amorsolo Corner Dela Rosa Sts., Legaspi Village, Makati City</p>
    <p>Tel. No: 8-867-1140</p>
    <hr />
  </div>

  <div class="greeting">
    <p>Dear <strong>$fullname</strong>,</p>
  </div>

  <div class="message">
    <p>Your appointment at <strong>HealthLab</strong> has been updated.</p>
    <p>Below are the new appointment details:</p>
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
      <td>$aptreason</td>
    </tr>
  </table>

  <div class="message">
    <p>Please arrive at least 10 minutes before your scheduled time and bring any relevant documents or prior test results, if available.</p>
    <p>If you have any questions or need to reschedule again, feel free to contact us at the number above.</p>
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
        echo "Appointment has been updated. Email has been sent to the patient.";
    } catch (Exception $e) {
        echo "Appointment has been updated. Email failed to send.";
    }
} else {
    echo "No changes have been made.";
}
$conn->close();
