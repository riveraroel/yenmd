// ------------------ Global Utility ------------------
function getQueryVariable(variable) {
  const query = window.location.search;
  if (!query || query.length <= 1) return false;

  const vars = query.substring(1).split("&");

  for (let i = 0; i < vars.length; i++) {
    const pair = vars[i].split("=");
    if (pair[0] === variable) {
      return pair[1];
    }
  }

  return false;
}

function getShortAgeString(birthDate) {
  const today = new Date();
  let years = today.getFullYear() - birthDate.getFullYear();
  let months = today.getMonth() - birthDate.getMonth();
  let days = today.getDate() - birthDate.getDate();

  if (days < 0) {
    months--;
    const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
    days += prevMonth.getDate();
  }

  if (months < 0) {
    years--;
    months += 12;
  }

  return `${years}y ${months}m ${days}d`;
}

// ------------------ Patient Info Loader ------------------

function populatePatientInfo() {
  console.log("populatePatientInfo() triggered");
  const queryId = getQueryVariable('id');
  if (!queryId) {
    console.warn("No patient ID found in URL.");
    return;
  }

  $.ajax({
    type: "GET",
    url: "get_px_details.php",
    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
    cache: false,
    dataType: "json",
    data: {
      id: btoa(queryId)
    },
    success: function(data) {
      console.log("Raw patient data:", data);
      if ($.isArray(data)) {
        const patient = data[0];

        // Labels and modal headings
        $('#viewPx_Name').html(patient.fullname);
        $('#addDiagnosisLabel').html("Add Diagnosis - " + patient.fullname);
        $('#viewDiagnosisLabel').html("View / Edit Diagnosis - " + patient.fullname);
        $('#bookAppointmentLabel').html("Book Appointment - " + patient.fullname);
        $('#viewAppointmentLabel').html("View / Edit Appointment - " + patient.fullname);
        $('#addPrescriptionLabel').html("Add Prescription - " + patient.fullname);
        $('#viewPrescriptionLabel').html("View / Edit Prescription - " + patient.fullname);
        $('#requestLabTestLabel').html("Add Labs - " + patient.fullname);
        $('#viewEditLabTestLabel').html("View / Edit Labs - " + patient.fullname);

        // Shared inputs
        $('#prescAddress').val(patient.px_address);
        $('#view_prescAddress').val(patient.px_address);

        // Mobile name
        $('#mobile_fullname').html(patient.fullname);

        const rawDob = patient.px_dob;
        let formattedDob = '';
        let ageText = '';
        let subtitleParts = [];
        let genderIcon = '';

        if (rawDob) {
          const [year, month, day] = rawDob.split('-');
          const dobDate = new Date(year, month - 1, day);

          formattedDob = dobDate.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
          });

          $('#mobile_dob').text(formattedDob);

          const ageShort = getShortAgeString(dobDate);
          const ageYears = new Date().getFullYear() - dobDate.getFullYear();

          ageText = `${ageShort}`;

          // Update mobile age and senior icon
          $('#mobile_age').text(ageText);
          $('#mobile_age_container .senior-icon').remove();
          if (ageYears >= 60) {
            $('#mobile_age_container').append(`<span class="senior-icon ms-1" title="Senior">♿</span>`);
          }

          subtitleParts.push(formattedDob);

          // Add to desktop subtitle later
          if (ageShort) {
            subtitleParts[subtitleParts.length - 1] += ` (${ageShort})`;
            if (ageYears >= 60) {
              subtitleParts[subtitleParts.length - 1] += ` ♿`;
            }
          }
        } else {
          $('#mobile_dob').text('');
          $('#mobile_age').text('');
          $('#mobile_age').next('.senior-icon').remove();
        }

        // Gender
        if (patient.px_gender === "Male") {
          genderIcon = `<span class="text-primary ms-1" data-bs-toggle="tooltip" title="Male"><i class="bi bi-gender-male"></i></span>`;
          $('#mobile_gender').html("Male " + genderIcon);
          subtitleParts.unshift("Male" + genderIcon);
        } else if (patient.px_gender === "Female") {
          genderIcon = `<span class="ms-1" style="color: hotpink;" data-bs-toggle="tooltip" title="Female"><i class="bi bi-gender-female"></i></span>`;
          $('#mobile_gender').html("Female " + genderIcon);
          subtitleParts.unshift("Female" + genderIcon);
        } else {
          $('#mobile_gender').text('');
        }

        // Subtitle (desktop only)
        const pxId = patient.px_id;
        let subtitleFinal = `Patient ID: ${pxId}`;
        if (subtitleParts.length > 0) {
          subtitleFinal += ' · ' + subtitleParts.join(' · ');
        }
        $('#viewPx_Subtitle').html(subtitleFinal);

        // Other fields
        $('#mobile_civstat').text(patient.px_civilstatus === "None" ? '' : patient.px_civilstatus);
        $('#mobile_cellnum').text(patient.px_cellnumber);
        $('#mobile_emailadd').text(patient.px_emailadd);
        $('#mobile_addr').text(patient.px_address);
        $('#mobile_hmo').text(patient.px_hmo === "None" ? '' : patient.px_hmo);
        $('#mobile_company').text(patient.px_company);

        // Desktop form fields
        setTimeout(() => {
          $('#viewpx_fname').val(patient.px_firstname);
          $('#viewpx_mname').val(patient.px_midname);
          $('#viewpx_lname').val(patient.px_lastname);
          $('#viewpx_suffix').val(patient.px_suffix);
          $('#viewpx_dob').val(patient.px_dob);
          $('#viewpx_gender').val(patient.px_gender);
          $('#viewpx_civstat').val(patient.px_civilstatus);
          $('#viewpx_cellnum').val(patient.px_cellnumber);
          $('#viewpx_emailadd').val(patient.px_emailadd);
          $('#viewpx_addr').val(patient.px_address);
          $('#viewpx_hmo').val(patient.px_hmo);
          $('#viewpx_company').val(patient.px_company);
        }, 50);
      }
    },
    error: function(xhr, ajaxoption, thrownerror) {
      console.log("Get Patient Details Error: " + xhr.status + " " + thrownerror);
    }
  });
}

