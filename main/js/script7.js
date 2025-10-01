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
  // $('#submit_px').click(function(){
  //     var fname = $('#addpx_fname').val();
  //     var mname = $('#addpx_mname').val();
  //     var lname = $('#addpx_lname').val();
  //     var addr = $('#addpx_addr').val();
  //     var suffix = $('#addpx_suffix option:selected').val();
  //     var dob = $('#addpx_dob').val();
  //     var gender = $('#addpx_gender option:selected').val();
  //     var civstat = $('#addpx_civstat option:selected').val();
  //     var cellnum = $('#addpx_cellnum').val();
  //     var emailadd = $('#addpx_emailadd').val();
  //     var hmo = $('#addpx_hmo option:selected').val();
  //     var company = $('#addpx_company').val();
  //     var is_pwd = $('#addpx_is_pwd').is(':checked') ? 1 : 0;
  //     if (!isName(fname) || !isName(lname))
  //     {
  //       $('#BannerContent').html('First Name & Last Name should not be blank or contain invalid characters. Please try again.')
  //       $('#notifierBanner').modal('show');
  //     }
  //     else
  //     {
  //       $('#loadingModal').modal('show');
  //       setTimeout(function(){
  //         $.ajax({
  //             type: 'POST',
  //             url: 'php/pxprofile/submit_px.php',
  //             contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
  //             data:{
  //               fname: encodeURIComponent(fname),
  //               mname: encodeURIComponent(mname),
  //               lname: encodeURIComponent(lname),
  //               suffix: encodeURIComponent(suffix),
  //               addr: encodeURIComponent(addr),
  //               dob: encodeURIComponent(dob),
  //               gender: encodeURIComponent(gender),
  //               civstat: encodeURIComponent(civstat),
  //               cellnum: encodeURIComponent(cellnum),
  //               emailadd: encodeURIComponent(emailadd),
  //               hmo: encodeURIComponent(hmo),
  //               company: encodeURIComponent(company),
  //               px_is_pwd: is_pwd
  //             },
  //             cache: false,
  //             success: function(data)
  //             {
  //               var res = data.split("||");
  //               if (res[1] == "1")
  //               {
  //                 $('#bannerBtn').click(function(){location.reload();});
  //                 $('#loadingModal').modal('hide');
  //                 $('#BannerContent').html(res[0]);
  //                 $('#notifierBanner').modal('show');
  //               }
  //               else if (res[0] == 0)
  //               {
  //                 $('#loadingModal').modal('hide');
  //                 $('#BannerContent').html(res[0]);
  //                 $('#notifierBanner').modal('show');
  //               }
  //               else
  //               {
  //                 $('#loadingModal').modal('hide');
  //                 $('#BannerContent').html(data);
  //                 $('#notifierBanner').modal('show');
  //               }
  //             },
  //             error: function(xhr, ajaxoption, thrownerror)
  //             {
  //               console.log(xhr.status+" "+thrownerror)
  //             }
  //           });
  //       }, 2500);
  //     }
  // });

// Block non-digit input and enforce 09 prefix
// 🔒 Block invalid input as you type
$('#addpx_cellnum').on('keypress', function (e) {
  const char = String.fromCharCode(e.which || e.keyCode);

  // Allow only digits
  if (!/^\d$/.test(char)) {
    e.preventDefault();
    return;
  }

  const current = $(this).val();
  const newValue = current + char;

  // Allow blank field
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

// 🧼 Clean pasted input
$('#addpx_cellnum').on('paste', function (e) {
  const pasteData = (e.originalEvent || e).clipboardData.getData('text');

  // Reject anything that is not exactly 11 digits starting with 09
  if (!/^09\d{9}$/.test(pasteData)) {
    e.preventDefault();
    alert('Paste must be exactly 11 digits and start with 09.');
  }
});

// 🟡 Optional: Live cleanup (remove accidental non-digits)
$('#addpx_cellnum').on('input', function () {
  let val = $(this).val().replace(/\D/g, '');
  if (val.length > 11) val = val.slice(0, 11);
  $(this).val(val);
});

});

document.addEventListener("DOMContentLoaded", function () {
  $('#addpx-form').on('submit', function (e) {
    e.preventDefault();

    if (!this.checkValidity()) {
      this.reportValidity();
      return;
    }

    const fname = $('#addpx_fname').val();
    const mname = $('#addpx_mname').val();
    const lname = $('#addpx_lname').val();
    const addr = $('#addpx_addr').val();
    const suffix = $('#addpx_suffix').val();
    const dob = $('#addpx_dob').val();
    const gender = $('#addpx_gender').val();
    let civstat = $('#addpx_civstat').val();
    civstat = (!civstat || civstat.trim() === "") ? null : civstat;

    const cellnum = $('#addpx_cellnum').val().trim();
    if (cellnum !== '' && !/^09\d{9}$/.test(cellnum)) {
      Swal.fire({
        icon: 'error',
        title: 'Invalid Cellphone Number',
        text: 'It must be exactly 11 digits and start with 09.',
        confirmButtonColor: '#d33'
      });
      return;
    }

    const emailadd = $('#addpx_emailadd').val();
    const hmo = $('#addpx_hmo').val();
    const company = $('#addpx_company').val();
    const is_pwd = $('#addpx_is_pwd').is(':checked') ? 1 : 0;

    $.ajax({
      type: 'POST',
      url: 'php/pxprofile/submit_px.php',
      contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
      data: {
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
        company: encodeURIComponent(company),
        px_is_pwd: is_pwd
      },
      cache: false,
      success: function (data) {
        const res = data.split("||");
        const message = res[0].trim();
        const isSuccess = res[1] === "1";

        Swal.fire({
          icon: isSuccess ? 'success' : 'error',
          title: isSuccess ? 'Patient Added' : 'Failed',
          text: message,
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
      }
    });
  });
});





