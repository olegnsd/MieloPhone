  	<div class="alert alert-success" role="alert">
  		<div class="form-group">
    			<label for="addinfo">Добавить новую информацию о контакте</label>
    			<textarea class="form-control" id="addinfo" placeholder="Введите текст" rows="3"></textarea>
			<input type="hidden" id="addinfo-id" value="{!ID!}" />

			<div class="pull-right" style="margin-top: 2px">
				<button type="button" class="btn btn-xs btn-success" id="btn_addinfo">
					<span class="glyphicon glyphicon-floppy-saved" aria-hidden="true"></span> Сохранить
				</button>
			</div>
  		</div>
	</div>

	<hr/>
	<button class="btn btn-info btn-block" id="getCalls">Загрузить файлы записей разговора</button>
	<div id="calls" style="margin-top: 20px">
	    {!STATS!}
	</div>