// ------------------ Document Ready ------------------

$(document).ready(function(){
  let medhis_id = "";
  let apt_id = "";
  let presc_id = "";
  let labtest_id = "";
  let originalPtrValue = '';
  var today = new Date().toISOString().split('T')[0];
  // var today2 = new Date().toISOString().split('T')[0];

  // ✅ Auto-load patient info on first load (even before switching tabs)
  populatePatientInfo();
  console.log("Desktop input check:", $('#viewpx_fname').length ? "FOUND" : "NOT FOUND");

  // ✅ Load again if switching back to Patient Info tab
  $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
    const target = $(e.target).attr("href");
    if (target === '#nav-home') {
      populatePatientInfo();
    }
  });

  // ✅ Optional: toggle visibility when clicking Edit Info on mobile
  $('#editinfobtn').on('click', function () {
    $('#mobile_card_view').addClass('d-none');
    $('#edit_form_view').removeClass('d-none').addClass('d-block');
  });

  $('#cancel_edit_mobile').on('click', function () {
    $('#edit_form_view').removeClass('d-block').addClass('d-none');
    $('#mobile_card_view').removeClass('d-none');
  });

  // if ($("#mobile_card_view").length) {
  //   $('html, body').animate({
  //     scrollTop: $("#mobile_card_view").offset().top
  //   }, 300);
  // }

// ------------------ Other JS & DataTable Setup ------------------

// (Your DataTables, other modal handlers, etc., can follow here)




var tb_pres_history = $('#tb_pres_history').DataTable({  
  responsive: true,
  autoWidth: false,
  language: {
    emptyTable: "No prescription history found"
  },
  order: [],
  columnDefs: [
    {
      targets: 0,
      width: '50%',
      render: function(data, type, row) {
        if (type === 'display') {
          return data.split('\n').join('<br>');
        }
        return data;
      }
    },
    { targets: 1, width: '25%' },
    { targets: 2, width: '15%', className: 'text-center' },
    {
      targets: -1,
      data: null,
      width: '80px',
      className: 'text-center',
      defaultContent: "<button type='button' class='btn btn-outline-primary btn-sm view_preshistory'>View</button>"
    }
  ]
});

 function bulletListRenderer(data, type, row) {
  if (type === 'display') {
    if (!data || data.trim() === "") {
      return data;
    }
    return data.split('\n').map(line => '• ' + line).join('<br>');
  }
  return data;
}

