<div class="page-header"><h2>Изменение параметров телефона</h2></div>
<form method="post" action="/caller/save" enctype="multipart/form-data">
    <div class="form-group">
        <label>Наименование</label>
        <input type="text" value="{!NAME!}" name="name" class="form-control" />
    </div>

    <div class="form-group">
        <label>Маркировка</label>
        <input type="text" value="{!MARK!}" name="mark" class="form-control" disabled />
    </div>

    <div class="form-group">
        <label>Телефон</label>
        <input type="text" value="{!PHONE!}" name="phone" class="form-control" />
        <small>
    	    Формат: 79991234567
        </small>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="text" value="{!EMAIL!}" name="email" class="form-control" />
    </div>

    <div class="form-group">
        <label>Уведомление</label><br/>
        <label><input type="radio" name="send" value="0" {!SENDNO!}> Не отправлять</label>&nbsp;&nbsp;&nbsp;&nbsp;
        <label><input type="radio" name="send" value="1" {!SENDSMS!}> SMS</label>&nbsp;&nbsp;&nbsp;&nbsp;
        <label><input type="radio" name="send" value="2" {!SENDEMAIL!}> Email</label>
    </div>

    <div class="form-group">
        <label>Часовой пояс</label>
        <select name="timezone" class="form-control">
        {!HOURS!}
        </select>
    </div>

    <input type="hidden" name="id" value="{!ID!}" />
    <button type="submit" class="btn btn-default">Сохранить</button>
</form>