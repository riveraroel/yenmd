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
  responsive: true,
  autoWidth: false,
  language: {
    emptyTable: "No issued diagnosis found"
  },
  order: [],
  columnDefs: [
    {
      targets: 2, // 3rd column - "Date"
      width: '100px'
    },
    {
      data: null,
      defaultContent: "<button type='button' class='btn btn-outline-primary button'>View</button>",
      targets: -1, // last column - "Option"
      width: '80px',
      className: 'text-center'
    }
  ]
});


     var tb_issued_appointment = $('#tb_issued_appointment').DataTable({
  responsive: true,
  autoWidth: false,
  language: {
    emptyTable: "No issued appointment found"
  },
  order: [],
  columnDefs: [
    {
      targets: 2, // 3rd column - "Date"
      width: '100px'
    },
    {
      data: null,
      defaultContent: "<button type='button' class='btn btn-outline-primary button'>View</button>",
      targets: -1, // last column - "Option"
      width: '80px',
      className: 'text-center'
    }
  ]
});


     var tb_uploaded_images = $('#tb_uploaded_images').DataTable({
  responsive: true,
  autoWidth: false,
  language: {
    emptyTable: "No uploaded images found"
  },
  order: [],
  columnDefs: [
    {
      targets: 2,
      width: '100px'
    },
    {
      data: null,
      defaultContent: "<button type='button' class='btn btn-outline-primary button'>View</button>",
      targets: -1,
      width: '80px',
      className: 'text-center'
    }
  ]
});


     var tb_issued_prescription = $('#tb_issued_prescription').DataTable({
  responsive: true,
  autoWidth: false,
  language: {
    emptyTable: "No issued prescription found"
  },
  order: [],
  columnDefs: [
    {
      targets: 2, // 3rd column - "Date"
      width: '100px'
    },
    {
      data: null,
      defaultContent: "<button type='button' class='btn btn-outline-primary button'>View</button>",
      targets: -1, // last column - "Option"
      width: '80px',
      className: 'text-center'
    }
  ]
});


     var tb_issued_labs = $('#tb_issued_labs').DataTable({
  responsive: true,
  autoWidth: false,
  language: {
    emptyTable: "No issued labtest found"
  },
  order: [],
  columnDefs: [
    {
      targets: 2, // 3rd column - "Date"
      width: '100px'
    },
    {
      data: null,
      defaultContent: "<button type='button' class='btn btn-outline-primary button'>View</button>",
      targets: -1, // last column - "Option"
      width: '80px',
      className: 'text-center'
    }
  ]
});
     

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