// ✅ DataTable initialization for Medical History
var tb_medhistory = $('#tb_medhistory').DataTable({
  responsive: true,
  autoWidth: false,
  language: {
    emptyTable: "No medical history found"
  },
  order: [],
  columnDefs: [
    {
      targets: 0,
      width: '20%',
      render: bulletListRenderer
    },
    {
      targets: 1,
      width: '20%',
      render: bulletListRenderer
    },
    {
      targets: 2,
      width: '20%',
      render: bulletListRenderer
    },
    {
      targets: 3,
      width: '15%'
    },
    {
      targets: 4,
      width: '15%'
    },
    {
      targets: -1,
      width: '80px',
      className: 'text-center',
      data: null,
      defaultContent: "<button type='button' class='btn btn-outline-primary btn-sm view_medhistory'>View</button>"
    }
  ]
});

     var tb_apt_history = $('#tb_apt_history').DataTable({
    responsive: true,
    autoWidth: false,
        "language": {
          "emptyTable": "No appointment history found"
        },
        "order": [],
        columnDefs: [
          {
            data: null,
            defaultContent: "<button type='button' class='btn btn-outline-primary btn-sm view_apt_history'>View</button>",
            targets: -1,
            width: '80px',
            className: 'text-center'
          }
        ]
     });

var tb_lab = $('#tb_lab').DataTable({
  responsive: true,
  autoWidth: false,
  language: {
    emptyTable: "No lab test history found"
  },
  order: [],
  columnDefs: [
    {
      targets: -1,
      data: null,
      defaultContent: "<button type='button' class='btn btn-outline-primary btn-sm view_labtest_history'>View</button>",
      width: '80px',
      className: 'text-center'
    },
    {
      targets: 0,
      width: '45%'
    },
    {
      targets: 1,
      width: '30%'
    },
    {
      targets: 2,
      width: '15%',
      className: 'text-center'
    }
  ]
});

$('#tb_pxlist').on('click', '.view-px-btn', function () {
  const px_id = $(this).closest('tr').attr('id');

  // Redirect or open modal using px_id
  window.location.href = 'view_px.php?id=' + btoa(px_id);
});
  

    $('#labtest_request_date').datepicker({
        uiLibrary: 'bootstrap5',
        format: 'yyyy-mm-dd'
    });
    
    $('#labtest_folldate').datepicker({
        uiLibrary: 'bootstrap5',
        format: 'yyyy-mm-dd'
    });

    $('#edit_labtest_folldate').datepicker({
        uiLibrary: 'bootstrap5',
        format: 'yyyy-mm-dd'
    });    

    $('#edit_labtest_request_date').datepicker({
        uiLibrary: 'bootstrap5',
        format: 'yyyy-mm-dd'
    });
    
    $('#viewpx_dob').datepicker({
        uiLibrary: 'bootstrap5',
        format: 'yyyy-mm-dd'
    });

    $('#diagnosisDate').datepicker({
        uiLibrary: 'bootstrap5',
        format: 'yyyy-mm-dd'
    });
    $('#issuanceDate').datepicker({
      uiLibrary: 'bootstrap5',
      format: 'yyyy-mm-dd'
  });
    $('#viewDiagnosisDate').datepicker({
      uiLibrary: 'bootstrap5',
      format: 'yyyy-mm-dd'
  });
  $('#viewIssuanceDate').datepicker({
    uiLibrary: 'bootstrap5',
    format: 'yyyy-mm-dd'
});

    $('#appointmentDate').datepicker({
        uiLibrary: 'bootstrap5',
        footer: true, 
        modal: true
    });

    $('#appointmentStart').timepicker({
      uiLibrary: 'bootstrap5',
      footer: true,
      format: "hh:MM TT", 
      modal: true
  });
  $('#prescDate').datepicker({
    uiLibrary: 'bootstrap5',
    format: 'yyyy-mm-dd',
    footer: true, 
    modal: true
  });

  $('#view_prescDate').datepicker({
    uiLibrary: 'bootstrap5',
    format: 'yyyy-mm-dd',
    footer: true, 
    modal: true
  });
  $("#prescDate, #labtest_request_date, #diagnosisDate, #issuanceDate").val(today);
  $('#prescFollDate').datepicker({
    uiLibrary: 'bootstrap5',
    format: 'yyyy-mm-dd',
    footer: true, 
    modal: true
  });
  $('#view_prescFollDate').datepicker({
    uiLibrary: 'bootstrap5',
    format: 'yyyy-mm-dd',
    footer: true, 
    modal: true
  });
    $('#appointmentEnd').timepicker({
        uiLibrary: 'bootstrap5',
        footer: true, 
        format: "hh:MM TT",
        modal: true
    });

    $('#viewAppointmentDate').datepicker({
      uiLibrary: 'bootstrap5',
      format: 'yyyy-mm-dd'
  });

  $('#viewAppointmentStart').timepicker({
    uiLibrary: 'bootstrap5',
        footer: true,
        format: "hh:MM TT", 
        modal: true
  });
  $('#viewAppointmentEnd').timepicker({
    uiLibrary: 'bootstrap5',
    footer: true,
    format: "hh:MM TT", 
    modal: true
  });
