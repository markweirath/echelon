// Clientdetails Page JS Document

$(document).ready(function() {

	$('.slide-panel').hide();
	
	if ($('#pb:checked').val() == 'on') {
		$("#ban-duration").hide();
	}
	$('#pb').click(function(){
		if ($('#pb:checked').val() == 'on') {
			$("#ban-duration").slideUp();
		} else {
			$("#ban-duration").slideDown();
		}
	});
	
	// Tabs
	$("a.cd-tab").click(function() {
		
		$(".cd-active").removeClass("cd-active");
		
		$(this).parent().addClass("cd-active");
		
		$(".act-slide").slideUp();
		
		var content_show = $(this).attr("rel");
		$("#"+content_show).slideDown('slow');
	  
	});
	
	$('.cd-slide').click(function() {
	
		var slideName = $(this).attr("id");
		
		var slideArea = slideName + '-table';
		
		$('#'+slideArea).slideToggle('slow');
		
	});
	
	// Chats Tabs
	$("a.chat-tab").click(function() {
		
		$(".chat-active").removeClass("chat-active");
		
		$(this).parent().addClass("chat-active");
		
		$(".chat-content").slideUp();
		
		var content_show = $(this).attr("rel");
		$("#"+content_show).slideDown('slow');
	  
	});
	
	if ($('#eb-pb:checked').val() == 'on') {
		$("#eb-ban-duration").hide();
	}
	$('#eb-pb').click(function(){
		editBanCheck();
	});
	  
});

function editBanBox(thisItem) {

	var ban_id = $(thisItem).attr('rel');
	$.fn.colorbox({href:"inc/cd/editban.php?banid="+ ban_id}); 

}

function editBanCheck() {
	if ($('#eb-pb:checked').val() == 'on') {
		$("#eb-ban-duration").slideUp();
	} else {
		$("#eb-ban-duration").slideDown();
	}
}