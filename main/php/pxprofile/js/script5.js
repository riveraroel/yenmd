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
  const otherTestValue = $('#otherTests').val().trim(); // Adjust selector if needed

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
      alert("Lab Test Request submitted successfully!");
      location.reload();
    },
    error: function (xhr, ajaxoption, thrownerror) {
      alert(xhr.status + " " + thrownerror);
    },
    complete: function () {
      $('#submit_labtest').prop('disabled', false);
    }
  });
});








//$('#submit_prescription').click(function(){
 //     var px_id = 'px_id='+getQueryVariable('id');
 //     var addpresc = $('#addPrescriptionForm').serializeArray();
 //     var serializedaddPrescriptionForm = addpresc.map(function(field) {
 //       return field.name + '=' + encodeURIComponent(field.value);
 //     }).join('&');
 //     serializedaddPrescriptionForm += '&' + px_id;
 //     $.ajax({
  //      type: 'POST',
  //      url: 'submit_prescription.php',
 //       contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
    //    dataType: 'text',
    //    data: serializedaddPrescriptionForm,
   //     cache: false,
    //    success: function(data)
   //     {
  //        alert(data);
 //         location.reload();
 //       },
  //      error: function(xhr, ajaxoption, thrownerror)
   //     {
  //        alert(xhr.status+" "+thrownerror);
 //       }
 //     });









    $('#delete_px').click(function(){
      if (confirm("Are you sure you want to delete this patient?"))
      {
        $.ajax({
          type: "POST",
          url: "delete_px.php",
          contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
          data:{
            px_id: encodeURIComponent(getQueryVariable('id'))
          },
          cache: false,
          success: function(data)
          {
            alert(data);
            window.close();
            window.opener.location.reload(false);
          },
          error: function(xhr, ajaxoption, thrownerror)
          {
            console.log(xhr.status+" "+thrownerror)
          }
        })
      }
    });
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
        if (isEmpty(px_id) || !isName(fname) || !isName(lname))
        {
          alert("Names should not be blank/invalid. Please try again.")
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
              hmo: encodeURIComponent(hmo)
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
    $('#submit_prescription').click(function(){
      var px_id = 'px_id='+getQueryVariable('id');
      var addpresc = $('#addPrescriptionForm').serializeArray();
      var serializedaddPrescriptionForm = addpresc.map(function(field) {
        return field.name + '=' + encodeURIComponent(field.value);
      }).join('&');
      serializedaddPrescriptionForm += '&' + px_id;
      $.ajax({
        type: 'POST',
        url: 'submit_prescription.php',
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        dataType: 'text',
        data: serializedaddPrescriptionForm,
        cache: false,
        success: function(data)
        {
          alert(data);
          location.reload();
        },
        error: function(xhr, ajaxoption, thrownerror)
        {
          alert(xhr.status+" "+thrownerror);
        }
      });
    });
    $('#submit_appointment').click(function(){
      var appointmentDate = $('#appointmentDate').val();
      var appointmentStart = $('#appointmentStart').val();
      var appointmentEnd = $('#appointmentEnd').val();
      var reason = $('#reason').val();

      if ( appointmentDate == '' || appointmentStart == '' || appointmentEnd == '' || reason == '')
      {
        alert("Invalid booking details. Please try again");
      }
      else
      {
        $('#loadingModal_apt').modal('show');
        $.ajax({
          type: 'POST',
          url: 'submit_appointment.php',
          contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
          data: {
            appointmentDate: appointmentDate,
            appointmentStart: appointmentStart,
            appointmentEnd: appointmentEnd,
            reason: encodeURIComponent(reason),
            px_id: getQueryVariable('id')
          },
          cache: false,
          success: function(data)
          {
            if (data == "X")
            {
              alert("Invalid time");
            }
            else
            {
              alert(data);
              location.reload();
            }
          },
          error: function(xhr, ajaxoption, thrownerror)
          {
            console.log(xhr.status+" "+thrownerror)
          }
        });
      }
    });

     $('#submit_diagnosis').click(function(){
      var labresult = $('#labresult').val();
      var diag = $('#diag').val();
      var medication = $('#medication').val();
      var recom = $('#recom').val();
      var diagnosisDate = $('#diagnosisDate').val();
      var issuanceDate = $('#issuanceDate').val();
      if (issuanceDate == '' || diagnosisDate == '')
      {
        alert("Date of issuance or Date of diagnosis is required. Please try again.");
      }
      else
      {
        $.ajax({
          type: 'POST',
          url: 'submit_diagnosis.php',
          contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
          data:{
            labresult: encodeURIComponent(labresult),
            diag: encodeURIComponent(diag),
            medication: encodeURIComponent(medication),
            recom: encodeURIComponent(recom),
            diagdate: encodeURIComponent(diagnosisDate),
            issuanceDate: encodeURIComponent(issuanceDate),
            px_id: getQueryVariable('id')
          },
          cache: false,
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
});