//Appointment History View button//
    $(document).on('click', '.view_apt_history', function () {
  apt_id = $(this).closest('tr, .card').attr('id');

  $('#delete_appointment').data('id', apt_id);

  $.ajax({
    type: "GET",
    url: "get_appointment.php",
    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
    cache: false,
    dataType: "json",
    data: {
      apt_id: btoa(apt_id)
    },
    success: function(data) {
      if ($.isArray(data)) {
        $('#viewAppointmentDate').val(data[0].apt_date);
        $('#viewAppointmentStart').val(data[0].apt_start);
        $('#viewAppointmentEnd').val(data[0].apt_end);
        $('#viewReason').val(data[0].apt_reason);
        $('#viewAppointment').modal('show');
      }
    },
    error: function(xhr, ajaxoption, thrownerror) {
      console.log("Get Appointment History Error: " + xhr.status + " " + thrownerror);
    }
  });
});

  // Handle click to open prescription view modal
  $(document).on('click', '.view_preshistory', function () {
  const id = $(this).closest('tr, .card').attr('id');
  presc_id = id;
  console.log("Clicked View for ID:", id);

    // Reset form and show modal early to avoid flicker
    $('#viewPrescriptionForm')[0].reset();
    $('#viewPrescription').modal('show');

    $.ajax({
      type: "GET",
      url: "get_prescription.php",
      dataType: "json",
      data: { presc_id: btoa(id) },
      success: function (data) {
        if (Array.isArray(data) && data.length > 0) {
          const presc = data[0];
          const isRegulated = parseInt(presc.regulated) === 1;
          originalPtrValue = presc.ptr || '';

          // Populate fields
          $('#view_prescDate').val(presc.presc_date).prop('readonly', false);
          $('#view_prescription').val(presc.presc).prop('readonly', false);
          $('#view_prescFollDate').val(presc.presc_folldate).prop('readonly', false);
          $('#view_prescAddress').val(presc.px_address || '').prop('disabled', true);
          $('#dl_tag').attr('href', presc.fl);
          $('#view_prescPTR').val(originalPtrValue).prop('readonly', true);

          // Regulated logic
          if (isRegulated) {
            $('#view_includePtrToggle').addClass('d-none');
            $('#view_prescPTR').removeClass('d-none').show();
            $('#view_s2Wrapper').removeClass('d-none').show();
            $('#view_prescS2').val(presc.s2 || '').prop('readonly', true);
          } else {
            $('#view_includePtrToggle').removeClass('d-none').show();
            $('#view_ptrWrapper').removeClass('d-none').show();

            if (originalPtrValue !== '') {
            $('#view_includePtr').prop('checked', true);
            $('#view_prescPTR').removeClass('d-none').show().val(originalPtrValue);
          } else {
            $('#view_includePtr').prop('checked', false);
            $('#view_prescPTR').addClass('d-none').hide().val('');
          }

            $('#view_s2Wrapper').addClass('d-none').hide();
            $('#view_prescS2').val('');
          }

          // Regulated toggle status
          $('#view_isRegulated').prop('checked', isRegulated).prop('disabled', true);
          $('#view_regulatedStatus')
            .text(isRegulated ? 'YES' : '')
            .toggleClass('text-success', isRegulated)
            .toggleClass('text-danger', !isRegulated);
          $('#view_isRegulated_hidden').val(isRegulated ? '1' : '0');
        }
      },
      error: function () {
        alert("Error fetching prescription data.");
      }
    });
  });

  // Handle PTR checkbox toggle for non-regulated drugs
  $('#view_includePtr').on('change', function () {
  const ptrInput = $('#view_prescPTR');

  if (this.checked) {
    ptrInput.removeClass('d-none').show().prop('readonly', true);
    
    // If PTR is blank, try fetching the default one
    if (!ptrInput.val().trim()) {
      fetch('fetch_ptr_s2.php')
        .then(res => res.json())
        .then(data => {
          const ptr = data.ptr || '';
          ptrInput.val(ptr);
        });
    }
  } else {
    ptrInput.addClass('d-none').hide().val('');
  }
});

  // Reset modal on close
  $('#viewPrescription').on('hidden.bs.modal', () => {
  $('#viewPrescriptionForm')[0].reset();

  $('#view_ptrWrapper').removeClass('d-none').show();        // always show wrapper
  $('#view_prescPTR').addClass('d-none').hide().val('');
  $('#view_includePtrToggle').removeClass('d-none').show();  // always show toggle
  $('#view_includePtr').prop('checked', false);
  $('#view_s2Wrapper').addClass('d-none').hide();
});

