<?php
include('../../connection/conn.php');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if(!IS_AJAX) {
    die('<h1>404 Not Found</h1>');
} else {
    $is_active = 1;
    $presc_id = base64_decode($_GET['presc_id']);
    
    $s = $conn->prepare("SELECT 
        p.prescription_date AS presc_date, 
        p.prescription AS presc, 
        p.prescription_folldate AS presc_folldate, 
        p.prescription_ptr AS ptr, 
        p.prescription_s2 AS s2, 
        p.presc_filepath AS fl,
        p.is_regulated AS regulated,
        d.px_address
    FROM tbl_prescrip p
    INNER JOIN tbl_px_details d ON p.prescription_for = d.px_id
    WHERE p.presc_id = ? AND p.prescription_is_active = ? 
    ORDER BY p.prescription_cre_datetime DESC");

$s->bind_param("ii", $presc_id, $is_active);
$s->execute();
$res = $s->get_result();


    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    $res->free_result();
    $s->close();

    echo json_encode($data);
}
$conn->close();
?>
