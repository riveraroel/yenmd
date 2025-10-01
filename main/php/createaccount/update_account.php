<?php
include('../../connection/conn.php');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if (!IS_AJAX) {
    die('<h1>404 Not Found</h1>');
} else {
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

    if ($role == '2' && $licno == "") {
        echo "License number should not be empty";
        return false;
    }
    if ($contact == "") {
        echo "Contact number should not be empty";
        return false;
    }

    // Check if username already exists
    $s = $conn->prepare("SELECT login_id FROM tbl_login WHERE username = ? AND information_id <> ? AND is_active = ?");
    $s->bind_param("sis", $uname, $acc_id, $is_active);
    $s->execute();
    $s->store_result();

    if ($s->num_rows > 0) {
        echo "Username already existing";
        $s->close();
        $conn->close();
        exit;
    }

    // Get current values
    $stmt = $conn->prepare("SELECT l.username, l.pword, i.information_name, i.information_licno, i.information_ptr, i.information_s2, i.information_address, i.information_contact, i.information_emailadd, i.role_id 
                            FROM tbl_login l 
                            INNER JOIN tbl_information i ON l.information_id = i.information_id 
                            WHERE l.information_id = ?");
    $stmt->bind_param("i", $acc_id);
    $stmt->execute();
    $stmt->bind_result($cur_uname, $cur_pword, $cur_name, $cur_licno, $cur_ptr, $cur_s2, $cur_address, $cur_contact, $cur_email, $cur_role);
    $stmt->fetch();
    $stmt->close();

    // Check if changes were made
    if (
        $uname === $cur_uname &&
        $pword === $cur_pword &&
        $name === $cur_name &&
        $licno === $cur_licno &&
        $ptr === $cur_ptr &&
        $s2 === $cur_s2 &&
        $address === $cur_address &&
        $contact === $cur_contact &&
        $email === $cur_email &&
        $role == $cur_role
    ) {
        echo "No changes have been made";
    } else {
        // Perform the update
        $z = $conn->prepare("UPDATE tbl_login SET username = ?, pword = ? WHERE information_id = ?");
        $z->bind_param("ssi", $uname, $pword, $acc_id);
        $z->execute();
        $z->close();

        $x = $conn->prepare("UPDATE tbl_information SET information_name = ?, information_licno = ?, information_ptr = ?, information_s2 = ?, information_address = ?, information_contact = ?, information_emailadd = ?, role_id = ? WHERE information_id = ?");
        $x->bind_param("ssssssssi", $name, $licno, $ptr, $s2, $address, $contact, $email, $role, $acc_id);
        $x->execute();
        $x->close();

        echo "Account info successfully updated";
    }
}
$conn->close();
?>