<div class="page-header"><h2>Изменение параметров клиента</h2></div>
<form method="post" action="/clients/save" enctype="multipart/form-data">
    <div class="form-group">
        <label>Наименование</label>
        <input type="text" value="{!NAME!}" name="name" class="form-control" />
    </div>

    <div class="form-group">
        <label>Количество заказанных поднятий трубок</label>
        <input type="text" value="{!PICKED!}" name="picked" class="form-control" />
    </div>

    <input type="hidden" name="id" value="{!ID!}" />
    <button type="submit" class="btn btn-default">Сохранить</button>
</form>