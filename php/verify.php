<?php
include('../connection/conn.php');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if(!IS_AJAX)
{
	die('<h1>404 Not Found</h1>');
}
else
{
	$user = strtoupper($_POST['username']);
	$user = encrypt_decrypt('encrypt', $user);
	$pass = $_POST['password'];
	$pass = encrypt_decrypt('encrypt', $pass);
	if (isset($user) && isset($pass))
	{
		$s = $conn->prepare("SELECT lgn.username, lgn.pword, info.information_id, info.information_name, info.information_licno, info.role_id FROM tbl_login AS lgn INNER JOIN tbl_information AS info ON lgn.information_id = info.information_id WHERE lgn.username = ? AND lgn.pword = ? AND lgn.is_active = '1' ");
		$s->bind_param("ss", $user, $pass);
		$s->execute();
		$s->store_result();
		$num = $s->num_rows;
		$s->bind_result($uname, $pword, $info_id, $info_name, $info_licno, $info_role);
		$s->fetch();
		if ($num == "1")
		{
            session_start();
            $_SESSION['USER_LAST_ACTIVITY'] = time();
            $_SESSION['USER_ID'] = $info_id;
			$_SESSION['USER_NAME'] = $info_name;
			$_SESSION['USER_LICNO'] = $info_licno;
			$_SESSION['USER_ROLE'] = $info_role;
			echo "1";
		}
		else
		{
			echo "Incorrect Username/Password. Please try again.";
		}
	}
	else
	{
		echo "Unknown error. Please try again";
	}
	
}
$conn->close();
?>