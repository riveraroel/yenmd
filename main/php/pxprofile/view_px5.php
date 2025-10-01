<?php
include '../../connection/conn.php';
include '../../check_session.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Virtual Clinic</title>

  <!-- Favicon and Fonts -->
  <link rel="icon" type="image/x-icon" href="../favicon/myicon.ico">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  
  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">

  <!-- DataTables 1.13.8 + Responsive 2.5.0 -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css">

  <!-- FullCalendar CSS -->
  <link href="https://unpkg.com/fullcalendar@3.10.0/dist/fullcalendar.min.css" rel="stylesheet">
  <link href="https://unpkg.com/fullcalendar@3.10.0/dist/fullcalendar.print.css" rel="stylesheet" media="print">

  <!-- Datepicker CSS -->
  <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Bootstrap JS Bundle (with Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

  <!-- DataTables 1.13.8 + Responsive -->
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

  <!-- Datepicker JS -->
  <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js"></script>

  <!-- FullCalendar JS -->
  <script src="https://unpkg.com/moment@2.24.0/min/moment.min.js"></script>
  <script src="https://unpkg.com/fullcalendar@3.10.0/dist/fullcalendar.min.js"></script>

  <!-- Tooltip.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
  <script src="https://unpkg.com/tooltip.js@1.3.2/dist/umd/tooltip.min.js"></script>
</head>

<body>
    


<div class="bg-body-tertiary py-3">
  <div class="container">
    <div class="row text-center">
      <div class="col-12">
        <span id="viewPx_Name" class="d-block text-center text-wrap" style="font-size: clamp(1rem, 5vw, 2.5rem); font-weight: bold;"></span>
      </div>
      <div class="col-12 d-flex flex-wrap justify-content-center gap-2 mt-2">
        <button class="btn btn-outline-info" type="button" data-bs-toggle="modal" data-bs-target="#addDiagnosis" title="Add Diagnosis">
          <i class="fas fa-stethoscope text-primary"></i>
        </button>
        <button class="btn btn-outline-info" type="button" data-bs-toggle="modal" data-bs-target="#bookAppointment" title="Book Appointment">
          <i class="fas fa-calendar-plus text-danger"></i>
        </button>
        <button class="btn btn-outline-info" type="button" data-bs-toggle="modal" data-bs-target="#addPrescription" title="Add Prescription">
          <i class="fas fa-prescription text-success"></i>
        </button>
        <button class="btn btn-outline-info" type="button" data-bs-toggle="modal" data-bs-target="#addlabs" title="Add Labs">
          <i class="fas fa-microscope text-warning"></i>
        </button>
      </div>
    </div>
  </div>
</div>
  </div>
</div>
    
<nav class='navbar justify-content-center'>
  <div class="nav nav-tabs" id="nav-tab" role="tablist">
    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">
      <i class="fas fa-user"></i> Patient Details
    </button>
    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
      <i class="fas fa-stethoscope"></i> Medical History
    </button>
    <button class="nav-link" id="nav-appointment-tab" data-bs-toggle="tab" data-bs-target="#nav-appointment" type="button" role="tab" aria-controls="nav-appointment" aria-selected="false">
      <i class="fas fa-calendar-check"></i> Appointment History
    </button>
    <button class="nav-link" id="nav-pres-tab" data-bs-toggle="tab" data-bs-target="#nav-pres" type="button" role="tab" aria-controls="nav-pres" aria-selected="false">
      <i class="fas fa-prescription"></i> Prescription History
    </button>
    <button class="nav-link" id="nav-lab-tab" data-bs-toggle="tab" data-bs-target="#nav-lab" type="button" role="tab" aria-controls="nav-lab" aria-selected="false">
      <i class="fas fa-microscope"></i> Labs History
    </button>
  </div>
</nav>

    <div class="tab-content" id="nav-tabContent">
<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
  <br>
  <div class="container">
    <form id="viewpx-form">
      <div class="row mb-3">
        <div class="col-12 col-md-3">
          <label for="viewpx_fname"><p><span class="asterisk">*</span>First name:</p></label>
          <input type="text" class="form-control" id="viewpx_fname" autocomplete="off">
        </div>
        <div class="col-12 col-md-3">
          <label for="viewpx_mname"><p>Middle name:</p></label>
          <input type="text" class="form-control" id="viewpx_mname" autocomplete="off">
        </div>
        <div class="col-12 col-md-3">
          <label for="viewpx_lname"><p><span class="asterisk">*</span>Last name:</p></label>
          <input type="text" class="form-control" id="viewpx_lname" autocomplete="off">
        </div>
        <div class="col-12 col-md-3">
          <label for="viewpx_suffix"><p>Suffix:</p></label>
          <select class="form-select" id="viewpx_suffix">
            <option value="">No suffix</option>
            <option value="JR.">JR.</option>
            <option value="SR.">SR.</option>
            <option value="II">II</option>
            <option value="III">III</option>
            <option value="IV">IV</option>
          </select>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-12 col-md-4">
          <label for="viewpx_dob"><p>Date of birth:</p></label>
          <input type="text" class="form-control" id="viewpx_dob" autocomplete="off" placeholder="YYYY-MM-DD">
        </div>
        <div class="col-12 col-md-4">
          <label for="viewpx_gender"><p>Gender:</p></label>
          <select class="form-select" id="viewpx_gender">
            <option value="None" selected disabled>Select here...</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
        </div>
        <div class="col-12 col-md-4">
          <label for="viewpx_civstat"><p>Civil Status:</p></label>
          <select class="form-select" id="viewpx_civstat">
            <option value="None" selected disabled>Select here...</option>
            <option value="Single">Single</option>
            <option value="Married">Married</option>
            <option value="Widowed">Widowed</option>
            <option value="Legally Separated">Legally Separated</option>
          </select>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-12 col-md-6">
          <label for="viewpx_cellnum"><p>Cellphone number:</p></label>
          <input type="text" class="form-control" id="viewpx_cellnum" autocomplete="off" placeholder="09XXXXXXXXX" onkeypress="return isNumber(event)">
        </div>
        <div class="col-12 col-md-6">
          <label for="viewpx_emailadd"><p>Email address:</p></label>
          <input type="text" class="form-control" id="viewpx_emailadd" autocomplete="off" placeholder="Enter a valid email address">
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-12">
          <label for="viewpx_addr">Address:</label>
          <input type="text" class="form-control" id="viewpx_addr" autocomplete="off" placeholder="Patient's address">
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-12 col-md-6">
          <label for="viewpx_hmo"><p>HMO Provider:</p></label>
          <select class="form-select" id="viewpx_hmo">
            <option value="None">None</option>
            <option value="Avega Managed Care, Inc.">Avega Managed Care, Inc.</option>
            <option value="BenLife">BenLife</option>
            <option value="Cocolife">Cocolife</option>
            <option value="Eastwest Healthcare, Inc.">Eastwest Healthcare, Inc.</option>
            <option value="Generali">Generali</option>
            <option value="iCare">iCare</option>
            <option value="InLife">InLife</option>
            <option value="Intellicare">Intellicare</option>
            <option value="Kaiser International Healthgroup, Inc.">Kaiser International Healthgroup, Inc.</option>
            <option value="Lacson & Lacson">Lacson & Lacson</option>
            <option value="Medocare Health Systems, Inc.">Medocare Health Systems, Inc.</option>
            <option value="PhilBritish">PhilBritish</option>
            <option value="PhilCare">PhilCare</option>
          </select>
        </div>
        <div class="col-12 col-md-6">
          <label for="viewpx_company"><p>Company name:</p></label>
          <input type="text" class="form-control" id="viewpx_company" autocomplete="off" placeholder="Company name">
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-12 d-flex gap-3 flex-wrap">
          <button type="button" id="update_px" class="btn btn-outline-success btn-lg">Update details</button>
          <button type="button" id="delete_px" class="btn btn-outline-danger btn-lg">Delete patient</button>
        </div>
      </div>
    </form>
  </div>
</div>

<br>
        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
            <div class = 'row mb-3'>
                <div class = 'col-12'>
                    <div class="table-responsive">
                    <table id="tb_medhistory" class="table table-striped nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Diagnosis</th>
                                <th>Lab Results</th>
                                <th>Medication/Treatment</th>
                                <th>Issued by</th>
                                <th>Date</th>
                                <th>Option</th>
                            </tr>
                        </thead>
                        <tbody id = 'tb_medhistory2'>
                            <?php
                                $is_active = '1';
                                $id = $_GET['id'];
                                $s = $conn->prepare("SELECT info.information_name, mh.medhis_id, mh.medhis_labresult, mh.medhis_medication, mh.medhis_diagnosis, mh.medhis_diagnosis_date FROM tbl_medical_history AS mh INNER JOIN tbl_information AS info ON mh.issued_by = info.information_id WHERE mh.px_id = ? AND mh.medhis_is_active = ? ORDER BY mh.medhis_cre_datetime DESC");
                                $s->bind_param("ii", $id, $is_active);
                                $s->execute();
                                $res = $s->get_result();
                                while ($row = $res->fetch_assoc())
                                {
                                    $row['medhis_diagnosis'] = encrypt_decrypt('decrypt', $row['medhis_diagnosis']);
                                    $row['medhis_labresult'] = encrypt_decrypt('decrypt', $row['medhis_labresult']);
                                    $row['medhis_medication'] = encrypt_decrypt('decrypt', $row['medhis_medication']);
                                    echo "<tr id = '".$row['medhis_id']."'>";
                                        echo "<td>".$row['medhis_diagnosis']."</td>";
                                        echo "<td>".$row['medhis_labresult']."</td>";
                                        echo "<td>".$row['medhis_medication']."</td>";
                                        echo "<td>".$row['information_name']."</td>";
                                        echo "<td>".$row['medhis_diagnosis_date']."</td>";
                                        echo "<td></td>";
                                    echo "</tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                            </div>
                </div>
            </div>
        </div>
<!-- Lab Test History Tab -->
<div class="tab-pane fade" id="nav-lab" role="tabpanel" aria-labelledby="nav-lab-tab" tabindex="0">
    <div class="table-responsive">
        <table id="tb_lab" class="table table-striped table-hover table-sm w-100">
            <thead>
                <tr>
                    <th>Requested Tests</th>
                    <th>Issued By</th>
                    <th>Date</th>
                    <th>Option</th>                    
                </tr>
            </thead>
            <tbody id="tb_lab2">
                <?php
$is_active = 1;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("
    SELECT 
        info.information_name, 
        lab.labtest_id, 
        lab.labtest_selected, 
        lab.labtest_others, 
        lab.labtest_cre_datetime 
    FROM tbl_lab_requests AS lab 
    INNER JOIN tbl_information AS info ON lab.issued_by = info.information_id 
    WHERE lab.px_id = ? AND lab.labtest_is_active = ? 
    ORDER BY lab.labtest_cre_datetime DESC
");


$stmt->bind_param("ii", $id, $is_active);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $labtest_id = htmlspecialchars($row['labtest_id']);
    $labtest_selected = htmlspecialchars($row['labtest_selected']);
    $labtest_others = trim($row['labtest_others']);
    $created_datetime = date('Y-m-d', strtotime($row['labtest_cre_datetime']));
    $issued_by = htmlspecialchars($row['information_name']); 

    if (!empty($labtest_others)) {
        if (!empty($labtest_selected)) {
            $labtest_selected .= ', ' . htmlspecialchars($labtest_others);
        } else {
            $labtest_selected = htmlspecialchars($labtest_others);
        }
    }    

    echo "<tr id= '".$row['labtest_id']."'>";
    echo "<td>$labtest_selected</td>";
    echo "<td>$issued_by</td>";
    echo "<td>$created_datetime</td>";
    echo "<td></td>";         
    echo "</tr>";
}

$stmt->close();
?>
            </tbody>
        </table>
    </div>
</div>         
        <div class="tab-pane fade" id="nav-pres" role="tabpanel" aria-labelledby="nav-pres-tab" tabindex="0">
            <div class = 'row mb-3'>
                <div class = 'col-12'>
                    <div class="table-responsive">
                        <table id="tb_pres_history" class="table table-striped display nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Prescription</th>
                                    <th>Issued by</th>
                                    <th>Date</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody id = 'tb_pres_history2'>
                                <?php
                                    $is_active = '1';
                                    $id = $_GET['id'];
                                    $s = $conn->prepare("SELECT info.information_name AS info_name, pr.presc_id AS id, pr.prescription, pr.prescription_date AS presc_date FROM tbl_prescrip AS pr INNER JOIN tbl_information AS info ON pr.issued_by = info.information_id WHERE pr.prescription_for = ? AND pr.prescription_is_active = ? ORDER BY pr.prescription_cre_datetime DESC");
                                    $s->bind_param("ii", $id, $is_active);
                                    $s->execute();
                                    $res = $s->get_result();
                                    while ($row = $res->fetch_assoc())
                                    {
                                        echo "<tr id = '".$row['id']."'>";
                                            echo "<td>".$row['prescription']."</td>";
                                            echo "<td>".$row['info_name']."</td>";
                                            echo "<td>".$row['presc_date']."</td>";
                                            echo "<td></td>";
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                                </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade p-3 m-3" id="nav-appointment" role="tabpanel" aria-labelledby="nav-appointment-tab" tabindex="0">
            <div class = 'row mb-3'>
                <div class = 'col-12'>
                    <div class="table-responsive">
                    <table id="tb_apt_history" class="table table-striped nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Reason</th>
                                <th>Issued by</th>
                                <th>Date</th>
                                <th>Option</th>
                            </tr>
                        </thead>
                        <tbody id = 'tb_apt_history2'>
                            <?php
                                $is_active = '1';
                                $id = $_GET['id'];
                                $s = $conn->prepare("SELECT info.information_name, apt.apt_id, apt.apt_reason, apt.apt_date FROM tbl_appointment AS apt INNER JOIN tbl_information AS info ON apt.issued_by = info.information_id WHERE apt.px_id = ? AND apt.apt_is_active = ? ORDER BY apt.apt_cre_datetime DESC");
                                $s->bind_param("ii", $id, $is_active);
                                $s->execute();
                                $res = $s->get_result();
                                while ($row = $res->fetch_assoc())
                                {
                                    echo "<tr id = '".$row['apt_id']."'>";
                                        echo "<td>".$row['apt_reason']."</td>";
                                        echo "<td>".$row['information_name']."</td>";
                                        echo "<td>".$row['apt_date']."</td>";
                                        echo "<td></td>";
                                    echo "</tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                            </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="modal fade" id="addDiagnosis" tabindex="-1" aria-labelledby="addDiagnosisLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addDiagnosisLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id = 'addDiagnosisForm'>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                        <label for="issuanceDate" class="form-label"><span class="asterisk">*</span><strong>Date of issuance:</strong></label>
                        <input type="text" class = 'form-control' id = 'issuanceDate' autocomplete = 'off' placeholder='YYYY-MM-DD'>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="labresult" class="form-label"><strong>Laboratory results:</strong></label>
                            <textarea class="form-control" id="labresult" rows="3"></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="diag" class="form-label"><strong>Diagnosis:</strong></label>
                            <textarea class="form-control" id="diag" rows="3"></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="medication" class="form-label"><strong>Medication/Treatment:</strong></label>
                            <textarea class="form-control" id="medication" rows="3"></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="recom" class="form-label"><strong>Recommendation:</strong></label>
                            <textarea class="form-control" id="recom" rows="3" ></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-5'>
                            <label for="diagnosisDate" class="form-label"><span class="asterisk">*</span><strong>Date of diagnosis:</strong></label>
                            <input type="text" class = 'form-control' id = 'diagnosisDate' autocomplete = 'off' placeholder='YYYY-MM-DD'>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id = 'submit_diagnosis' >Add</button>
            </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="viewDiagnosis" tabindex="-1" aria-labelledby="viewDiagnosisLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="viewDiagnosisLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id = 'viewDiagnosisForm'>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="viewIssuanceDate" class="form-label"><span class="asterisk">*</span><strong>Date of issuance:</strong></label>
                            <input type="text" class = 'form-control' id = 'viewIssuanceDate' autocomplete = 'off' placeholder='YYYY-MM-DD'>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="viewLabResult" class="form-label"><strong>Laboratory results:</strong></label>
                            <textarea class="form-control" id="viewLabResult" rows="3"></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="viewDiag" class="form-label"><strong>Diagnosis:</strong></label>
                            <textarea class="form-control" id="viewDiag" rows="3"></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="viewMedication" class="form-label"><strong>Medication/Treatment:</strong></label>
                            <textarea class="form-control" id="viewMedication" rows="3"></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="viewRecom" class="form-label"><strong>Recommendation:</strong></label>
                            <textarea class="form-control" id="viewRecom" rows="3" ></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-5'>
                            <label for="viewDiagnosisDate" class="form-label"><span class="asterisk">*</span><strong>Date of diagnosis:</strong></label>
                            <input type="text" class = 'form-control' id = 'viewDiagnosisDate' autocomplete = 'off' placeholder='YYYY-MM-DD'>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-5'>
                            <a id = "download_tag" href = "#" download>Download here...</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id = 'generate_medcert'>Send to Email</button>
                <button type="button" class="btn btn-danger" id = 'delete_diagnosis'>Delete Diagnosis</button>
                <button type="button" class="btn btn-success" id = 'update_diagnosis'>Update Diagnosis</button>
            </div>
            </div>
        </div>
    </div>
    <div class="modal fade modal-lg" id="addPrescription" tabindex="-1" aria-labelledby="addPrescriptionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addPrescriptionLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id = 'addPrescriptionForm'>
                    <div class = 'row mb-3'>
                        <div class = 'col-3'>
                            <label for="prescDate" class="form-label"><span class="asterisk">*</span><strong>Prescription Date:</strong></label>
                            <input type="text" class = 'form-control' id = 'prescDate' name = 'prescDate' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-9'>
                            <label for="prescAddress" class="form-label"><strong>Patient Address:</strong></label>
                            <input type="text" class = 'form-control' id = 'prescAddress' name = 'prescAddress' autocomplete = 'off' placeholder='' disabled>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="prescription" class="form-label"><span class="asterisk">*</span><strong>Prescription:</strong></label>
                            <textarea class="form-control" id="prescription" name="prescription" rows="8"></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-4'>
                            <label for="prescFollDate" class="form-label"><strong>Follow-up date:</strong></label>
                            <input type="text" class = 'form-control' id = 'prescFollDate' name = 'prescFollDate' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-4'>
                            <label for="prescPTR" class="form-label"><strong>PTR #:</strong></label>
                            <input type="text" class = 'form-control' id = 'prescPTR' name = 'prescPTR' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-4'>
                            <label for="prescS2" class="form-label"><strong>S2 #:</strong></label>
                            <input type="text" class = 'form-control' id = 'prescS2' name = 'prescS2' autocomplete = 'off' placeholder=''>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id = 'submit_prescription'>Submit</button>
            </div>
            </div>
        </div>
    </div>
    <div class="modal fade modal-lg" id="viewPrescription" tabindex="-1" aria-labelledby="viewPrescriptionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="viewPrescriptionLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id = 'addPrescriptionForm2'>
                    <div class = 'row mb-3'>
                        <div class = 'col-3'>
                            <label for="prescDate" class="form-label"><span class="asterisk">*</span><strong>Prescription Date:</strong></label>
                            <input type="text" class = 'form-control' id = 'view_prescDate' name = 'view_prescDate' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-9'>
                            <label for="prescAddress" class="form-label"><strong>Patient Address:</strong></label>
                            <input type="text" class = 'form-control' id = 'view_prescAddress' name = 'view_prescAddress' autocomplete = 'off' placeholder='' disabled>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="prescription" class="form-label"><span class="asterisk">*</span><strong>Prescription:</strong></label>
                            <textarea class="form-control" id="view_prescription" name="view_prescription" rows="8"></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-4'>
                            <label for="prescFollDate" class="form-label"><strong>Follow-up date:</strong></label>
                            <input type="text" class = 'form-control' id = 'view_prescFollDate' name = 'view_prescFollDate' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-4'>
                            <label for="prescPTR" class="form-label"><strong>PTR #:</strong></label>
                            <input type="text" class = 'form-control' id = 'view_prescPTR' name = 'view_prescPTR' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-4'>
                            <label for="prescS2" class="form-label"><strong>S2 #:</strong></label>
                            <input type="text" class = 'form-control' id = 'view_prescS2' name = 'view_prescS2' autocomplete = 'off' placeholder=''>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-5'>
                            <a id = "dl_tag" href = "#" download>Download here...</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id = 'generate_prescription'>Send Prescription</button>
                <button type="button" class="btn btn-danger" id = 'delete_prescription'>Delete Prescription</button>
                <button type="button" class="btn btn-success" id = 'update_prescription'>Update Prescription</button>
            </div>
            </div>
        </div>
    </div>
    <div class="modal fade modal-lg" id="bookAppointment" tabindex="-1" aria-labelledby="bookAppointmentLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="bookAppointmentLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id = 'bookAppointmentForm'>
                    <div class = 'row mb-3'>
                        <div class = 'col-4'>
                            <label for="appointmentDate" class="form-label"><span class="asterisk">*</span><strong>Date:</strong></label>
                            <input type="text" class = 'form-control' id = 'appointmentDate' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-4'>
                            <label for="appointmentStart" class="form-label"><span class="asterisk">*</span><strong>Start time:</strong></label>
                            <input type="text" class = 'form-control' id = 'appointmentStart' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-4'>
                            <label for="appointmentEnd" class="form-label"><span class="asterisk">*</span><strong>End time:</strong></label>
                            <input type="text" class = 'form-control' id = 'appointmentEnd' autocomplete = 'off' placeholder=''>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="reason" class="form-label"><span class="asterisk">*</span><strong>Reason:</strong></label>
                            <textarea class="form-control" id="reason" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id = 'submit_appointment'>Book</button>
            </div>
            </div>
        </div>
    </div>
    <div class="modal fade modal-lg" id="viewAppointment" tabindex="-1" aria-labelledby="viewAppointmentLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="viewAppointmentLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id = 'viewAppointmentForm'>
                    <div class = 'row mb-3'>
                        <div class = 'col-4'>
                            <label for="viewAppointmentDate" class="form-label"><span class="asterisk">*</span><strong>Date:</strong></label>
                            <input type="text" class = 'form-control' id = 'viewAppointmentDate' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-4'>
                            <label for="viewAppointmentStart" class="form-label"><span class="asterisk">*</span><strong>Start time:</strong></label>
                            <input type="text" class = 'form-control' id = 'viewAppointmentStart' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-4'>
                            <label for="viewAppointmentEnd" class="form-label"><span class="asterisk">*</span><strong>End time:</strong></label>
                            <input type="text" class = 'form-control' id = 'viewAppointmentEnd' autocomplete = 'off' placeholder=''>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="viewReason" class="form-label"><span class="asterisk">*</span><strong>Reason:</strong></label>
                            <textarea class="form-control" id="viewReason" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id = 'delete_appointment'>Delete Appointment</button>
                <button type="button" class="btn btn-success" id = 'update_appointment'>Update Appointment</button>
            </div>
            </div>
        </div>
    </div>

<!-- Modal for Request Lab Test -->

<div class="modal fade" id="addlabs" tabindex="-1" aria-labelledby="requestLabTestLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <!-- Modal Header -->
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="requestLabTestLabel">Request Lab Test</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body" id="labtestDetailsContent">
        <form id="addLabTestForm">

          <!-- Request Date -->
          <div class="mb-3">
            <label for="labtest_request_date" class="form-label"><span class="asterisk">*</span><strong>Request Date:</strong></label>
            <input type="text" class="form-control" id="labtest_request_date" name="labtest_request_date" placeholder='YYYY-MM-DD' required>
          </div>

          <!-- Lab Tests Checklist -->
          <div class="mb-3">
            <label class="form-label"><span class="asterisk">*</span><strong>Select Lab Tests:</strong></label>
            <div class="row">
              <!-- Column 1 -->
              <div class="col-md-3">
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="CBC" id="cbc"><label class="form-check-label" for="cbc">CBC</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Spec 23" id="spec23"><label class="form-check-label" for="spec23">Spec 23</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Spec M" id="specm"><label class="form-check-label" for="specm">Spec M</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Cardiac Panel" id="cardiac"><label class="form-check-label" for="cardiac">Cardiac Panel</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Lipid Profile" id="lipid"><label class="form-check-label" for="lipid">Lipid Profile</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="FBS" id="fbs"><label class="form-check-label" for="fbs">FBS</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="HbA1c" id="hba1c"><label class="form-check-label" for="hba1c">HbA1c</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="BUN" id="bun"><label class="form-check-label" for="bun">BUN</label></div>
              </div>

              <!-- Column 2 -->
              <div class="col-md-3">
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Creatinine" id="creatinine"><label class="form-check-label" for="creatinine">Creatinine</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="SGPT" id="sgpt"><label class="form-check-label" for="sgpt">SGPT</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="SGOT" id="sgot"><label class="form-check-label" for="sgot">SGOT</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Blood Uric Acid" id="uricacid"><label class="form-check-label" for="uricacid">Blood Uric Acid</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Sodium" id="sodium"><label class="form-check-label" for="sodium">Sodium</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Potassium" id="potassium"><label class="form-check-label" for="potassium">Potassium</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Vitamin D" id="vitamind"><label class="form-check-label" for="vitamind">Vitamin D</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Calcium" id="calcium"><label class="form-check-label" for="calcium">Calcium</label></div>
              </div>

              <!-- Column 3 -->
              <div class="col-md-3">
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="FT3" id="ft3"><label class="form-check-label" for="ft3">FT3</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="FT4" id="ft4"><label class="form-check-label" for="ft4">FT4</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="TSH" id="tsh"><label class="form-check-label" for="tsh">TSH</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Amylase" id="amylase"><label class="form-check-label" for="amylase">Amylase</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Lipase" id="lipase"><label class="form-check-label" for="lipase">Lipase</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="HBsAg" id="hbsag"><label class="form-check-label" for="hbsag">HBsAg</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Hepatitis Profile" id="hepatitis"><label class="form-check-label" for="hepatitis">Hepatitis Profile</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Whole Abdomen UTZ" id="utz"><label class="form-check-label" for="utz">Whole Abdomen UTZ</label></div>
              </div>

              <!-- Column 4 -->
              <div class="col-md-3">
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="KUB" id="kub"><label class="form-check-label" for="kub">KUB</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Chest X-ray" id="xray"><label class="form-check-label" for="xray">Chest X-ray</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="2D-Echo" id="echo"><label class="form-check-label" for="echo">2D-Echo</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="ECG" id="ecg"><label class="form-check-label" for="ecg">ECG</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Urinalysis" id="urinalysis"><label class="form-check-label" for="urinalysis">Urinalysis</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="H. Pylori" id="hpylori"><label class="form-check-label" for="hpylori">H. Pylori</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Fecalysis" id="fecalysis"><label class="form-check-label" for="fecalysis">Fecalysis</label></div>
              </div>
            </div>
          </div>

          <!-- Other Tests -->
          <div class="mb-3">
            <label for="otherTests" class="form-label"><strong>Other Lab Tests:</strong></label>
            <textarea class="form-control" id="otherTests" name="labtest_others" rows="1" placeholder="Specify other lab tests..."></textarea>
          </div>

          <!-- Clinical Impression / Diagnosis -->
          <div class="form-group mb-3">
            <label for="labtest_clinical_impression"><strong>Clinical Impression / Diagnosis:</strong></label>
            <textarea class="form-control" id="labtest_clinical_impression" name="labtest_clinical_impression" rows="3" placeholder="Enter clinical impression or diagnosis..."></textarea>
          </div>

          <!-- Remarks / Special Instructions -->
          <div class="form-group mb-3">
            <label for="labtest_remarks"><strong>Remarks / Special Instructions:</strong></label>
            <textarea class="form-control" id="labtest_remarks" name="labtest_remarks" rows="3" placeholder="Enter any remarks or special instructions..."></textarea>
          
          <!-- Follow-up Date -->
          <div class="mb-3">
            <label for="labtest_folldate" class="form-label"><strong>Follow-up Date:</strong></label>
            <input type="text" class="form-control" id="labtest_folldate" name="labtest_folldate" placeholder="">
          </div>  
          </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="submit_labtest">Submit</button>
            </div>                       
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal for View/Edit Lab Test -->
<div class="modal fade" id="viewEditLabTestModal" tabindex="-1" aria-labelledby="viewEditLabTestLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <!-- Modal Header -->
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="viewEditLabTestLabel">View / Edit Lab Test</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <form id="viewEditLabTestForm">

          <!-- Request Date -->
          <div class="mb-3">
            <label for="edit_labtest_request_date" class="form-label"><span class="asterisk">*</span><strong>Request Date:</strong></label>
            <input type="text" class="form-control" id="edit_labtest_request_date" name="labtest_request_date" placeholder='YYYY-MM-DD' required>
          </div>

          <!-- Lab Tests Checklist -->
          <div class="mb-3">
            <label class="form-label"><span class="asterisk">*</span><strong>Selected Lab Tests:</strong></label>
            <div class="row">
              <!-- Column 1 -->
              <div class="col-md-3">
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="CBC" id="edit_cbc"><label class="form-check-label" for="edit_cbc">CBC</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Spec 23" id="edit_spec23"><label class="form-check-label" for="edit_spec23">Spec 23</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Spec M" id="edit_specm"><label class="form-check-label" for="edit_specm">Spec M</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Cardiac Panel" id="edit_cardiac"><label class="form-check-label" for="edit_cardiac">Cardiac Panel</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Lipid Profile" id="edit_lipid"><label class="form-check-label" for="edit_lipid">Lipid Profile</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="FBS" id="edit_fbs"><label class="form-check-label" for="edit_fbs">FBS</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="HbA1c" id="edit_hba1c"><label class="form-check-label" for="edit_hba1c">HbA1c</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="BUN" id="edit_bun"><label class="form-check-label" for="edit_bun">BUN</label></div>
              </div>

              <!-- Column 2 -->
              <div class="col-md-3">
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Creatinine" id="edit_creatinine"><label class="form-check-label" for="edit_creatinine">Creatinine</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="SGPT" id="edit_sgpt"><label class="form-check-label" for="edit_sgpt">SGPT</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="SGOT" id="edit_sgot"><label class="form-check-label" for="edit_sgot">SGOT</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Blood Uric Acid" id="edit_uricacid"><label class="form-check-label" for="edit_uricacid">Blood Uric Acid</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Sodium" id="edit_sodium"><label class="form-check-label" for="edit_sodium">Sodium</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Potassium" id="edit_potassium"><label class="form-check-label" for="edit_potassium">Potassium</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Vitamin D" id="edit_vitamind"><label class="form-check-label" for="edit_vitamind">Vitamin D</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Calcium" id="edit_calcium"><label class="form-check-label" for="edit_calcium">Calcium</label></div>
              </div>

              <!-- Column 3 -->
              <div class="col-md-3">
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="FT3" id="edit_ft3"><label class="form-check-label" for="edit_ft3">FT3</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="FT4" id="edit_ft4"><label class="form-check-label" for="edit_ft4">FT4</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="TSH" id="edit_tsh"><label class="form-check-label" for="edit_tsh">TSH</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Amylase" id="edit_amylase"><label class="form-check-label" for="edit_amylase">Amylase</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Lipase" id="edit_lipase"><label class="form-check-label" for="edit_lipase">Lipase</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="HBsAg" id="edit_hbsag"><label class="form-check-label" for="edit_hbsag">HBsAg</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Hepatitis Profile" id="edit_hepatitis"><label class="form-check-label" for="edit_hepatitis">Hepatitis Profile</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Whole Abdomen UTZ" id="edit_utz"><label class="form-check-label" for="edit_utz">Whole Abdomen UTZ</label></div>
              </div>

              <!-- Column 4 -->
              <div class="col-md-3">
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="KUB" id="edit_kub"><label class="form-check-label" for="edit_kub">KUB</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Chest X-ray" id="edit_xray"><label class="form-check-label" for="edit_xray">Chest X-ray</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="2D-Echo" id="edit_echo"><label class="form-check-label" for="edit_echo">2D-Echo</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="ECG" id="edit_ecg"><label class="form-check-label" for="edit_ecg">ECG</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Urinalysis" id="edit_urinalysis"><label class="form-check-label" for="edit_urinalysis">Urinalysis</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="H. Pylori" id="edit_hpylori"><label class="form-check-label" for="edit_hpylori">H. Pylori</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="Fecalysis" id="edit_fecalysis"><label class="form-check-label" for="edit_fecalysis">Fecalysis</label></div>
              
            </div>
            </div>
          
          <!-- Other Tests -->
          <div class="mb-3">
            <label for="edit_otherTests" class="form-label"><strong>Other Lab Tests:</strong></label>
            <textarea class="form-control" id="edit_otherTests" name="labtest_others" rows="1" placeholder="Specify other lab tests..."></textarea>
          </div>

          <!-- Clinical Impression / Diagnosis -->
          <div class="form-group mb-3">
            <label for="edit_labtest_clinical_impression"><strong>Clinical Impression / Diagnosis:</strong></label>
            <textarea class="form-control" id="edit_labtest_clinical_impression" name="labtest_clinical_impression" rows="3" placeholder="Enter clinical impression or diagnosis..."></textarea>
          </div>

          <!-- Remarks / Special Instructions -->
          <div class="form-group mb-3">
            <label for="edit_labtest_remarks"><strong>Remarks / Special Instructions:</strong></label>
            <textarea class="form-control" id="edit_labtest_remarks" name="labtest_remarks" rows="3" placeholder="Enter any remarks or special instructions..."></textarea>
          
          <!-- Follow-up Date -->
          <div class="mb-3">
            <label for="labtest_folldate" class="form-label"><strong>Follow-up Date:</strong></label>
            <input type="text" class="form-control" id="edit_labtest_folldate" name="labtest_folldate" placeholder="">        
          </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-5'>
                            <a id = "dload_tag" href = "#" download>Download here...</a>
                        </div>
                    </div>

          <!-- Action Buttons -->
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" id="delete_labtest">Delete Labs</button>            
            <button type="button" class="btn btn-success" id="generate_labtest">Send to Email</button>
            <button type="button" class="btn btn-primary" id="update_labtest">Update Labs</button>
          </div>
        </form>
      </div>
     
       <div class="row mb-3 px-3">
        
    </div>
  </div>
</div>


    <div id = 'loadingModal' class = "modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog">
        <div class = 'modal-dialog modal-dialog-centered modal-lg' role="document">
            <div class="modal-content">
                <div class = 'modal-header'>
                    <h4 class = 'modal-title'>Virtual Clinic</h4>
                </div>
                <div class = 'modal-body'>
                    <div class = 'text-center'>
                        <h5><p>Email with attachment is being sent...</p></h5>
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id = 'loadingModal_apt' class = "modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog">
        <div class = 'modal-dialog modal-dialog-centered modal-lg' role="document">
            <div class="modal-content">
                <div class = 'modal-header'>
                    <h4 class = 'modal-title'>Virtual Clinic</h4>
                </div>
                <div class = 'modal-body'>
                    <div class = 'text-center'>
                        <h5><p>Sending appointment...</p></h5>
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id = 'loadingModal_upd_apt' class = "modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog">
        <div class = 'modal-dialog modal-dialog-centered modal-lg' role="document">
            <div class="modal-content">
                <div class = 'modal-header'>
                    <h4 class = 'modal-title'>Virtual Clinic</h4>
                </div>
                <div class = 'modal-body'>
                    <div class = 'text-center'>
                        <h5><p>Updating appointment...</p></h5>
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id = 'loadingModal_del_apt' class = "modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog">
        <div class = 'modal-dialog modal-dialog-centered modal-lg' role="document">
            <div class="modal-content">
                <div class = 'modal-header'>
                    <h4 class = 'modal-title'>Virtual Clinic</h4>
                </div>
                <div class = 'modal-body'>
                    <div class = 'text-center'>
                        <h5><p>Deleting appointment...</p></h5>
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/get.js<?php echo "?version=".$version?>" type="text/javascript"></script>
    <script src="js/script.js<?php echo "?version=".$version?>" type="text/javascript"></script>
</body>
</html>