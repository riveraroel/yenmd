<?php
include '../../connection/conn.php';
include '../../check_session.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mary Yentl Borazon, MD - Virtual Clinic</title>

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

<!-- Sticky Wrapper Start -->
<div class="sticky-top bg-white" style="z-index: 1020;">
  <!-- Top Patient Name + Buttons -->
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

  <!-- Tabs Navigation -->
  <nav class="navbar bg-white border-top border-bottom">
   <div class="nav nav-tabs w-100 d-flex flex-nowrap justify-content-between" id="nav-tab" role="tablist">

    <button class="nav-link flex-fill text-center active"
      id="nav-home-tab"
      data-bs-toggle="tab"
      data-bs-target="#nav-home"
      type="button"
      role="tab"
      aria-controls="nav-home"
      aria-selected="true">
      <i class="fas fa-user"></i>
      <span class="d-inline d-sm-none"> Info</span>
      <span class="d-none d-sm-inline"> Patient Info</span>
    </button>

    <button class="nav-link flex-fill text-center"
      id="nav-profile-tab"
      data-bs-toggle="tab"
      data-bs-target="#nav-profile"
      type="button"
      role="tab"
      aria-controls="nav-profile"
      aria-selected="false">
      <i class="fas fa-stethoscope"></i>
      <span class="d-inline d-sm-none"> Medical</span>
      <span class="d-none d-sm-inline"> Medical History</span>
    </button>

    <button class="nav-link flex-fill text-center"
      id="nav-appointment-tab"
      data-bs-toggle="tab"
      data-bs-target="#nav-appointment"
      type="button"
      role="tab"
      aria-controls="nav-appointment"
      aria-selected="false">
      <i class="fas fa-calendar-check"></i>
      <span class="d-inline d-sm-none"> Appt</span>
      <span class="d-none d-sm-inline"> Appointment History</span>
    </button>

    <button class="nav-link flex-fill text-center"
      id="nav-pres-tab"
      data-bs-toggle="tab"
      data-bs-target="#nav-pres"
      type="button"
      role="tab"
      aria-controls="nav-pres"
      aria-selected="false">
      <i class="fas fa-prescription"></i>
      <span class="d-inline d-sm-none"> Rx</span>
      <span class="d-none d-sm-inline"> Prescription History</span>
    </button>

    <button class="nav-link flex-fill text-center"
      id="nav-lab-tab"
      data-bs-toggle="tab"
      data-bs-target="#nav-lab"
      type="button"
      role="tab"
      aria-controls="nav-lab"
      aria-selected="false">
      <i class="fas fa-microscope"></i>
      <span class="d-inline d-sm-none"> Labs</span>
      <span class="d-none d-sm-inline"> Labs History</span>
    </button>

     </div>
 </nav>
</div>
<!-- Sticky Wrapper End -->


<div class="tab-content" id="nav-tabContent">
  <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
    <br>
<!-- 📱 MOBILE CARD VIEW -->
<div id="mobile_card_view" class="d-md-none">
  <div class="container">
    <div class="card mb-3 shadow-sm">
      <div class="card-body">

        <div class="d-flex mb-1">
          <div class="me-2 text-end fw-bold ps-0 ms-0" style="min-width: 100px;">Full Name:</div>
          <div class="flex-grow-1" id="mobile_fullname"></div>
        </div>

        <div class="d-flex mb-1">
          <div class="me-2 text-end fw-bold ps-0 ms-0" style="min-width: 100px;">Date of Birth:</div>
          <div class="flex-grow-1" id="mobile_dob"></div>
        </div>

        <div class="d-flex mb-1">
          <div class="me-2 text-end fw-bold ps-0 ms-0" style="min-width: 100px;">Age:</div>
          <div class="flex-grow-1" id="mobile_age"></div>
        </div>

        <div class="d-flex mb-1">
          <div class="me-2 text-end fw-bold ps-0 ms-0" style="min-width: 100px;">Gender:</div>
          <div class="flex-grow-1" id="mobile_gender"></div>
        </div>

        <div class="d-flex mb-1">
          <div class="me-2 text-end fw-bold ps-0 ms-0" style="min-width: 100px;">Civil Status:</div>
          <div class="flex-grow-1" id="mobile_civstat"></div>
        </div>

        <div class="d-flex mb-1">
          <div class="me-2 text-end fw-bold ps-0 ms-0" style="min-width: 100px;">Cellphone:</div>
          <div class="flex-grow-1" id="mobile_cellnum"></div>
        </div>

        <div class="d-flex mb-1">
          <div class="me-2 text-end fw-bold ps-0 ms-0" style="min-width: 100px;">Email:</div>
          <div class="flex-grow-1 text-break" id="mobile_emailadd" style="word-break: break-word;"></div>
        </div>

        <div class="d-flex mb-1">
          <div class="me-2 text-end fw-bold ps-0 ms-0" style="min-width: 100px;">Address:</div>
          <div class="flex-grow-1" id="mobile_addr"></div>
        </div>

        <div class="d-flex mb-1">
          <div class="me-2 text-end fw-bold ps-0 ms-0" style="min-width: 100px;">HMO:</div>
          <div class="flex-grow-1" id="mobile_hmo"></div>
        </div>

        <div class="d-flex mb-1">
          <div class="me-2 text-end fw-bold ps-0 ms-0" style="min-width: 100px;">Company:</div>
          <div class="flex-grow-1" id="mobile_company"></div>
        </div>

        <div class="d-flex justify-content-end mt-3">
          <button class="btn btn-outline-primary btn-sm" id="editinfobtn">Edit</button>
        </div>

      </div>
    </div>
  </div>
