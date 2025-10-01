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
    $month = $_GET['month'];
    $year = $_GET['year'];
    $s = $conn->prepare("SELECT apt.apt_id as aptid, CONCAT(px.px_firstname, ' ', px.px_midname, ' ', px.px_lastname, ' ', px.px_suffix) AS fullname, apt.apt_date AS aptdate, apt.apt_start AS aptstart, apt.apt_end AS aptend, apt.apt_reason AS aptreason FROM tbl_appointment AS apt INNER JOIN tbl_px_details AS px ON apt.px_id = px.px_id WHERE MONTH(apt.apt_date) = ? AND YEAR(apt.apt_date) = ? AND apt.apt_is_active = ? AND px.px_is_active = ?");
    $s->bind_param("ssii", $month, $year, $is_active, $is_active);
    $s->execute();
    $s->store_result();
    echo "Total appointment for this month: ".$s->num_rows;
    $s->close();
}
$conn->close();
?>