<?php
include('../../connection/conn.php');
require '../PHPMailer/PHPMailerAutoload.php';

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if (!IS_AJAX) {
    die('<h1>404 Not Found</h1>');
} else {
    $labtest_id = urldecode($_POST['labtest_id']);

    $s = $conn->prepare("
        SELECT 
            px.px_emailadd AS email, 
            CONCAT_WS(' ', px.px_firstname, px.px_midname, px.px_lastname, px.px_suffix) AS fullname, 
            px.px_company AS company, 
            lr.pdf_filename 
        FROM tbl_lab_requests AS lr
        INNER JOIN tbl_px_details AS px ON lr.px_id = px.px_id 
        WHERE lr.labtest_id = ?
    ");
    $s->bind_param("i", $labtest_id);
    $s->execute();
    $s->bind_result($email, $fullname, $company, $output_path);
    $s->fetch();
    $s->close();

    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';

    try {
        $z = $conn->prepare("SELECT smtp_host, smtp_username, smtp_password, smtp_senderfrom, smtp_sendername FROM tbl_smtp_service WHERE smtp_is_active = '1'");
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
        $mail->Subject = "HealthLab Diagnostic Procedure Request for: " . $fullname;
        $mail->Body = "Please check the attachment for your lab test request.";

        $mail->addAttachment($output_path);

        if ($mail->send()) {
            echo 'Email sent successfully.';
        } else {
            echo 'Failed to send email.';
        }

    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }
}

$conn->close();
?>
