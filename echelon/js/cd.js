// Clientdetails Page JS Document

$(document).ready(function() {
	
	$('#cd-h-pen').click(function(){
							  
		var table = $(this).attr('rel');
		$("#"+table).slideDown('slow');
		
		$(this).children('.cd-open').hide();
		
		$('#penalties').prepend('<h3 class="cd-h">Penalties</h3>');
		
		// after showing the empty table load in the data
		
		var loader = $('#cd-tr-load-pen');
		
		var cid = $('#cd-table-pen').attr('rel');
				
		var container = $('#contain-pen');
		
		$(this).remove();
		
		$.ajax({
			url: "inc/cd/penalties.php?id="+cid,
			timeout: 5000,
			success: function(data){
				container.html(data);
			},
			error: function(req,error){
				if(error === 'error'){error = req.statusText;}
				var errormsg = '<tr class="table-error"><td colspan="7"><span><strong>Communication Error:</strong> '+error+'</span></td></tr>';
				container.html(errormsg);
			},
		}); // end ajax
				
	}); // #cd-h-pen click
	  
});