<div class="tab-pane fade" id="pills-createacc" role="tabpanel" aria-labelledby="pills-quickforms-tab">
    <ul class="nav nav-tabs" id="px-tab" role="tablist">
    <li class="nav-item" role="presentation">
            <a class="nav-link active" id="acclist-tab" data-bs-toggle="tab" data-bs-target="#acclist-tab-pane" type="button" role="tab" aria-controls="acclist-tab-pane" aria-selected="false">List of accounts</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="addacc-tab" data-bs-toggle="tab" data-bs-target="#addacc-tab-pane" type="button" role="tab" aria-controls="addacc-tab-pane" aria-selected="true">Add new account</a>
        </li>
    </ul>
    <div class="tab-content" id="acc-tabContent">
        <div class="tab-pane fade" id="addacc-tab-pane" role="tabpanel" aria-labelledby="addacc-tab" tabindex="0">
            <br>
            <form id = 'addacc-form' autocomplete="off">
                <div class = 'row mb-3'>
                    <div class = 'col-4'>
                        <label for="addacc_name"><p>Name: </p></label>
                        <input type="text" class = 'form-control' name = 'addacc_name' autocomplete = 'off' placeholder = 'Including titles: e.g John Doe, MD, PhD'>
                    </div>
                    <div class = 'col-4'>
                        <label for="addacc_uname"><p>Username: </p></label>
                        <input type="text" class = 'form-control' name = 'addacc_uname' autocomplete = 'off' placeholder = 'Enter a new username'>
                    </div>
                    <div class = 'col-4'>
                        <label for="addacc_pword"><p>Password: </p></label>
                        <input type="password" class = 'form-control' name = 'addacc_pword' autocomplete = 'off' placeholder = 'Enter the password'>
                    </div>
                </div>
                <div class = 'row mb-3'>
                    <div class = 'col-4'>
                        <label for="addacc_role"><p>Role: </p></label>
                        <select class="form-select" aria-label="Default select example" name = 'addacc_role'>
                            <option value = "2">Doctor</option>
                            <option value = "1">Staff</option>
                        </select>
                    </div>
                    <div class = 'col-4'>
                        <label for="addacc_licno"><p>License Number: </p></label>
                        <input type="text" class = 'form-control' name = 'addacc_licno' autocomplete = 'off' placeholder = 'Required if MD'>
                    </div>
                    <div class = 'col-4'>
                        <label for="addacc_contact"><p>Contact #: </p></label>
                        <input type="text" class = 'form-control' name = 'addacc_contact' autocomplete = 'off' placeholder = ''>
                    </div>
                </div>
                <div class = 'row mb-3'>
                    <div class = 'col-5'>
                        <label for="addacc_emailadd"><p>Email address: </p></label>
                        <input type="text" class = 'form-control' name = 'addacc_emailadd' autocomplete = 'off' placeholder = ''>
                    </div>
                    <div class = 'col-7'>
                        <label for="addacc_address"><p>Address: </p></label>
                        <textarea type="text" class = 'form-control' name = 'addacc_address' autocomplete = 'off'></textarea>
                    </div>
                </div>
                <div class = 'row mb-3'>
                    <div class = 'col-6'>
                        <button type="button" id = 'submit_acc' class="btn btn-outline-primary btn-lg">Submit</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="tab-pane fade  show active container" id="acclist-tab-pane" role="tabpanel" aria-labelledby="acclist-tab" tabindex="0">
            <br>
            <div class = 'row mb-3'>
                <div class = 'col-12'>
                    <table id="tb_acclist" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Option</th>
                            </tr>
                        </thead>
                        <tbody id = 'tb_acclist2'>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>