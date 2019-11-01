        <tr>
            <td colspan="7" align="left"><a href="/users/add" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Добавить</a></td>
        </tr>

	</tbody>
</table>

<div class="modal " " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
	<div class="modal-content">
	    <div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    		<h4 class="modal-title" id="myModalLabel">Удаление пользователя</h4>
	    </div>
	    <div class="modal-body" id="myModalLabel2">
		<p>Вы действительно хотите удалить этого пользователя?</p>
    	    </div>
    	    <div class="modal-footer">
    		<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    		<button type="button" class="btn btn-primary" id="myModalLabel3">Принять</button>
    	    </div>
	</div>
    </div>
</div>

<script>
    function del(id) {
	$('#myModalLabel3').unbind('click');
	$('#myModalLabel3').bind('click', function() {$(location).attr('href','/users/delete/'+id);}); 
    }
</script>