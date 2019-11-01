	</div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

<script>
jQuery(function($){
    if ($("#clock").length > 0) {
/*
        $('#clock').flipcountdown({tzoneOffset: {!ZONE!}, tick:function(){
  return new Date('{!ZONEDATE!}');
}});
        $('#clock').flipcountdown({tick:function(){
  return new Date('{!ZONEDATE!}');
}});
*/
        $('#clock').flipcountdown({tick:function(){
  return new Date({!ZONEDATE!});
}});

	$( window ).scroll(function() {
	    $("#clock").css("top", $( window ).scrollTop() + $( window ).height() - 58);
//	    $("#clock").css("left", $( window ).scrollLeft() + $( window ).width() - 250);
//	    $("#clock").css("width", "250px");
	});
	$("#clock").css("top", $( window ).scrollTop() + $( window ).height() - 58);
//	$("#clock").css("left", $( window ).scrollLeft() + $( window ).width() - 250);
//	$("#clock").css("width", "250px");
    }
})
</script>


</body>

</html>
