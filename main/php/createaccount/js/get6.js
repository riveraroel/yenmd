$(document).ready(function(){
  $('#viewacc-form :input').prop('disabled', true);
    function getQueryVariable(variable)
    {
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i=0;i<vars.length;i++)
        {
          var pair = vars[i].split("=");
          if(pair[0] == variable)
            {
              return pair[1];             
            }
        }
       return(false);
    }

    var tb_issued_diagnosis = $('#tb_issued_diagnosis').DataTable({
        "language": {
          "emptyTable": "No issued diagnosis found"
        },
        "order": [],
        columnDefs: [
          {
            data: null,
            defaultContent: "<button type = 'button' class = 'btn btn-outline-primary' id = 'view_medhistory'>View Account</button>",
            targets: -1
          }
        ]
     });

     var tb_issued_appointment = $('#tb_issued_appointment').DataTable({
        "language": {
          "emptyTable": "No issued appointment found"
        },
        "order": [],
        columnDefs: [
          {
            data: null,
            defaultContent: "<button type = 'button' class = 'btn btn-outline-primary' id = 'view_apthistory'>View Account</button>",
            targets: -1
          }
        ]
     });

     var tb_issued_prescription = $('#tb_issued_prescription').DataTable({
      "language": {
        "emptyTable": "No issued prescription found"
      },
      "order": [],
      columnDefs: [
        {
          data: null,
          defaultContent: "<button type = 'button' class = 'btn btn-outline-primary' id = 'view_apthistory'>View Account</button>",
          targets: -1
        }
      ]
   });

     //Get Appointment History Details
    $.ajax({
        type: "GET",
        url: "get_issued_appointment_history.php",
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        cache: false,
        dataType: "json",
        data:{
            id: btoa(getQueryVariable('id'))
        },
        success: function(data)
        {
            if ($.isArray(data))
            {
                for (i=0;i<data.length;i++)
                {
                    tb_issued_appointment.row.add([data[i].fullname, data[i].aptreason, data[i].aptdate]).draw().node().id = data[i].aptid;
                    tb_issued_appointment.draw(false);
                }
            }
        },
        error: function(xhr, ajaxoption, thrownerror)
        {
            console.log("Get Appointment History Error: "+xhr.status+" "+thrownerror);
        }
    });

     //Get Prescription History Details
  //    $.ajax({
  //     type: "GET",
  //     url: "get_issued_prescription_history.php",
  //     contentType: "application/x-www-form-urlencoded; charset=UTF-8",
  //     cache: false,
  //     dataType: "json",
  //     data:{
  //         id: btoa(getQueryVariable('id'))
  //     },
  //     success: function(data)
  //     {
  //         if ($.isArray(data))
  //         {
  //             for (i=0;i<data.length;i++)
  //             {
  //               tb_issued_prescription.row.add([data[i].fullname, data[i].presc, data[i].prdate]).draw().node().id = data[i].prid;
  //               tb_issued_prescription.draw(false);
  //             }
  //         }
  //     },
  //     error: function(xhr, ajaxoption, thrownerror)
  //     {
  //         console.log("Get Appointment History Error: "+xhr.status+" "+thrownerror);
  //     }
  // });

     //Get Issued Diagnosis History Details
    // $.ajax({
    //     type: "GET",
    //     url: "get_issued_diagnosis_history.php",
    //     contentType: "application/x-www-form-urlencoded; charset=UTF-8",
    //     cache: false,
    //     dataType: "json",
    //     data:{
    //         id: btoa(getQueryVariable('id'))
    //     },
    //     success: function(data)
    //     {
    //         if ($.isArray(data))
    //             {
    //               for (i=0;i<data.length;i++)
    //               {
    //                 tb_issued_diagnosis.row.add([data[i].fullname, data[i].mhdiag, data[i].mhdate]).draw().node().id = data[i].mhid;
    //                 tb_issued_diagnosis.draw(false);
    //               }
    //             }
    //     },
    //     error: function(xhr, ajaxoption, thrownerror)
    //     {
    //         console.log("Get Issued Diagnosis History Error: "+xhr.status+" "+thrownerror);
    //     }
    // });

    //Get Account Details to form
    $.ajax({
        type: "GET",
        url: "get_acc_details.php",
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        cache: false,
        dataType: "json",
        data:{
            id: btoa(getQueryVariable('id'))
        },
        success: function(data)
        {
          if($.isArray(data))
            {
                $('#viewacc-form :input').prop('disabled', false);
                $('#viewAcc_Name').html(data[0].info_name);
                $('input[name="viewacc_name"]').val(data[0].info_name);
                $('input[name="viewacc_uname"]').val(data[0].uname);
                $('input[name="viewacc_pword"]').val(data[0].pword);
                $('input[name="viewacc_role"]').val(data[0].role);
                $('input[name="viewacc_licno"]').val(data[0].licno);
                $('input[name="viewacc_ptr"]').val(data[0].ptr);
                $('input[name="viewacc_s2"]').val(data[0].s2);
                $('input[name="viewacc_email"]').val(data[0].info_emailadd);
                $('input[name="viewacc_contact"]').val(data[0].info_cont);
                $('textarea[name="viewacc_address"]').val(data[0].addr);
            }
        },
        error: function(xhr, ajaxoption, thrownerror)
        {
            console.log("Get Account Details Error: "+xhr.status+" "+thrownerror);
        }
    });
});