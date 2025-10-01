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
    $s = $conn->prepare("SELECT info.information_name AS doc, apt.apt_id as aptid, CONCAT(px.px_firstname, ' ', px.px_midname, ' ', px.px_lastname, ' ', px.px_suffix) AS fullname, apt.apt_date AS aptdate, apt.apt_start AS aptstart, apt.apt_end AS aptend, apt.apt_reason AS aptreason FROM tbl_appointment AS apt INNER JOIN tbl_px_details AS px ON apt.px_id = px.px_id INNER JOIN tbl_information AS info ON apt.issued_by = info.information_id WHERE apt.apt_is_active = ? AND px.px_is_active = ?");
    $s->bind_param("ii", $is_active, $is_active);
    $s->execute();
    $res = $s->get_result();
    while ($row = $res->fetch_assoc())
    {
        $date1 = $row['aptdate'];
        $date2 = $row['aptdate'];
        $appointmentStart = date('h:i A', strtotime($row['aptstart']));
        $appointmentEnd = date('h:i A', strtotime($row['aptend']));
        $dateStart = date('Y-m-d\TH:i', strtotime("$date1 $appointmentStart"));
        $dateEnd = date('Y-m-d\TH:i', strtotime("$date2 $appointmentEnd"));
        $data[] = array(
            'id' => $row['aptid'],
            'title' => $row['fullname'],
            'start' => $dateStart,
            'end' => $dateEnd,
            'description' => "Reason: ".$row['aptreason']. "<br>Appointment for: ".$row['doc']. "<br>Duration: ".$appointmentStart." - ".$appointmentEnd,
            'textColor' => '#FFF'
        );
    }
    $res->free_result();
    $s->close();
    echo json_encode($data);
}
$conn->close();
?>