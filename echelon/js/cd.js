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
				
				// if the error was a timeouut then suppy a more informative error message
				if(error == 'timeout'){errormsg = 'There was a communication error, your request timed out.';}
				
				var errormsg = formatError(errormsg);
				container.html(errormsg);
			},
		}); // end ajax
				
	}); // #cd-h-pen click
	  
});

function formatError(msg) {
	
	return '<tr class="table-error"><td colspan="7"><span><strong>Error:</strong> ' + msg + '</span></td></tr>';
	
}