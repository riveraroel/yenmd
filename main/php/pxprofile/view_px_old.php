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
    <meta charset="utf-8">
    <link rel="prefetch" href="https://cdn.svgator.com/images/2023/06/hearthbeat-svg-loader-animation.svg" as="image" type="image/svg+xml"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="../favicon/myicon.ico">
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css'>
    <link rel="stylesheet" href="css/style.css">
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js'></script>
    <!-- Full Calendar -->
    <link href='https://unpkg.com/fullcalendar@3.10.0/dist/fullcalendar.min.css' rel='stylesheet' />
    <link href='https://unpkg.com/fullcalendar@3.10.0/dist/fullcalendar.print.css' rel='stylesheet' media='print' />
    <script src='https://unpkg.com/moment@2.24.0/min/moment.min.js'></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src='https://unpkg.com/fullcalendar@3.10.0/dist/fullcalendar.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
    <script src="https://unpkg.com/tooltip.js@1.3.2/dist/umd/tooltip.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!---Datepicker-->  
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <!---DataTables-->  
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js" type="text/javascript"></script>
    <link href='https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css' rel='stylesheet' />
</head>
<body>
    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid">
            <form class="container-fluid justify-content-start">
                <span class="navbar-brand mb-0 h1" id = 'viewPx_Name'>Patient name</span>
                <button class="btn btn-outline-info me-2" type="button" data-bs-toggle="modal" data-bs-target="#addDiagnosis">Add Diagnosis</button>
                <button class="btn btn-outline-info me-2" type="button" data-bs-toggle="modal" data-bs-target="#bookAppointment">Book an appointment</button>
                <button class="btn btn-outline-info me-2" type="button" data-bs-toggle="modal" data-bs-target="#addPrescription">Add Prescription</button>
            </form>
        </div>
    </nav>
    <nav class = 'navbar justify-content-center'>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Patient Details</button>
            <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Medical History</button>
            <button class="nav-link" id="nav-appointment-tab" data-bs-toggle="tab" data-bs-target="#nav-appointment" type="button" role="tab" aria-controls="nav-appointment" aria-selected="false">Appointment History</button>
            <button class="nav-link" id="nav-pres-tab" data-bs-toggle="tab" data-bs-target="#nav-pres" type="button" role="tab" aria-controls="nav-pres" aria-selected="false">Prescription History</button>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0"><br>
        <form id = 'viewpx-form container'>
                <div class = 'row mb-3'>
                    <div class = 'col-3'>
                        <label for="viewpx_fname"><p><span class="asterisk">*</span>First name: </p></label>
                        <input type="text" class = 'form-control' id = 'viewpx_fname' autocomplete = 'off'>
                    </div>
                    <div class = 'col-3'>
                    <label for="viewpx_mname" ><p>Middle name: </p></label>
                        <input type="text" class = 'form-control' id = 'viewpx_mname' autocomplete = 'off'>
                    </div>
                    <div class = 'col-3'>
                        <label for="viewpx_lname"><p><span class="asterisk">*</span>Last name: </p></label>
                        <input type="text" class = 'form-control' id = 'viewpx_lname' autocomplete = 'off' >
                    </div>
                    <div class = 'col-2'>
                        <label for="viewpx_suffix"><p>Suffix: </p></label>
                        <select class="form-select" aria-label="Default select example" id = 'viewpx_suffix'>
                            <option value = "">No suffix</option>
                            <option value = "JR.">JR.</option>
                            <option value = "SR.">SR.</option>
                            <option value = "II">II</option>
                            <option value = "III">III</option>
                            <option value = "IV">IV</option>
                        </select>
                    </div>
                </div>
                <div class = 'row mb-3'>
                    <div class = 'col-4'>
                        <label for="viewpx_dob"><p>Date of birth: </p></label>
                        <input type="text" class = 'form-control' id = 'viewpx_dob' autocomplete = 'off' placeholder='YYYY-MM-DD'>
                    </div>
                    <div class = 'col-4'>
                        <label for="viewpx_gender"><p>Gender: </p></label>
                        <select class="form-select" aria-label="Default select example" id = 'viewpx_gender'>
                            <option value = "None" selected disabled>Select here...</option>
                            <option value = "Male">Male</option>
                            <option value = "Female">Female</option>
                        </select>
                    </div>
                    <div class = 'col-4'>
                        <label for="viewpx_civstat"><p>Civil Status: </p></label>
                        <select class="form-select" aria-label="Default select example" id = 'viewpx_civstat'>
                            <option value = "None" selected disabled>Select here...</option>
                            <option value = "Single">Single</option>
                            <option value = "Married">Married</option>
                            <option value = "Widowed">Widowed</option>
                            <option value = "Legally Seperated">Legally Seperated</option>
                        </select>
                    </div>
                </div>

                <div class = 'row mb-3'>
                    <div class = 'col-6'>
                        <label for="viewpx_cellnum"><p>Cellphone number: </p></label>
                        <input type="text" class = 'form-control' id = 'viewpx_cellnum' autocomplete = 'off' placeholder='09XXXXXXXXX' onkeypress="return isNumber(event)">
                    </div>
                    <div class = 'col-6'>
                        <label for="viewpx_emailadd"><p>Email address: </p></label>
                        <input type="text" class = 'form-control' id = 'viewpx_emailadd' autocomplete = 'off' placeholder='Enter a valid email address'>
                    </div>
                </div>

                <div class = 'row mb-3'>
                    <div class = 'col-12'>
                        <label for="viewpx_addr">Address: </label>
                        <input type="text" class = 'form-control' id = 'viewpx_addr' autocomplete = 'off' placeholder="Patient's address">
                    </div>
                </div>

                <div class = 'row mb-3'>
                    <div class = 'col-6'>
                        <label for="viewpx_hmo"><p>HMO Provider: </p></label>
                        <select class="form-select" aria-label="Default select example" id = 'viewpx_hmo'>
                            <option value = "None">None</option>
                            <option value = "Avega Managed Care, Inc.">Avega Managed Care, Inc.</option>
                            <option value = "BenLife">BenLife</option>
                            <option value = "Cocolife">Cocolife</option>
                            <option value = "Eastwest Healthcare, Inc.">Eastwest Healthcare, Inc.</option>
                            <option value = "Generali">Generali</option>
                            <option value = "iCare">iCare</option>
                            <option value = "InLife">InLife</option>
                            <option value = "Intellicare">Intellicare</option>
                            <option value = "Kaiser International Healthgroup, Inc.">Kaiser International Healthgroup, Inc.</option>
                            <option value = "Lacson & Lacson">Lacson & Lacson</option>
                            <option value = "Medocare Health Systems, Inc.">Medocare Health Systems, Inc.</option>
                            <option value = "PhilBritish">PhilBritish</option>
                            <option value = "PhilCare">PhilCare</option>
                        </select>
                    </div>
                    <div class = 'col-6'>
                        <label for="viewpx_company"><p>Company name: </p></label>
                        <input type="text" class = 'form-control' id = 'viewpx_company' autocomplete = 'off' placeholder='Company name'>
                    </div>
                </div>

                <div class = 'row mb-3'>
                    <div class = 'col-6'>
                        <button type="button" id = 'update_px' class="btn btn-outline-success btn-lg">Update details</button>
                        <button type="button" id = 'delete_px' class="btn btn-outline-danger btn-lg">Delete patient</button>
                    </div>
                </div>
            </form>
        </div>
        <br>
        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
            <div class = 'row mb-3'>
                <div class = 'col-12'>
                    <table id="tb_medhistory" class="table table-striped" style="width:100%">
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
                                    echo "</tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="nav-pres" role="tabpanel" aria-labelledby="nav-pres-tab" tabindex="0">
            <div class = 'row mb-3'>
                <div class = 'col-12'>
                        <table id="tb_pres_history" class="table table-striped" style="width:100%">
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
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade p-3 m-3" id="nav-appointment" role="tabpanel" aria-labelledby="nav-appointment-tab" tabindex="0">
            <div class = 'row mb-3'>
                <div class = 'col-12'>
                    <table id="tb_apt_history" class="table table-striped" style="width:100%">
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
                        <label for="issuanceDate" class="form-label"><span class="asterisk">*</span>Date of issuance:</label>
                        <input type="text" class = 'form-control' id = 'issuanceDate' autocomplete = 'off' placeholder='YYYY-MM-DD'>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="labresult" class="form-label">Laboratory results:</label>
                            <textarea class="form-control" id="labresult" rows="3"></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="diag" class="form-label">Diagnosis:</label>
                            <textarea class="form-control" id="diag" rows="3"></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="medication" class="form-label">Medication/Treatment:</label>
                            <textarea class="form-control" id="medication" rows="3"></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="recom" class="form-label">Recommendation:</label>
                            <textarea class="form-control" id="recom" rows="3" ></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-5'>
                            <label for="diagnosisDate" class="form-label"><span class="asterisk">*</span>Date of diagnosis:</label>
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
                            <label for="viewIssuanceDate" class="form-label"><span class="asterisk">*</span>Date of issuance:</label>
                            <input type="text" class = 'form-control' id = 'viewIssuanceDate' autocomplete = 'off' placeholder='YYYY-MM-DD'>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="viewLabResult" class="form-label">Laboratory results:</label>
                            <textarea class="form-control" id="viewLabResult" rows="3"></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="viewDiag" class="form-label">Diagnosis:</label>
                            <textarea class="form-control" id="viewDiag" rows="3"></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="viewMedication" class="form-label">Medication/Treatment:</label>
                            <textarea class="form-control" id="viewMedication" rows="3"></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="viewRecom" class="form-label">Recommendation:</label>
                            <textarea class="form-control" id="viewRecom" rows="3" ></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-5'>
                            <label for="viewDiagnosisDate" class="form-label"><span class="asterisk">*</span>Date of diagnosis:</label>
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
                            <label for="prescDate" class="form-label"><span class="asterisk">*</span>Prescription Date:</label>
                            <input type="text" class = 'form-control' id = 'prescDate' name = 'prescDate' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-9'>
                            <label for="prescAddress" class="form-label">Patient Address:</label>
                            <input type="text" class = 'form-control' id = 'prescAddress' name = 'prescAddress' autocomplete = 'off' placeholder='' disabled>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="prescription" class="form-label"><span class="asterisk">*</span>Prescription:</label>
                            <textarea class="form-control" id="prescription" name="prescription" rows="8"></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-4'>
                            <label for="prescFollDate" class="form-label">Follow-up date:</label>
                            <input type="text" class = 'form-control' id = 'prescFollDate' name = 'prescFollDate' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-4'>
                            <label for="prescPTR" class="form-label">PTR #:</label>
                            <input type="text" class = 'form-control' id = 'prescPTR' name = 'prescPTR' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-4'>
                            <label for="prescS2" class="form-label">S2 #:</label>
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
    <div class="modal fade modal-lg" id="viewPrescription" tabindex="-1" aria-labelledby="addPrescriptionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addPrescriptionLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id = 'addPrescriptionForm2'>
                    <div class = 'row mb-3'>
                        <div class = 'col-3'>
                            <label for="prescDate" class="form-label"><span class="asterisk">*</span>Prescription Date:</label>
                            <input type="text" class = 'form-control' id = 'view_prescDate' name = 'view_prescDate' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-9'>
                            <label for="prescAddress" class="form-label">Patient Address:</label>
                            <input type="text" class = 'form-control' id = 'view_prescAddress' name = 'view_prescAddress' autocomplete = 'off' placeholder='' disabled>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="prescription" class="form-label"><span class="asterisk">*</span>Prescription:</label>
                            <textarea class="form-control" id="view_prescription" name="view_prescription" rows="8"></textarea>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-4'>
                            <label for="prescFollDate" class="form-label">Follow-up date:</label>
                            <input type="text" class = 'form-control' id = 'view_prescFollDate' name = 'view_prescFollDate' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-4'>
                            <label for="prescPTR" class="form-label">PTR #:</label>
                            <input type="text" class = 'form-control' id = 'view_prescPTR' name = 'view_prescPTR' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-4'>
                            <label for="prescS2" class="form-label">S2 #:</label>
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
                            <label for="appointmentDate" class="form-label"><span class="asterisk">*</span>Date:</label>
                            <input type="text" class = 'form-control' id = 'appointmentDate' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-4'>
                            <label for="appointmentStart" class="form-label"><span class="asterisk">*</span>Start time:</label>
                            <input type="text" class = 'form-control' id = 'appointmentStart' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-4'>
                            <label for="appointmentEnd" class="form-label"><span class="asterisk">*</span>End time:</label>
                            <input type="text" class = 'form-control' id = 'appointmentEnd' autocomplete = 'off' placeholder=''>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="reason" class="form-label"><span class="asterisk">*</span>Reason:</label>
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
                            <label for="viewAppointmentDate" class="form-label"><span class="asterisk">*</span>Date:</label>
                            <input type="text" class = 'form-control' id = 'viewAppointmentDate' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-4'>
                            <label for="viewAppointmentStart" class="form-label"><span class="asterisk">*</span>Start time:</label>
                            <input type="text" class = 'form-control' id = 'viewAppointmentStart' autocomplete = 'off' placeholder=''>
                        </div>
                        <div class = 'col-4'>
                            <label for="viewAppointmentEnd" class="form-label"><span class="asterisk">*</span>End time:</label>
                            <input type="text" class = 'form-control' id = 'viewAppointmentEnd' autocomplete = 'off' placeholder=''>
                        </div>
                    </div>
                    <div class = 'row mb-3'>
                        <div class = 'col-12'>
                            <label for="viewReason" class="form-label"><span class="asterisk">*</span>Reason:</label>
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
    <link href="css/style.css" rel="stylesheet" type="text/css" />
</body>
</html>