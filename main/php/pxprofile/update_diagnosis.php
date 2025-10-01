<?php
include('../../connection/conn.php');
include('../../check_session.php');
require('../FPDF/fpdf.php');
require_once __DIR__ . '/vendor/autoload.php'; // QR Code autoload
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

if (!IS_AJAX) {
    die('<h1>404 Not Found</h1>');
} else {
    $issued_by = $_SESSION['USER_ID'];
    $medhis_id = $_POST['medhis_id'];
    $labresult = encrypt_decrypt('encrypt', urldecode($_POST['labresult']));
    $diagnosis = encrypt_decrypt('encrypt', urldecode($_POST['diagnosis']));
    $medication = encrypt_decrypt('encrypt', urldecode($_POST['medication']));
    $recommendation = encrypt_decrypt('encrypt', urldecode($_POST['recommendation']));
    $issue_date = urldecode($_POST['issuancedate']);
    $diagdate = urldecode($_POST['diagdate']);

    if ($issue_date == "" || $issue_date == "0000-00-00") {
        echo "Date of issuance is required";
        return false;
    }

    if ($diagdate == "" || $diagdate == "0000-00-00") {
        echo "Date of diagnosis is required";
        return false;
    }

    // Get token and px_id
    $s = $conn->prepare("SELECT medhis_token, px_id FROM tbl_medical_history WHERE medhis_id = ?");
    $s->bind_param("i", $medhis_id);
    $s->execute();
    $s->bind_result($token, $px_id);
    $s->fetch();
    $s->close();

    $is_active = 1;
    $diag = $diagnosis;
    $recom = $recommendation;

    // Update record
    $s = $conn->prepare("UPDATE tbl_medical_history 
        SET px_id = ?, issued_by = ?, medhis_labresult = ?, medhis_diagnosis = ?, 
            medhis_medication = ?, medhis_recommendation = ?, 
            medhis_diagnosis_date = ?, medhis_issuance_date = ?, 
            medhis_is_active = ? 
        WHERE medhis_id = ?");
    $s->bind_param("iissssssii", $px_id, $issued_by, $labresult, $diag, $medication, $recom, $diagdate, $issue_date, $is_active, $medhis_id);
    $s->execute();

    if ($s->affected_rows >= 1) {
        $s->close();

        // Fetch updated patient info
        $s = $conn->prepare("SELECT px.px_emailadd AS email, 
                            px.px_firstname AS firstname, 
                            px.px_lastname AS lastname, 
                            px.px_suffix AS suffix, 
                            px.px_company AS company, 
                            mh.medhis_labresult AS labresult, 
                            mh.medhis_diagnosis AS diag, 
                            mh.medhis_medication AS medication, 
                            mh.medhis_recommendation AS recom, 
                            mh.medhis_diagnosis_date AS diag_date, 
                            mh.medhis_issuance_date AS issdate 
                            FROM tbl_medical_history AS mh 
                            INNER JOIN tbl_px_details AS px ON mh.px_id = px.px_id 
                            WHERE mh.medhis_id = ?");
        $s->bind_param("i", $medhis_id);
        $s->execute();
        $s->bind_result($email, $firstname, $lastname, $suffix, $company, $labresult, $diag, $medication, $recom, $diag_date, $issue_date);
        $s->fetch();
        $s->close();

        $fullname = strtoupper($firstname) . ' ' . strtoupper($lastname);
        if (!empty($suffix)) $fullname .= ' ' . strtoupper($suffix);
        $formattedDx = (new DateTime($diag_date))->format('F j, Y');

        // QR Code
        $verify_link = "https://yenmd.com/verify.php?id={$medhis_id}&token={$token}";
        $qrOptions = new QROptions(['outputType' => QRCode::OUTPUT_IMAGE_PNG, 'eccLevel' => QRCode::ECC_L, 'scale' => 3]);
        $qrcode = new QRCode($qrOptions);
        $qr_tmp_path = __DIR__ . '/qr_tmp_' . $medhis_id . '.png';
        $qrcode->render($verify_link, $qr_tmp_path);

        // Generate PDF
        $pdf = new FPDF();
        $pdf->AddPage('P', 'Letter');
        $pdf->SetFont('Arial', 'B', 12);
        $page_width = $pdf->GetPageWidth();
        $page_height = $pdf->GetPageHeight();

        $image_path = '../../../favicon/bannerzxc.png';
        $image_width_mm = (350 * 25.4) / 96;
        $image_height_mm = (100 * 25.4) / 96;
        $x_centered = ($page_width - $image_width_mm) / 2;
        $pdf->Image($image_path, $x_centered, 10, $image_width_mm, $image_height_mm);

        $pdf->Ln(30);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 5, 'Suite 1601, Medical Plaza Makati, Amorsolo Corner', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Dela Rosa Sts., Legaspi Village, Makati City', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Tel. No : 8867-1140', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->Line(10, $pdf->GetY(), 205, $pdf->GetY());
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'MEDICAL CERTIFICATE', 0, 1, 'C');
        $pdf->Ln(5);
        
        $pdf->SetRightMargin(20);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, (new DateTime($issue_date))->format('F j, Y'), 0, 1, 'R');
        $pdf->Ln(10);

        $pdf->SetLeftMargin(20);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'TO WHOM IT MAY CONCERN:', 0, 1, 'L');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', '', 12);
        $pdf->Write(5, "This is to certify that ");
        $pdf->SetFont('Arial', 'B', 12);
        // $pdf->Write(5, $fullname);
        $pdf->Write(5, utf8_decode($fullname));
        $pdf->SetFont('Arial', '', 12);
        if (!empty($company)) {
            $pdf->Write(5, " of " . strtoupper($company) . ",");
        }
        $pdf->Write(5, " was seen and examined on " . $formattedDx . " and diagnosed to have / findings: ");
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'BU', 12);
        $pdf->MultiCell(0, 10, strtoupper(encrypt_decrypt('decrypt', $diag)));
        $pdf->Ln(5);

        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 5, "This certification is issued per request of patient for whatever purpose except those involving legal matters.");
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, "Recommendation/s:");
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'BU', 12);
        $pdf->MultiCell(0, 10, strtoupper(encrypt_decrypt('decrypt', $recom)));

        $pdf->Image('../../../favicon/zxc.png', $page_width - 80, $page_height - 55, 60, 30);

        // Doctor's name and license
        $s = $conn->prepare("SELECT information_name, information_licno FROM tbl_information WHERE information_id = ?");
        $s->bind_param("i", $issued_by);
        $s->execute();
        $s->bind_result($info_name, $info_licno);
        $s->fetch();
        $s->close();

        $x_position = $page_width - 90;
        $cell_width = 70;
        $y_position = $page_height - 43;

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY($x_position, $y_position);
        $pdf->Cell($cell_width, 10, $info_name, 0, 1, 'C');
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetXY($x_position, $y_position + 5);
        $pdf->Cell($cell_width, 10, "PRC License No.: " . $info_licno, 0, 1, 'C');
        $pdf->Line($x_position, $y_position + 12, $x_position + $cell_width, $y_position + 12);
        $pdf->SetXY($x_position, $y_position + 10);
        $pdf->Cell($cell_width, 10, 'Physician', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(20, $page_height - 60);
        $pdf->Cell(40, 5, 'Scan to Validate this Medical Certificate', 0, 0, 'L');
        $pdf->Image($qr_tmp_path, 20, $page_height - 55, 30, 30);

        if (file_exists($qr_tmp_path)) unlink($qr_tmp_path);

        $filename_date = (new DateTime($diag_date))->format('Y_m_d');
        $output_path = '../MedCertPDFs/'.$fullname.'_MedCert_'.$filename_date.'.pdf';

        $x = $conn->prepare("UPDATE tbl_medical_history SET medhis_attachment = ? WHERE medhis_id = ?");
        $x->bind_param("si", $output_path, $medhis_id);
        $x->execute();
        $x->close();

        $pdf->Output('F', $output_path);
        echo "Diagnosis has been successfully updated.";
    } else {
        echo "No changes have been made." . $s->error;
        $s->close();
    }
}
$conn->close();
