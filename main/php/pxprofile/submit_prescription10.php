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

    $isRegulated = isset($_POST['is_regulated']) && $_POST['is_regulated'] == '1' ? 1 : 0;
    $include_ptr = !empty($_POST['include_ptr']);

    $flupdate = urldecode($_POST['prescFollDate']) ?? null;
    if (empty($flupdate)) {
        $flupdate = null;
    }
    
    // Always default to empty string, not space, to avoid DB null issues
 
    $ptr = ($isRegulated || $include_ptr) ? ($_POST['prescPTR'] ?? '') : '';
    $s2 = $isRegulated ? ($_POST['prescS2'] ?? '') : '';

    $presc_date = urldecode($_POST['prescDate']);
    $blank = "none";
    $is_active = '1';

    // $is_regulated = isset($_POST['is_regulated']) && $_POST['is_regulated'] === 'true';

    
    if ($isRegulated) {
        // Fetch PTR and S2 from tbl_information using information_id
        $stmt = $conn->prepare("SELECT information_PTR, information_S2 FROM tbl_information WHERE information_id = ?");
        $stmt->bind_param("i", $issued_by);
        $stmt->execute();
        $doc_info = $stmt->get_result()->fetch_assoc();

        // Override ptr and s2 with DB values if found, else default empty string
        $ptr = $doc_info['information_PTR'] ?? '';
        $s2 = $doc_info['information_S2'] ?? '';

        function numberToWords($number) {
            $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
            return ucfirst($f->format($number));
        }

        // Match all occurrences like #20, #5, etc.
        // if (preg_match_all("/#(\d+)(?!\s*-\s*\w+)/", $presc, $matches)) {
        //     foreach ($matches[1] as $numQty) {
        //         $wordQty = numberToWords((int)$numQty);
        //         // Use preg_replace to append the words only if not already present
        //         $presc = preg_replace("/#{$numQty}(?!\s*-\s*\w+)/", "#{$numQty} - {$wordQty}", $presc);
        //     }
        // }

    }

    if ($presc == "") {
        echo "Prescription must not be empty";
        return false;
    }
    
    // Insert prescription record with guaranteed non-null ptr and s2
    $s = $conn->prepare("INSERT INTO tbl_prescrip (prescription_for, prescription, issued_by, prescription_ptr, prescription_s2, prescription_date, prescription_folldate, prescription_is_active, presc_filepath, is_regulated) VALUES (?,?,?,?,?,?,?,?,?,?)");
    $s->bind_param("isissssssi", $px_id, $presc, $issued_by, $ptr, $s2, $presc_date, $flupdate, $is_active, $blank, $isRegulated);
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
                                    presc.prescription_folldate,
                                    presc.is_regulated 
                            FROM tbl_prescrip AS presc 
                            INNER JOIN tbl_px_details AS px ON presc.prescription_for = px.px_id 
                            INNER JOIN tbl_information AS inf ON presc.issued_by = inf.information_id 
                            WHERE presc.presc_id = ?");
        $s->bind_param("i", $id);
        $s->execute();
        $s->bind_result($email, $fullname, $info_name, $info_licno, $info_address, $info_contact, $px_address, $px_dob, $prescription, $ptr, $s2, $presc_date, $presc_folldate, $isRegulated);
        $s->fetch();
        $s->close();
        
        // Format the prescription date
        $presc_date = new DateTime($presc_date);
        $formattedPrescDate = $presc_date->format('M j, Y');

        // Format the Date of Birth
        if (!empty($px_dob)) {
            try {
                $px_dob_obj = new DateTime($px_dob);
                $formattedDOB = $px_dob_obj->format('M j, Y');
            } catch (Exception $e) {
                $formattedDOB = '';
            }
        } else {
            $formattedDOB = '';
        }

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
        $pdf->Ln(15);        

        // Set image path
        $image_path = '../../../favicon/bannerzxc.png';
        $page_width = 148; // A5 width in mm

        // Original size in px
        $image_width_px = 350;
        $image_height_px = 100;

        // Convert to mm
        $image_width_mm = ($image_width_px * 25.4) / 96;
        $image_height_mm = ($image_height_px * 25.4) / 96;

        // Scale down proportionally (e.g., 70%)
        $scale = 0.7;
        $image_width_mm *= $scale;
        $image_height_mm *= $scale;

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



        $pdf->SetFont('Arial', 'B', 13);
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
        
    if (!empty($isRegulated)) {
        // Line 1: Name and Date
        $pdf->Cell(18, 6, 'Name:', 0, 0);
        $pdf->Cell(60, 6, $fullname ?: ' ', 'B', 0);
        $pdf->Cell(10, 6, '', 0, 0); // Spacer
        $pdf->Cell(12, 6, 'Date:', 0, 0);
        $pdf->Cell(26, 6, $formattedPrescDate ?: ' ', 'B', 1);

        // Line 2: DoB and Age
        $pdf->Cell(18, 6, 'DoB:', 0, 0);
        $pdf->Cell(60, 6, $formattedDOB ?: ' ', 'B', 0);    
        $pdf->Cell(10, 6, '', 0, 0); // Spacer
        $pdf->Cell(12, 6, 'Age:', 0, 0);
        $pdf->Cell(26, 6, $age ?: ' ', 'B', 1);

        // Line 3: Address
        $pdf->Cell(18, 6, 'Address:', 0, 0);
        $pdf->Cell(108, 6, $px_address ?: ' ', 'B', 1);
    } else {
        // Original layout for non-regulated prescriptions
        // Line 1: Name and Age
        $pdf->Cell(18, 6, 'Name:', 0, 0);
        $pdf->Cell(60, 6, $fullname ?: ' ', 'B', 0);
        $pdf->Cell(10, 6, '', 0, 0);
        $pdf->Cell(12, 6, 'Age:', 0, 0);
        $pdf->Cell(26, 6, $age ?: ' ', 'B', 1);

        // Line 2: Address and Date
        $pdf->Cell(18, 6, 'Address:', 0, 0);
        $pdf->Cell(60, 6, $px_address ?: ' ', 'B', 0);
        $pdf->Cell(10, 6, '', 0, 0);
        $pdf->Cell(12, 6, 'Date:', 0, 0);
        $pdf->Cell(26, 6, $formattedPrescDate ?: ' ', 'B', 1);
    }

        $pdf->Ln(2);
        $pdf->Image($RxPath, 10, $pdf->GetY(), $image_width, $image_height);
        $pdf->Ln(10);    

        // Prescription text box