</div>
    <!-- 🖥️ DESKTOP FULL FORM -->
    <div id="edit_form_view" class="d-none d-md-block">
      <div class="container">
        <form id="viewpx-form">      
            <div class="row mb-3">
              <div class="col-12 col-md-3">
                <label for="viewpx_fname"><p><span class="asterisk">*</span>First Name:</p></label>
                <input type="text" class="form-control" id="viewpx_fname" autocomplete="off">
              </div>
            <div class="col-12 col-md-3">
                <label for="viewpx_mname"><p>Middle Name:</p></label>
                <input type="text" class="form-control" id="viewpx_mname" autocomplete="off">
            </div>
            <div class="col-12 col-md-3">
                <label for="viewpx_lname"><p><span class="asterisk">*</span>Last Name:</p></label>
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

            <div class="row mb-3">
              <div class="col-12 col-md-4">
                <label for="viewpx_dob"><p>Date of Birth:</p></label>
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
                <label for="viewpx_company"><p>Company Name:</p></label>
                <input type="text" class="form-control" id="viewpx_company" autocomplete="off" placeholder="Enter company name">
              </div>
            </div>

<div class="container mt-3">
  <div class="row gy-2 gx-2 justify-content-start">
    <!-- ✅ Update Button -->
    <div class="col-12 col-md-auto">
      <button type="button" id="update_px" class="btn btn-success w-100 w-md-auto">Update</button>
    </div>

    <!-- ✅ Delete Button -->
    <div class="col-12 col-md-auto">
      <button type="button" id="delete_px" class="btn btn-danger w-100 w-md-auto">Delete</button>
    </div>

    <!-- ✅ Close Button (Mobile only) -->
    <div class="col-12 col-md-auto d-block d-md-none text-end">
      <button type="button" id="cancel_edit_mobile" class="btn btn-secondary w-100">Close</button>
    </div>
  </div>
</div>
        </form>
      </div> <!-- container -->
    </div> <!-- d-none d-md-block -->
  </div>
</div>
<!-- MEDICAL HISTORY -->
<?php
$is_active = 1;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$diagnosisData = []; // Default empty array

$stmt = $conn->prepare("
  SELECT 
    info.information_name, 
    mh.medhis_id, 
    mh.medhis_labresult, 
    mh.medhis_medication, 
    mh.medhis_diagnosis, 
    mh.medhis_diagnosis_date 
  FROM tbl_medical_history AS mh 
  INNER JOIN tbl_information AS info ON mh.issued_by = info.information_id 
  WHERE mh.px_id = ? AND mh.medhis_is_active = ? 
  ORDER BY mh.medhis_cre_datetime DESC
");

$stmt->bind_param("ii", $id, $is_active);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $row['medhis_diagnosis'] = encrypt_decrypt('decrypt', $row['medhis_diagnosis']);
  $row['medhis_labresult'] = encrypt_decrypt('decrypt', $row['medhis_labresult']);
  $row['medhis_medication'] = encrypt_decrypt('decrypt', $row['medhis_medication']);
  $diagnosisData[] = $row;
}

