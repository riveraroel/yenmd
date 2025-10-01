<?php
include('../../connection/conn.php');
require '../PHPMailer/PHPMailerAutoload.php';
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if(!IS_AJAX)
{
	die('<h1>404 Not Found</h1>');
}
else
{
    $medhis_id = urldecode($_POST['medhis_id']);
    $s = $conn->prepare("SELECT px.px_emailadd AS email, CONCAT_WS(' ', px.px_firstname, px.px_midname, px.px_lastname, px.px_suffix) AS fullname, px.px_company AS company, mh.medhis_labresult AS labresult, mh.medhis_diagnosis AS diag, mh.medhis_medication AS medication, mh.medhis_recommendation AS recom, mh.medhis_diagnosis_date AS diag_date, mh.medhis_attachment AS mh_att FROM tbl_medical_history AS mh INNER JOIN tbl_px_details AS px ON mh.px_id = px.px_id WHERE mh.medhis_id = ?");
    $s->bind_param("i", $medhis_id);
    $s->execute();
    $s->bind_result($email, $fullname, $company, $labresult, $diag, $medication, $recom, $diag_date, $output_path);
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
        $mail->Subject = "HealthLab Medical Certificate Request for: ".$fullname;
        $mail->Body = "Please check the attachment for your medical certificate";
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