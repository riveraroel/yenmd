$(document).ready(function(){
    $('#atag').click(function(){
        var credsform = $('#login').serializeArray();
        var serializedCredsForm = credsform.map(function(field) {
            return field.name + '=' + encodeURIComponent(field.value);
          }).join('&');
		$.ajax({
            type: "POST",
            url: "php/changepass.php",
            dataType: "text",
            contentType: "application/x-www-form-urlencoded; charset=UTF-8;",
            data: serializedCredsForm,
            cache: false,
            success: function(data)
            {
                alert(data)
            },
            error: function(xhr, ajaxoption, thrownerror)
            {
                alert(xhr.status+" "+thrownerror);
            }
        })
	});

    function login(){
        $.ajax({
            type: "POST",
            url: "php/verify.php",
            dataType: "text",
            contentType: "application/x-www-form-urlencoded; charset=UTF-8;",
            data: $('#login').serialize(),
            cache: false,
            success: function(data)
            {
                if (data == "1")
                {
                    window.location.assign('main/');
                }
                else
                {
                    alert(data);
                }
            },
            error: function(xhr, ajaxoption, thrownerror)
            {
                alert(xhr.status+" "+thrownerror);
            }
        });
    }
    $(document).on('keydown', function(event) {
        if (event.keyCode === 13) {
          login();
        }
      });
    $('#submit').click(function(){
		login();
	});
})