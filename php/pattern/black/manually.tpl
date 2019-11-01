<!-- <link href="/css/add_manually.css" rel="stylesheet"> -->
<head>
	<link href="/css/bootstrap.min.css" rel="stylesheet">
</head>

<br>
<div class="container">
	<div class="row">
		<div class="col-md-9 col-sm-9 col-xs-9">
			<a href="/black" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Назад</a>
		</div>
		<br><br>
		<div class="col-md-9 col-sm-9 col-xs-9">
			{!ALERT!}
		</div>
			<!-- <form method="post" action="{!BUTTONS1!}" enctype="multipart/form-data"> -->
			    <!-- <div class="form-group"> -->
			        <!-- <label>Добавить телефон в черный список</label>
			        <br> -->
			        <!-- <input type="tel" value="" name="phone" class="form-control" pattern="2[0-9]{3}-[0-9]{3}" placeholder="9260000000"/> -->
			       <div class="col-md-9 col-sm-9 col-xs-9">
			        <a href="{!BUTTONS1!}" type="button" class="btn btn-default" >Добавить телефон в ручной черный список</a>
			       </div>
			       <br><br>
			       <div class="col-md-9 col-sm-9 col-xs-9">
			        <a href="{!BUTTONS2!}" type="button" class="btn btn-default" >Найти телефон</a>
			       </div>
			       <br><br>
			       <div class="col-md-9 col-sm-9 col-xs-9">
				    <div class="input-group">
				      <span class="input-group-btn">
				        <button class="btn btn-default" type="submit" form="pass_black">Показать все телефоны</button>
				      </span>
				      <input type="text" class="form-control" name="pass_black" form="pass_black" placeholder="Введите пароль">
				    </div><!-- /input-group -->
				   </div>
					
			        <!-- <a href="{!BUTTONS3!}" type="button" class="btn btn-default" >Показать все телефоны</a> -->
			    <!-- </div> -->
			<!-- </div> -->

			    <!-- <button type="submit" class="btn btn-default">Добавить</button> -->
			<!-- </form> -->
	</div>
</div>
<form action="{!BUTTONS3!}" method="post" id="pass_black" enctype="multipart/form-data">	
</form>




