<?php
include('../../connection/conn.php');
include('../../check_session.php');
require '../PHPMailer/PHPMailerAutoload.php';
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if(!IS_AJAX)
{
	die('<h1>404 Not Found</h1>');
}
else
{
    $px_id = $_POST['px_id'];
    $issued_by = $_SESSION['USER_ID'];
    $appointmentDate = date('Y-m-d', strtotime($_POST['appointmentDate']));
    $appointmentStart = date('H:i', strtotime($_POST['appointmentStart']));
    $appointmentEnd = date('H:i', strtotime($_POST['appointmentEnd']));
    $reason = urldecode($_POST['reason']);
    $a = strtotime($_POST['appointmentStart']);
    $b = strtotime($_POST['appointmentEnd']);
    $seconds_diff = $b - $a;
	$seconds_diff = ($seconds_diff/3600); /// by hours
    $is_attended = '2';
    $is_active = '1';
    if ($seconds_diff > 0 )
    {
        $x = $conn->prepare("SELECT apt_id FROM tbl_appointment WHERE apt_date = ? AND apt_start = ? AND apt_is_active = ?");
        $x->bind_param("ssi", $appointmentDate, $appointmentStart, $is_active);
        $x->execute();
        $x->store_result();
        $s = $conn->prepare("INSERT INTO tbl_appointment (px_id, issued_by, apt_date, apt_start, apt_end, apt_reason, apt_is_attended, apt_is_active) VALUES (?,?,?,?,?,?,?,?)");
        $s->bind_param("iissssss", $px_id, $issued_by, $appointmentDate, $appointmentStart, $appointmentEnd, $reason, $is_attended, $is_active);
        $s->execute();
        $s->store_result();
        $apt_id = $s->insert_id;
        if ($s->affected_rows == 1)
        {
            $s = $conn->prepare("SELECT px.px_emailadd as email, CONCAT(px.px_firstname, ' ', px.px_midname, ' ', px.px_lastname, ' ', px.px_suffix) AS fullname, apt.apt_date AS aptdate, TIME_FORMAT(apt.apt_start, '%h:%i %p') AS aptstart, TIME_FORMAT(apt.apt_end, '%h:%i %p') AS aptend, apt.apt_reason as aptreason FROM tbl_appointment AS apt INNER JOIN tbl_px_details AS px ON apt.px_id = px.px_id WHERE apt.apt_id = ?");
            $s->bind_param("i", $apt_id);
            $s->execute();
            $s->bind_result($email, $fullname, $aptdate, $aptstart, $aptend, $aptreason);
            $s->fetch();
            $s->close();
            if ($email == "")
            {
                echo "Appointment has been added";
                return false;
            }
            $image_path = 'banner.png';
            $dx = new DateTime($aptdate);
            $formattedDx = $dx->format('F j, Y');
            $mail = new PHPMailer(true);
            try{
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
                $mail->addEmbeddedImage($image_path, 'image_cid');
                $mail->isHTML(true);
                $mail->Subject = "HealthLab Appointment for: ".$fullname;
                $mail->Body = '<html><body>';
                $mail->Body .= '<div style="text-align: center;">';
                $mail->Body .= '<img src="cid:image_cid" alt="Embedded Image" width="350" height="100" style="display: inline-block;"/>';
                $mail->Body .= '</div><br>';
                $mail->Body .= '<center><p style="margin: 0;">Suite 1601, Medical Plaza Makati, Amorsolo Corner</p></center>';
                $mail->Body .= '<center><p style="margin: 0;">Dela Rosa Sts., Legaspi Village Makati City</p></center>';
                $mail->Body .= '<center><p style="margin: 0;">Tel. No : 8-867-1140</p></center>';
                $mail->Body .= '<hr>';
                $mail->Body .= '<center><h1>Appointment Information</h1></center><br>';
                $mail->Body .= '<table style = "width: 100%" border = "2">';
                $mail->Body .= '<tr>';
                $mail->Body .= '<th>Date</th>';
                $mail->Body .= '<th>Start</th>';
                $mail->Body .= '<th>End</th>';
                $mail->Body .= '<th>Reason</th>';
                $mail->Body .= '</tr>';
                $mail->Body .= '<tr>';
                $mail->Body .= '<td><center>'.$formattedDx.'</center></td>';
                $mail->Body .= '<td><center>'.$aptstart.'</center></td>';
                $mail->Body .= '<td><center>'.$aptend.'</center></td>';
                $mail->Body .= '<td><center>'.$aptreason.'</center></td>';
                $mail->Body .= '</tr>';
                $mail->Body .= '</table>';
                $mail->Body .= '</body></html>';
                $mail->send();
                echo "Appointment has been added";
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
        else
        {
            echo "Appointment unable to add. Please try again";
            $s->close();
        } 
    }
    else
    {
        echo "Invalid time. Please try again.";
    }
}
$conn->close();
?>