<div class="page-header"><h2>Добавление нового пользователя</h2></div>
<form method="post" action="/users/save" enctype="multipart/form-data">
    <div class="form-group">
        <label>Пользователь</label>
        <input type="text" value="" name="user" class="form-control" />
    </div>

    <div class="form-group">
        <label>Пароль</label>
        <input type="password" value="" name="pass" class="form-control" />
    </div>

    <div class="form-group">
        <label>Телефон</label>
        <input type="text" value="" name="phone" class="form-control" />
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
	    <label><input type="checkbox" name="pages[]" value="stats" CHECKED> Статистика</label>
	    <label><input type="checkbox" name="pages[]" value="task" CHECKED> Задачи</label>
	    <label><input type="checkbox" name="pages[]" value="users" CHECKED> Пользователи</label>
            <label><input type="checkbox" name="pages[]" value="caller" CHECKED> Телефоны</label>
            <label><input type="checkbox" name="pages[]" value="black" CHECKED> Черный лист</label>
            <label><input type="checkbox" name="pages[]" value="clients" CHECKED> Задачи</label>

	</div>
    </div>

    <div class="form-group">
	<label>Канал</label>
	<div class="checkbox">
{!CALLERS!}
	</div>
    </div>


    <button type="submit" class="btn btn-default" id="save">Добавить</button>
</form>

