<div class="page-header"><h2>Изменение параметров задания</h2></div>
<form method="post" action="/task/save" enctype="multipart/form-data">
    <div class="form-group">
        <label>Комментарий</label>
	<input type="text" value="{!COMMENT!}" name="comment" class="form-control" />
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
                <div class="col-md-2"><input type="time" name="timefrom" class="form-control" value="{!TIMEFROM!}" /></div>
                <div class="col-md-2"><input type="time" name="timeto" class="form-control" value="{!TIMETO!}" /></div>
        </div>
    </div>

    <div class="form-group">
        <label>Приоритет обзвона</label>
        <select name="prior" class="form-control">
            {!PRIOR!}
        </select>
    </div>


    <div class="form-group">
        <label>Количество секунд между отправками</label>
        <input type="text" value="{!SLEEP!}" name="sleep" class="form-control" />
    </div>

    <div class="form-group">
	<label>Отправлять SMS</label>
	<select name="sms_enable" class="form-control">
	    <option value="1" {!SMS_ENABLE_YES!} >Да
	    <option value="0" {!SMS_ENABLE_NO!} >Нет
	</select>
    </div>
    <div class="form-group">
	<label>Текст SMS</label>
	<textarea name="sms_text" class="form-control">{!SMS_TEXT!}</textarea>
    </div>
    <div class="form-group">
	<label>Отправлять Email</label>
	<select name="email_enable" class="form-control">
	    <option value="1" {!EMAIL_ENABLE_YES!} >Да
	    <option value="0" {!EMAIL_ENABLE_NO!} >Нет
	</select>
    </div>
    <div class="form-group">
	<label>Текст Email</label>
	<textarea name="email_text" class="form-control">{!EMAIL_TEXT!}</textarea>
    </div>

    <div class="form-group">
        <label>Файл с базой</label>
        <input type="file" name="file" class="form-control">
        <small>Структура файла CSV: <abbr title="Имя клиента">Имя</abbr>;<abbr title="Телефон клиента. 10 цифр без кода страны +7 или 8">Телефон</abbr>;<abbr title="Email клиента">Email</abbr></small>
    </div>

    <div class="form-group">
        <label>Звуковой файл</label>
        <input type="file" name="sound" class="form-control">
	{!SOUND!}
        <small>Обязательно в формате WAV для астериска</small>
    </div>

    <div class="form-group">
        <label>Уведомлять об отзывах на Email</label>
        <input type="text" value="{!EMAIL_NOTIFY!}" name="email_notify" class="form-control" placeholder="Введите Email" />
    </div>

    <div class="form-group">
        <label>Уведомлять об отзывах на URL</label>
        <input type="text" value="{!URL_NOTIFY!}" name="url_notify" class="form-control" placeholder="Введите URL" />
    </div>

                        

    <input type="hidden" name="id" value="{!ID!}" />
    <button type="submit" class="btn btn-default">Сохранить</button>
</form>