// Labtest button (consistent with others)
$(document).on('click', '.view_labtest_history', function () {
  labtest_id = $(this).closest('tr, .card').attr('id');

  $('#viewEditLabTestModal').modal('show');

  $.ajax({
    type: "GET",
    url: "get_labtest.php",
    dataType: "json",
    data: { labtest_id: btoa(labtest_id) },
    success: function (data) {
      if ($.isArray(data) && data.length > 0) {
        const lab = data[0];

        $('#labtest_id').val(lab.labtest_id);
        $('#edit_otherTests').val(lab.labtest_others);
        $('#edit_labtest_clinical_impression').val(lab.labtest_clinical_impression);
        $('#edit_labtest_remarks').val(lab.labtest_remarks);
        $('#edit_labtest_request_date').val(lab.labtest_request_date);
        $('#edit_labtest_folldate').val(lab.labtest_folldate);
        $('#dload_tag').attr('href', lab.pdf_filename);

        $('#viewEditLabTestForm input[type="checkbox"]').prop('checked', false);

        if (lab.labtest_selected) {
          const selectedTests = lab.labtest_selected
            .split(',')
            .map(test => test.trim().toLowerCase());

          $('#viewEditLabTestForm input[type="checkbox"]').each(function () {
            if (selectedTests.includes($(this).val().trim().toLowerCase())) {
              $(this).prop('checked', true);
            }
          });
        }
      }
    },
    error: function (xhr, status, error) {
      console.error("Lab test fetch failed:", xhr.status, error);
    }
  });
});



// Med History View button
$(document).on('click', '.view_medhistory', function () {
  medhis_id = $(this).closest('tr, .card').attr('id');

  $.ajax({
    type: "GET",
    url: "get_diagnosis.php",
    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
    cache: false,
    dataType: "json",
    data: {
      medhis_id: btoa(medhis_id)
    },
    success: function (data) {
      if ($.isArray(data)) {
        $('#viewLabResult').val(data[0].medhis_labresult);
        $('#viewDiag').val(data[0].medhis_diagnosis);
        $('#viewMedication').val(data[0].medhis_medication);
        $('#viewRecom').val(data[0].medhis_recommendation);
        $('#viewDiagnosisDate').val(data[0].medhis_diagnosis_date);
        $('#viewIssuanceDate').val(data[0].medhis_issuance_date);
        $('#download_tag').attr('href', data[0].medhis_attachment);
        $('#viewDiagnosis').modal('show');
      }
    },
    error: function (xhr, ajaxoption, thrownerror) {
      console.log("Get Table Medical History Error: " + xhr.status + " " + thrownerror);
    }
  });
});

     $('#update_prescription').click(function () {
  // Temporarily enable all disabled fields in the form
  const $form = $('#viewPrescriptionForm');
  const disabledFields = $form.find(':input:disabled').removeAttr('disabled');

  // Serialize the form after enabling
  const formData = $form.serialize();
  const fullData = formData + '&presc_id=' + encodeURIComponent(presc_id);

  // Re-disable only specific fields if necessary (like address, S2, etc.)
  $('#view_prescAddress').prop('disabled', true);
  $('#view_isRegulated').prop('disabled', true);

  $.ajax({
    type: 'POST',
    url: 'update_prescription.php',
    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
    dataType: 'text',
    data: fullData,
    cache: false,
    success: function (data) {
      alert(data);
      location.reload();
    },
    error: function (xhr, ajaxoption, thrownerror) {
      alert(xhr.status + " " + thrownerror);
    }
  });
});

