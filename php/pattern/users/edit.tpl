<div class="page-header"><h2>Изменение параметров пользователя</h2></div>
<form method="post" action="/users/save" enctype="multipart/form-data">
    <div class="form-group">
        <label>Пользователь</label>
        <input type="text" value="{!USER!}" name="user" class="form-control" />
    </div>

    <div class="form-group">
        <label>Новый пароль</label>
        <input type="password" value="" name="pass" class="form-control" />
    </div>

    <div class="form-group">
        <label>Телефон</label>
        <input type="text" value="{!PHONE!}" name="phone" class="form-control" />
        <small>
            Формат: 79991234567
        </small>
    </div>

    <div class="form-group">
        <label>Часовой пояс</label>
	<select name="timezone" class="form-control">
	{!HOURS!}
	</select>
    </div>



    <div class="form-group">
	<label>Доступ</label>
	<div class="checkbox">
	    <label><input type="checkbox" name="pages[]" value="stats" {!PAGESSTATS!}> Статистика</label>
	    <label><input type="checkbox" name="pages[]" value="task" {!PAGESTASK!}> Задачи</label>
	    <label><input type="checkbox" name="pages[]" value="users" {!PAGESUSERS!}> Пользователи</label>
	    <label><input type="checkbox" name="pages[]" value="caller" {!PAGESCALLER!}> Телефоны</label>
	    <label><input type="checkbox" name="pages[]" value="black" {!PAGESBLACK!}> Черный лист</label>
	    <label><input type="checkbox" name="pages[]" value="clients" {!PAGESCLIENTS!}> Задачи</label>
	</div>
    </div>

    <div class="form-group">
	<label>Канал</label>
	<div class="checkbox">
	    {!CALLERS!}
	</div>
    </div>


    <input type="hidden" name="id" value="{!ID!}" />
    <button type="submit" class="btn btn-default">Сохранить</button>
</form>