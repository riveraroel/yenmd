$(document).ready(function(){
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
    function isName(name)
    {
      var regex = /^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$/;
      return regex.test(name);
    }
    function isEmail(email) 
    {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }
    function isEmpty(xvar)
    {
      if (xvar == '' || xvar == null || xvar === undefined)
      {
        return true;
      }
    }
    function isPHNumber(number)
    {
        var regex = /^(09|\+639)\d{9}$/
        return regex.test(number);
    }

$('#submit_labtest').click(function (e) {
  e.preventDefault();

  const requestDate = $('#labtest_request_date').val();
  const selectedTests = $("input[name='lab_tests[]']:checked").length;
  const otherTestValue = $('#otherTests').val().trim();

  if (!requestDate) {
    alert("Please select a request date.");
    return;
  }
  if (selectedTests === 0 && otherTestValue === '') {
    alert("Please select at least one lab test or fill in the Other Lab Tests field.");
    return;
  }

  const px_id_value = getQueryVariable('id');
  const labtestForm = $('#addLabTestForm').serializeArray();
  labtestForm.push({ name: 'px_id', value: px_id_value });

  const serializedLabTestForm = $.param(labtestForm);
  console.log(serializedLabTestForm);

  $('#submit_labtest').prop('disabled', true);

  $.ajax({
    type: 'POST',
    url: 'submit_labtest.php',
    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
    dataType: 'text',
    data: serializedLabTestForm,
    cache: false,
    success: function (data) {
      const trimmed = data.trim().toLowerCase();
      const isSuccess = trimmed.includes('successfully');

      Swal.fire({
        icon: isSuccess ? 'success' : 'error',
        title: isSuccess ? 'Lab Test Saved' : 'Failed',
        text: data,
        confirmButtonColor: '#3085d6'
      }).then(() => {
        if (isSuccess) location.reload();
      });
    },
    error: function (xhr, ajaxoption, thrownerror) {
      Swal.fire({
        icon: 'error',
        title: 'Server Error',
        text: xhr.status + " " + thrownerror,
        confirmButtonColor: '#d33'
      });
    },
    complete: function () {
      $('#submit_labtest').prop('disabled', false);
    }
  });
});

// Replaced by #desktop_delete_btn on get.js
// $('#delete_px').click(function () {
//   Swal.fire({
//     title: 'Are you sure?',
//     text: 'This patient record will be permanently deleted.',
//     icon: 'warning',
//     showCancelButton: true,
//     confirmButtonColor: '#d33',
//     cancelButtonColor: '#6c757d',
//     confirmButtonText: 'Yes, delete it!',
//     cancelButtonText: 'Cancel'
//   }).then((result) => {
//     if (result.isConfirmed) {
//       $.ajax({
//         type: "POST",
//         url: "delete_px.php",
//         contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
//         data: {
//           px_id: encodeURIComponent(getQueryVariable('id'))
//         },
//         cache: false,
//         success: function (data) {
//           Swal.fire({
//             icon: 'success',
//             title: 'Deleted!',
//             text: data
//           }).then(() => {
//             window.close();
//             window.opener.location.reload(false);
//           });
//         },
//         error: function (xhr, ajaxoption, thrownerror) {
//           Swal.fire({
//             icon: 'error',
//             title: 'Failed!',
//             text: xhr.status + " " + thrownerror
//           });
//         }
//       });
//     }
//   });
// });


    $('#update_px').click(function(){
      var px_id = getQueryVariable('id');
      var fname = $('#viewpx_fname').val();
      var mname = $('#viewpx_mname').val();
      var lname = $('#viewpx_lname').val();
      var suffix = $('#viewpx_suffix option:selected').val();
      var dob = $('#viewpx_dob').val();
      var addr = $('#viewpx_addr').val();
      var gender = $('#viewpx_gender option:selected').val();
      var civstat = $('#viewpx_civstat option:selected').val();
      var cellnum = $('#viewpx_cellnum').val();
      var emailadd = $('#viewpx_emailadd').val();
      var company = $('#viewpx_company').val();
      var hmo = $('#viewpx_hmo option:selected').val();
      var is_pwd = $('#viewpx_is_pwd').is(':checked') ? 1 : 0;
        if (isEmpty(px_id) || !isName(fname) || !isName(lname))
        {
          alert("First Name & Last Name should not be blank or contain invalid characters. Please try again.")
        }
        else
        {
          $.ajax({
            type: "POST",
            url: "update_px.php",
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            data:{
              px_id: encodeURIComponent(px_id),
              fname: encodeURIComponent(fname),
              mname: encodeURIComponent(mname),
              lname: encodeURIComponent(lname),
              suffix: encodeURIComponent(suffix),
              dob: encodeURIComponent(dob),
              addr: encodeURIComponent(addr),
              gender: encodeURIComponent(gender),
              civstat: encodeURIComponent(civstat),
              cellnum: encodeURIComponent(cellnum),
              emailadd: encodeURIComponent(emailadd),
              company: encodeURIComponent(company),
              hmo: encodeURIComponent(hmo),
              is_pwd: is_pwd
            },
            cache: false,
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
    
$('#submit_prescription').click(function () {
  var px_id = 'px_id=' + getQueryVariable('id');
  var formData = $('#addPrescriptionForm').serialize();
  formData += '&' + px_id;

  $.ajax({
    type: 'POST',
    url: 'submit_prescription.php',
    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
    dataType: 'text',
    data: formData,
    cache: false,
    success: function (data) {
      const trimmed = data.trim().toLowerCase();
      const isSuccess = trimmed.includes('successfully');

      Swal.fire({
        icon: isSuccess ? 'success' : 'error',
        title: isSuccess ? 'Prescription Saved' : 'Failed',
        text: data,
        confirmButtonColor: '#3085d6'
      }).then(() => {
        if (isSuccess) location.reload();
      });
    },
    error: function (xhr, ajaxOption, thrownError) {
      Swal.fire({
        icon: 'error',
        title: 'Server Error',
        text: xhr.status + ' ' + thrownError,
        confirmButtonColor: '#d33'
      });
    }
  });
});

$('#submit_appointment').click(function () {
  const appointmentDate = $('#appointmentDate').val();
  const appointmentStart = $('#appointmentStart').val();
  const appointmentEnd = $('#appointmentEnd').val();
  const reason = $('#reason').val().trim();
  const px_id = getQueryVariable('id');

  if (!appointmentDate || !appointmentStart || !appointmentEnd || !reason) {
    Swal.fire({
      icon: 'warning',
      title: 'Missing Information',
      text: 'Please fill out all required appointment details.'
    });
    return;
  }

  Swal.fire({
    title: 'Booking Appointment...',
    html: 'Please wait while we save the appointment and send the confirmation email.',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  $.ajax({
    type: 'POST',
    url: 'submit_appointment.php',
    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
    data: {
      appointmentDate: appointmentDate,
      appointmentStart: appointmentStart,
      appointmentEnd: appointmentEnd,
      reason: encodeURIComponent(reason),
      px_id: px_id
    },
    success: function (data) {
      Swal.close();

      let response;
      try {
        response = JSON.parse(data);
      } catch (e) {
        response = { message: data.trim(), email_sent: false };
      }

      let alertText = response.message;
      if (response.email_sent) {
        alertText += "\n\n📧 A confirmation email has been sent to the patient.";
      }

      Swal.fire({
        icon: 'success',
        title: 'Appointment Booked',
        text: alertText
      }).then(() => {
        location.reload();
      });
    },
    error: function (xhr, ajaxoption, thrownerror) {
      Swal.close();
      Swal.fire({
        icon: 'error',
        title: 'Request Failed',
        text: xhr.status + " " + thrownerror
      });
    }
  });
});

$('#submit_diagnosis').click(function () {
  var labresult = $('#labresult').val();
  var diag = $('#diag').val();
  var medication = $('#medication').val();
  var recom = $('#recom').val();
  var diagnosisDate = $('#diagnosisDate').val();
  var issuanceDate = $('#issuanceDate').val();

  if (issuanceDate === '' || diagnosisDate === '') {
    Swal.fire({
      icon: 'warning',
      title: 'Missing Date',
      text: 'Date of issuance or Date of diagnosis is required. Please try again.',
      confirmButtonColor: '#d33'
    });
  } else {
    $.ajax({
      type: 'POST',
      url: 'submit_diagnosis.php',
      contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
      data: {
        labresult: encodeURIComponent(labresult),
        diag: encodeURIComponent(diag),
        medication: encodeURIComponent(medication),
        recom: encodeURIComponent(recom),
        diagdate: encodeURIComponent(diagnosisDate),
        issuanceDate: encodeURIComponent(issuanceDate),
        px_id: getQueryVariable('id')
      },
      cache: false,
      success: function (data) {
        const trimmed = data.trim().toLowerCase();
        const isSuccess = trimmed.includes('added');

        Swal.fire({
          icon: isSuccess ? 'success' : 'error',
          title: isSuccess ? 'Diagnosis Saved' : 'Failed',
          text: data,
          confirmButtonColor: '#3085d6'
        }).then(() => {
          if (isSuccess) location.reload();
        });
      },
      error: function (xhr, ajaxoption, thrownerror) {
        Swal.fire({
          icon: 'error',
          title: 'Server Error',
          text: `${xhr.status} ${thrownerror}`,
          confirmButtonColor: '#d33'
        });
      }
    });
  }
});

// 🔒 Block non-digit input on keystroke
$('#desktop_cellnum_input').on('keypress', function (e) {
  const char = String.fromCharCode(e.which || e.keyCode);

  // Allow digits only
  if (!/^\d$/.test(char)) {
    e.preventDefault();
    return;
  }

  const current = $(this).val();
  const newValue = current + char;

  // Allow empty, but if typing...
  if (newValue.length === 1 && char !== '0') {
    e.preventDefault();
    return;
  }

  if (newValue.length === 2 && newValue !== '09') {
    e.preventDefault();
    return;
  }

  if (newValue.length > 11) {
    e.preventDefault();
  }
});

// 🧼 Paste cleanup
$('#desktop_cellnum_input').on('paste', function (e) {
  const pasteData = (e.originalEvent || e).clipboardData.getData('text');

  if (!/^09\d{9}$/.test(pasteData)) {
    e.preventDefault();
    alert('Paste must be exactly 11 digits and start with 09.');
  }
});

// 🟡 Optional live cleanup (just to strip any non-digits if user tries to cheat)
$('#desktop_cellnum_input').on('input', function () {
  let val = $(this).val().replace(/\D/g, '');
  if (val.length > 11) val = val.slice(0, 11);
  $(this).val(val);
});

});