$('#delete_prescription').click(function(){
      if (confirm("Are you sure you want to delete this prescription?"))
        {
          $('#loadingModal').modal('show');
          $.ajax({
            type: "POST",
            url: "delete_prescription.php",
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            cache: false,
            data: {
              id: encodeURIComponent(presc_id)
            },
            success: function(data)
            {
              alert(data)
              location.reload();
            },
            error: function(xhr, ajaxoption, thrownerror)
            {
              console.log(xhr.status+" "+thrownerror)
            }
          })
        }
     });


     $('#generate_prescription').click(function(){
      if (confirm("Are you sure you want to generate and send this to patient's email?"))
      {
        $('#loadingModal').modal('show');
        $.ajax({
          type: "POST",
          url: "generate_prescription.php",
          contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
          cache: false,
          data: {
            id: encodeURIComponent(presc_id)
          },
          success: function(data)
          {
            alert(data);
            location.reload();
          },
          error: function(xhr, ajaxoption, thrownerror)
          {
            console.log(xhr.status+" "+thrownerror)
          }
        });
      }
     });

     

     $('#generate_medcert').click(function(){
        if (confirm("Are you sure you want to generate and send this to patient's email?"))
        {
          $('#loadingModal').modal('show');
          $.ajax({
            type: "POST",
            url: "generate_medcert.php",
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            cache: false,
            data:{
              medhis_id: encodeURIComponent(medhis_id)
            },
            success: function(data)
            {
              alert(data);
              location.reload();
            },
            error: function(xhr, ajaxoption, thrownerror)
            {
              console.log(xhr.status+" "+thrownerror)
            }
          });
        }
     });

     $('#generate_labtest').click(function(){
        if (confirm("Are you sure you want to generate and send this to patient's email?"))
        {
          $('#loadingModal').modal('show');
          $.ajax({
            type: "POST",
            url: "generate_labtest.php",
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            cache: false,
            data:{
              labtest_id: encodeURIComponent(labtest_id)
            },
            success: function(data)
            {
              alert(data);
              location.reload();
            },
            error: function(xhr, ajaxoption, thrownerror)
            {
              console.log(xhr.status+" "+thrownerror)
            }
          });
        }
     });     

     $('#delete_diagnosis').click(function(){
      if (confirm("Are you sure you want to delete this diagnosis?"))
        {
          $.ajax({
            type: "POST",
            url: "delete_diagnosis.php",
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            cache: false,
            data:{
              medhis_id: encodeURIComponent(medhis_id)
            },
            success: function(data)
            {
              alert(data);
              location.reload();
            },
            error: function(xhr, ajaxoption, thrownerror)
            {
              console.log(xhr.status+" "+thrownerror)
            }
          });
        }
     });
     
