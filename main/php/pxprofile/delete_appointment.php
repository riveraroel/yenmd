<?php
include('../../connection/conn.php');
require '../PHPMailer/PHPMailerAutoload.php';

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
if (!IS_AJAX) {
    die('<h1>404 Not Found</h1>');
}

$set_as_inactive = '0';
$apt_id = urldecode($_POST['apt_id']);

$s = $conn->prepare("UPDATE tbl_appointment SET apt_is_active = ? WHERE apt_id = ?");
$s->bind_param("si", $set_as_inactive, $apt_id);
$s->execute();
$s->store_result();

if ($s->affected_rows === 1) {
    $s->close();

    $s = $conn->prepare("SELECT px.px_emailadd as email,
                                CONCAT_WS(' ', px.px_firstname, px.px_midname, px.px_lastname, px.px_suffix) AS fullname,
                                apt.apt_date AS aptdate,
                                TIME_FORMAT(apt.apt_start, '%h:%i %p') AS aptstart,
                                TIME_FORMAT(apt.apt_end, '%h:%i %p') AS aptend,
                                apt.apt_reason as aptreason
                         FROM tbl_appointment AS apt
                         INNER JOIN tbl_px_details AS px ON apt.px_id = px.px_id
                         WHERE apt.apt_id = ?");
    $s->bind_param("i", $apt_id);
    $s->execute();
    $s->bind_result($email, $fullname, $aptdate, $aptstart, $aptend, $aptreason);
    $s->fetch();
    $s->close();

    if (empty($email)) {
        echo "Appointment has been deleted. No email address was found, so no confirmation email was sent.";
        exit;
    }

    $formattedDate = (new DateTime($aptdate))->format('F j, Y');
    // $image_path = 'banner.png';

    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    try {
        $z = $conn->prepare("SELECT smtp_host, smtp_username, smtp_password, smtp_senderfrom, smtp_sendername FROM tbl_smtp_service WHERE smtp_is_active = '1' LIMIT 1");
        $z->execute();
        $z->bind_result($host, $uname, $password, $sndr_from, $sndr_name);
        $z->fetch();
        $z->close();

        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $uname;
        $mail->Password = $password;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom($sndr_from, $sndr_name);
        $mail->addAddress($email, $fullname);
        // $mail->addEmbeddedImage($image_path, 'image_cid');

        $mail->isHTML(true);
        $mail->Subject = "HealthLab Appointment Cancellation for: $fullname";

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
    <p>We regret to inform you that your scheduled appointment at <strong>HealthLab</strong> has been canceled.</p>
    <p>Below were the appointment details:</p>
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
    <p>If this was a mistake or if you wish to reschedule, kindly contact us at Tel. No: 8867-1140.</p>
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
        echo "Appointment has been deleted. Email has been sent to the patient.";
    } catch (Exception $e) {
        echo "Appointment has been deleted, but email failed to send.";
    }
} else {
    echo "Failed to delete appointment. Please try again.";
}

$conn->close();
?>
