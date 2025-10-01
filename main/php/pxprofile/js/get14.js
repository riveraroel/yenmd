// ------------------ Global Utility ------------------
let patientIsPWD = false;
let patientDobDate = null;

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

function toggleDesktopEditMode(editMode = true) {
  const fieldIds = [
    "fname", "mname", "lname", "suffix", "dob", "age", "pwd",
    "gender", "civstat", "cellnum", "emailadd", "addr", "hmo", "company"
  ];

  fieldIds.forEach(id => {
    $(`#desktop_${id}_span`).toggleClass("d-none", editMode);
    $(`#desktop_${id}_input`).toggleClass("d-none", !editMode);
  });

  $('#desktop_suffix_row').toggleClass('d-none', !editMode);

  // Update button
  $('#desktop_edit_btn')
    .html(editMode ? '<i class="bi bi-check-lg"></i>' : '<i class="bi bi-pencil-square"></i>')
    .attr('data-bs-title', editMode ? 'Update' : 'Edit')
    .attr('data-bs-toggle', 'tooltip')
    .removeAttr('title');

  // Cancel button
  $('#desktop_cancel_btn')
    .toggleClass('d-none', !editMode)
    .attr('data-bs-title', 'Cancel')
    .attr('data-bs-toggle', 'tooltip')
    .removeAttr('title');

  // Delete button
  $('#desktop_delete_btn')
    .toggleClass('d-none', !editMode)
    .attr('data-bs-title', 'Delete')
    .attr('data-bs-toggle', 'tooltip')
    .removeAttr('title');

  // Re-init tooltips ONLY on visible buttons
  $('#desktop_edit_btn:visible, #desktop_cancel_btn:visible, #desktop_delete_btn:visible').each(function () {
    const existing = bootstrap.Tooltip.getInstance(this);
    if (existing) existing.dispose();
    new bootstrap.Tooltip(this);
  });
}

function showToast(message, type = 'success') {
  const toastHtml = `
    <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  `;

  let container = $('#toast-container');
  if (!container.length) {
    $('body').append('<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3 z-1055"></div>');
    container = $('#toast-container');
  }

  const $toast = $(toastHtml);
  container.append($toast);
  new bootstrap.Toast($toast[0]).show();
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

function loadPatientSummary(id) {
  $.ajax({
    url: 'get_latest_activity.php',
    type: 'GET',
    data: { px_id: btoa(id) },
    dataType: 'json',
    success: function (data) {
      const sections = [
        { key: 'last_medhist', label: '#summary_medhist' },
        { key: 'last_prescription', label: '#summary_prescription' },
        { key: 'last_lab', label: '#summary_lab' },
        { key: 'last_appointment', label: '#summary_appointment' }
      ];

      sections.forEach(section => {
        const value = data[section.key];
        const display = formatShortDate(value);
        const tooltip = timeAgo(value);
        $(section.label).html(
          `<span data-bs-toggle="tooltip" title="${tooltip}">${display}</span>`
        );
      });

      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        const existing = bootstrap.Tooltip.getInstance(el);
        if (existing) existing.dispose();
        new bootstrap.Tooltip(el);
      });
    },
    error: function () {
      $('#summary_medhist, #summary_prescription, #summary_lab, #summary_appointment').text('Error');
    }
  });
}

function parseDateLocal(dateStr) {
  const [year, month, day] = dateStr.split('-').map(Number);
  return new Date(year, month - 1, day);
}

function formatShortDate(dateStr) {
  if (!dateStr) return 'None';
  const date = parseDateLocal(dateStr);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
}