$('#delete_labtest').click(function(){
      if (confirm("Are you sure you want to delete this labtest?"))
        {
          $.ajax({
            type: "POST",
            url: "delete_labtest.php",
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            cache: false,
            data:{
              labtest_id: encodeURIComponent(labtest_id)
            },
            success: function(data)
            {
              alert(data);
              location.reload();
            },
            error: function(xhr, ajaxoption, thrownerror)
            {
              console.log(xhr.status+" "+thrownerror)
            }
          });
        }
     });



     $('#delete_appointment').click(function () {
      
      if (confirm("Are you sure you want to delete this appointment?"))
        {
          $('#loadingModal_del_apt').modal('show');
          $.ajax({
            type: "POST",
            url: "delete_appointment.php",
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            cache: false,
            data:{
              apt_id: encodeURIComponent(apt_id)
            },
            success: function(data)
            {
              alert(data);
              location.reload();
            },
            error: function(xhr, ajaxoption, thrownerror)
            {
              console.log(xhr.status+" "+thrownerror)
            }
          });
        }
     });

     $('#update_diagnosis').click(function(){
      var labresult = $('#viewLabResult').val();
      var diagnosis = $('#viewDiag').val();
      var medication = $('#viewMedication').val();
      var recommendation = $('#viewRecom').val();
      var diagdate = $('#viewDiagnosisDate').val();
      var issued_date = $('#viewIssuanceDate').val();
      $.ajax({
        type: "POST",
        url: "update_diagnosis.php",
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        cache: false,
        data: {
          medhis_id: encodeURIComponent(medhis_id),
          labresult: encodeURIComponent(labresult),
          diagnosis: encodeURIComponent(diagnosis),
          medication: encodeURIComponent(medication),
          recommendation: encodeURIComponent(recommendation),
          diagdate: encodeURIComponent(diagdate),
          issuancedate: encodeURIComponent(issued_date)
        },
        success: function(data)
        {
          alert(data)
          location.reload();
        },
        error: function(xhr, ajaxoption, thrownerror)
        {
          console.log(xhr.status+" "+thrownerror)
        }
      })
    });


    $('#update_labtest').click(function () {
  var labtest_request_date = $('#edit_labtest_request_date').val().trim();
  var labtest_clinical_impression = $('#edit_labtest_clinical_impression').val().trim();
  var labtest_remarks = $('#edit_labtest_remarks').val().trim();
  var labtest_folldate = $('#edit_labtest_folldate').val().trim();
  var labtest_others = $('#edit_otherTests').val().trim();

  // Collect checked lab tests
  var selectedTests = [];
  $("input[name='lab_tests[]']:checked").each(function () {
    selectedTests.push($(this).val());
  });

  $('#update_labtest').prop('disabled', true); // prevent double click

  $.ajax({
    type: "POST",
    url: "update_labtest.php",
    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
    cache: false,
    data: {
      labtest_id: labtest_id, // uses globally declared variable
      edit_labtest_request_date: labtest_request_date,
      edit_labtest_clinical_impression: labtest_clinical_impression,
      edit_labtest_remarks: labtest_remarks,
      edit_labtest_folldate: labtest_folldate,
      labtest_others: labtest_others,
      'lab_tests[]': selectedTests // no need to encode this array
    },
    success: function (response) {
      alert(response);
      location.reload();
    },
    error: function (xhr, ajaxOptions, thrownError) {
      console.log(xhr.status + " " + thrownError);
    }
  });
});



    $('#update_appointment').click(function(){
      var apt_date = $('#viewAppointmentDate').val();
      var apt_start = $('#viewAppointmentStart').val();
      var apt_end = $('#viewAppointmentEnd').val();
      var apt_reason = $('#viewReason').val();
      $('#loadingModal_upd_apt').modal('show');
      $.ajax({
        type: "POST",
        url: "update_appointment.php",
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        cache: false,
        data: {
          apt_date: apt_date,
          apt_start: apt_start,
          apt_end: apt_end,
          apt_reason: encodeURIComponent(apt_reason),
          apt_id: encodeURIComponent(apt_id)
        },
        success:function(data)
        {
          alert(data);
          location.reload();
        },
        error: function(xhr, ajaxoption, thrownerror)
        {
          console.log(xhr.status+" "+thrownerror)
        }
      });
    });
    

    // //Get PX Details to form
    // $.ajax({
    //     type: "GET",
    //     url: "get_px_details.php",
    //     contentType: "application/x-www-form-urlencoded; charset=UTF-8",
    //     cache: false,
    //     dataType: "json",
    //     data:{
    //         id: btoa(getQueryVariable('id'))
    //     },
    //     success: function(data)
    //     {
    //         if($.isArray(data))
    //         {
    //             $('#viewPx_Name').html(data[0].fullname)
    //             $('#addDiagnosisLabel').html("Add Diagnosis - " + data[0].fullname) //BUTTON TRIGGER FOR Add Functions//
    //             $('#viewDiagnosisLabel').html("View / Edit Diagnosis - " + data[0].fullname)                
    //             $('#bookAppointmentLabel').html("Book Appointment - " + data[0].fullname)
    //             $('#viewAppointmentLabel').html("View / Edit Appointment - " + data[0].fullname)                
    //             $('#addPrescriptionLabel').html("Add Prescription - " + data[0].fullname)
    //             $('#viewPrescriptionLabel').html("View / Edit Prescription - " + data[0].fullname)
    //             $('#requestLabTestLabel').html("Add Labs - " + data[0].fullname)
    //             $('#viewEditLabTestLabel').html("View / Edit Labs - " + data[0].fullname)
    //             $('#prescAddress').val(data[0].px_address)
    //             $('#view_prescAddress').val(data[0].px_address)
    //             $('#mobile_dob').text(data[0].px_dob);
    //             // Mobile card spans
    //             $('#mobile_fullname').html(data[0].fullname);
    //             $('#mobile_gender').text(data[0].px_gender);
    //             $('#mobile_civstat').text(data[0].px_civilstatus);
    //             $('#mobile_cellnum').text(data[0].px_cellnumber);
    //             $('#mobile_emailadd').text(data[0].px_emailadd);
    //             $('#mobile_addr').text(data[0].px_address);
    //             $('#mobile_hmo').text(data[0].px_hmo);
    //             $('#mobile_company').text(data[0].px_company);

    //             $('#viewpx_fname').val(data[0].px_firstname)
    //             $('#viewpx_mname').val(data[0].px_midname)
    //             $('#viewpx_lname').val(data[0].px_lastname)
    //             $('#viewpx_suffix').val(data[0].px_suffix)
    //             $('#viewpx_dob').val(data[0].px_dob)
    //             $('#viewpx_gender').val(data[0].px_gender)
    //             $('#viewpx_civstat').val(data[0].px_civilstatus)
    //             $('#viewpx_cellnum').val(data[0].px_cellnumber)
    //             $('#viewpx_emailadd').val(data[0].px_emailadd)
    //             $('#viewpx_addr').val(data[0].px_address)
    //             $('#viewpx_hmo').val(data[0].px_hmo)
    //             $('#viewpx_company').val(data[0].px_company)
    //         }
    //     },
    //     error: function(xhr, ajaxoption, thrownerror)
    //     {
    //         console.log("Get Patient Details Error: "+xhr.status+" "+thrownerror);
    //     }
    // });


});

