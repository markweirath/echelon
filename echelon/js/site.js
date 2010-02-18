// JavaScript Document
$(document).ready(function() {
	
	// Tabs
	$("a.cd-tab").click(function() {
		
		$(".cd-active").removeClass("cd-active");
		
		$(this).parent().addClass("cd-active");
		
		$(".act-slide").slideUp();
		
		var content_show = $(this).attr("rel");
		$("#"+content_show).slideDown('slow');
	  
	});
	
	// Expand Panel
	$("#open").click(function(){
		$("div#panel").slideDown("slow");
	});

	// Collapse Panel
	$("#close").click(function(){
		$("div#panel").slideUp("slow");
	});

	// Switch buttons from "Log In | Register" to "Close Panel" on click
	$("#toggle a").click(function () {
		$("#toggle a").toggle();
	});
	
	$(".clr-txt").focus(function(){
	   if (this.value == this.defaultValue){
		  this.value = '';
	   }
	});
	
	// hide Site Error Warning 
	$(".err-close").click(function() {		 
		$(this).parent().slideUp("slow");
	});
	
	// Confirm action
	$(".confirm").click(function(){
		var answer = confirm("Are you sure you want to do this?");

		if(answer==true) {
			return true;
		} else {
			return false;
		}
	});
	
	// Confirm Hard Delete
	$(".harddel").click(function(){
		var answer = confirm("Are you sure you want to delete this forever?");

		if(answer==true) {
			return true;
		} else {
			return false;
		}
	});
	
	// Confirm Logout
	$(".logout").click(function(){
		var answer = confirm("Are you sure you want to logout of Echelon?");

		if(answer==true) {
			return true;
		} else {
			return false;
		}
	});
	
	// Site Admin Page JS //
	
	$(".edit-key-comment").show();
	// Edit key reg comment
	$(".edit-key-comment").click(function(){
			
		thisItem = $(this);
		
		var td = thisItem.parent();			 
		var comment = td.find("span.comment");
		var commentText = comment.text(); // get the comment text
		
		var key = td.parent().find("td.key").text();
		
		thisItem.fadeOut("fast"); // fade out the edit button
		comment.fadeOut("fast"); 
		
		td.append('<form action="actions/key-edit.php" method="post" style="display: none;" class="edit-comment-form"><input type="text" name="comment" id="comment-text-box" value="' + commentText + '" /><input type="hidden" name="key" value="' + key + '" id="key-input" /></form>');
		
		$(".edit-comment-form").slideDown("slow"); // slide in the form since the form is usually large than the table row so the animation makes the form addition less jerky when added
			
		$('#comment-text-box').blur(function() {
										   
			var newText = $(this).val();
			var key = $("#key-input").val();
			
			var dataString = 'key=' + key + '&text=' + newText;
			
			// Troubleshooting
			//alert(dataString);
			//return false;
			
			$.post("actions/key-edit.php", { comment: newText, key: key}, function(data){
																				 
				if(data.length >0) {
					
					$(".edit-comment-form").remove(); // remove the form
					comment.slideDown(); // unhide the comment
					$('.edit-key-comment').show(); // reshow the edit button
					
					// Add success/error message to the body of the page
					if(data=='yes') {
						comment.text(newText); // update the comment on the page with the submitted text
						$("#content").prepend('<div class="success" id="msg"><strong>Success:</strong> Your comment has been updated</div>');
					} else if(data=='no') {						
						$("#content").prepend('<div class="error" id="msg"><strong>Error:</strong> Your comment has not been updated</div>');
					}
				}
				
			}); // end post
			
		}); // end onBlur

		return false;
			
	}); // end .edit-key-comment clikc
	
	// Check Username Function
	$("#uname-check").blur(function(){
		
		var loading = $(".loader").fadeIn("normal");
		var key = $("#key").val();
		
		
		$.post("actions/check-username.php",{ username:$(this).val() } ,function(data){
			loading.fadeOut('fast');
			$('div.result').removeClass('r-bad').removeClass('r-good');
			
			if(data=='no') {
				$('div.result').html('Username unavailable').addClass('r-bad').fadeTo(900,1);
			} else if(data=='yes') {
				$('div.result').html('Username available').addClass('r-good').fadeTo(900,1);
			} else {
				$('div.result').html('Name is required').addClass('r-bad').fadeTo(900,1);
			}
		});
		
	});

});