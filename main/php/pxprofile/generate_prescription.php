<?php
include('../../connection/conn.php');
require('../FPDF/fpdf.php');
require '../PHPMailer/PHPMailerAutoload.php';
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if(!IS_AJAX)
{
	die('<h1>404 Not Found</h1>');
}
else
{
    $id = urldecode($_POST['id']);
    $s = $conn->prepare("SELECT px.px_emailadd, CONCAT_WS(' ', px.px_firstname, px.px_midname, px.px_lastname, px.px_suffix) AS fullname, inf.information_name, inf.information_licno, inf.information_address, inf.information_contact, CONCAT(px.px_firstname, ' ', px.px_midname, ' ', px.px_lastname, ' ', px.px_suffix) AS fullname, px.px_address, px.px_dob, presc.prescription, presc.prescription_ptr, presc.prescription_s2, presc.prescription_date, presc.prescription_folldate, presc.presc_filepath FROM tbl_prescrip AS presc INNER JOIN tbl_px_details AS px ON presc.prescription_for = px.px_id INNER JOIN tbl_information AS inf ON presc.issued_by = inf.information_id WHERE presc.presc_id = ?");
    $s->bind_param("i", $id);
    $s->execute();
    $s->bind_result($email, $fullname, $info_name, $info_licno, $info_address, $info_contact, $px_fullname, $px_address, $px_dob, $prescription, $ptr, $s2, $presc_date, $presc_folldate, $output_path);
    $s->fetch();
    $s->close();
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    try
    {
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
        $mail->Subject = "HealthLab Prescription Receipt Request for: ".$fullname;
        $mail->Body = "Please check the attachment for your prescription receipt";
        $mail->addAttachment($output_path);
        if ($mail->send()) 
        {
            echo 'Email sent successfully.';
        } 
        else 
        {
            echo 'Failed to send email.';
        }
    } 
    catch (Exception $e) 
    {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }
}
$conn->close();
?>