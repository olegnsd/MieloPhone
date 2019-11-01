<link href="/css/add_manually.css" rel="stylesheet">
<link href="/css/bootstrap.min.css" rel="stylesheet">

<br>
<div class="container">
    <div class="row">
        <div class="col-md-9 col-sm-9 col-xs-9">
            <a href="{!BUTTONS2!}" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Назад</a>
            <br><br>
            <form method="post" action="{!BUTTONS1!}" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Найти телефон в ручном черном списке</label>
                    <br>
                    <input name="phone_man" id="phone" type="text" value="{!PHONE!}" class="" placeholder="7(___) ___-____" autofocus>
                </div>

                {!DATA_MAN!}

                <button type="submit" class="btn btn-default">Поиск</button>
            </form>
        </div>
    </div>
</div>

<script src="/js/jquery.min.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="/js/bootstrap.min.js"></script>
<script src="/js/jquery.maskedinput.min.js"></script>

<script>
    //Код jQuery, установливающий маску для ввода телефона элементу input
    //1. После загрузки страницы,  когда все элементы будут доступны выполнить...
    $(function(){
      //2. Получить элемент, к которому необходимо добавить маску
      $("#phone").mask("7(999) 999-9999");
    });
</script>

