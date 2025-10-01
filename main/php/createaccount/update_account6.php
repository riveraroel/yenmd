<?php
include('../../connection/conn.php');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if(!IS_AJAX)
{
	die('<h1>404 Not Found</h1>');
}
else
{
    $acc_id = urldecode($_POST['acc_id']);
    $name = urldecode($_POST['viewacc_name']);
    $uname = encrypt_decrypt('encrypt', strtoupper(urldecode($_POST['viewacc_uname'])));
    $pword = encrypt_decrypt('encrypt', urldecode($_POST['viewacc_pword']));
    $role = urldecode($_POST['viewacc_role']);
    $licno = urldecode($_POST['viewacc_licno']);
    $ptr = urldecode($_POST['viewacc_ptr']);
    $s2 = urldecode($_POST['viewacc_s2']);
    $contact = urldecode($_POST['viewacc_contact']);
    $email = urldecode($_POST['viewacc_email']);
    $address = urldecode($_POST['viewacc_address']);
    $is_active = '1';
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
    $s = $conn->prepare("SELECT login_id FROM tbl_login WHERE username = ? AND information_id <> ? AND is_active = ?");
    $s->bind_param("sis", $uname, $acc_id, $is_active);
    $s->execute();
    $s->store_result();
    if ($s->num_rows == 0)
    {
        $z = $conn->prepare("UPDATE tbl_login SET username = ?, pword = ? WHERE information_id = ?");
        $z->bind_param("ssi", $uname, $pword, $acc_id);
        $z->execute();
        $x = $conn->prepare("UPDATE tbl_information SET information_name = ?, information_licno = ?, information_ptr = ?, information_s2 = ?, information_address = ?, information_contact = ?, information_emailadd = ?, role_id = ? WHERE information_id = ?");
        $x->bind_param("ssssssssi", $name, $licno, $ptr, $s2, $address, $contact, $email, $role, $acc_id);
        $x->execute();
        echo "Account info successfully updated";
    }
    else
    {
        echo "Username already existing";
    }
}
$conn->close();
?>