$stmt->close();
?>

<div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
  <div class="container mt-4">
    <div class="col-12">

      <!-- 🖥️ DESKTOP TABLE VIEW -->
      <div class="d-none d-md-block">
        <div class="table-responsive">
          <table id="tb_medhistory" class="table table-striped table-hover table-sm w-100">
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
            <tbody>
              <?php foreach ($diagnosisData as $row): ?>
                <tr id="<?= $row['medhis_id'] ?>">
                  <td><?= htmlspecialchars($row['medhis_diagnosis']) ?></td>
                  <td><?= htmlspecialchars($row['medhis_labresult']) ?></td>
                  <td><?= htmlspecialchars($row['medhis_medication']) ?></td>
                  <td><?= htmlspecialchars($row['information_name']) ?></td>
                  <td><?= htmlspecialchars($row['medhis_diagnosis_date']) ?></td>
                  <td>
                    <button type="button" class="btn btn-outline-primary btn-sm view_medhistory">View</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 📱 MOBILE CARD VIEW -->
<?php $count = 1; ?>
<div class="d-md-none">
  <?php if (!empty($diagnosisData)): ?>
    <?php foreach ($diagnosisData as $row): ?>
      <div class="card mb-2 shadow-sm position-relative" id="<?= $row['medhis_id'] ?>">
        <div class="card-body py-2 px-3">

          <!-- 🔢 Mobile-only badge watermark -->
          <div class="position-absolute top-0 end-0 me-2 mt-2 d-md-none">
            <span class="badge bg-primary rounded-pill watermark-badge"><?= $count++ ?></span>
          </div>

          <p class="mb-1"><strong>Diagnosis:</strong><br><?= nl2br(htmlspecialchars($row['medhis_diagnosis'])) ?></p>
          <p class="mb-1"><strong>Lab Results:</strong><br><?= nl2br(htmlspecialchars($row['medhis_labresult'])) ?></p>
          <p class="mb-1"><strong>Medication/Treatment:</strong><br><?= nl2br(htmlspecialchars($row['medhis_medication'])) ?></p>
          <p class="mb-1 d-none d-md-block"><strong>Issued by:</strong> <?= $row['info_name'] ?></p>
          <div class="d-flex justify-content-between align-items-center mb-2">
          <p class="mb-0"><strong>Date:</strong> <?= htmlspecialchars($row['medhis_diagnosis_date']) ?></p>
          <button type="button" class="btn btn-outline-primary btn-sm view_medhistory">View</button>
          </div>

        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="alert alert-info text-center">No diagnosis history found</div>
  <?php endif; ?>
</div>

    </div>
  </div>
</div>
<!-- LAB TEST HISTORY TAB -->
<?php
$is_active = 1;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$labTests = [];

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
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $selected = trim($row['labtest_selected']);
    $others = trim($row['labtest_others']);

    // Combine selected + others
    $combined = $selected;
    if (!empty($others)) {
        $combined = !empty($selected) ? "$selected | $others" : $others;
    }

    $labTests[] = [
        'labtest_id' => htmlspecialchars($row['labtest_id']),
        'labtest_combined' => htmlspecialchars($combined),
        'issued_by' => htmlspecialchars($row['information_name']),
        'created_date' => date('Y-m-d', strtotime($row['labtest_cre_datetime']))
    ];
}

$stmt->close();
?>

