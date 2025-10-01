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
                <span class="navbar-brand mb-0 h1" id = 'viewAcc_Name'></span>
            </form>
        </div>
    </nav>
    <nav class = 'navbar justify-content-center'>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-acc_details-tab" data-bs-toggle="tab" data-bs-target="#nav-acc_details" type="button" role="tab" aria-controls="nav-acc_details" aria-selected="true">Account Details</button>
            <button class="nav-link" id="nav-issued_diagnosis-tab" data-bs-toggle="tab" data-bs-target="#nav-issued_diagnosis" type="button" role="tab" aria-controls="nav-issued_diagnosis" aria-selected="false">Issued Diagnosis</button>
            <button class="nav-link" id="nav-issued_appointment-tab" data-bs-toggle="tab" data-bs-target="#nav-issued_appointment" type="button" role="tab" aria-controls="nav-issued_appointment" aria-selected="false">Issued Appointment</button>
            <button class="nav-link" id="nav-issued_prescription-tab" data-bs-toggle="tab" data-bs-target="#nav-issued_prescription" type="button" role="tab" aria-controls="nav-issued_prescription" aria-selected="false">Issued Prescription</button>
        </div>
    </nav>
    <div class="tab-content container" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-acc_details" role="tabpanel" aria-labelledby="nav-acc_details-tab" tabindex="0"><br>
            <form id = 'viewacc-form' name = 'viewacc-form'>
                <div class = 'row mb-3'>
                    <div class = 'col-4'>
                        <label for="viewacc_name"><p>Name: </p></label>
                        <input type="text" class = 'form-control' name = 'viewacc_name' autocomplete = 'off' placeholder = 'Including titles: e.g John Doe, MD, PhD'>
                    </div>
                    <div class = 'col-4'>
                        <label for="viewacc_uname"><p>Username: </p></label>
                        <input type="text" class = 'form-control' name = 'viewacc_uname' autocomplete = 'viewacc_uname' placeholder = 'Enter a new username'>
                    </div>
                    <div class = 'col-4'>
                        <label for="viewacc_pword"><p>Password: </p></label>
                        <input type="password" class = 'form-control' name = 'viewacc_pword' autocomplete = 'curr-viewacc_pword' placeholder = 'Enter the password'>
                    </div>
                </div>
                <div class = 'row mb-3'>
                    <div class = 'col-4'>
                        <label for="viewacc_role"><p>Role: </p></label>
                        <select class="form-select" aria-label="Default select example" name = 'viewacc_role'>
                            <option value = "2">Doctor</option>
                            <option value = "1">Staff</option>
                        </select>
                    </div>
                    <div class = 'col-4'>
                        <label for="viewacc_licno"><p>License Number: </p></label>
                        <input type="text" class = 'form-control' name = 'viewacc_licno' autocomplete = 'off' placeholder = 'Required if MD'>
                    </div>
                    <div class = 'col-4'>
                        <label for="viewacc_contact"><p>Contact #: </p></label>
                        <input type="text" class = 'form-control' name = 'viewacc_contact' autocomplete = 'off'></input>
                    </div>
                </div>
                <div class = 'row mb-3'>
                    <div class = 'col-5'>
                        <label for="viewacc_email"><p>Email address: </p></label>
                        <input type="text" class = 'form-control' name = 'viewacc_email' autocomplete = 'off'></input>
                    </div>
                    <div class = 'col-7'>
                        <label for="viewacc_address"><p>Address: </p></label>
                        <textarea type="text" class = 'form-control' name = 'viewacc_address' autocomplete = 'off'></textarea>
                    </div>
                </div>
                <div class = 'row mb-3'>
                    <div class = 'col-6'>
                        <button type="button" id = 'update_acc' class="btn btn-outline-success btn-lg">Update Account</button>
                        <button type="button" id = 'delete_acc' class="btn btn-outline-danger btn-lg">Delete Account</button>
                    </div>
                </div>
            </form>
        </div>
        <br>
        <div class="tab-pane fade" id="nav-issued_diagnosis" role="tabpanel" aria-labelledby="nav-issued_diagnosis-tab" tabindex="0">
            <div class = 'row mb-3'>
                <div class = 'col-12'>
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
                                $s = $conn->prepare("SELECT CONCAT(px.px_firstname, ' ', px.px_midname, ' ', px.px_lastname, ' ', px.px_suffix) AS fullname, mh.px_id AS mhid, mh.medhis_diagnosis AS mhdiag, mh.medhis_diagnosis_date AS mhdate FROM tbl_medical_history AS mh INNER JOIN tbl_px_details AS px ON mh.px_id = px.px_id WHERE mh.issued_by = ? AND mh.medhis_is_active = ? AND px.px_is_active = '1' ORDER BY medhis_cre_datetime DESC");
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
        <div class="tab-pane fade" id="nav-issued_appointment" role="tabpanel" aria-labelledby="nav-issued_appointment-tab" tabindex="0">
            <div class = 'row mb-3'>
                <div class = 'col-12'>
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
                                $s = $conn->prepare("SELECT CONCAT(px.px_firstname, ' ', px.px_midname, ' ', px.px_lastname, ' ', px.px_suffix) AS fullname, apt.px_id AS aptid, apt.apt_reason AS aptreason, apt.apt_date AS aptdate FROM tbl_appointment AS apt INNER JOIN tbl_px_details AS px ON apt.px_id = px.px_id WHERE apt.issued_by = ? AND apt.apt_is_active = ? AND px.px_is_active = '1' ORDER BY apt.apt_cre_datetime DESC");
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
        <div class="tab-pane fade" id="nav-issued_prescription" role="tabpanel" aria-labelledby="nav-issued_prescription-tab" tabindex="0">
        <div class = 'row mb-3'>
                <div class = 'col-12'>
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
                                $s = $conn->prepare("SELECT CONCAT(px.px_firstname, ' ', px.px_midname, ' ', px.px_lastname, ' ', px.px_suffix) AS fullname, pr.prescription_for AS prid, pr.prescription AS presc, pr.prescription_date AS prdate FROM tbl_prescrip AS pr INNER JOIN tbl_px_details AS px ON pr.prescription_for = px.px_id WHERE pr.issued_by = ? AND pr.prescription_is_active = ? AND px.px_is_active = '1' ORDER BY prescription_cre_datetime DESC");
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
    <br>
    <script src="js/get.js<?php echo "?version=".$version?>" type="text/javascript"></script>
    <script src="js/script.js<?php echo "?version=".$version?>" type="text/javascript"></script>
    <link href="css/style.css" rel="stylesheet" type="text/css" />
</body>
</html>