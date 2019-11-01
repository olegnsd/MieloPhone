    </tbody>
</table>
<center>
	<button type="button" class="btn btn-success {!HIDEMORE!}" id="more">
		<span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> Показать еще <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>
	</button>
	<input type="hidden" id="page" value="1" />
</center>

<div class="modal fade" id="infoRing" tabindex="-1" role="dialog" aria-labelledby="infoRingLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="infoRing">Информация о подключении абонента +7<span id="modalPhone"></span></h4>
      </div>
      <div class="modal-body" id="modalText">
		
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>


<script>
    $(function() {
	$('#infoRing').on('show.bs.modal', function (event) {
		$.post( "/task/response/{!ID!}/info/"+$(event.relatedTarget).data('ringid'), function( data ) {
			$("#modalPhone").html(data.phone);
			$("#modalText").html(data.result);
		}, "json");
	})

	$("body").on("click", "#btn_addinfo", function() {
		$.post( "/task/response/{!ID!}/addinfo/"+$("#addinfo-id").val(), {text: $("#addinfo").val()}, function( data ) {
			$.jGrowl(data.result);
			if (data.success) {
				$("#countInfo"+$("#addinfo-id").val()).text(($("#countInfo"+$("#addinfo-id").val()).text() == ""? 1: parseInt($("#countInfo"+$("#addinfo-id").val()).text())+1));
				$('#infoRing').modal('hide');
			}
		}, "json");
	});


        $("#onlypress").on("change", function() {
		if ($(this).is(":checked")) 
			window.location.href = "/task/response/{!ID!}/interest";
		else
			window.location.href = "/task/response/{!ID!}";
	});

	$("#more").click(function() {
		$.post( "/task/response/{!ID!}/more/"+($("#onlypress").is(":checked")? "interest": ""), { page: $("#page").val() }, function( data ) {
			if (data.success) {
				$("#more").show();
				$(".table tbody").append(data.result);
				$("#page").val(parseInt($("#page").val())+1);
			}
			else {
				if (data.end)
					$("#more").hide();
				else
					$.jGrowl(data.result);
			}
		}, "json");
	});

        $("body").on("click", "#getCalls", function() {
		$("#calls").html('<div class="progress">\
  <div class="progress-bar progress-bar-striped active active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">\
    <span class="sr-only">45% Complete</span>\
  </div>\
</div>');

		$.post( "/task/response/{!ID!}/info/"+$("#addinfo-id").val()+"/calls", function( data ) {
		    $("#calls").html(data.result);
		}, "json");
        });

    });
</script>

<div id="clock"></div>