<?php
include('../../connection/conn.php');
require('../FPDF/fpdf.php');
include('../../check_session.php');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

if (!IS_AJAX) {
	die('<h1>404 Not Found</h1>');
} else {
    $presc_id = urldecode($_POST['presc_id']);
    $issued_by = $_SESSION['USER_ID'];
    $presc = urldecode($_POST['view_prescription']);
    $flupdate = urldecode($_POST['view_prescFollDate']);
    if (empty($flupdate)) {
        $flupdate = null;
    }
    $ptr = urldecode($_POST['view_prescPTR']);
    $s2 = urldecode($_POST['view_prescS2']);
    $presc_date = urldecode($_POST['view_prescDate']);
    $blank = "none";
    $is_active = '1';

    if ($presc == "") {
        echo "Prescription must not be empty";
        return false;
    }

    $s = $conn->prepare("UPDATE tbl_prescrip  SET issued_by = ?, prescription = ?, prescription_ptr = ?, prescription_s2 = ?, prescription_date = ?, prescription_folldate = ? WHERE presc_id = ?");
    $s->bind_param("isssssi", $issued_by, $presc, $ptr, $s2, $presc_date, $flupdate, $presc_id);
    $s->execute();
    $s->store_result();

    if ($s->affected_rows == 1) {

        
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
                                    presc.prescription_folldate,
                                    presc.presc_filepath 
                            FROM tbl_prescrip AS presc 
                            INNER JOIN tbl_px_details AS px ON presc.prescription_for = px.px_id 
                            INNER JOIN tbl_information AS inf ON presc.issued_by = inf.information_id 
                            WHERE presc.presc_id = ?");
        $s->bind_param("i", $presc_id);
        $s->execute();
        $s->bind_result($email, $fullname, $info_name, $info_licno, $info_address, $info_contact, $px_address, $px_dob, $prescription, $ptr, $s2, $presc_date, $presc_folldate, $pdfatt);
        $s->fetch();
        $s->close();
        unlink($pdfatt);
        
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

        $pdf = new FPDF('P', 'mm', 'A5');
        $pdf->AddPage();

        // Optional: Set font or other properties here
        $pdf->SetFont('Arial', '', 12);

        // Draw border (frame) around the page
        $page_width = 148;  // A5 width in mm
        $page_height = 210; // A5 height in mm
        $margin = 2;        // Margin from edge

        $pdf->SetLineWidth(0.5); // Border thickness
        $pdf->Rect($margin, $margin, $page_width - 2 * $margin, $page_height - 2 * $margin);

        // Add your content after this
        // Example: add image, text, QR, etc.


        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 5);
        $pdf->Ln(25);        

        // Set image path
        $image_path = '../../../favicon/bannerzxc.png';
        $page_width = 148; // A5 width in mm

        // Original size in px
        $image_width_px = 350;
        $image_height_px = 100;

        // Convert to mm
        $image_width_mm = ($image_width_px * 25.4) / 96;
        $image_height_mm = ($image_height_px * 25.4) / 96;

        // Limit width
        $max_width_mm = 120;
        if ($image_width_mm > $max_width_mm) {
            $scale = $max_width_mm / $image_width_mm;
            $image_width_mm *= $scale;
            $image_height_mm *= $scale;
        }

        // Center horizontally
        $x_centered = ($page_width - $image_width_mm) / 2;
        $y_position = 5; // from top

        $pdf->Image($image_path, $x_centered, $y_position, $image_width_mm, $image_height_mm);



        $pdf->SetFont('Arial', 'B', 15);
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

               
        // === FOOTER ===
        // Move to a position approx. 60 units from the bottom
        $pdf->SetY(-48);

        // Page width and right alignment
        $page_width = $pdf->GetPageWidth();
        $cell_width = 60;
        $right_margin = -5;
        $x_position = $page_width - $cell_width - $right_margin;

        // === Physician Signature (Image above name) ===
        // Adjust path and dimensions to your actual signature file
        $signaturePath = '../../../favicon/zxc.png';  // Path to the signature image
        $signatureWidth = 45;
        $signatureHeight = 18;
        $signatureX = $x_position;
        $signatureY = $pdf->GetY() + 10; // Move 10 units *down*

        // Output the signature image
        $pdf->Image($signaturePath, $signatureX, $signatureY, $signatureWidth, $signatureHeight);
        $pdf->Ln($signatureHeight + 1); // Move below the signature

        // === Physician Name ===
        $pdf->SetX($x_position);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell($cell_width, 6, $info_name, 0, 1, 'L');

        // === License No. (Underlined) ===
        $pdf->SetX($x_position);
        $pdf->SetFont('Arial', 'U', 11);
        $pdf->Cell($cell_width, 6, 'License No.: ' . $info_licno, 0, 1, 'L');

        // === PTR No. ===
        $pdf->SetX($x_position);
        $pdf->SetFont('Arial', 'U', 11);
        $pdf->Cell($cell_width, 6, 'PTR No.: ' . $ptr, 0, 1, 'L');

        // === S2 No. ===
        $pdf->SetX($x_position);
        $pdf->Cell($cell_width, 6, 'S2 No.: ' . $s2, 0, 1, 'L');

        // === Follow-up Date (Left-aligned) ===
        $pdf->SetY(-11);
        $pdf->SetX(10); // left margin
        $pdf->Cell(60, 6, 'Follow-up Date: ' . $formattedPrescFollDate, 0, 1, 'L');
        
        // Save the PDF to a file
        $date = new DateTime('now');
        $filename_date = $date->format('F_j_Y_His');
        $output_path = '../PrescriptionPDFs/' . $fullname . '_' . $filename_date . '.pdf';

        // Update the file path in the database
        $x = $conn->prepare("UPDATE tbl_prescrip SET presc_filepath = ? WHERE presc_id = ?");
        $x->bind_param("si", $output_path, $presc_id);
        $x->execute();
        $pdf->Output('F', $output_path);
        if ($x->affected_rows == 1)
        {
            echo "Prescription successfully updated";
            $x->close();
        }
        else
        {
            echo "PDF File not updated";
            $x->close();
        }
    }
    else
    {
        echo "No changes have been made";
        $s->close();
    }
}
$conn->close();
?>