<div class="tab-pane fade" id="nav-lab" role="tabpanel" aria-labelledby="nav-lab-tab" tabindex="0">
  <div class="container mt-4">
    <div class="col-12">

      <!-- 🖥️ DESKTOP TABLE VIEW -->
      <div class="table-responsive d-none d-md-block">
        <table id="tb_lab" class="table table-striped table-hover table-sm w-100">
          <thead>
            <tr>
              <th>Requested Tests</th>
              <th>Issued By</th>
              <th>Date</th>
              <th>Option</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($labTests as $test): ?>
              <tr id="<?= $test['labtest_id'] ?>">
                <td><?= $test['labtest_combined'] ?></td>
                <td><?= $test['issued_by'] ?></td>
                <td><?= $test['created_date'] ?></td>
                <td>
                  <button class="btn btn-outline-primary btn-sm view_labtest_history">View</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- 📱 MOBILE CARD VIEW -->
      <div class="d-md-none">
        <?php if (empty($labTests)): ?>
          <div class="alert alert-info text-center">No labs history found</div>
        <?php else: ?>
          <?php $counter = 1; ?>
          <?php foreach ($labTests as $test): ?>
            <div class="card mb-2 shadow-sm position-relative" id="<?= $test['labtest_id'] ?>">

              <!-- 🔢 Mobile-only badge watermark -->
              <div class="position-absolute top-0 end-0 me-2 mt-2 d-md-none">
                <span class="badge bg-primary rounded-pill watermark-badge"><?= $counter++ ?></span>
              </div>

              <div class="card-body py-2 px-3">
                <p class="mb-1"><strong>Requested Tests:</strong><br><?= nl2br($test['labtest_combined']) ?></p>
                <p class="mb-1 d-none d-md-block"><strong>Issued by:</strong> <?= $test['issued_by'] ?></p>

                <div class="d-flex justify-content-between align-items-center mb-2">
                  <p class="mb-0"><strong>Date:</strong> <?= $test['created_date'] ?></p>
                  <button type="button" class="btn btn-outline-primary btn-sm view_labtest_history">View</button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>
