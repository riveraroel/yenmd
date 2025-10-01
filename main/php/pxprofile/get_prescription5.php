<?php
include('../../connection/conn.php');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if(!IS_AJAX)
{
	die('<h1>404 Not Found</h1>');
}
else
{
    $is_active = '1';
    $presc_id = base64_decode($_GET['presc_id']);
    $s = $conn->prepare("SELECT prescription_date AS presc_date, prescription AS presc, prescription_folldate AS presc_folldate, prescription_ptr AS ptr, prescription_s2 AS s2, presc_filepath AS fl FROM tbl_prescrip WHERE presc_id = ? AND prescription_is_active = ? ORDER BY prescription_cre_datetime DESC");
    $s->bind_param("ii", $presc_id, $is_active);
    $s->execute();
    $res = $s->get_result();
    while ($row = $res->fetch_assoc())
    {
        $data[] = $row;
    }
    $res->free_result();
    $s->close();
    echo json_encode($data);
}
$conn->close();
?>