function timeAgo(dateStr) {
  if (!dateStr) return '';
  const today = new Date();
  const date = parseDateLocal(dateStr);
  const diffMs = today - date;
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
  const diffMonths = Math.floor(diffDays / 30);

  if (diffDays === 0) return 'Today';
  if (diffDays === 1) return 'Yesterday';
  if (diffMonths >= 1) return `${diffMonths} month${diffMonths > 1 ? 's' : ''} ago`;
  return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
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
      if (!$.isArray(data)) return;

      const px = data[0];

      // 🔵 Full name for mobile and modal titles
      const fullName = [px.px_firstname, px.px_midname, px.px_lastname, px.px_suffix]
        .filter(Boolean)
        .join(' ');

      // 🔵 Set modal labels / headers
      $('#viewPx_Name').html(fullName);
      $('#addDiagnosisLabel').html("Add Diagnosis - " + fullName);
      $('#viewDiagnosisLabel').html("View / Edit Diagnosis - " + fullName);
      $('#bookAppointmentLabel').html("Book Appointment - " + fullName);
      $('#viewAppointmentLabel').html("View / Edit Appointment - " + fullName);
      $('#addPrescriptionLabel').html("Add Prescription - " + fullName);
      $('#viewPrescriptionLabel').html("View / Edit Prescription - " + fullName);
      $('#requestLabTestLabel').html("Add Labs - " + fullName);
      $('#viewEditLabTestLabel').html("View / Edit Labs - " + fullName);

      // 🔵 Age & DOB calculation
      let formattedDob = '';
      let ageShort = '';
      let ageYears = null;
      let ageMonths = null;

      const rawDob = px.px_dob;
      if (rawDob) {
        const [year, month, day] = rawDob.split('-');
        if (year && month && day) {
          const dobDate = new Date(year, month - 1, day);
          const today = new Date();

          ageYears = today.getFullYear() - dobDate.getFullYear();
          ageMonths = today.getMonth() - dobDate.getMonth();

          if (ageMonths < 0 || (ageMonths === 0 && today.getDate() < dobDate.getDate())) {
            ageYears--;
            ageMonths += 12;
          }

          ageShort = `${ageYears}y ${ageMonths}m`;
          formattedDob = dobDate.toLocaleDateString('en-US', {
            year: 'numeric', month: 'short', day: 'numeric'
          });
        }
      }

      // 🔵 Status icon
      let statusParts = [];
      if (ageYears >= 60) {
        statusParts.push(`<i class="bi bi-person-standing text-secondary" data-bs-toggle="tooltip" title="Senior"></i>`);
      }
      if (px.px_is_pwd == 1) {
        statusParts.push(`<span class="ms-1" data-bs-toggle="tooltip" title="PWD">♿</span>`);
      }
      const statusIcon = statusParts.join(' ');

      // 🔵 Gender icon
      let genderHtml = '';
      if (px.px_gender === "Male") {
        genderHtml = `Male <i class="bi bi-gender-male text-primary" data-bs-toggle="tooltip" title="Male"></i>`;
      } else if (px.px_gender === "Female") {
        genderHtml = `Female <i class="bi bi-gender-female" style="color: hotpink;" data-bs-toggle="tooltip" title="Female"></i>`;
      }

      // ✅ Subtitle for top navbar
      const subtitleParts = [];

      if (px.px_id) subtitleParts.push(`Patient ID: ${px.px_id}`);
      if (px.px_gender && px.px_gender !== "None") subtitleParts.push(genderHtml);
      if (formattedDob) {
        subtitleParts.push(`${formattedDob} (${ageShort}) ${statusIcon}`);
      } else if (statusIcon) {
        subtitleParts.push(statusIcon);
      }

      $('#viewPx_Subtitle').html(subtitleParts.join(' · '));

      // ✅ DESKTOP: View + Input
      function setDesktopField(fieldId, value) {
        if (fieldId === "pwd") {
          const isPWD = value == 1 || value === true || value === "1";
          $('#desktop_pwd_span').html(
            isPWD
              ? `<span data-bs-toggle="tooltip" title="PWD">Yes ♿</span>`
              : 'No'
          );
          $('#desktop_is_pwd_checkbox').prop('checked', isPWD);

        } else if (fieldId === "dob") {
          $('#desktop_dob_span').html(formattedDob || '');
          $('#desktop_dob_input').val(px.px_dob || '');

        } else if (fieldId === "age") {
          let ageDisplay = value || '';
          if (ageYears >= 60) {
            ageDisplay += ` <i class="bi bi-person-standing text-secondary" data-bs-toggle="tooltip" title="Senior"></i>`;
          }
          $('#desktop_age_span').html(ageDisplay);
          $('#desktop_age_input').val(value || '');

        } else if (fieldId === "gender") {
          const displayVal = typeof value === 'object' ? value.display : value;
          const rawVal = typeof value === 'object' ? value.raw : value;
          $('#desktop_gender_span').html(displayVal || '');
          $('#desktop_gender_input').val(rawVal || '');

        } else if (fieldId === "hmo") {
          const displayVal = typeof value === 'object' ? value.display : value;
          const rawVal = typeof value === 'object' ? value.raw : value;
          $('#desktop_hmo_span').html(displayVal || '');
          $('#desktop_hmo_input').val(rawVal || '');

        } else if (fieldId === "civstat") {
          const displayVal = typeof value === 'object' ? value.display : value;
          const rawVal = typeof value === 'object' ? value.raw : value;
          $('#desktop_civstat_span').html(displayVal || '');
          $('#desktop_civstat_input').val(rawVal || '');

        } else {
          $(`#desktop_${fieldId}_span`).html(value || '');
          $(`#desktop_${fieldId}_input`).val(value || '');
        }

        // ✅ Re-enable Bootstrap tooltips
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
          const existing = bootstrap.Tooltip.getInstance(el);
          if (existing) existing.dispose();
          new bootstrap.Tooltip(el);
        });
      }

      setDesktopField("fname", px.px_firstname);
      setDesktopField("mname", px.px_midname);
      setDesktopField("lname", px.px_lastname);
      setDesktopField("suffix", px.px_suffix);
      setDesktopField("dob", formattedDob);
      setDesktopField("age", ageShort);
      setDesktopField("pwd", px.px_is_pwd); // ✅ raw value only (1 or 0)
      setDesktopField("gender", {
        display: genderHtml,
        raw: px.px_gender
      });
      setDesktopField("civstat", {
        display: px.px_civilstatus === "None" ? '' : px.px_civilstatus,
        raw: px.px_civilstatus
      });
      setDesktopField("cellnum", px.px_cellnumber);
      setDesktopField("emailadd", px.px_emailadd);
      setDesktopField("addr", px.px_address);
      setDesktopField("hmo", {
        display: px.px_hmo === "None" ? '' : px.px_hmo,
        raw: px.px_hmo
      });
      setDesktopField("company", px.px_company);
      const px_id = px.px_id;
      loadPatientSummary(px_id);
    
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