$pdf->SetFont('Arial', 'I', 11);
$prescriptionLines = explode("\n", $presc);
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
            // Match quantity with optional word portion (e.g., "#20 - Twenty")
            // preg_match('/^(.*?)(\s+#\d+(?:\s*-\s*[\w\s]+)?)?$/', $trimmedLine, $matches);
preg_match('/^(.*?)(\s+#\s*\d+(?:\s*-\s*[\w\s]+)?)?$/', $trimmedLine, $matches);
$medicineText = trim($matches[1]);
$quantityText = isset($matches[2]) ? trim($matches[2]) : '';

// Normalize: turn "# 40" → "#40"
$quantityText = preg_replace('/#\s+/', '#', $quantityText);

// Now apply logic to append spelled-out quantity if missing
if ($isRegulated && preg_match('/#(\d+)(?:\s*-\s*([\w\s]+))?$/', $quantityText, $qmatches)) {
    $qtyNum = intval($qmatches[1]);
    $hasWorded = isset($qmatches[2]) && trim($qmatches[2]) !== '';

    if (!$hasWorded) {
        $qtyWorded = numberToWords($qtyNum);
        $quantityText .= ' - ' . ucfirst($qtyWorded);
    }
}

            // Add numbering if more than one medicine
            $fullMedicineText = ($medicineCount > 1 ? $lineNumber . '. ' : '') . $medicineText;

            $textWidth = $pdf->GetStringWidth($fullMedicineText);
            $qtyWidth = $pdf->GetStringWidth($quantityText);
            $dot = '.';

            $spaceWidth = $pdf->GetStringWidth('  ');
            $availableWidth = $quantityAlignRightX - $currentX - $textWidth - $spaceWidth - $qtyWidth;
            $dotCount = max(0, floor($availableWidth / $pdf->GetStringWidth($dot)));
            $dotLine = str_repeat($dot, $dotCount);

            // Write medicine + dots
            $pdf->SetXY($currentX, $currentY);
            $pdf->Cell(0, $cellHeight, $fullMedicineText . ' ' . $dotLine, 0, 0, 'L');

            // Align full quantity including "- Twenty" to the right
            if ($quantityText !== '') {
                $pdf->SetXY($quantityAlignRightX - $qtyWidth, $currentY);
                $pdf->Cell($qtyWidth, $cellHeight, $quantityText, 0, 0, 'L');
            }

            $lineNumber++;
            $currentY += $cellHeight + $lineSpacing;

        } else {
            // Sig. or instructions
            $pdf->SetXY($indentX, $currentY);
            $pdf->Cell(0, $cellHeight, $trimmedLine, 0, 2, 'L');
            $currentY += $cellHeight + $lineSpacing;
        }

    } else {
        $currentY += $cellHeight;
    }
}

