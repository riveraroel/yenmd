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
    $('#tb_issued_diagnosis, #tb_issued_appointment, #tb_issued_prescription').on('click', 'button', function(){
      var id = $(this).closest('tr').attr('id');
      url = "../../php/pxprofile/view_px.php?id="+id;
      window.open(url,'liveMatches','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=900');
    });
    $('#update_acc').click(function(){
        var acc_id = 'acc_id='+getQueryVariable('id');
        var acc_form = $('#viewacc-form').serializeArray();
        var serializedAccForm = acc_form.map(function(field) {
            return field.name + '=' + encodeURIComponent(field.value);
          }).join('&');
          serializedAccForm += '&' + acc_id;
        $.ajax({
            type: 'POST',
            url: 'update_account.php',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            dataType: 'text',
            data: serializedAccForm,
            cache: false,
            success: function(data)
            {
                alert(data)
                location.reload()
            },
            error: function(xhr, ajaxoption, thrownerror)
            {
                alert(xhr.status+" "+thrownerror);
            }
        });
    });

    $('#delete_acc').click(function(){
        if(confirm("Are you sure you want to delete this account?"))
        {
            $.ajax({
                type: "POST",
                url: "delete_account.php",
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                data:{
                  acc_id: encodeURIComponent(getQueryVariable('id'))
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
});