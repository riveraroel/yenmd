<?php
include('../../connection/conn.php');
require('../FPDF/fpdf.php');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if(!IS_AJAX)
{
	die('<h1>404 Not Found</h1>');
}
else
{
    $medhis_id = urldecode($_POST['medhis_id']);
    $labresult = encrypt_decrypt('encrypt', urldecode($_POST['labresult']));
    $diagnosis = encrypt_decrypt('encrypt', urldecode($_POST['diagnosis']));
    $medication = encrypt_decrypt('encrypt', urldecode($_POST['medication']));
    $recommendation = encrypt_decrypt('encrypt', urldecode($_POST['recommendation']));
    $diagdate = urldecode($_POST['diagdate']);
    $issuancedate = urldecode($_POST['issuancedate']);
    $s = $conn->prepare("UPDATE tbl_medical_history SET medhis_labresult = ?, medhis_diagnosis = ?, medhis_medication = ?, medhis_recommendation = ?, medhis_diagnosis_date = ?, medhis_issuance_date = ? WHERE medhis_id = ?");
    $s->bind_param("ssssssi", $labresult, $diagnosis, $medication, $recommendation, $diagdate, $issuancedate, $medhis_id);
    $s->execute();
    $s->store_result();
    if ($s->affected_rows == 1)
    {
        $s = $conn->prepare("SELECT px.px_emailadd AS email, CONCAT(px.px_firstname, ' ', px.px_midname, ' ', px.px_lastname, ' ', px.px_suffix) AS fullname, px.px_company AS company, mh.medhis_labresult AS labresult, mh.medhis_diagnosis AS diag, mh.medhis_medication AS medication, mh.medhis_recommendation AS recom, mh.medhis_attachment AS pdf_att, mh.medhis_diagnosis_date AS diag_date, mh.medhis_issuance_date FROM tbl_medical_history AS mh INNER JOIN tbl_px_details AS px ON mh.px_id = px.px_id WHERE mh.medhis_id = ?");
        $s->bind_param("i", $medhis_id);
        $s->execute();
        $s->bind_result($email, $fullname, $company, $labresult, $diag, $medication, $recom, $pdfatt, $diag_date, $issuance_date);
        $s->fetch();
        $s->close();
        unlink($pdfatt);
        $dx = new DateTime($diag_date);
        $formattedDx = $dx->format('F j, Y');
        $pdf = new FPDF();
        $pdf->AddPage('P', 'Letter');
        $pdf->SetFont('Arial', 'B', 12);
        $page_width = $pdf->GetPageWidth();
        $page_height= $pdf->getPageHeight();
        $imageWidth = 60;
        $imageHeight = 30;
        $imageX = $page_width - $imageWidth - 10;
        $imageY = $page_height - $imageHeight - 10;
        $imagePath = '../../../favicon/license.png';
        $image_width_px = 350; 
        $image_height_px = 100;
        $image_width_mm = ($image_width_px * 25.4) / 96;
        $image_height_mm = ($image_height_px * 25.4) / 96;
        $x_centered = ($page_width - $image_width_mm) / 2;
        $image_path = '../../../favicon/bannerzxc.png';
        $pdf->Image($image_path, $x_centered, 10, $image_width_mm, $image_height_mm);
        $pdf->Ln(30);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 5, 'Suite 1601, Medical Plaza Makati, Amorsolo Corner', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Dela Rosa Sts., Legaspi Village Makati City', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Tel. No : 8-867-1140', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, strtoupper('Medical Certificate'), 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 12);
        $date = new DateTime($issuance_date);
        $formattedDate = $date->format('F j, Y');
        $filename_date = $date->format('F_j_Y_His');
        $pdf->Cell(0, 10, $formattedDate, 0, 1, 'R');
        $pdf->Ln(10);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'TO WHOM IT MAY CONCERN:', 0, 1, 'L');
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', '', 12);
        if ($company == "")
        {
            $text = "This is to certify that ".strtoupper($fullname)." was seen and examined on ".$formattedDx." and diagnosed to have / findings: ";
        }
        else
        {
            $text = "This is to certify that ".strtoupper($fullname)." of ".strtoupper($company).", was seen and examined on ".$formattedDx." and diagnosed to have / findings: ";
        }
        $pdf->MultiCell(0, 5, $text);
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'BU', 12);
        $pdf->MultiCell(0, 10, strtoupper(encrypt_decrypt('decrypt', $diag)));
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', '', 12);
        $text2 = "This certification is issued per request of patient for whatever purpose except those involving legal matters.";
        $pdf->MultiCell(0, 5, $text2);
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, "Recommendation:");
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'BU', 12);
        $pdf->Ln(5);
        $pdf->SetX(20);
        $pdf->MultiCell(0, 10, strtoupper(encrypt_decrypt('decrypt', $recom)));
        $pdf->Image($imagePath, $imageX, $imageY, $imageWidth, $imageHeight);
        $output_path ='../MedCertPDFs/'.$fullname.'_'.$filename_date.'.pdf';
        $x = $conn->prepare("UPDATE tbl_medical_history SET medhis_attachment = ? WHERE medhis_id = ?");
        $x->bind_param("si", $output_path, $medhis_id);
        $x->execute();
        $x->close();
        $pdf->Output('F', $output_path);
        echo "Medical diagnosis successfully updated";
    }
    else
    {
        echo "No changes have been made";
        $s->close();
    }
}
$conn->close();
?>