// === FOOTER ===
// Move to approx. 48 units from bottom
$pdf->SetY(-60);

$page_width = $pdf->GetPageWidth();
$cell_width = 60;
$right_margin = -5;
$x_position = $page_width - $cell_width - $right_margin - 7;

$signaturePath = '../../../favicon/zxc.png';
$signatureWidth = 45;
$signatureHeight = 18;
$signatureX = $x_position;
$signatureY = $pdf->GetY() + 10; // 10 units down from current Y

// Show signature image only if NOT regulated
if (!$isRegulated) {
    $pdf->Image($signaturePath, $signatureX, $signatureY, $signatureWidth, $signatureHeight);
    $pdf->Ln($signatureHeight + 1); // Move below the signature
} else {
    // If regulated, skip signature image, but add vertical space
    $pdf->Ln($signatureHeight + 1);
}

// Physician Name
$pdf->SetX($x_position);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell($cell_width, 6, $info_name, 0, 1, 'L');

// License No. and PTR No.
$pdf->SetFont('Arial', '', 11);
$pdf->SetX($x_position);
$pdf->Cell(24, 6, 'License No.:', 0, 0);
$pdf->Cell(27, 6, $info_licno ?: ' ', 'B', 1);
$pdf->SetX($x_position);
$pdf->Cell(24, 6, 'PTR No.     :', 0, 0);
$pdf->Cell(27, 6, ($isRegulated || $include_ptr) ? $ptr : ' ', 'B', 1);

// Follow-up Date and S2 No.
$pdf->SetY(-23);
$pdf->Cell(30, 6, 'Follow-up Date:', 0, 0);
$pdf->Cell(27, 6, $formattedPrescFollDate ?: ' ', 'B', 0);

$pdf->SetX($x_position);
$pdf->Cell(24, 6, 'S2 No.        :', 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(27, 6, $isRegulated ? ($s2 ?: ' ') : ' ', 'B', 0);

// Save the PDF to file
$date = new DateTime('now');
$filename_date = $date->format('F_j_Y_His');
$output_path = '../PrescriptionPDFs/' . $fullname . '_' . $filename_date . '.pdf';

// Update DB with filepath
$x = $conn->prepare("UPDATE tbl_prescrip SET presc_filepath = ? WHERE presc_id = ?");
$x->bind_param("si", $output_path, $id);
$x->execute();
$x->close();

// Output PDF to file
$pdf->Output('F', $output_path);
echo "Prescription has been successfully added.";

    } else {
        echo $conn->error;
        $s->close();
    }
}

$conn->close();
?>