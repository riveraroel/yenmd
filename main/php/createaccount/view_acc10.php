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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../favicon/favicon.png">

    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link href='https://unpkg.com/fullcalendar@3.10.0/dist/fullcalendar.min.css' rel='stylesheet' />
    <link href='https://unpkg.com/fullcalendar@3.10.0/dist/fullcalendar.print.css' rel='stylesheet' media='print' />
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" />
    <link href='https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css' rel='stylesheet' />

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src='https://unpkg.com/moment@2.24.0/min/moment.min.js'></script>
    <script src='https://unpkg.com/fullcalendar@3.10.0/dist/fullcalendar.min.js'></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js"></script>
    <script src="https://unpkg.com/tooltip.js@1.3.2/dist/umd/tooltip.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    <style>
    
    @media (min-width: 992px) {
      #tabNavCollapse {
        display: block !important;
      }
    }
  </style> 
</head>
<body>

  <!-- Sticky Top Navbar with Collapse Toggle -->
  <nav class="navbar navbar-expand-lg sticky-top bg-body-tertiary shadow-sm">
    <div class="container-fluid">
      <span class="navbar-brand mb-0 h1" id="viewAcc_Name">Dr. Jane Doe</span>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#tabNavCollapse" aria-controls="tabNavCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </nav>

  <!-- Collapsible Tab Navigation -->
  <div class="collapse navbar-collapse sticky-top bg-white border-bottom shadow-sm" id="tabNavCollapse" style="z-index: 1020;">
    <div class="nav nav-tabs justify-content-center" id="nav-tab" role="tablist">
      <button class="nav-link active" id="nav-acc_details-tab" data-bs-toggle="tab" data-bs-target="#nav-acc_details" type="button" role="tab">Account Details</button>
      <button class="nav-link" id="nav-issued_diagnosis-tab" data-bs-toggle="tab" data-bs-target="#nav-issued_diagnosis" type="button" role="tab">Issued Diagnosis</button>
      <button class="nav-link" id="nav-issued_appointment-tab" data-bs-toggle="tab" data-bs-target="#nav-issued_appointment" type="button" role="tab">Issued Appointment</button>
      <button class="nav-link" id="nav-issued_prescription-tab" data-bs-toggle="tab" data-bs-target="#nav-issued_prescription" type="button" role="tab">Issued Prescription</button>
      <button class="nav-link" id="nav-issued_labs-tab" data-bs-toggle="tab" data-bs-target="#nav-issued_labs" type="button" role="tab">Issued Labs</button>
      <button class="nav-link" id="nav-uploaded_images-tab" data-bs-toggle="tab" data-bs-target="#nav-uploaded_images" type="button" role="tab">Uploaded Images</button>
    </div>
  </div>

    <div class="tab-content container" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-acc_details" role="tabpanel" aria-labelledby="nav-acc_details-tab" tabindex="0"><br>
            <form id='viewacc-form' name='viewacc-form'>
                <!-- Account Details -->
                <div class='row mb-3'>
                    <div class='col-12 col-md-4'>
                        <label for="viewacc_name"><p>Name: </p></label>
                        <input type="text" class='form-control' name='viewacc_name' autocomplete='off' placeholder='Including titles: e.g John Doe, MD, PhD'>
                    </div>
                    <div class='col-12 col-md-4'>
                        <label for="viewacc_uname"><p>Username: </p></label>
                        <input type="text" class='form-control' name='viewacc_uname' autocomplete='viewacc_uname' placeholder='Enter a new username'>
                    </div>
                    <div class='col-12 col-md-4'>
                        <label for="viewacc_pword"><p>Password: </p></label>
                        <input type="password" class='form-control' name='viewacc_pword' autocomplete='curr-viewacc_pword' placeholder='Enter the password'>
                    </div>
                </div>

                <div class='row mb-3'>
                    <div class='col-12 col-md-4'>
                        <label for="viewacc_role"><p>Role: </p></label>
                        <select class="form-select" name='viewacc_role'>
                            <option value="2">Doctor</option>
                            <option value="1">Staff</option>
                        </select>
                    </div>
                    <div class='col-12 col-md-4'>
                        <label for="viewacc_licno"><p>License Number: </p></label>
                        <input type="text" class='form-control' name='viewacc_licno' autocomplete='off' placeholder='Required if MD'>
                    </div>
                    <div class='col-12 col-md-4'>
                        <label for="viewacc_ptr"><p>PTR: </p></label>
                        <input type="text" class='form-control' name='viewacc_ptr' autocomplete='off'>
                    </div>
                    <div class='col-12 col-md-4'>
                        <label for="viewacc_s2"><p>S2: </p></label>
                        <input type="text" class='form-control' name='viewacc_s2' autocomplete='off'>
                    </div>
                    <div class='col-12 col-md-4'>
                        <label for="viewacc_contact"><p>Contact #: </p></label>
                        <input type="text" class='form-control' name='viewacc_contact' autocomplete='off'>
                    </div>
                </div>

                <div class='row mb-3'>
                    <div class='col-12 col-md-5'>
                        <label for="viewacc_email"><p>Email address: </p></label>
                        <input type="text" class='form-control' name='viewacc_email' autocomplete='off'>
                    </div>
                    <div class='col-12 col-md-7'>
                        <label for="viewacc_address"><p>Address: </p></label>
                        <textarea class='form-control' name='viewacc_address' autocomplete='off'></textarea>
                    </div>
                </div>

                <div class="modal-footer d-flex flex-column flex-md-row align-items-start justify-content-start gap-2">
                <button type="button" id="update_acc" class="btn btn-success">Update</button>
                <button type="button" id="delete_acc" class="btn btn-danger">Delete</button>
                </div>
                
            </form>
        </div>

        <!-- Each table section should wrap with table-responsive -->
        <!-- Issued Diagnosis Table -->
        <div class="tab-pane fade" id="nav-issued_diagnosis" role="tabpanel" aria-labelledby="nav-issued_diagnosis-tab" tabindex="0">
            <div class='row mb-3'>
                <div class='col-12'>
                    <div class="table-responsive">
                        <table id="tb_issued_diagnosis" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Diagnosis</th>
                                    <th>Date</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody id = 'tb_issued_diagnosis2'>
                                <?php
                                    $is_active = '1';
                                    $id = $_GET['id'];
                                    $s = $conn->prepare("SELECT CONCAT_WS(' ', px.px_firstname, px.px_midname, px.px_lastname, px.px_suffix) AS fullname, mh.px_id AS mhid, mh.medhis_diagnosis AS mhdiag, mh.medhis_diagnosis_date AS mhdate FROM tbl_medical_history AS mh INNER JOIN tbl_px_details AS px ON mh.px_id = px.px_id WHERE mh.issued_by = ? AND mh.medhis_is_active = ? AND px.px_is_active = '1' ORDER BY medhis_cre_datetime DESC");
                                    $s->bind_param("ii", $id, $is_active);
                                    $s->execute();
                                    $res = $s->get_result();
                                    while ($row = $res->fetch_assoc())
                                    {
                                        $row['mhdiag'] = encrypt_decrypt('decrypt', $row['mhdiag']);
                                        echo "<tr id = '".$row['mhid']."'>";
                                            echo "<td>".$row['fullname']."</td>";
                                            echo "<td>".$row['mhdiag']."</td>";
                                            echo "<td>".$row['mhdate']."</td>";
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>                            
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Issued Appointment Table -->
        <div class="tab-pane fade" id="nav-issued_appointment" role="tabpanel" aria-labelledby="nav-issued_appointment-tab" tabindex="0">
            <div class='row mb-3'>
                <div class='col-12'>
                    <div class="table-responsive">
                        <table id="tb_issued_appointment" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Reason</th>
                                    <th>Date</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody id = 'tb_issued_appointment2'>
                                <?php
                                    $is_active = '1';
                                    $id = $_GET['id'];
                                    $s = $conn->prepare("SELECT CONCAT_WS(' ', px.px_firstname, px.px_midname, px.px_lastname, px.px_suffix) AS fullname, apt.px_id AS aptid, apt.apt_reason AS aptreason, apt.apt_date AS aptdate FROM tbl_appointment AS apt INNER JOIN tbl_px_details AS px ON apt.px_id = px.px_id WHERE apt.issued_by = ? AND apt.apt_is_active = ? AND px.px_is_active = '1' ORDER BY apt.apt_cre_datetime DESC");
                                    $s->bind_param("ii", $id, $is_active);
                                    $s->execute();
                                    $res = $s->get_result();
                                    while ($row = $res->fetch_assoc())
                                    {
                                        echo "<tr id = '".$row['aptid']."'>";
                                            echo "<td>".$row['fullname']."</td>";
                                            echo "<td>".$row['aptreason']."</td>";
                                            echo "<td>".$row['aptdate']."</td>";
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Issued Prescription Table -->                            
        <div class="tab-pane fade" id="nav-issued_prescription" role="tabpanel" aria-labelledby="nav-issued_prescription-tab" tabindex="0">
            <div class='row mb-3'>
                <div class='col-12'>
                    <div class="table-responsive">
                        <table id="tb_issued_prescription" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Prescription</th>
                                    <th>Date</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody id = 'tb_issued_prescription2'>
                                <?php
                                    $is_active = '1';
                                    $id = $_GET['id'];
                                    $s = $conn->prepare("SELECT CONCAT_WS(' ', px.px_firstname, px.px_midname, px.px_lastname, px.px_suffix) AS fullname, pr.prescription_for AS prid, pr.prescription AS presc, pr.prescription_date AS prdate FROM tbl_prescrip AS pr INNER JOIN tbl_px_details AS px ON pr.prescription_for = px.px_id WHERE pr.issued_by = ? AND pr.prescription_is_active = ? AND px.px_is_active = '1' ORDER BY prescription_cre_datetime DESC");
                                    $s->bind_param("ii", $id, $is_active);
                                    $s->execute();
                                    $res = $s->get_result();
                                    while ($row = $res->fetch_assoc())
                                    {
                                        echo "<tr id = '".$row['prid']."'>";
                                            echo "<td>".$row['fullname']."</td>";
                                            echo "<td>".$row['presc']."</td>";
                                            echo "<td>".$row['prdate']."</td>";
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>                      
        <!-- Issued Labtest Table -->                            
        <div class="tab-pane fade" id="nav-issued_labs" role="tabpanel" aria-labelledby="nav-issued_labs-tab" tabindex="0">
            <div class='row mb-3'>
                <div class='col-12'>
                    <div class="table-responsive">
                        <table id="tb_issued_labs" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Requested Tests</th>
                                    <th>Date</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody id = 'tb_issued_labs2'>
                                <?php
                                    $is_active = '1';
                                    $id = $_GET['id'];
                                    $s = $conn->prepare("SELECT CONCAT_WS(' ', px.px_firstname, px.px_midname, px.px_lastname, px.px_suffix) AS fullname, lab.px_id AS pxid, lab.labtest_selected AS labtest_selected, lab.labtest_others AS labtest_others, lab.labtest_request_date AS labtest_request_date FROM tbl_lab_requests AS lab INNER JOIN tbl_px_details AS px ON lab.px_id = px.px_id WHERE lab.issued_by = ? AND lab.labtest_is_active = ? AND px.px_is_active = '1' ORDER BY labtest_cre_datetime DESC");
                                    $s->bind_param("ii", $id, $is_active);
                                    $s->execute();
                                    $res = $s->get_result();
                                    while ($row = $res->fetch_assoc())
                                    {
                                        $requested_tests = $row['labtest_selected'];
                                        if (!empty($row['labtest_others'])) {
                                            $requested_tests .= " | " . $row['labtest_others'];
                                        }

                                        echo "<tr id = '".$row['pxid']."'>";
                                            echo "<td>".$row['fullname']."</td>";
                                            echo "<td>".$requested_tests."</td>";
                                            echo "<td>".$row['labtest_request_date']."</td>";
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Uploaded Images Table -->                            
        <div class="tab-pane fade" id="nav-uploaded_images" role="tabpanel" aria-labelledby="nav-uploaded_images-tab" tabindex="0">
            <div class='row mb-3'>
                <div class='col-12'>
                    <div class="table-responsive">
                        <table id="tb_uploaded_images" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Lab Type</th>
                                    <th>Upload Date/Time</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody id ='tb_uploaded_images2'>
                                <?php
                                    $is_active = '1';
                                    $id = $_GET['id'];
                                    $s = $conn->prepare("SELECT CONCAT_WS(' ', px.px_firstname, px.px_midname, px.px_lastname, px.px_suffix) AS fullname, upl.px_id AS uplid, upl.lab_type AS upllabtype, upl.upload_datetime AS upldate FROM tbl_uploads AS upl INNER JOIN tbl_px_details AS px ON upl.px_id = px.px_id WHERE upl.uploaded_by = ? AND px.px_is_active = '1' ORDER BY upl.upload_datetime DESC");
                                    $s->bind_param("i", $id);
                                    $s->execute();
                                    $res = $s->get_result();
                                    while ($row = $res->fetch_assoc())
                                    {
                                        echo "<tr id = '".$row['uplid']."'>";
                                            echo "<td>".$row['fullname']."</td>";
                                            echo "<td>".$row['upllabtype']."</td>";
                                            echo "<td>".$row['upldate']."</td>";
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>                
    <br>
    <script src="js/get.js<?php echo "?version=".$version?>" type="text/javascript"></script>
    <script src="js/script.js<?php echo "?version=".$version?>" type="text/javascript"></script>
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <style>
        @media (max-width: 576px) {
            .modal-footer .btn { width: 100%; }
            .navbar-brand { font-size: 1rem; }
            .nav-tabs .nav-link { font-size: 0.8rem; padding: 0.3rem; }
            input, select, textarea { font-size: 0.9rem; }
        }
    </style>
<!-- Bootstrap JS Bundle (includes Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>    
</body>
</html>
