<?php
include('../../connection/conn.php');
include('../../check_session.php');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if(!IS_AJAX)
{
	die('<h1>404 Not Found</h1>');
}
else
{
    $name = urldecode($_POST['addacc_name']);
    $contact = urldecode($_POST['addacc_contact']);
    $emailadd = urldecode($_POST['addacc_emailadd']);
    $uname = encrypt_decrypt('encrypt', strtoupper(urldecode($_POST['addacc_uname'])));
    $mypass = urldecode($_POST['addacc_pword']);
    $pword = encrypt_decrypt('encrypt', urldecode($_POST['addacc_pword']));
    $role = urldecode($_POST['addacc_role']);
    $licno = urldecode($_POST['addacc_licno']);
    $address = urldecode($_POST['addacc_address']);
    if ($role == '2' && $licno == "")
    {
        echo "License number should not be empty";
        return false;
    }
    if ($contact == "")
    {
        echo "Contact number should not be empty";
        return false;
    }
    $is_active = '1';
    $s = $conn->prepare("SELECT login_id FROM tbl_login WHERE username = ? AND is_active = '1'");
    $s->bind_param("s", $uname);
    $s->execute();
    $s->store_result();
    if ($s->num_rows == 0)
    {
        $s = $conn->prepare("INSERT INTO tbl_information (information_emailadd, information_contact, information_name, information_licno, information_address, information_is_active, role_id) VALUES (?,?,?,?,?,?,?)");
        $s->bind_param("ssssssi", $emailadd, $contact, $name, $licno, $address, $is_active, $role);
        $s->execute();
        $s->store_result();
        $info_id = $s->insert_id;
        if ($s->affected_rows == 1)
        {
            $s = $conn->prepare("INSERT INTO tbl_login (username, pword, information_id, is_active) VALUES (?,?,?,?)");
            $s->bind_param("ssis", $uname, $pword, $info_id, $is_active);
            $s->execute();
            $s->store_result();
            if ($s->affected_rows == 1)
            {
                echo "Account successfully submitted";
                $s->close();
            }
            else
            {
                echo "Unable to add account. Please try again.1";
                $s->close();
            }
        }
        else
        {
            echo "Unable to add account. Please try again. ".$conn->error;
            $s->close();
        }
    }
    else
    {
        echo "Username already existing";
    }
}
$conn->close();
?>