/*
 * jQuery Color Animations
 * Copyright 2007 John Resig
 * Released under the MIT and GPL licenses.
 */
(function(jQuery){jQuery.each(['backgroundColor','borderBottomColor','borderLeftColor','borderRightColor','borderTopColor','color','outlineColor'],function(i,attr){jQuery.fx.step[attr]=function(fx){if(fx.state==0){fx.start=getColor(fx.elem,attr);fx.end=getRGB(fx.end);}
fx.elem.style[attr]="rgb("+[Math.max(Math.min(parseInt((fx.pos*(fx.end[0]-fx.start[0]))+fx.start[0]),255),0),Math.max(Math.min(parseInt((fx.pos*(fx.end[1]-fx.start[1]))+fx.start[1]),255),0),Math.max(Math.min(parseInt((fx.pos*(fx.end[2]-fx.start[2]))+fx.start[2]),255),0)].join(",")+")";}});function getRGB(color){var result;if(color&&color.constructor==Array&&color.length==3)
return color;if(result=/rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(color))
return[parseInt(result[1]),parseInt(result[2]),parseInt(result[3])];if(result=/rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(color))
return[parseFloat(result[1])*2.55,parseFloat(result[2])*2.55,parseFloat(result[3])*2.55];if(result=/#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(color))
return[parseInt(result[1],16),parseInt(result[2],16),parseInt(result[3],16)];if(result=/#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(color))
return[parseInt(result[1]+result[1],16),parseInt(result[2]+result[2],16),parseInt(result[3]+result[3],16)];return colors[jQuery.trim(color).toLowerCase()];}
function getColor(elem,attr){var color;do{color=jQuery.curCSS(elem,attr);if(color!=''&&color!='transparent'||jQuery.nodeName(elem,"body"))
break;attr="backgroundColor";}while(elem=elem.parentNode);return getRGB(color);};var colors={aqua:[0,255,255],};})(jQuery);

// Javascript for the chatlogger plugin
var autorefreshtimerId;
$(document).ready(function() {

	$('span#refreshcommand').append('<label for="autorefresh-button" class="chat-fh">Refresh?</label>Auto load in new chats:<input type="checkbox" id="autorefresh-button" name="autorefresh" />');

	// if autorefresh box is checked, stats updating right away
	if ($('#autorefresh-button').get(0).checked) {
		autorefreshtimerId = window.setInterval(updateChat, 5000);
	}

	// on autorefresh checkbox change :
	$('#autorefresh-button').click(function() {
		clearInterval(autorefreshtimerId);
		if (this.checked) {
			updateChat();
			autorefreshtimerId = window.setInterval(updateChat, 5000);
		}
	});
	
	$('#tb-form').submit(function(){
	
		var msg = $('#talkback').val();
		var srvId = $('#tb-srv').val();
		var lastId = $("tr:first", "tbody#chatlog-body").attr("id");

		$('#tb-form').append('<em class="loading">Sending...</em>');
		
		var status = $('.loading');
		
		$.get("lib/plugins/chatlogs/actions.php", { 
			'talkback': msg, 
			'srv': srvId,
			'last-id': lastId,
		}, function(data){
			if(data.length > 0) {
				$("tbody#chatlog-body").prepend(data);
				$('.tb-row').css('background-color','#d8eee1');
			}
			status.html('Done!').fadeOut(5000);
		});
		
		return false;
	});

});

function updateChat() {
	// display the loading image
	$('#chats-header').append($('<div id="loading" style="position:absolute; top:15px; right:45px"><h3 style="display: inline;">Loading.....</div>'));

	var lastId = $("tr:first", "tbody#chatlog-body").attr("id");

	var tableId = $("table#chat").attr('rel');

	$.get("lib/plugins/chatlogs/actions.php", {
		'auto': 1, 
		'last-id': lastId,
		'table-num': tableId,
	}, function(data){
		if(data.length > 0) {
			$("tbody#chatlog-body").prepend(data);
			$('.animate').css('background-color','#bde4f4');
			$('.animate').animate({ backgroundColor: '#FFF' }, 20000);
		}
	});

	$('#loading').fadeOut("3500",function(){$(this).remove();});
}; 
