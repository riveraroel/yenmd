<div class="tab-pane fade justify-content-center" id="pills-px_profile" role="tabpanel" aria-labelledby="pills-px_profile-tab">
    <ul class="nav nav-tabs" id="px-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="pxlist-tab" data-bs-toggle="tab" data-bs-target="#pxlist-tab-pane" type="button" role="tab" aria-controls="pxlist-tab-pane" aria-selected="false">List of patients</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="addpx-tab" data-bs-toggle="tab" data-bs-target="#addpx-tab-pane" type="button" role="tab" aria-controls="addpx-tab-pane" aria-selected="true">Add new patient</a>
        </li>
        
    </ul>
    <div class="tab-content" id="px-tabContent">
        <div class="tab-pane fade" id="addpx-tab-pane" role="tabpanel" aria-labelledby="addpx-tab" tabindex="0">
            <br>
            <form id = 'addpx-form container'>
                <div class = 'row mb-3'>
                    <div class = 'col-3'>
                        <label for="addpx_fname"><p><span class="asterisk">*</span>First name: </p></label>
                        <input type="text" class = 'form-control' id = 'addpx_fname' autocomplete = 'off'>
                    </div>
                    <div class = 'col-3'>
                    <label for="addpx_mname" ><p>Middle name: </p></label>
                        <input type="text" class = 'form-control' id = 'addpx_mname' autocomplete = 'off'>
                    </div>
                    <div class = 'col-3'>
                        <label for="addpx_lname"><p><span class="asterisk">*</span>Last name: </p></label>
                        <input type="text" class = 'form-control' id = 'addpx_lname' autocomplete = 'off' >
                    </div>
                    <div class = 'col-2'>
                        <label for="addpx_suffix"><p>Suffix: </p></label>
                        <select class="form-select" aria-label="Default select example" id = 'addpx_suffix'>
                            <option value = "" selected disabled>No suffix</option>
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
                        <label for="addpx_dob"><p>Date of birth: </p></label>
                        <input type="text" class = 'form-control' id = 'addpx_dob' autocomplete = 'off' placeholder='YYYY-MM-DD'>
                    </div>
                    <div class = 'col-4'>
                        <label for="addpx_gender"><p>Gender: </p></label>
                        <select class="form-select" aria-label="Default select example" id = 'addpx_gender'>
                            <option value = "None" selected disabled>Select here...</option>
                            <option value = "Male">Male</option>
                            <option value = "Female">Female</option>
                        </select>
                    </div>
                    <div class = 'col-4'>
                        <label for="addpx_civstat"><p>Civil Status: </p></label>
                        <select class="form-select" aria-label="Default select example" id = 'addpx_civstat'>
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
                        <label for="addpx_cellnum"><p>Cellphone number: </p></label>
                        <input type="text" class = 'form-control' id = 'addpx_cellnum' autocomplete = 'off' placeholder='09XXXXXXXXX'>
                    </div>
                    <div class = 'col-6'>
                        <label for="addpx_emailadd"><p>Email address: </p></label>
                        <input type="text" class = 'form-control' id = 'addpx_emailadd' autocomplete = 'off' placeholder='Enter a valid email address'>
                    </div>
                </div>
                <div class = 'row mb-3'>
                    <div class = 'col-12'>
                        <label for="addpx_cellnum"><p>Address: </p></label>
                        <input type="text" class = 'form-control' id = 'addpx_addr' autocomplete = 'off' placeholder="Patient's address">
                    </div>
                </div>

                <div class = 'row mb-3'>
                    <div class = 'col-6'>
                        <label for="addpx_hmo"><p>HMO Provider: </p></label>
                        <select class="form-select" aria-label="Default select example" id = 'addpx_hmo'>
                            <option value = "None" selected>None</option>
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
                        <label for="addpx_company"><p>Company name: </p></label>
                        <input type="text" class = 'form-control' id = 'addpx_company' autocomplete = 'off' placeholder='Company name'>
                    </div>
                </div>

                <div class = 'row mb-3'>
                    <div class = 'col-6'>
                        <button type="button" id = 'submit_px' class="btn btn-outline-primary btn-lg">Submit</button>
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
                            $is_active = '1';
                            $s = $conn->prepare("SELECT CONCAT(px_firstname, ' ', px_midname, ' ', px_lastname, ' ', px_suffix) AS fullname, px_id FROM tbl_px_details WHERE px_is_active = ? ORDER by px_cre_datetime DESC");
                            $s->bind_param("i", $is_active);
                            $s->execute();
                            $res = $s->get_result();
                            while ($row = $res->fetch_assoc())
                            {
                                echo "<tr id = ".$row['px_id'].">";
                                echo "<td>".$row['fullname']."</td>";
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

