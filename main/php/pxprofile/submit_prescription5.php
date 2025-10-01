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
        $pdf->SetFont('Arial', '', 11);
        $pdf->Ln(5);
        $image_height = 20;
        $image_width = 20;

        // Calculate the patient's age
        $age = calculateAge($px_dob);
        if ($age == 0) {
            $age = "";
        }

       $pdf->SetLineWidth(0.1); // Default is 0.2, this makes the line thinner
        
        // Line 1: Name and Age
        $pdf->Cell(18, 6, 'Name:', 0, 0);
        $pdf->Cell(60, 6, $fullname ?: ' ', 'B', 0);
        $pdf->Cell(10, 6, '', 0, 0); // Spacer (adjust width as needed)
        $pdf->Cell(12, 6, 'Age:', 0, 0);
        $pdf->Cell(26, 6, $age ?: ' ', 'B', 1);

        // Line 2: Address and Date
        $pdf->Cell(18, 6, 'Address:', 0, 0);
        $pdf->Cell(60, 6, $px_address ?: ' ', 'B', 0);
        $pdf->Cell(10, 6, '', 0, 0); // Spacer (adjust width as needed)
        $pdf->Cell(12, 6, 'Date:', 0, 0);
        $pdf->Cell(26, 6, $formattedPrescDate ?: ' ', 'B', 1);

        $pdf->Ln(2);
        $pdf->Image($RxPath, 10, $pdf->GetY(), $image_width, $image_height);
        $pdf->Ln(20);    

        // Prescription text box
$pdf->SetFont('Arial', 'I', 11);
$prescriptionLines = explode("\n", $prescription);
$currentX = 15;
$currentY = 100;
$cellHeight = 5;
$lineSpacing = 0.5;
$indentX = 20;
$lineNumber = 1;

// Adjust for A5 page width
$pageWidth = $pdf->GetPageWidth(); // Should be ~148 for A5
$rightMargin = 44;
$quantityAlignRightX = $pageWidth - $rightMargin; // ~138

// Count medicine blocks
$medicineCount = 0;
foreach ($prescriptionLines as $index => $line) {
    $trimmedLine = trim($line);
    if ($trimmedLine !== '') {
        $isNewBlock = ($index === 0 || trim($prescriptionLines[$index - 1]) === '');
        if ($isNewBlock) {
            $medicineCount++;
        }
    }
}

// Add extra space before the first medicine based on count
if ($medicineCount === 1) {
    $currentY += 10;
} elseif ($medicineCount === 2) {
    $currentY += 7;
} elseif ($medicineCount === 3) {
    $currentY += 5;
}

foreach ($prescriptionLines as $index => $line) {
    $trimmedLine = trim($line);

    if ($trimmedLine !== '') {
        $isNewBlock = ($index === 0 || trim($prescriptionLines[$index - 1]) === '');

        if ($isNewBlock) {
            preg_match('/^(.*?)(\s+#\d+)?$/', $trimmedLine, $matches);
            $medicineText = $matches[1];
            $quantityText = isset($matches[2]) ? trim($matches[2]) : '';

            // Only add numbering if more than one medicine
            $fullMedicineText = ($medicineCount > 1 ? $lineNumber . '. ' : '') . $medicineText;

            $textWidth = $pdf->GetStringWidth($fullMedicineText);
            $qtyWidth = $pdf->GetStringWidth($quantityText);
            $dot = '.';

            // Calculate dots to fill between med name and quantity
            $spaceWidth = $pdf->GetStringWidth('  '); // Two spaces
            $availableWidth = $quantityAlignRightX - $currentX - $textWidth - $spaceWidth - $qtyWidth;
            $dotCount = max(0, floor($availableWidth / $pdf->GetStringWidth($dot)));
            $dotLine = str_repeat($dot, $dotCount);

            // Write medicine name with dots
            $pdf->SetXY($currentX, $currentY);
            $pdf->Cell(0, $cellHeight, $fullMedicineText . ' ' . $dotLine, 0, 0, 'L');

            // Right-align quantity
            if ($quantityText !== '') {
                $pdf->SetXY($quantityAlignRightX - $qtyWidth, $currentY);
                $pdf->Cell($qtyWidth, $cellHeight, $quantityText, 0, 0, 'L');
            }

            $lineNumber++;
            $currentY += $cellHeight + $lineSpacing;

        } else {
            // Write Sig. line
            $pdf->SetXY($indentX, $currentY);
            $pdf->Cell(0, $cellHeight, $trimmedLine, 0, 2, 'L');
            $currentY += $cellHeight + $lineSpacing;
        }

    } else {
        $currentY += $cellHeight;
    }
}

        // === FOOTER ===
        // Move to a position approx. 48 units from the bottom
        $pdf->SetY(-48);

        // Page width and right alignment
        $page_width = $pdf->GetPageWidth();
        $cell_width = 60;
        $right_margin = -5;
        $x_position = $page_width - $cell_width - $right_margin -7;

        // Physician Signature (Image above name) //
        $signaturePath = '../../../favicon/zxc.png';
        $signatureWidth = 45;
        $signatureHeight = 18;
        $signatureX = $x_position;
        $signatureY = $pdf->GetY() + 10; // Move 10 units *down*

        // Output the signature image
        $pdf->Image($signaturePath, $signatureX, $signatureY, $signatureWidth, $signatureHeight);
        $pdf->Ln($signatureHeight + 1); // Move below the signature

        // Physician Name
        $pdf->SetX($x_position);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell($cell_width, 6, $info_name, 0, 1, 'L');

        // Line 1: License No., PTR No.
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetX($x_position);
        $pdf->Cell(24, 6, 'License No.:', 0, 0);
        $pdf->Cell(27, 6, $info_licno ?: ' ', 'B', 1);
        $pdf->SetX($x_position);
        $pdf->Cell(24, 6, 'PTR No.     :', 0, 0);
        $pdf->Cell(27, 6, $ptr ?: ' ', 'B', 1);

        // Line 2: Follow-up Date, S2 No.
        $pdf->SetY(-11);
        $pdf->Cell(30, 6, 'Follow-up Date:', 0, 0);
        $pdf->Cell(27, 6, $formattedPrescFollDate ?: ' ', 'B', 0);

        $pdf->SetX($x_position);
        $pdf->Cell(24, 6, 'S2 No.        :', 0, 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(27, 6, $s2 ?: ' ', 'B', 0);

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