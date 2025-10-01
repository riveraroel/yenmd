  $(document).ready(function(){
      var tb_pxlist = $('#tb_pxlist').DataTable({
          pageLength: 10,
          ordering: false,
          "language": {
            "emptyTable": "No patients found"
          },
          columnDefs: [
            {
              data: null,
              defaultContent: "<button type='button' class='btn btn-outline-primary btn-view-patient'>View</button>",
              targets: -1
            }
          ]
      });
      var tb_acclist = $('#tb_acclist').DataTable({
        pageLength: 10,
        ordering: false,
        "language": {
          "emptyTable": "No patients found"
        },
        columnDefs: [
          {
            data: null,
            defaultContent: "<button type = 'button' class = 'btn btn-outline-primary'>View</button>",
            targets: -1
          }
        ]
    });

  //    $('#tb_pxlist').on('click', '.btn-view-patient', function () {
  //   const pxId = $(this).closest('tr').attr('id');

  //   // AJAX to fetch patient data
  //   $.ajax({
  //     type: 'GET',
  //     url: 'get_patient_details.php', // or your actual file
  //     data: { id: btoa(pxId) },
  //     dataType: 'json',
  //     success: function (data) {
  //       if (Array.isArray(data) && data.length > 0) {
  //         const patient = data[0];
  //         $('#modal_px_name').text(patient.fullname);
  //         $('#modal_px_email').text(patient.px_emailadd);
  //         $('#modal_px_contact').text(patient.px_cellnumber);
  //         // ... populate other fields
  //         $('#viewPatientModal').modal('show');
  //       }
  //     },
  //     error: function (xhr, status, error) {
  //       console.error('Failed to load patient:', xhr.status, error);
  //     }
  //   });
  // });

      // $('#addpx_dob').datepicker({
      //     uiLibrary: 'bootstrap5',
      //     format: 'yyyy-mm-dd'
      // });

      var calendar = $('#calendar').fullCalendar({
        plugins: ['interaction'],
        eventLimit: true,
        eventSources:
        [
          'php/calendar/events.php'
        ],
eventRender: function(event, element) {
  element.css('font-size', '.90em');

  const title = event.title || 'No Title';
  const content = event.description || 'No description';

  // Only apply popover if title and content are safe
  element.popover({
    title: title,
    content: content,
    trigger: 'hover',
    html: true,
    placement: 'top',
    container: 'body'
  });
},

        eventAfterAllRender: function(view)
        {
          var moment = $('#calendar').fullCalendar('getDate').month();
          var month = moment + 1;
          var year = $('#calendar').fullCalendar('getDate').year();
          $.ajax({
            type: "GET",
            url: "php/calendar/count_appointment.php",
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            data:{
              month: month,
              year: year
            },
            cache: false,
            success: function(data)
            {
              $('#apt_count').html(data);
            },
            error:  function(xhr, ajaxoption, thrownerror)
            {
              console.log(xhr.status+" "+thrownerror)
            }
          });
        }
      });

      //Get PX details to table
      // $.ajax({
      //   type: "GET",
      //   url: "php/pxprofile/get_px.php",
      //   contentType: "application/x-www-form-urlencoded; charset=UTF-8;",
      //   cache: false,
      //   dataType: "json",
      //   success: function(data)
      //   {
      //     if ($.isArray(data))
      //     {
      //       for (i=0;i<data.length;i++)
      //       {
      //         tb_pxlist.row.add([data[i].fullname]).draw().node().id = data[i].px_id;
      //         tb_pxlist.draw(false);
      //       }
      //     }
      //   },
      //   error: function(xhr, ajaxoption, thrownerror)
      //   {
      //     console.log("Get Patient Details: "+xhr.status+" "+thrownerror);
      //   }
      // });

      //Get account details to table
      $.ajax({
        type: "GET",
        url: "php/createaccount/get_account.php",
        contentType: "application/x-www-form-urlencoded; charset=UTF-8;",
        cache: false,
        dataType: "json",
        success: function(data)
        {
          if ($.isArray(data))
          {
            for (i=0;i<data.length;i++)
            {
              tb_acclist.row.add([data[i].info_name]).draw().node().id = data[i].info_id;
              tb_acclist.draw(false);
            }
          }
        },
        error: function(xhr, ajaxoption, thrownerror)
        {
          console.log("Get Patient Details: "+xhr.status+" "+thrownerror);
        }
      })
  });