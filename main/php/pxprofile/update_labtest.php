<?php
include('../../connection/conn.php');
require('../FPDF/fpdf.php');
include('../../check_session.php');

// === AJAX Check ===
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if (!IS_AJAX) {
    die('<h1>404 Not Found</h1>');
}

// === POST Values ===
$labtest_id = urldecode($_POST['labtest_id']);
$issued_by = $_SESSION['USER_ID'];

$labtest_selected = isset($_POST['lab_tests']) ? implode(", ", array_filter($_POST['lab_tests'], function($item) {
    return strtolower(trim($item)) !== 'others';
})) : '';

$labtest_others_raw = $_POST['labtest_others'] ?? '';

// Replace newlines with commas, then split
$others_array = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $labtest_others_raw)));

// Rejoin as sanitized comma-separated string
$labtest_others = implode(', ', $others_array);


$labtest_request_date = urldecode($_POST['edit_labtest_request_date'] ?? '');
$labtest_clinical_impression = urldecode($_POST['edit_labtest_clinical_impression'] ?? '');
$labtest_remarks = urldecode($_POST['edit_labtest_remarks'] ?? '');
$labtest_folldate = !empty($_POST['edit_labtest_folldate']) ? $_POST['edit_labtest_folldate'] : null;


if (empty($labtest_request_date)) $labtest_request_date = null;

// === Get Old PDF ===
$old_pdf_stmt = $conn->prepare("SELECT pdf_filename FROM tbl_lab_requests WHERE labtest_id = ?");
$old_pdf_stmt->bind_param("i", $labtest_id);
$old_pdf_stmt->execute();
$old_pdf_stmt->bind_result($old_pdf_filename);
$old_pdf_stmt->fetch();
$old_pdf_stmt->close();

// === Fetch existing data to compare with new values ===
$existing_stmt = $conn->prepare("SELECT issued_by, labtest_selected, labtest_others, labtest_request_date, labtest_clinical_impression, labtest_remarks, labtest_folldate FROM tbl_lab_requests WHERE labtest_id = ?");
$existing_stmt->bind_param("i", $labtest_id);
$existing_stmt->execute();
$existing_stmt->bind_result($existing_issued_by, $existing_selected, $existing_others, $existing_date, $existing_impression, $existing_remarks, $existing_folldate);
$existing_stmt->fetch();
$existing_stmt->close();

// === Normalize data for comparison ===
$normalize = function($val) {
    return trim((string)$val);
};

if (
    $normalize($existing_issued_by) === $normalize($issued_by) &&
    $normalize($existing_selected) === $normalize($labtest_selected) &&
    $normalize($existing_others) === $normalize($labtest_others) &&
    $normalize($existing_date) === $normalize($labtest_request_date) &&
    $normalize($existing_impression) === $normalize($labtest_clinical_impression) &&
    $normalize($existing_remarks) === $normalize($labtest_remarks) &&
    $normalize($existing_folldate) === $normalize($labtest_folldate)
) {
    echo "No changes have been made.";
    exit;
}


// === Delete Old PDF ===
if (!empty($old_pdf_filename)) {
    // Extract just the filename from old_pdf_filename (remove folder paths)
    $old_pdf_basename = basename($old_pdf_filename);

    $old_pdf_path = realpath(__DIR__ . '/../LabRequestPDFs') . DIRECTORY_SEPARATOR . $old_pdf_basename;

    if (file_exists($old_pdf_path)) {
        unlink($old_pdf_path);
    } else {
        error_log("Old PDF file not found for deletion: " . $old_pdf_path);
    }
}


// === Update Lab Request ===
$update = $conn->prepare("UPDATE tbl_lab_requests SET issued_by = ?, labtest_selected = ?, labtest_others = ?, labtest_request_date = ?, labtest_clinical_impression = ?, labtest_remarks = ?, labtest_folldate = ? WHERE labtest_id = ?");
$update->bind_param("issssssi", $issued_by, $labtest_selected, $labtest_others, $labtest_request_date, $labtest_clinical_impression, $labtest_remarks, $labtest_folldate, $labtest_id);
$update->execute();
$update->close();

// === Get Patient ID and DateTime ===
$get_pxid = $conn->prepare("SELECT px_id, labtest_cre_datetime FROM tbl_lab_requests WHERE labtest_id = ?");
$get_pxid->bind_param("i", $labtest_id);
$get_pxid->execute();
$get_pxid->bind_result($px_id, $labtest_cre_datetime);
$get_pxid->fetch();
$get_pxid->close();

