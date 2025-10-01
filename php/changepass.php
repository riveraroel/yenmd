<?php
date_default_timezone_set('Asia/Manila');
include('../connection/conn.php');
require '../main/php/PHPMailer/PHPMailerAutoload.php';
function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if(!IS_AJAX)
{
	die('<h1>404 Not Found</h1>');
}
else
{
    $username = strtoupper(urldecode($_POST['username']));
    $dbuname = encrypt_decrypt('encrypt', $username);
    $image_path = 'banner.png';
    $s = $conn->prepare("SELECT lg.login_id, info.information_emailadd, info.information_name FROM tbl_login AS lg INNER JOIN tbl_information AS info ON lg.login_id = info.information_id WHERE lg.username = ?");
    $s->bind_param("s", $dbuname);
    $s->execute();
    $s->store_result();
    $s->bind_result($id, $email, $fullname);
    $s->fetch();
    if ($s->num_rows == 1)
    {
        $newpass = randomPassword();
        $dbpass = encrypt_decrypt('encrypt', $newpass);
        $s = $conn->prepare("UPDATE tbl_login SET pword = ? WHERE login_id = ?");
        $s->bind_param("si", $dbpass, $id);
        $s->execute();
        $s->store_result();
        if ($s->affected_rows == 1)
        {
            $mail = new PHPMailer(true);
            try{
                $z = $conn->prepare("SELECT smtp_host, smtp_username, smtp_password, smtp_senderfrom, smtp_sendername FROM tbl_smtp_service WHERE smtp_is_active = '1'");
                $z->execute();
                $z->bind_result($host, $uname, $password, $sndr_from, $sndr_name);
                $z->fetch();
                $z->close();
                $mail->isSMTP();
                $mail->Host = $host;
                $mail->SMTPAuth = false;                          
                $mail->Username = $uname;    
                $mail->Password = $password;
                $mail->setFrom($sndr_from, $sndr_name);
                $mail->addAddress($email, $fullname);
                $mail->addEmbeddedImage($image_path, 'image_cid');
                $mail->isHTML(true);
                $mail->Subject = "HealthLab New Password Update for: ".$fullname;
                $mail->Body = '<html><body>';
                $mail->Body .= '<div style="text-align: center;">';
                $mail->Body .= '<img src="cid:image_cid" alt="Embedded Image" width="350" height="100" style="display: inline-block;"/>';
                $mail->Body .= '</div><br>';
                $mail->Body .= '<center><h2 style="margin: 0;">MAKATI OFFICE</h2></center>';
                $mail->Body .= '<center><p style="margin: 0;">Suite 1601, Medical Plaza Makati, Amorsolo Corner</p></center>';
                $mail->Body .= '<center><p style="margin: 0;">Dela Rosa Sts., Legaspi Village Makati City</p></center>';
                $mail->Body .= '<center><p style="margin: 0;">Tel. No : 8-867-1140</p></center>';
                $mail->Body .= '<hr>';
                $mail->Body .= '<center><h1>New password</h1></center><br>';
                $mail->Body .= '<table style = "width: 100%" border = "2">';
                $mail->Body .= '<tr>';
                $mail->Body .= '<th>Name</th>';
                $mail->Body .= '<th>Username</th>';
                $mail->Body .= '<th>Password</th>';;
                $mail->Body .= '</tr>';
                $mail->Body .= '<tr>';
                $mail->Body .= '<td><center>'.$fullname.'</center></td>';
                $mail->Body .= '<td><center>'.$username.'</center></td>';
                $mail->Body .= '<td><center>'.$newpass.'</center></td>';
                $mail->Body .= '</tr>';
                $mail->Body .= '</table>';
                $mail->Body .= '</body></html>';
                $mail->send();
                echo "Password sent to username's email";
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    }
    else
    {
        echo "Username does not exist";
    }
}
?>