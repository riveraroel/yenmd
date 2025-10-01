<div class="tab-pane fade justify-content-center" id="pills-px_profile" role="tabpanel" aria-labelledby="pills-px_profile-tab">
    <ul class="nav nav-tabs" id="px-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="pxlist-tab" data-bs-toggle="tab" data-bs-target="#pxlist-tab-pane" type="button" role="tab" aria-controls="pxlist-tab-pane" aria-selected="false">
                <i class="bi bi-people-fill me-2"></i>Patient List
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link text-success" id="addpx-tab" data-bs-toggle="tab" data-bs-target="#addpx-tab-pane" type="button" role="tab" aria-controls="addpx-tab-pane" aria-selected="true">
                <i class="bi bi-person-plus-fill me-2"></i> Add Patient
            </a>
        </li>
    </ul>
    <div class="tab-content" id="px-tabContent">
        <div class="tab-pane fade" id="addpx-tab-pane" role="tabpanel" aria-labelledby="addpx-tab" tabindex="0">
    <br>
    <form id="addpx-form" class="container py-3">
        <div class="row mb-3 align-items-end">
        <!-- First Name -->
        <div class="col-12 col-sm-6 col-md-3 mb-2">
            <label for="addpx_fname"><span class="asterisk">*</span> First Name:</label>
            <input type="text" class="form-control" id="addpx_fname" required>
        </div>

        <!-- Middle Name -->
        <div class="col-12 col-sm-6 col-md-3 mb-2">
            <label for="addpx_mname">Middle Name:</label>
            <input type="text" class="form-control" id="addpx_mname">
        </div>

        <!-- Last Name -->
        <div class="col-12 col-sm-6 col-md-3 mb-2">
            <label for="addpx_lname"><span class="asterisk">*</span> Last Name:</label>
            <input type="text" class="form-control" id="addpx_lname" required>
        </div>

        <!-- Suffix -->
        <div class="col-6 col-md-1 mb-2">
            <label for="addpx_suffix">Suffix:</label>
            <select class="form-select" id="addpx_suffix">
            <option value="" selected disabled>No suffix</option>
            <option value="JR.">JR.</option>
            <option value="SR.">SR.</option>
            <option value="II">II</option>
            <option value="III">III</option>
            <option value="IV">IV</option>
            </select>
        </div>

        <!-- PWD -->
        <div class="col-6 col-md-2 mb-2 d-flex align-items-end">
        <div class="form-check ms-1">
            <input class="form-check-input" type="checkbox" id="addpx_is_pwd">
            <label class="form-check-label ms-2" for="addpx_is_pwd">
            PWD
            </label>
        </div>
        </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4 col-sm-12 mb-2">
                <label for="addpx_dob">Date of birth:</label>
                <input type="text" class = 'form-control' id = 'addpx_dob' autocomplete = 'off' placeholder='YYYY-MM-DD'>
            </div>
            <div class="col-md-4 col-sm-6 mb-2">
                <label for="addpx_gender"><span class="asterisk">*</span> Gender:</label>
                    <select class="form-select" id="addpx_gender" required>
                    <option value="" selected disabled>Select here...</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    </select>
            </div>
            <div class="col-md-4 col-sm-6 mb-2">
                <label for="addpx_civstat">Civil Status:</label>
                    <select class="form-select" id="addpx_civstat" name="civstat">
                    <option value="" selected disabled>Select here...</option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Widowed">Widowed</option>
                    <option value="Legally Separated">Legally Separated</option>
                    <option value="Legally Separated">Divorced</option>
                    </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6 col-sm-12 mb-2">
                <label for="addpx_cellnum">Cellphone Number:</label>
                <input type="tel" class="form-control" id="addpx_cellnum" maxlength="11" autocomplete="off" placeholder="09XXXXXXXXX">
            </div>
            <div class="col-md-6 col-sm-12 mb-2">
                <label for="addpx_emailadd">Email Address:</label>
                <input type="email" class="form-control" id="addpx_emailadd" autocomplete="off" placeholder="Enter a valid email address">
            </div>
        </div>

        <div class="row mb-3 align-items-end">
        <div class="col-md-12 col-sm-12 mb-2">
            <label for="addpx_addr">Address:</label>
            <input type="text" class="form-control" id="addpx_addr" autocomplete="off" placeholder="Patient's address">
        </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6 col-sm-12 mb-2">
                <label for="addpx_hmo">HMO Provider:</label>
                <select class="form-select" id="addpx_hmo">
                    <option value="None" selected>None</option>
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
            <div class="col-md-6 col-sm-12 mb-2">
                <label for="addpx_company">Company Name:</label>
                <input type="text" class="form-control" id="addpx_company" autocomplete="off" placeholder="Enter company name">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12 col-md-auto">
                <button type="submit" id="submit_px" class="btn btn-outline-primary btn-lg w-100 w-md-auto">Submit</button>
            </div>
        </div>
    </form>
</div>


        <div class="tab-pane fade active show container" id="pxlist-tab-pane" role="tabpanel" aria-labelledby="pxlist-tab" tabindex="0">
            <br>
            <div class = 'row mb-3'>
                <div class = 'col-12'>
                    <table id="tb_pxlist" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Option</th>
                            </tr>
                        </thead>
                        <tbody id = 'tb_pxlist2'>
                        <?php
$is_active = 1;
$s = $conn->prepare("
    SELECT 
      CONCAT_WS(' ', px_firstname, px_midname, px_lastname, px_suffix) AS fullname,
      px_id 
    FROM tbl_px_details 
    WHERE px_is_active = ? 
    ORDER BY px_cre_datetime DESC
");

$s->bind_param("i", $is_active);
$s->execute();
$res = $s->get_result();
while ($row = $res->fetch_assoc()) {
    echo "<tr id='{$row['px_id']}'>";
    echo "<td>{$row['fullname']}</td>";
    echo "<td><button class='btn btn-sm btn-primary view-px-btn'>View</button></td>";
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