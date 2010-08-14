// JavaScript Document
$(document).ready(function() {

	// disable/enable password change fields
	$('#change-pw').click(function(){
		if ($('#change-pw:checked').val() == 'on') {
			$('.disable').removeAttr("disabled"); 
		} else {
			$('.disable').attr("disabled", true); 
		}
	});
	
});