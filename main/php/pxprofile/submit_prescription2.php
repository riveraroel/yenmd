<?php
include('../../connection/conn.php');
require('../FPDF/fpdf.php');
include('../../check_session.php');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if(!IS_AJAX)
{
	die('<h1>404 Not Found</h1>');
}
else
{
    $px_id = urldecode($_POST['px_id']);
    $issued_by = $_SESSION['USER_ID'];
    $presc = urldecode($_POST['prescription']);
    $flupdate = urldecode($_POST['prescFollDate']) ?? null;
    if (empty($flupdate))
    {
        $flupdate = null;
    }
    $ptr = urldecode($_POST['prescPTR']);
    $s2 = urldecode($_POST['prescS2']);
    $presc_date = urldecode($_POST['prescDate']);
    $blank = "none";
    $is_active = '1';
    if ($presc == "")
    {
        echo "Prescription must not be empty";
        return false;
    }
    $s = $conn->prepare("INSERT INTO tbl_prescrip (prescription_for, prescription, issued_by, prescription_ptr, prescription_s2, prescription_date, prescription_folldate, prescription_is_active, presc_filepath) VALUES (?,?,?,?,?,?,?,?,?)");
    $s->bind_param("isissssss", $px_id, $presc, $issued_by, $ptr, $s2, $presc_date, $flupdate, $is_active, $blank);
    $s->execute();
    $s->store_result();
    if ($s->affected_rows == 1)
    {
        $id = $s->insert_id;
        $s = $conn->prepare("SELECT px.px_emailadd, CONCAT(px.px_firstname, ' ', px.px_midname, ' ', px.px_lastname, ' ', px.px_suffix) AS fullname, inf.information_name, inf.information_licno, inf.information_address, inf.information_contact, CONCAT(px.px_firstname, ' ', px.px_midname, ' ', px.px_lastname, ' ', px.px_suffix) AS fullname, px.px_address, px.px_dob, presc.prescription, presc.prescription_ptr, presc.prescription_s2, presc.prescription_date, presc.prescription_folldate FROM tbl_prescrip AS presc INNER JOIN tbl_px_details AS px ON presc.prescription_for = px.px_id INNER JOIN tbl_information AS inf ON presc.issued_by = inf.information_id WHERE presc.presc_id = ?");
        $s->bind_param("i", $id);
        $s->execute();
        $s->bind_result($email, $fullname, $info_name, $info_licno, $info_address, $info_contact, $px_fullname, $px_address, $px_dob, $prescription, $ptr, $s2, $presc_date, $presc_folldate);
        $s->fetch();
        $s->close();
        $presc_date = new DateTime($presc_date);
        $formattedPrescDate = $presc_date->format('M j, Y');
        $presc_folldate = new DateTime($presc_folldate);
        $formattedPrescFollDate = $presc_folldate->format('M j, Y');
        if ($presc_folldate == "")
        {
            $formattedPrescFollDate = "";
        }
        else
        {
            $presc_folldate = new DateTime($presc_folldate);
            $formattedPrescFollDate = $presc_folldate->format('F j, Y');
        }
        $RxPath = 'rx_logo.png';
        $pdf = new FPDF();
        $pdf->AddPage('P', 'A5');
        $pdf->SetMargins(10, 10, 10);  // Set left, top, and right margins
        $pdf->SetAutoPageBreak(true, 5); // Set bottom margin
        $pdf->Ln(-3);        
        $pdf->SetFont('Arial', 'B', 17);
        $pdf->Cell(0, 10, $info_name, 0, 1, 'C');
        $pdf->Ln(1);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 5, 'Suite 1601, Medical Plaza Makati, Amorsolo Corner', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Dela Rosa Sts., Legaspi Village, Makati City', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Contact #: ' . $info_contact, 0, 1, 'C');
        $pdf->Ln(2);
        $pdf->Line(10, $pdf->GetY(), 138, $pdf->GetY());
        $page_width = $pdf->GetPageWidth();
        $fcol_x = 10;
        $scol_x = $page_width / 1.45;
        $pdf->SetFont('Arial', 'U', 11);
        $pdf->Ln(5);
        $image_height = 20;
        $image_width = 20;
        $age = calculateAge($px_dob);
        $pdf->SetX($fcol_x);
        $pdf->Cell(50, 10, 'Name: '.$px_fullname, 0, 0);
        $pdf->SetX($scol_x);
        $pdf->Cell(50, 10, 'Age: '.$age, 0, 1);
        $pdf->SetX($fcol_x);
        $address_start_y = $pdf->GetY();
        $pdf->SetY($address_start_y);
        $pdf->MultiCell($scol_x - 5, 8, 'Address: '.$px_address, 0, 'L');
        $pdf->SetY($address_start_y);
        $pdf->SetX($scol_x);
        $pdf->Cell(50, 10, 'Date: '.$formattedPrescDate, 0, 1);
        $pdf->Ln(2);
        $pdf->Image($RxPath, 10, $pdf->GetY(), $image_width, $image_height);
        $pdf->Ln(20);
        $pdf->SetFont('Arial', 'I', 11);
        $cellWidth = 130; 
        $cellHeight = 5; 
        $pageWidth = $pdf->GetPageWidth();
        $x = ($pageWidth - $cellWidth);
        $pdf->SetX($x);
        $pdf->MultiCell($cellWidth, $cellHeight, $prescription, 0, '');
        $pdf->Ln(-8); //space from last med and signature
        $page_width = $pdf->GetPageWidth();
        $page_height= $pdf->getPageHeight();
        $imageWidth = 60; 
        $imageHeight = 30;
        $imageX = $page_width - $imageWidth - 10;
        $imageY = $page_height - $imageHeight - 10;
        $imagePath = '../../../favicon/zxc.png';
        $pdf->SetFont('Arial', 'U', 11);
        $imagePath = '../../../favicon/zxc.png';
        $page_width = $pdf->GetPageWidth();
        $right_margin = 10;
        $cell_width = 60;
        $x_position = $page_width - $cell_width - $right_margin + 15;
        $image_width = 60; //signature
        $image_height = 20; 
        $image_y_position = $pdf->GetY(); 
        $pdf->Image($imagePath, $x_position - 5, $image_y_position + 15, $image_width, $image_height);
        $text_y_position = $image_y_position + $image_height + 2;
        $pdf->SetY($text_y_position);
        $pdf->SetX($x_position);
        $pdf->Cell($cell_width, 10, 'License No.: ' . $info_licno, 0, 0, 'L');
        $pdf->Ln(5);
        $pdf->SetX($x_position);
        $pdf->Cell($cell_width, 10, 'PTR No.: ' . $ptr, 0, 0, 'L');
        $pdf->Ln(5);
        $pdf->SetX($x_position);
        $pdf->Cell($cell_width, 10, 'S2 No.: ' . $s2, 0, 0, 'L');
        $pdf->SetX(5);
        $pdf->Cell($cell_width, 10, 'Follow-up Date: ' . $formattedPrescFollDate, 0, 0, 'L');
        $date = new DateTime('now');
        $filename_date = $date->format('F_j_Y_His');
        $output_path = '../PrescriptionPDFs/'.$px_fullname.'_'.$filename_date.'.pdf';
        $x = $conn->prepare("UPDATE tbl_prescrip SET presc_filepath = ? WHERE presc_id = ?");
        $x->bind_param("si", $output_path, $id);
        $x->execute();
        $x->close();
        $pdf->Output('F', $output_path);
        echo "Prescription successfully added";
    }
    else
    {
        echo $conn->error;
        $s->close();
    }
}
$conn->close();
?>