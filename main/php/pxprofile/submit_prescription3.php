<?php
include('../../connection/conn.php');
require('../FPDF/fpdf.php');
include('../../check_session.php');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

if (!IS_AJAX) {
    die('<h1>404 Not Found</h1>');
} else {
    $px_id = urldecode($_POST['px_id']);
    $issued_by = $_SESSION['USER_ID'];
    $presc = urldecode($_POST['prescription']);
    $flupdate = urldecode($_POST['prescFollDate']) ?? null;
    if (empty($flupdate)) {
        $flupdate = null;
    }
    $ptr = urldecode($_POST['prescPTR']);
    $s2 = urldecode($_POST['prescS2']);
    $presc_date = urldecode($_POST['prescDate']);
    $blank = "none";
    $is_active = '1';

    if ($presc == "") {
        echo "Prescription must not be empty";
        return false;
    }
    
    $s = $conn->prepare("INSERT INTO tbl_prescrip (prescription_for, prescription, issued_by, prescription_ptr, prescription_s2, prescription_date, prescription_folldate, prescription_is_active, presc_filepath) VALUES (?,?,?,?,?,?,?,?,?)");
    $s->bind_param("isissssss", $px_id, $presc, $issued_by, $ptr, $s2, $presc_date, $flupdate, $is_active, $blank);
    $s->execute();
    $s->store_result();

    if ($s->affected_rows == 1) {
        $id = $s->insert_id;

        // Retrieve data from the database including formatted fullname
        $s = $conn->prepare("SELECT px.px_emailadd, 
                                    CONCAT(px.px_firstname, ' ', px.px_lastname, IF(px.px_suffix != '', CONCAT(' ', px.px_suffix), '')) AS fullname, 
                                    inf.information_name, 
                                    inf.information_licno, 
                                    inf.information_address, 
                                    inf.information_contact, 
                                    px.px_address, 
                                    px.px_dob, 
                                    presc.prescription, 
                                    presc.prescription_ptr, 
                                    presc.prescription_s2, 
                                    presc.prescription_date, 
                                    presc.prescription_folldate 
                            FROM tbl_prescrip AS presc 
                            INNER JOIN tbl_px_details AS px ON presc.prescription_for = px.px_id 
                            INNER JOIN tbl_information AS inf ON presc.issued_by = inf.information_id 
                            WHERE presc.presc_id = ?");
        $s->bind_param("i", $id);
        $s->execute();
        $s->bind_result($email, $fullname, $info_name, $info_licno, $info_address, $info_contact, $px_address, $px_dob, $prescription, $ptr, $s2, $presc_date, $presc_folldate);
        $s->fetch();
        $s->close();
        
        // Format the prescription date
        $presc_date = new DateTime($presc_date);
        $formattedPrescDate = $presc_date->format('M j, Y');

        // Format the follow-up date if available
        if ($presc_folldate == "") {
            $formattedPrescFollDate = "";
        } else {
            $presc_folldate = new DateTime($presc_folldate);
            $formattedPrescFollDate = $presc_folldate->format('M j, Y');
        }

        // Prescription PDF Generation
        $RxPath = 'rx_logo.png';
        $pdf = new FPDF();
        $pdf->AddPage('P', 'A5');
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 5);
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

        // Calculate the patient's age
        $age = calculateAge($px_dob);
        if ($age == 0) {
            $age = "";
        }

        // Add name and age to PDF
        $pdf->SetX($fcol_x);
        $pdf->Cell(50, 5, 'Name: ' . $fullname, 0, 0);
        $pdf->SetX($scol_x);
        $pdf->Cell(50, 5, 'Age: ' . $age, 0, 1);

        // Add address, date, and Rx logo to the PDF
        $pdf->SetX($fcol_x);
        $address_start_y = $pdf->GetY();
        $pdf->SetY($address_start_y);
        $pdf->MultiCell($scol_x - 5, 8, 'Address: ' . $px_address, 0, 'L');
        $pdf->SetY($address_start_y);
        $pdf->SetX($scol_x);
        $pdf->Cell(50, 8, 'Date: ' . $formattedPrescDate, 0, 1);
        $pdf->Ln(2);
        $pdf->Image($RxPath, 10, $pdf->GetY(), $image_width, $image_height);
        $pdf->Ln(20);        

        // Prescription text box
        $pdf->SetFont('Arial', 'I', 11);
        $cellWidth = 125;
        $cellHeight = 5;
        $pageWidth = $pdf->GetPageWidth();
        $x = 15;
        $pdf->SetX($x);
        $pdf->MultiCell($cellWidth, $cellHeight, $prescription, 0, '');
        $pdf->Ln(-8);

        // Ensure footer is at the bottom of the page
        $footerHeight = 30;  // Adjust based on the height of the footer content
        $footerY = $pdf->GetY();
        $remainingHeight = $pdf->GetPageHeight() - $footerY - 10;  // Calculate remaining space at the bottom of the page

        // If the footer doesn’t fit on the current page, add a new page
        if ($remainingHeight < $footerHeight) {
            $pdf->AddPage('P', 'A5');
            $pdf->SetY(-25);  // Position footer close to the bottom of the new page
        } else {
            $pdf->SetY(-25);  // Position footer close to the bottom of the current page
        }

        // Signature image position on the right side above footer
        $signatureX = $page_width - 60;  // Position just on the right of the page
        $signatureY = $pdf->GetY() - 7;  // Place just above footer text
        $signatureWidth = 60;  // Width of the signature image
        $signatureHeight = 20;  // Height of the signature image
        $signaturePath = '../../../favicon/zxc.png';  // Path to the signature image
        $pdf->Image($signaturePath, $signatureX, $signatureY, $signatureWidth, $signatureHeight);

        // Footer positioning: License No., PTR No., and S2, Follow-up Date
        $page_width = $pdf->GetPageWidth();
        $right_margin = -5;  // Move closer to the right
        $cell_width = 60;
        $x_position = $page_width - $cell_width - $right_margin;  // Position for License No., PTR No., and S2 No.
        $footer_y_position = $pdf->GetY(); // Start at the current Y position

        // License No., PTR No., and S2 No. on the right side
        $pdf->SetY($footer_y_position);
        $pdf->SetX($x_position);
        $pdf->SetFont('Arial', 'U', 11);
        $pdf->Cell($cell_width, 10, 'License No.: ' . $info_licno, 0, 0, 'L');
        $pdf->Ln(5);

        $pdf->SetX($x_position);
        $pdf->Cell($cell_width, 10, 'PTR No.: ' . $ptr, 0, 0, 'L');
        $pdf->Ln(5);

        // S2 No. on the right side
        $pdf->SetX($x_position);
        $pdf->Cell($cell_width, 10, 'S2 No.: ' . $s2, 0, 0, 'L');

        // Follow-up Date at the start of the left margin
        $followup_x_position = 10;  // Align with the left margin
        $pdf->SetX($followup_x_position);  // Position for Follow-up Date
        $pdf->Cell($cell_width, 10, 'Follow-up Date: ' . $formattedPrescFollDate, 0, 0, 'L');

        // Save the PDF to a file
        $date = new DateTime('now');
        $filename_date = $date->format('F_j_Y_His');
        $output_path = '../PrescriptionPDFs/' . $fullname . '_' . $filename_date . '.pdf';

        // Update the file path in the database
        $x = $conn->prepare("UPDATE tbl_prescrip SET presc_filepath = ? WHERE presc_id = ?");
        $x->bind_param("si", $output_path, $id);
        $x->execute();
        $x->close();

        // Output the PDF to the file
        $pdf->Output('F', $output_path);
        echo "Prescription successfully added";
    } else {
        echo $conn->error;
        $s->close();
    }
}

$conn->close();
?>