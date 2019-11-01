<table class="table table-hover">
	<thead>
		<tr>
            <td colspan="7" align="left">
				<a href="/clients" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Назад</a>
				<a href="/clients/press2/{!HASH!}/export" class="btn btn-xs btn-info">Экспорт лидов</a>
			</td>
        </tr>
        <tr>
        	<th colspan="4">{!TITLE!}</th>
        <tr>
        	<th colspan="4">Клиент: {!CLIENT!}</th>
        </tr>
        <tr>
        	<th>Заказано</th>
            <th>Загружено</th>
            <th>Обзвонено</th>
            <th>Подняли трубку</th>
            <th>Откликов</th>
        </tr>
	</thead>
    <tbody>
        <tr>
            <td>{!PICKED!}</td>
            <td>{!ALL!}</td>
            <td>{!CALLS!}</td>
            <td>{!UP!}</td>
            <td>{!PRESS!}</td>
        </tr>
    </tbody>
</table>
