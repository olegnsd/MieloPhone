<form method="post" action="/black" enctype="multipart/form-data">
    <div class="form-group">
        <label>Телефон</label>
        <input type="text" value="{!PHONE!}" name="phone" class="form-control" placeholder="9260000000"/>
    </div>

    {!DATA!}
    {!DATA_MAN!}
    {!DATA_ALL!}

    <button type="submit" class="btn btn-default">Поиск</button>
</form>
<br>
{!BUTTONS1!}

