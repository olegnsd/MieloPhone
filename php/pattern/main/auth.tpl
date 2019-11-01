<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Звонок</title>

    <!-- Bootstrap Core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>


    <script>
	$(function() {
	    $('[name="code_sms"]').click(function() {
		$("#alert_dngr").addClass("hidden");
		    $.post( "/getcode", {login: $('[name="login"]').val()}, function( data ) {
			if (!data.success) {
			    $("#alert_dngr").html(data.error);
			    $("#alert_dngr").removeClass("hidden");
			}
			else {
			    $('[name="code_sms"]').prop("disabled", true);
			    $('#code_sms').html("На Ваш телефон отправлено смс с кодом");
                $("#code_sms").removeClass("hidden");
			}
		    }, "json").always(function(data) {
//                alert( JSON.stringify(data) );
              });
	    });
        $('[name="auth"]').click(function() {
		    $.post( "/auth", {login: $('[name="login"]').val(), code: $('[name="code"]').val()}, function( data ) {
			if (!data.success) {
			    $("#alert_dngr").html(data.error);
			    $("#alert_dngr").removeClass("hidden");
			}
			else {
			    window.location.href="/";
			}
		    }, "json");
		});
	});
    </script>

</head>

<body>
    <div id="wrapper">
      <form class="form-signin" method="post">
        <h2 class="form-signin-heading">Авторизация</h2>
        <div class="alert alert-danger hidden" id="alert_dngr">
            {!AUTH_ERROR!}
        </div>
        <input type="text" name="login" class="form-control input-block-level" placeholder="Логин">
        <input type="text" name="code" class="form-control input-block-level" placeholder="Код">
        <button class="btn btn-large btn-success btn-block" type="button" name="auth">Войти</button>
        <label>Забыли код?</label><button class="btn btn-large btn-primary btn-block" type="button" name="code_sms">Выслать новый код</button>
          <div class="alert alert-success hidden" id="code_sms"></div>
      </form>
    </div>
</body>
</html>
