$(document).ready(function(){
  $('#tb_pxlist').on('click', 'button', function(){
    var id = $(this).closest('tr').attr('id');
    url = "php/pxprofile/view_px.php?id="+id;
    window.open(url,'liveMatches','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=900');
  });
  $('#tb_acclist').on('click', 'button', function(){
    var id = $(this).closest('tr').attr('id');
    url = "php/createaccount/view_acc.php?id="+id;
    window.open(url,'liveMatches','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=900');
  });
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
  function isPHNumber(number)
  {
      var regex = /^(09|\+639)\d{9}$/
  		return regex.test(number);
  }

  $(document).on('click', '.btn-view-patient', function () {
  const pxId = $(this).data('id');

  $('#viewPatientModal').modal('show');
  $('#viewPatientContent').html('<div class="text-center">Loading...</div>');

  $.ajax({
    type: "GET",
    url: "get_px_details.php", // or your existing handler
    data: { id: btoa(pxId) },
    dataType: "json",
    success: function (data) {
      if (Array.isArray(data) && data.length) {
        const p = data[0];
        $('#viewPatientContent').html(`
          <p><strong>Name:</strong> ${p.fullname}</p>
          <p><strong>Birthdate:</strong> ${p.px_dob}</p>
          <p><strong>Gender:</strong> ${p.px_gender}</p>
          <p><strong>Address:</strong> ${p.px_address}</p>
          <!-- Add more fields as needed -->
        `);
      } else {
        $('#viewPatientContent').html('<p class="text-danger">No data found.</p>');
      }
    },
    error: function () {
      $('#viewPatientContent').html('<p class="text-danger">Failed to fetch patient details.</p>');
    }
  });
});

 
  $('#pills-logout-tab').click(function(){
    if (confirm("Are you sure you want to logout?"))
      {
        window.location.assign('logout.php');
      }
  });
  $('select[name="addacc_role"]').change(function(){
    var role = $(this).val();
    if (role == "1")
    {
      $('input[name="addacc_licno"]').val("##########");
    }
    else
    {
      $('input[name="addacc_licno"]').val("");
    }
  });
  $('#submit_acc').click(function(){
    var addaccform = $('#addacc-form').serializeArray();
    var serializedAddAccForm = addaccform.map(function(field) {
      return field.name + '=' + encodeURIComponent(field.value);
    }).join('&');
    $('#loadingModal_acc').modal('show');
    setTimeout(function(){
      $.ajax({
        type: "POST",
        url: "php/createaccount/submit_account.php",
        dataType: "text",
        contentType: "application/x-www-form-urlencoded; charset=UTF-8;",
        data: serializedAddAccForm,
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
    }, 2500);
  });
  $('#submit_px').click(function(){
      var fname = $('#addpx_fname').val();
      var mname = $('#addpx_mname').val();
      var lname = $('#addpx_lname').val();
      var addr = $('#addpx_addr').val();
      var suffix = $('#addpx_suffix option:selected').val();
      var dob = $('#addpx_dob').val();
      var gender = $('#addpx_gender option:selected').val();
      var civstat = $('#addpx_civstat option:selected').val();
      var cellnum = $('#addpx_cellnum').val();
      var emailadd = $('#addpx_emailadd').val();
      var hmo = $('#addpx_hmo option:selected').val();
      var company = $('#addpx_company').val();
      if (!isName(fname) || !isName(lname))
      {
        $('#BannerContent').html('First Name & Last Name should not be blank or contain invalid characters. Please try again.')
        $('#notifierBanner').modal('show');
      }
      else
      {
        $('#loadingModal').modal('show');
        setTimeout(function(){
          $.ajax({
              type: 'POST',
              url: 'php/pxprofile/submit_px.php',
              contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
              data:{
                fname: encodeURIComponent(fname),
                mname: encodeURIComponent(mname),
                lname: encodeURIComponent(lname),
                suffix: encodeURIComponent(suffix),
                addr: encodeURIComponent(addr),
                dob: encodeURIComponent(dob),
                gender: encodeURIComponent(gender),
                civstat: encodeURIComponent(civstat),
                cellnum: encodeURIComponent(cellnum),
                emailadd: encodeURIComponent(emailadd),
                hmo: encodeURIComponent(hmo),
                company: encodeURIComponent(company)
              },
              cache: false,
              success: function(data)
              {
                var res = data.split("||");
                if (res[1] == "1")
                {
                  $('#bannerBtn').click(function(){location.reload();});
                  $('#loadingModal').modal('hide');
                  $('#BannerContent').html(res[0]);
                  $('#notifierBanner').modal('show');
                }
                else if (res[0] == 0)
                {
                  $('#loadingModal').modal('hide');
                  $('#BannerContent').html(res[0]);
                  $('#notifierBanner').modal('show');
                }
                else
                {
                  $('#loadingModal').modal('hide');
                  $('#BannerContent').html(data);
                  $('#notifierBanner').modal('show');
                }
              },
              error: function(xhr, ajaxoption, thrownerror)
              {
                console.log(xhr.status+" "+thrownerror)
              }
            });
        }, 2500);
      }
  });
});