let isInEditMode = false;

// ✅ DESKTOP: EDIT/UPDATE BUTTON
$('#desktop_edit_btn').on('click', function () {
  console.log("🟢 Edit/Update button clicked");
  if (!isInEditMode) {
    isInEditMode = true;
    toggleDesktopEditMode(true);
    return;
  }

  console.log("🟢 Submitting form...");

  const updatedData = {
    px_id: getQueryVariable('id'),
    fname: $('#desktop_fname_input').val(),
    mname: $('#desktop_mname_input').val(),
    lname: $('#desktop_lname_input').val(),
    suffix: $('#desktop_suffix_input').val(),
    dob: $('#desktop_dob_input').val(),
    is_pwd: $('#desktop_is_pwd_checkbox').is(':checked') ? 1 : 0,
    gender: $('#desktop_gender_input').val(),
    civstat: $('#desktop_civstat_input').val(),
    cellnum: $('#desktop_cellnum_input').val(),
    emailadd: $('#desktop_emailadd_input').val(),
    addr: $('#desktop_addr_input').val(),
    hmo: $('#desktop_hmo_input').val(),
    company: $('#desktop_company_input').val()
  };

  $.ajax({
    type: 'POST',
    url: 'update_px.php',
    data: updatedData,
    success: function (response) {
      console.log("Update response:", response);

      if (typeof response === 'string' && response.includes('already existing')) {
        showToast(response, 'danger');
        return;
      }

      if (typeof response === 'string' && response.includes('No patient records updated')) {
        showToast(response, 'warning');
        return;
      }

      showToast('Patient info updated successfully', 'success');
      populatePatientInfo();
      isInEditMode = false;
      toggleDesktopEditMode(false);
    },
    error: function (xhr, status, error) {
      console.error("Update failed:", xhr.status, error);
      showToast('Failed to update patient info', 'danger');
    }
  });
});

$('#desktop_delete_btn').on('click', function () {
  const pxId = getQueryVariable('id');

  if (!pxId) {
    alert("Missing patient ID.");
    return;
  }

  if (!confirm("Are you sure you want to delete this patient?")) {
    return;
  }

  $.ajax({
    type: "POST",
    url: "delete_px.php",
    data: { px_id: pxId },
    success: function (data) {
      alert(data); // ✅ Show response from PHP
      window.close(); // ✅ Close current popup
      if (window.opener && !window.opener.closed) {
        window.opener.location.reload(false); // ✅ Refresh parent
      }
    },
    error: function (xhr, status, error) {
      alert("Error deleting patient.");
      console.error("Delete error:", xhr.status, error);
    }
  });
});

$('#desktop_cancel_btn').on('click', function () {
  isInEditMode = false;
  toggleDesktopEditMode(false);
});

const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

// ------------------ Other JS & DataTable Setup ------------------

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

  // ✅ Add Prescription Modal logic
  document.getElementById('addPrescription').addEventListener('shown.bs.modal', () => {
    // Fetch PTR and S2
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

  // Auto-toggle PTR based on age or PWD
  const ptrCheckbox = document.getElementById('includePtr');
  const ptrNotice = document.getElementById('ptrNotice');

  let isSenior = false;

  if (patientDobDate instanceof Date) {
    const today = new Date();
    let age = today.getFullYear() - patientDobDate.getFullYear();
    const m = today.getMonth() - patientDobDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < patientDobDate.getDate())) {
      age--;
    }
    isSenior = age >= 60;
  }

  // 🔁 Separate PWD from Senior logic
  if (isSenior || patientIsPWD) {
    ptrCheckbox.checked = true;
    ptrCheckbox.dispatchEvent(new Event('change'));

    // 🔔 Dynamic notice
    const reasons = [];
    if (isSenior) reasons.push('Senior');
    if (patientIsPWD) reasons.push('PWD');

    ptrNotice.textContent = `PTR auto-applied for ${reasons.join(' + ')}`;
    ptrNotice.classList.remove('d-none');
  } else {
    ptrCheckbox.checked = false;
    ptrNotice.classList.add('d-none');
  }
});

document.getElementById('includePtr').addEventListener('change', function () {
  const ptrNotice = document.getElementById('ptrNotice');
  if (this.checked) {
    ptrNotice.classList.remove('d-none');
  } else {
    ptrNotice.classList.add('d-none');
  }
});
});