// === Get Patient Details ===
$patient_stmt = $conn->prepare("SELECT CONCAT(px_firstname, ' ', px_lastname, IF(px_suffix != '', CONCAT(' ', px_suffix), '')) AS fullname, px_dob, px_address FROM tbl_px_details WHERE px_id = ?");
$patient_stmt->bind_param("i", $px_id);
$patient_stmt->execute();
$patient_stmt->bind_result($fullname, $px_dob, $px_address);
$patient_stmt->fetch();
$patient_stmt->close();

// === Get Clinic Info ===
$s = $conn->prepare("SELECT information_name, information_licno, information_contact FROM tbl_information WHERE information_id = ?");
$s->bind_param("i", $issued_by);
$s->execute();
$s->bind_result($info_name, $info_licno, $info_contact);
$s->fetch();
$s->close();

$age = calculateAge($px_dob);
if (empty($px_dob)) {
    $age = '';
}

$formattedLabRequestDate = date("M j, Y", strtotime($labtest_request_date));

// === Prepare Lab Test Items ===
$test_items = array_filter(array_map('trim', preg_split('/\r\n|\r|\n|,/', $labtest_selected)));
// If $labtest_others is not empty, split by comma and add to $test_items
if (!empty(trim($labtest_others))) {
    $others_lines = array_filter(array_map('trim', explode(',', $labtest_others)));
    $test_items = array_merge($test_items, $others_lines);
}


// === PDF Generation ===
$pdf = new FPDF('P', 'mm', 'A5');
$pdf->AddPage();

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

$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 5);
$pdf->Ln(18);

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

// === Clinic Info ===
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 5, 'Suite 1601, Medical Plaza Makati, Amorsolo Corner', 0, 1, 'C');
$pdf->Cell(0, 5, 'Dela Rosa Sts., Legaspi Village, Makati City', 0, 1, 'C');
$pdf->Cell(0, 5, 'Contact #: ' . $info_contact, 0, 1, 'C');
$pdf->Ln(2);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 138, $pdf->GetY());

// === Title ===
$pdf->Ln(5);
$pdf->SetLineWidth(0.1); // Default is 0.2, this makes the line thinner
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 6, 'DIAGNOSTIC PROCEDURE REQUEST', 0, 1, 'C');
$pdf->Ln(10);

// === Patient Info ===
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(18, 6, 'Name:', 0, 0);
// $pdf->Cell(60, 6, $fullname, 'B', 0);
$pdf->Cell(60, 6, utf8_decode($fullname ?: ' '), 'B', 0);
$pdf->Cell(10, 6, '', 0, 0);
$pdf->Cell(12, 6, 'Age:', 0, 0);
$pdf->Cell(26, 6, $age, 'B', 1);

$pdf->Cell(18, 6, 'Address:', 0, 0);
$pdf->Cell(60, 6, $px_address, 'B', 0);
$pdf->Cell(10, 6, '', 0, 0);
$pdf->Cell(12, 6, 'Date:', 0, 0);
$pdf->Cell(26, 6, $formattedLabRequestDate, 'B', 1);

$pdf->Ln(10);

// === Test List ===
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, "Requested Tests:", 0, 1);
$pdf->Ln(5);

function bullet($text) {
    return chr(149) . '  ' . $text;
}

$col_width = 50;
$gap = -8;
$startX_col1 = 15;
$startX_col2 = $startX_col1 + $col_width + $gap;
$startX_col3 = $startX_col2 + $col_width + $gap;
$startY = $pdf->GetY();
$num_tests = count($test_items);

if ($num_tests <= 5) {
    foreach ($test_items as $i => $test) {
        $pdf->SetXY($startX_col1, $startY + $i * 6);
        $pdf->Cell(0, 6, bullet($test), 0, 1, 'L');
    }
} elseif ($num_tests <= 10) {
    $col1 = array_slice($test_items, 0, ceil($num_tests / 2));
    $col2 = array_slice($test_items, ceil($num_tests / 2));
    foreach ($col1 as $i => $test) {
        $pdf->SetXY($startX_col1, $startY + $i * 6);
        $pdf->Cell($col_width, 6, bullet($test), 0, 0, 'L');
    }
    foreach ($col2 as $j => $test) {
        $pdf->SetXY($startX_col2, $startY + $j * 6);
        $pdf->Cell($col_width, 6, bullet($test), 0, 0, 'L');
    }
    $rows = max(count($col1), count($col2));
    $pdf->SetY($startY + $rows * 6 + 4);
} else {
    $col1 = array_slice($test_items, 0, 5);
    $col2 = array_slice($test_items, 5, 5);
    $col3 = array_slice($test_items, 10);
    foreach ($col1 as $i => $test) {
        $pdf->SetXY($startX_col1, $startY + $i * 6);
        $pdf->Cell($col_width, 6, bullet($test), 0, 0, 'L');
    }
    foreach ($col2 as $j => $test) {
        $pdf->SetXY($startX_col2, $startY + $j * 6);
        $pdf->Cell($col_width, 6, bullet($test), 0, 0, 'L');
    }
    foreach ($col3 as $k => $test) {
        $pdf->SetXY($startX_col3, $startY + $k * 6);
        $pdf->Cell($col_width, 6, bullet($test), 0, 0, 'L');
    }
    $rows = max(count($col1), count($col2), count($col3));
    $pdf->SetY($startY + $rows * 6 + 4);
}

