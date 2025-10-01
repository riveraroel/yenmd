<?php
include('../../connection/conn.php');
require('../FPDF/fpdf.php');
include('../../check_session.php');

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if (!IS_AJAX) {
    die('<h1>404 Not Found</h1>');
}

$presc_id = $_POST['presc_id'] ?? null;
if (!$presc_id) {
    echo "Invalid prescription ID";
    exit;
}

// Fetch existing prescription data
$stmt = $conn->prepare("
    SELECT px.px_emailadd, 
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
           presc.presc_filepath,
           presc.is_regulated,
           presc.no_refill
    FROM tbl_prescrip AS presc
    INNER JOIN tbl_px_details AS px ON presc.prescription_for = px.px_id
    INNER JOIN tbl_information AS inf ON presc.issued_by = inf.information_id
    WHERE presc.presc_id = ?
");
$stmt->bind_param("i", $presc_id);
$stmt->execute();
$stmt->bind_result($email, $fullname, $info_name, $info_licno, $info_address, $info_contact, $px_address, $px_dob, $prescription, $ptr, $s2, $presc_date, $presc_folldate, $pdfatt, $isRegulated, $no_refill);
$stmt->fetch();
$include_ptr = !empty($ptr);
$stmt->close();

// Delete old PDF
if (file_exists($pdfatt)) unlink($pdfatt);

// Format dates
$formattedPrescDate = (new DateTime($presc_date))->format('M j, Y');
$formattedPrescFollDate = $presc_folldate ? (new DateTime($presc_folldate))->format('M j, Y') : "";
$noRefill = ($no_refill == 1);

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

// Generate PDF (same structure as in `update_prescription.php`)
// Reuse your entire PDF generation logic here — you can directly copy everything from:
    // $RxPath = 'rx_logo.png';
    // ...
    // up to: $pdf->Output('F', $output_path);

        // Prescription PDF Generation
        $RxPath = 'rx_logo.png';
        $pdf = new FPDF('P', 'mm', 'A5');
        $pdf->AddPage();

if (!function_exists('wrapTextPdf')) {
    function wrapTextPdf($text, $maxWidth, $pdfObj) {
        $text = trim($text);
        $output = '';
        $words = explode(' ', $text);
        $line = '';

        foreach ($words as $word) {
            $testLine = ($line === '') ? $word : $line . ' ' . $word;
            if ($pdfObj->GetStringWidth($testLine) > $maxWidth) {
                if ($line === '') {
                    $output .= $word . "\n";
                    $line = '';
                } else {
                    $output .= $line . "\n";
                    $line = $word;
                }
            } else {
                $line = $testLine;
            }
        }

        $output .= $line;
        return $output;
    }
}


if (!function_exists('numberToWords')) {
    function numberToWords($number) {
        $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        return $f->format($number);
    }
}


        // Optional: Set font or other properties here
        $pdf->SetFont('Arial', '', 12);

        if (!$isRegulated) {
            // ✅ Draw border (frame) around the page with even spacing
            $page_width = 148;   // A5 width in mm
            $page_height = 210;  // A5 height in mm
            $margin = 0.1;       // Desired margin
            $lineWidth = 0.1;    // Border thickness

            $pdf->SetLineWidth($lineWidth);

            // Fixed correction factor (to remove right-edge gap)
            $correction = 0.5; // mm (adjust if still tiny gap, e.g. 0.7)

            $pdf->Rect(
                $margin,
                $margin,
                $page_width - 2 * $margin + $correction, // extend to fix right edge
                $page_height - 2 * $margin
            );
        }

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
        $pdf->SetLineWidth(0.5);
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
        // $pdf->Cell(60, 6, $fullname ?: ' ', 'B', 0);
        $pdf->Cell(60, 6, utf8_decode($fullname ?: ' '), 'B', 0);
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
        // $pdf->Cell(60, 6, $fullname ?: ' ', 'B', 0);
        $pdf->Cell(60, 6, utf8_decode($fullname ?: ' '), 'B', 0);
        $pdf->Cell(10, 6, '', 0, 0);
        $pdf->Cell(12, 6, 'Age:', 0, 0);
        $pdf->Cell(26, 6, $age ?: ' ', 'B', 1);

        // Line 2: Address and Date
        $pdf->Cell(18, 6, 'Address:', 0, 0);

        // Save starting X and Y
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // Address (MultiCell, may take 1+ lines)
        $pdf->MultiCell(60, 6, $px_address ?: ' ', 'B', 'L');

        // How tall the address block was
        $addressHeight = $pdf->GetY() - $y;

        // Reset XY for Date (keep it fixed beside first line of Address)
        $pdf->SetXY($x + 60 + 10, $y);
        $pdf->Cell(12, 6, 'Date:', 0, 0);
        $pdf->Cell(26, 6, $formattedPrescDate ?: ' ', 'B', 0);

        // Compute the maximum row height: address vs date
        $rowHeight = max($addressHeight, 6);

        // Move Y cursor below the tallest content (so Rx logo is always below)
        $pdf->SetY($y + $rowHeight);
    }

        $pdf->Ln(2);
        $pdf->Image($RxPath, 10, $pdf->GetY(), $image_width, $image_height);
        $pdf->Ln(10);    

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
foreach ($prescriptionLines as $line) {
    if (preg_match('/#\s*\d+/', $line)) {
        $medicineCount++;
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

$prescriptionLines = explode("\n", $prescription);
$currentX = 15;
$currentY = 100;
$cellHeight = 5;
$lineSpacing = 0.5;
$lineNumber = 1;

$pageWidth = $pdf->GetPageWidth();
$textBlockWidth = $pageWidth - $currentX - 15;

foreach ($prescriptionLines as $index => $line) {
    $trimmedLine = trim($line);

    if ($trimmedLine !== '') {
        $isNewBlock = ($index === 0 || trim($prescriptionLines[$index - 1]) === '');

        if ($isNewBlock) {
            // Extract medicine and quantity
            preg_match('/^(.*?)(\s+#\s*\d+(?:\s*-\s*[\w\s]+)?)?$/', $trimmedLine, $matches);
            $medicineText = trim($matches[1]);
            $quantityText = isset($matches[2]) ? trim($matches[2]) : '';

            // Normalize: turn "# 40" → "#40"
            $quantityText = preg_replace('/#\s+/', '#', $quantityText);

            // Add worded quantity if missing
            if ($isRegulated && preg_match('/#(\d+)(?:\s*-\s*[\w\s]+)?$/', $quantityText, $qmatches)) {
                $qtyNum = intval($qmatches[1]);
                $hasWorded = isset($qmatches[2]) && trim($qmatches[2]) !== '';
                if (!$hasWorded) {
                    $quantityText .= ' - ' . ucfirst(numberToWords($qtyNum));
                }
            }

            // Compose base line (e.g., "1. PARACETAMOL 500mg")
            $leftPart = ($medicineCount > 1 ? $lineNumber . '. ' : '') . $medicineText;
            $leftWidth = $pdf->GetStringWidth($leftPart);
            $qtyWidth = $pdf->GetStringWidth($quantityText);
            $spaceWidth = $pdf->GetStringWidth('  ');
            $dotWidth = $pdf->GetStringWidth('.');

            $availableWidth = $textBlockWidth - $leftWidth - $qtyWidth - $spaceWidth;
            $dotCount = max(0, floor($availableWidth / $dotWidth));
            $dots = str_repeat('.', $dotCount);

            $finalLine = $leftPart . ' ' . $dots . ' ' . $quantityText;

            // Wrap entire line if it exceeds page width
            $wrappedLines = explode("\n", wrapTextPdf($finalLine, $textBlockWidth, $pdf));
            foreach ($wrappedLines as $wline) {
                $pdf->SetXY($currentX, $currentY);
                $pdf->Cell($textBlockWidth, $cellHeight, $wline, 0, 1, 'L');
                $currentY += $cellHeight + $lineSpacing;
            }

            if ($medicineCount > 1) $lineNumber++;

        } else {
            // Sig. or instructions
            $pdf->SetXY($currentX + 5, $currentY);
            $pdf->Cell(0, $cellHeight, $trimmedLine, 0, 1, 'L');
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

// Row: NO REFILL on left, License No. on right (same line)
$pdf->SetFont('Arial', '', 11);
$pdf->SetY($pdf->GetY()); // stay on current line
if ($noRefill) {
    $pdf->SetX(10);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetTextColor(255, 0, 0); // Red color
    $pdf->Cell(40, 6, '*** NO REFILL ***', 0, 0, 'L');
    $pdf->SetTextColor(0, 0, 0);   // Restore to black for the rest

}

$pdf->SetFont('Arial', '', 11);
$pdf->SetX($x_position); // right side
$pdf->Cell(24, 6, 'License No.:', 0, 0);
$pdf->Cell(27, 6, $info_licno ?: ' ', 'B', 1); // now go to next line


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

// Save to new file
$date = new DateTime('now');
$filename_date = $date->format('F_j_Y_His');
$output_path = '../PrescriptionPDFs/' . $fullname . '_' . $filename_date . '.pdf';

// Update DB path
$update = $conn->prepare("UPDATE tbl_prescrip SET presc_filepath = ? WHERE presc_id = ?");
$update->bind_param("si", $output_path, $presc_id);
$update->execute();
$pdf->Output('F', $output_path);

if ($update->affected_rows === 1) {
    echo "Prescription PDF regenerated successfully";
} else {
    echo "PDF file path update failed";
}

$update->close();
$conn->close();
?>
