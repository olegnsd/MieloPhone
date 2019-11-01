<div class="page-header"><h2>Добавление нового задания</h2></div>
<form method="post" action="/task/save" enctype="multipart/form-data">
    <div class="form-group">
	<label>Комментарий</label>
	<input type="text" value="" name="comment" class="form-control" />
    </div>

    <div class="form-group">
	<label>Канал</label>
	<div class="input-group">
	    {!CALLERS!}
	</div>
    </div>

    <div class="form-group">
        <label>Клиент</label>
        <select name="client_id" class="form-control">
		{!CLIENTS!}
        </select>
    </div>


    <div class="form-group">
	<label>Временной диапазон обзвона</label>
	<div class="row">
		<div class="col-md-2"><input type="time" name="timefrom" class="form-control" value="10:00" /></div>
		<div class="col-md-2"><input type="time" name="timeto" class="form-control" value="20:00" /></div>
	</div>
</div>

	<div class="form-group">
		<label>Приоритет обзвона</label>
		<select name="prior" class="form-control">
		    <option value="-3">-3
		    <option value="-2">-2
		    <option value="-1">-1
		    <option value="0" selected>0
		    <option value="1">1
		    <option value="2">2
		    <option value="3">3
		</select>
    </div>

    <div class="form-group">
	<label>Количество секунд между отправками</label>
	<input type="text" value="75" name="sleep" class="form-control" />
    </div>

    <div class="form-group">
	<label>Способ формирования списка</label>
	<div class="checkbox">
	    <label><input type="radio" name="typebase" value="file" checked id="typebasefile"> Загрузка базы</label>
	    <label><input type="radio" name="typebase" value="autolist" id="typebaseautolist"> Автоматическое формирование списка</label>
	</div>
    </div>

    <div class="form-group">
	<label>Отправлять SMS</label>
	<select name="sms_enable" class="form-control">
	    <option value="1">Да
	    <option value="0">Нет
	</select>
    </div>
    <div class="form-group">
	<label>Текст SMS</label>
	<textarea name="sms_text" class="form-control">Текст sms</textarea>
    </div>
    <div class="form-group">
	<label>Отправлять Email</label>
	<select name="email_enable" class="form-control">
	    <option value="1">Да
	    <option value="0">Нет
	</select>
    </div>
    <div class="form-group">
	<label>Текст Email</label>
	<textarea name="email_text" class="form-control">Текст Email</textarea>
    </div>
    <div class="form-group">
	<label>Файл с базой</label>
	<input type="file" name="file" class="form-control">
	<small>Структура файла CSV: <abbr title="Имя клиента">Имя</abbr>;<abbr title="Телефон клиента. 10 цифр без кода страны +7 или 8">Телефон</abbr>;<abbr title="Email клиента">Email</abbr></small>
    </div>

    <div class="form-group">
	<label>Начало диапазона</label>
	<input type="text" value="" name="range1" class="form-control" placeholder="9260000000" DISABLED  />
    </div>
    <div class="form-group">
	<label>Конец диапазона</label>
	<input type="text" value="" name="range2" class="form-control" placeholder="9269999999" DISABLED />
    </div>


    <div class="form-group">
        <label>Звуковой файл</label>
        <input type="file" name="sound" class="form-control">
        <small>Обязательно в формате WAV для астериска</small>
    </div>

    <div class="form-group">
	<label>Уведомлять об отзывах на Email</label>
	<input type="text" value="" name="email_notify" class="form-control" placeholder="Введите Email" />
    </div>
    <div class="form-group">
        <label>Уведомлять об отзывах на URL</label>
        <input type="text" value="" name="url_notify" class="form-control" placeholder="Введите URL" />
    </div>



    <button type="submit" class="btn btn-default" id="save">Добавить</button>
</form>

<script>
    $(function() {
        $("#save").click(function() {
	    err = 0;

	    $("input").css("border", "");

            if ($("[name='sound']").val() == "") {
		err++;
		$("[name='sound']").css("border", "1px solid #F00");
	    }

            if ($("[name='file']").val() == "" && $("#typebasefile").prop("checked")) {
		err++;
		$("[name='file']").css("border", "1px solid #F00");
	    }

	    if ($("#typebaseautolist").prop("checked") && ($("[name='range1']").val().length != 10 || $("[name='range2']").val().length != 10 || $("[name='range1']").val() > $("[name='range2']").val() || $("[name='range2']").val() - $("[name='range1']").val() > 10000)) {
		err++;
		$("[name='range1']").css("border", "1px solid #F00");
		$("[name='range2']").css("border", "1px solid #F00");
	    }

	    if (err > 0)
		return false;
        });

	$("#typebasefile").on("change", function() {
	    if ($(this).prop("checked")) {
		$("[name='range1']").prop("disabled", true);
		$("[name='range2']").prop("disabled", true);
		$("[name='email_enable']").prop("disabled", false);
		$("[name='email_text']").prop("disabled", false);
		$("[name='file']").prop("disabled", false);
	    }
	});

	$("#typebaseautolist").on("change", function() {
	    if ($(this).prop("checked")) {
		$("[name='range1']").prop("disabled", false);
		$("[name='range2']").prop("disabled", false);
		$("[name='email_enable']").prop("disabled", true);
		$("[name='email_text']").prop("disabled", true);
		$("[name='file']").prop("disabled", true);
	    }
	});

	$("[name='range1']").prop("disabled", true);
	$("[name='range2']").prop("disabled", true);
    });
</script>