<!-- Prescription History Tab -->        
<div class="tab-pane fade" id="nav-pres" role="tabpanel" aria-labelledby="nav-pres-tab" tabindex="0">
  <div class="container mt-4">
    <div class="col-12">

      <?php
        $is_active = '1';
        $id = $_GET['id'];

        $s = $conn->prepare("SELECT 
            info.information_name AS info_name, 
            pr.presc_id AS id, 
            pr.prescription, 
            pr.prescription_date AS presc_date 
          FROM tbl_prescrip AS pr 
          INNER JOIN tbl_information AS info 
            ON pr.issued_by = info.information_id 
          WHERE pr.prescription_for = ? 
            AND pr.prescription_is_active = ? 
          ORDER BY pr.prescription_cre_datetime DESC");

        $s->bind_param("ii", $id, $is_active);
        $s->execute();
        $res = $s->get_result();

        // 🔁 Store result set in an array
        $all_prescriptions = [];
        while ($row = $res->fetch_assoc()) {
          $all_prescriptions[] = $row;
        }
      ?>

      <!-- DESKTOP TABLE VIEW -->
      <div class="d-none d-md-block">
        <div class="table-responsive">
          <table id="tb_pres_history" class="table table-striped table-hover table-sm w-100">
            <thead>
              <tr>
                <th>Prescription</th>
                <th>Issued by</th>
                <th>Date</th>
                <th>Option</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($all_prescriptions as $row): ?>
                <tr id="<?= $row['id'] ?>">
                  <td><?= ($row['prescription']) ?></td>
                  <td><?= $row['info_name'] ?></td>
                  <td><?= $row['presc_date'] ?></td>
                  <td></td> <!-- View button will be injected via DataTables JS -->
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 📱 MOBILE CARD VIEW -->
      <div class="d-md-none">
        <?php if (!empty($all_prescriptions)): ?>
          <?php $counter = 1; ?>
          <?php foreach ($all_prescriptions as $row): ?>
            <div class="card mb-2 shadow-sm position-relative" id="<?= $row['id'] ?>">

              <!-- 🔢 Mobile-only badge watermark -->
              <div class="position-absolute top-0 end-0 me-2 mt-2 d-md-none">
                <span class="badge bg-primary rounded-pill watermark-badge"><?= $counter++ ?></span>
              </div>

              <div class="card-body py-2 px-3">
                <p class="mb-1"><strong>Prescription:</strong><br><?= nl2br($row['prescription']) ?></p>
                <p class="mb-1 d-none d-md-block"><strong>Issued by:</strong> <?= $row['info_name'] ?></p>
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <p class="mb-0"><strong>Date:</strong> <?= $row['presc_date'] ?></p>
                  <button type="button" class="btn btn-outline-primary btn-sm view_preshistory">View</button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="alert alert-info text-center">No prescription history found</div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>
<!-- Appointment History Tab -->
<div class="tab-pane fade" id="nav-appointment" role="tabpanel" aria-labelledby="nav-appointment-tab" tabindex="0">
  <div class="container mt-4">
    <div class="col-12">

      <!-- ✅ DESKTOP TABLE VIEW -->
      <div class="d-none d-md-block">
        <div class="table-responsive">
          <table id="tb_apt_history" class="table table-striped table-hover table-sm w-100">
            <thead>
              <tr>
                <th>Reason</th>
                <th>Issued by</th>
                <th>Date</th>
                <th>Option</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $is_active = '1';
                $id = $_GET['id'];
                $s = $conn->prepare("SELECT info.information_name, apt.apt_id, apt.apt_reason, apt.apt_date FROM tbl_appointment AS apt INNER JOIN tbl_information AS info ON apt.issued_by = info.information_id WHERE apt.px_id = ? AND apt.apt_is_active = ? ORDER BY apt.apt_cre_datetime DESC");
                $s->bind_param("ii", $id, $is_active);
                $s->execute();
                $res = $s->get_result();
                $appointmentData = [];
                while ($row = $res->fetch_assoc()) {
                  $appointmentData[] = $row;
                }
                foreach ($appointmentData as $row):
              ?>
                <tr id="<?= $row['apt_id'] ?>">
                  <td><?= $row['apt_reason'] ?></td>
                  <td><?= $row['information_name'] ?></td>
                  <td><?= $row['apt_date'] ?></td>
                  <td><button type="button" class="btn btn-outline-primary btn-sm view_apt_history">View</button></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 📱 MOBILE CARD VIEW -->
      <div class="d-md-none">
        <?php if (!empty($appointmentData)): ?>
          <?php $counter = 1; ?>
          <?php foreach ($appointmentData as $row): ?>
            <div class="card mb-2 shadow-sm position-relative" id="<?= $row['apt_id'] ?>">

              <!-- 🔢 Mobile-only badge watermark -->
              <div class="position-absolute top-0 end-0 me-2 mt-2 d-md-none">
                <span class="badge bg-primary rounded-pill watermark-badge"><?= $counter++ ?></span>
              </div>

              <div class="card-body py-2 px-3">
                <p class="mb-1"><strong>Reason:</strong><br><?= nl2br($row['apt_reason']) ?></p>
                <p class="mb-1 d-none d-md-block"><strong>Issued by:</strong> <?= $row['information_name'] ?></p>
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <p class="mb-0"><strong>Date:</strong> <?= $row['apt_date'] ?></p>
                  <button type="button" class="btn btn-outline-primary btn-sm view_apt_history">View</button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="alert alert-info text-center">No appointment history found</div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>
    <div class="modal fade" id="addDiagnosis" tabindex="-1" aria-labelledby="addDiagnosisLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addDiagnosisLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id = 'addDiagnosisForm'>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="issuanceDate" class="form-label">
                                <span class="asterisk">*</span><strong>Date of issuance:</strong>
                            </label>
                            <input type="text" class="form-control" id="issuanceDate" autocomplete="off" placeholder="YYYY-MM-DD">
                        </div>
                        <div class="col-6">
                            <label for="diagnosisDate" class="form-label">
                                <span class="asterisk">*</span><strong>Date of diagnosis:</strong>
                            </label>
                            <input type="text" class="form-control" id="diagnosisDate" autocomplete="off" placeholder="YYYY-MM-DD">
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
        <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="viewDiagnosisLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id = 'viewDiagnosisForm'>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="issuanceDate" class="form-label">
                                <span class="asterisk">*</span><strong>Date of issuance:</strong>
                            </label>
                            <input type="text" class="form-control" id="viewIssuanceDate" autocomplete="off" placeholder="YYYY-MM-DD">
                        </div>
                        <div class="col-6">
                            <label for="diagnosisDate" class="form-label">
                                <span class="asterisk">*</span><strong>Date of diagnosis:</strong>
                            </label>
                            <input type="text" class="form-control" id="viewDiagnosisDate" autocomplete="off" placeholder="YYYY-MM-DD">
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
                            <a id = "download_tag" href = "#" download>Download here...</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id = 'generate_medcert'>Email</button>
                <button type="button" class="btn btn-danger" id = 'delete_diagnosis'>Delete</button>
                <button type="button" class="btn btn-success" id = 'update_diagnosis'>Update</button>
            </div>
            </div>
        </div>
    </div>
<!-- Add Prescription Modal -->
<div class="modal fade" id="addPrescription" tabindex="-1" aria-labelledby="addPrescriptionLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
    <div class="modal-content">

      <div class="modal-header">
        <h1 class="modal-title fs-5" id="addPrescriptionLabel"></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="addPrescriptionForm">

          <div class="row mb-3">
            <div class="col-6 col-md-4 mb-2 mb-md-0">
              <label for="prescDate" class="form-label"><span class="asterisk">*</span><strong>Prescription Date:</strong></label>
              <input type="text" class="form-control" id="prescDate" name="prescDate" autocomplete="off" placeholder="">
            </div>
            <div class="col-6 col-md-8">
              <label for="prescAddress" class="form-label"><strong>Patient Address:</strong></label>
              <input type="text" class="form-control" id="prescAddress" name="prescAddress" autocomplete="off" placeholder="" disabled>
            </div>
          </div>

          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="isRegulated" name="is_regulated" value="1">
            <label class="form-check-label" for="isRegulated">
              Is this a regulated drug? <span id="regulatedStatus" class="fw-bold text-success"></span>
            </label>
          </div>

          <div class="row mb-3">
            <div class="col-12">
              <label for="prescription" class="form-label"><span class="asterisk">*</span><strong>Prescription:</strong></label>
              <textarea class="form-control" id="prescription" name="prescription" rows="6"></textarea>
            </div>
          </div>

          <div class="row mb-3">
            <!-- Follow-up Date -->
            <div class="col-12 col-md-4 mb-2 mb-md-0">
              <label for="prescFollDate" class="form-label mb-1"><strong>Follow-up date:</strong></label>
              <input type="text" class="form-control" id="prescFollDate" name="prescFollDate" autocomplete="off" placeholder="">
            </div>

            <!-- PTR with toggle -->
            <div class="col-6 col-md-4 mb-2 mb-md-0" id="ptrWrapper">
              <label for="prescPTR" class="form-label d-flex align-items-center gap-2 mb-1">
                <strong>PTR #:</strong>
                <div id="includePtrToggle" class="form-check form-switch m-0">
                  <input class="form-check-input" type="checkbox" id="includePtr" name="include_ptr" value="true">
                </div>
              </label>
              <input type="text" class="form-control d-none" id="prescPTR" name="prescPTR" readonly>
            </div>

            <!-- S2 Field -->
            <div class="col-6 col-md-4 d-none" id="s2Wrapper">
              <label for="prescS2" class="form-label mb-1"><strong>S2 #:</strong></label>
              <input type="text" class="form-control" id="prescS2" name="prescS2" readonly>
            </div>
          </div>

        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="submit_prescription">Submit</button>         
      </div>

    </div>
  </div>
</div>

<!-- View Prescription Modal -->
<div class="modal fade modal-lg" id="viewPrescription" tabindex="-1" aria-labelledby="viewPrescriptionLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
    <div class="modal-content">

      <div class="modal-header">
        <h1 class="modal-title fs-5" id="viewPrescriptionLabel">View / Edit Prescription</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="viewPrescriptionForm">

          <div class="row mb-3">
            <!-- Prescription Date -->
            <div class="col-6 col-md-4 mb-2 mb-md-0">
              <label for="view_prescDate" class="form-label"><span class="asterisk">*</span><strong>Prescription Date:</strong></label>
              <input type="text" class="form-control" id="view_prescDate" name="view_prescDate" autocomplete="off" placeholder="">
            </div>

            <!-- Patient Address -->
            <div class="col-6 col-md-8">
              <label for="view_prescAddress" class="form-label"><strong>Patient Address:</strong></label>
              <input type="text" class="form-control" id="view_prescAddress" name="view_prescAddress" autocomplete="off" placeholder="" disabled>
            </div>
          </div>

          <div class="form-check form-switch mb-3 ms-3">
            <input class="form-check-input" type="checkbox" id="view_isRegulated" name="is_regulated" value="1" disabled>
            <label class="form-check-label" for="view_isRegulated">
              Is this a regulated drug? <span id="view_regulatedStatus" class="fw-bold text-success"></span>
            </label>
            <input type="hidden" name="is_regulated" id="view_isRegulated_hidden" value="1">
          </div>

          <div class="row mb-3">
            <div class="col-12">
              <label for="view_prescription" class="form-label"><span class="asterisk">*</span><strong>Prescription:</strong></label>
              <textarea class="form-control" id="view_prescription" name="view_prescription" rows="8"></textarea>
            </div>
          </div>

          <div class="row mb-3">
            <!-- Follow-up Date -->
            <div class="col-12 col-md-4 mb-2 mb-md-0">
              <label for="view_prescFollDate" class="form-label mb-1"><strong>Follow-up date:</strong></label>
              <input type="text" class="form-control" id="view_prescFollDate" name="view_prescFollDate" autocomplete="off" placeholder="">
            </div>

            <!-- PTR with inline toggle -->
            <div class="col-6 col-md-4 mb-2 mb-md-0" id="view_ptrWrapper">
              <label for="view_prescPTR" class="form-label d-flex align-items-center gap-2 mb-1">
                <strong>PTR #:</strong>
                <div id="view_includePtrToggle" class="form-check form-switch m-0">
                  <input class="form-check-input" type="checkbox" id="view_includePtr" name="include_ptr" value="true">
                </div>
              </label>
              <input type="text" class="form-control d-none" id="view_prescPTR" name="prescPTR" readonly>
            </div>

            <!-- S2 Field -->
            <div class="col-6 col-md-4 d-none" id="view_s2Wrapper">
              <label for="view_prescS2" class="form-label mb-1"><strong>S2 #:</strong></label>
              <input type="text" class="form-control" id="view_prescS2" name="prescS2" readonly>
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
        <button type="button" class="btn btn-primary" id = 'generate_prescription'>Email</button>
        <button type="button" class="btn btn-danger" id = 'delete_prescription'>Delete</button>
        <button type="button" class="btn btn-success" id = 'update_prescription'>Update</button>
      </div>

    </div>
  </div>
</div>
   
    <div class="modal fade" id="bookAppointment" tabindex="-1" aria-labelledby="bookAppointmentLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="bookAppointmentLabel"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bookAppointmentForm">
                        <div class="row mb-3">
                            <div class="col-12 col-sm-4 mb-3 mb-sm-0">
                                <label for="appointmentDate" class="form-label">
                                    <span class="asterisk">*</span><strong>Date:</strong>
                                </label>
                                <input type="text" class="form-control" id="appointmentDate" autocomplete="off" placeholder="">
                            </div>
                            <div class="col-12 col-sm-4 mb-3 mb-sm-0">
                                <label for="appointmentStart" class="form-label">
                                    <span class="asterisk">*</span><strong>Start time:</strong>
                                </label>
                                <input type="text" class="form-control" id="appointmentStart" autocomplete="off" placeholder="">
                            </div>
                            <div class="col-12 col-sm-4">
                                <label for="appointmentEnd" class="form-label">
                                    <span class="asterisk">*</span><strong>End time:</strong>
                                </label>
                                <input type="text" class="form-control" id="appointmentEnd" autocomplete="off" placeholder="">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="reason" class="form-label">
                                    <span class="asterisk">*</span><strong>Reason:</strong>
                                </label>
                                <textarea class="form-control" id="reason" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submit_appointment">Book</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade modal-lg" id="viewAppointment" tabindex="-1" aria-labelledby="viewAppointmentLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
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
                <button type="button" class="btn btn-danger" id = 'delete_appointment'>Delete</button>
                <button type="button" class="btn btn-success" id = 'update_appointment'>Update</button>
            </div>
            </div>
        </div>
    </div>

<!-- Modal for Request Lab Test -->

<div class="modal fade" id="addlabs" tabindex="-1" aria-labelledby="requestLabTestLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
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
              <div class="col-6 col-md-3">
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
              <div class="col-6 col-md-3">
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
              <div class="col-6 col-md-3">
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
              <div class="col-6 col-md-3">
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
            <textarea class="form-control" id="otherTests" name="labtest_others" rows="1" placeholder="Specify other lab tests - separate each test with a comma or enter in new line..."></textarea>
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
                                   
        </form>
      </div>
      <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="submit_labtest">Submit</button>
            </div>
    </div>
  </div>
</div>

<!-- Modal for View/Edit Lab Test -->
<div class="modal fade" id="viewEditLabTestModal" tabindex="-1" aria-labelledby="viewEditLabTestLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
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
              <div class="col-6 col-md-3">
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
              <div class="col-6 col-md-3">
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
              <div class="col-6 col-md-3">
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
              <div class="col-6 col-md-3">
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
          </div>
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
         
        </form>
      </div>     
        <!-- Action Buttons -->
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="generate_labtest">Email</button>
            <button type="button" class="btn btn-danger" id="delete_labtest">Delete</button>
            <button type="button" class="btn btn-success" id="update_labtest">Update</button>
          </div>
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