// === Dynamic Y position adjustment ===
$line_height = 5;
$default_y_position = 146; // base position
$left_margin = 15;
$underline_width = 50;

// Process Clinical Impression
$raw_impression_lines = array_map('trim', explode("\n", $labtest_clinical_impression));
$visible_impression_lines = array_filter($raw_impression_lines);
$shown_impression_lines = array_slice($visible_impression_lines ?: [''], 0, 3);
$hidden_impression_count = 3 - count($shown_impression_lines);

// Process Remarks
$raw_remarks_lines = array_map('trim', explode("\n", $labtest_remarks));
$visible_remarks_lines = array_filter($raw_remarks_lines);
$shown_remarks_lines = array_slice($visible_remarks_lines ?: [''], 0, 3);
$hidden_remarks_count = 3 - count($shown_remarks_lines);

// Total hidden lines
$total_hidden_lines = $hidden_impression_count + $hidden_remarks_count;

// Adjust Y position
$adjusted_y_position = $default_y_position + ($total_hidden_lines * $line_height);
$pdf->SetY($adjusted_y_position);

// === Clinical Impression Section ===
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, "Clinical Impression / Diagnosis:", 0, 1);
$pdf->SetFont('Arial', '', 10);

foreach ($shown_impression_lines as $line) {
    $pdf->SetX($left_margin);
    $pdf->Cell($underline_width, $line_height, $line, 'B', 1);
}
$pdf->Ln(4);

// === Remarks Section ===
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, "Remarks / Special Instructions:", 0, 1);
$pdf->SetFont('Arial', '', 10);

foreach ($shown_remarks_lines as $line) {
    $pdf->SetX($left_margin);
    $pdf->Cell($underline_width, $line_height, $line, 'B', 1);
}

// === Follow-up Date ===
if ($labtest_folldate == "") {
    $formattedlabtest_folldate = "";
} else {
    $labtest_folldate = new DateTime($labtest_folldate);
    $formattedlabtest_folldate = $labtest_folldate->format('M j, Y');
}

$pdf->Ln(4);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(30, 6, 'Follow-up Date:', 0, 0);
$pdf->Cell(25, 6, $formattedlabtest_folldate ?: ' ', 'B', 0);

// === Signature Section ===
$pdf->SetY(-43);
$page_width = $pdf->GetPageWidth();
$cell_width = 60;
$right_margin = -5;
$x_position = $page_width - $cell_width - $right_margin - 7;

// Signature Image
$signaturePath = '../../../favicon/zxc.png';
$signatureWidth = 45;
$signatureHeight = 18;
$signatureX = $x_position;
$signatureY = $pdf->GetY() + 10;

$pdf->Image($signaturePath, $signatureX, $signatureY, $signatureWidth, $signatureHeight);
$pdf->Ln($signatureHeight + 1);

// Physician Name
$pdf->SetX($x_position);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell($cell_width, 6, $info_name, 0, 1, 'L');

// License No.
$pdf->SetFont('Arial', '', 11);
$pdf->SetX($x_position);
$pdf->Cell(24, 6, 'License No.:', 'B', 0); 
$pdf->Cell(24, 6, $info_licno ?: ' ', 'B', 1);

// Footer Label
$pdf->SetX($x_position);
$pdf->Cell(0, 6, 'Requesting Physician', 0, 1);

// === Save PDF ===
$relative_folder = '../LabRequestPDFs';
$pdf_folder = realpath(__DIR__ . '/../LabRequestPDFs');
$filename_date = (new DateTime())->format('F_j_Y_His');
$raw_filename = $fullname . '_' . $filename_date . '.pdf';
$pdf_filename = $relative_folder . '/' . $raw_filename;
$pdf_path = $pdf_folder . DIRECTORY_SEPARATOR . $raw_filename;
$pdf->Output('F', $pdf_path);

// === Update PDF filename in DB ===
$update_pdf = $conn->prepare("UPDATE tbl_lab_requests SET pdf_filename = ? WHERE labtest_id = ?");
$update_pdf->bind_param("ss", $pdf_filename, $labtest_id);
$update_pdf->execute();
$update_pdf->close();

$conn->close();

echo "Lab test has been successfully updated.";