// --- Add Prescription Modal (Vanilla JS) ---
document.addEventListener('DOMContentLoaded', () => {
  const isRegulatedToggle = document.getElementById('isRegulated');
  if (!isRegulatedToggle) return;

  const regulatedStatus = document.getElementById('regulatedStatus');
  const ptrField = document.getElementById('prescPTR');
  const s2Field = document.getElementById('prescS2');
  const includePtr = document.getElementById('includePtr');
  const includePtrToggle = document.getElementById('includePtrToggle');
  const s2Wrapper = document.getElementById('s2Wrapper');

  let savedPTR = '';
  let savedS2 = '';

  function forceRepaint(el) {
    el.style.display = 'none';
    el.offsetHeight;
    el.style.display = '';
  }

  function updateFields() {
    const isRegulated = isRegulatedToggle.checked;
    const includePtrChecked = includePtr.checked;

    regulatedStatus.textContent = isRegulated ? 'YES' : '';

    if (isRegulated) {
      ptrField.classList.remove('d-none');
      ptrField.readOnly = true;
      forceRepaint(ptrField);
      if (ptrField.value.trim() === '') ptrField.value = savedPTR;

      s2Wrapper.classList.remove('d-none');
      s2Field.readOnly = true;
      forceRepaint(s2Field);
      if (s2Field.value.trim() === '') s2Field.value = savedS2;

      includePtrToggle.style.display = 'none';
    } else {
      s2Wrapper.classList.add('d-none');
      s2Field.readOnly = false;
      s2Field.value = '';

      includePtrToggle.style.display = 'block';

      if (includePtrChecked) {
        ptrField.classList.remove('d-none');
        ptrField.readOnly = true;
        forceRepaint(ptrField);
        if (ptrField.value.trim() === '') ptrField.value = savedPTR;
      } else {
        ptrField.classList.add('d-none');
        ptrField.readOnly = false;
        forceRepaint(ptrField);
        ptrField.value = '';
      }
    }
  }

  isRegulatedToggle.addEventListener('change', updateFields);
  includePtr.addEventListener('change', updateFields);

  updateFields();

  document.getElementById('addPrescription').addEventListener('shown.bs.modal', () => {
    fetch('fetch_ptr_s2.php')
      .then(res => res.json())
      .then(data => {
        savedPTR = data.ptr || '';
        savedS2 = data.s2 || '';
        if (ptrField.value.trim() === '') ptrField.value = savedPTR;
        if (s2Field.value.trim() === '') s2Field.value = savedS2;
      })
      .catch(() => {
        savedPTR = '';
        savedS2 = '';
      });
  });
});

