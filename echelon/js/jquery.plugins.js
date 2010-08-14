/*
 * Copyright (c) 2006/2007 Sam Collett (http://www.texotela.co.uk)
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * Version 1.0
 * Demo: http://www.texotela.co.uk/code/jquery/numeric/
 */
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('r.E.W=7(c,d){c=c||".";d=q d=="7"?d:7(){};6.K(7(e){g a=e.i?e.i:e.h?e.h:0;2(a==k&&6.N.J()=="G"){5 3}f 2(a==k){5 j}g b=j;2((e.4&&a==y)||(e.4&&a==v))5 3;2((e.4&&a==t)||(e.4&&a==u))5 3;2((e.4&&a==V)||(e.4&&a==S))5 3;2((e.4&&a==R)||(e.4&&a==Q))5 3;2((e.4&&a==P)||(e.4&&a==O)||(e.L&&a==p))5 3;2(a<I||a>H){2(a==p&&6.l.F==0)5 3;2(a==c.n(0)&&6.l.o(c)!=-1){b=j}2(a!=8&&a!=9&&a!=k&&a!=D&&a!=C&&a!=M&&a!=B&&a!=A){b=j}f{2(q e.i!="z"){2(e.h==e.m&&e.m!=0){b=3}f 2(e.h!=0&&e.i==0&&e.m==0){b=3}}}2(a==c.n(0)&&6.l.o(c)==-1){b=3}}f{b=3}5 b}).x(7(){g a=r(6).w();2(a!=""){g b=T U("^\\\\d+$|\\\\d*"+c+"\\\\d+");2(!b.s(a)){d.X(6)}}});5 6}',60,60,'||if|true|ctrlKey|return|this|function||||||||else|var|keyCode|charCode|false|13|value|which|charCodeAt|indexOf|45|typeof|jQuery|exec|120|88|65|val|blur|97|undefined|46|39|36|35|fn|length|input|57|48|toLowerCase|keypress|shiftKey|37|nodeName|86|118|90|122|67|new|RegExp|99|numeric|apply'.split('|'),0,{}))

/**
 * Written by Blair Mitchelmore
 * Licensed under the WTFPL (http://sam.zoy.org/wtfpl/).
 *
 * @author Blair Mitchelmore
 **/
jQuery.fn.extend({
	everyTime: function(interval, label, fn, times) {
		return this.each(function() {
			jQuery.timer.add(this, interval, label, fn, times);
		});
	},
	oneTime: function(interval, label, fn) {
		return this.each(function() {
			jQuery.timer.add(this, interval, label, fn, 1);
		});
	},
	stopTime: function(label, fn) {
		return this.each(function() {
			jQuery.timer.remove(this, label, fn);
		});
	}
});

jQuery.extend({
timer: {
	global: [],
	guid: 1,
	dataKey: "jQuery.timer",
	regex: /^([0-9]+(?:\.[0-9]*)?)\s*(.*s)?$/,
	powers: {
		// Yeah this is major overkill...
		'ms': 1,
		'cs': 10,
		'ds': 100,
		's': 1000,
		'das': 10000,
		'hs': 100000,
		'ks': 1000000
	},
	timeParse: function(value) {
		if (value == undefined || value == null)
			return null;
		var result = this.regex.exec(jQuery.trim(value.toString()));
		if (result[2]) {
			var num = parseFloat(result[1]);
			var mult = this.powers[result[2]] || 1;
			return num * mult;
		} else {
			return value;
		}
	},
	add: function(element, interval, label, fn, times) {
		var counter = 0;
		
		if (jQuery.isFunction(label)) {
			if (!times) 
				times = fn;
			fn = label;
			label = interval;
		}
		
		interval = jQuery.timer.timeParse(interval);

		if (typeof interval != 'number' || isNaN(interval) || interval < 0)
			return;

		if (typeof times != 'number' || isNaN(times) || times < 0) 
			times = 0;
		
		times = times || 0;
		
		var timers = jQuery.data(element, this.dataKey) || jQuery.data(element, this.dataKey, {});
		
		if (!timers[label])
			timers[label] = {};
		
		fn.timerID = fn.timerID || this.guid++;
		
		var handler = function() {
			if ((++counter > times && times !== 0) || fn.call(element, counter) === false)
				jQuery.timer.remove(element, label, fn);
		};
		
		handler.timerID = fn.timerID;
		
		if (!timers[label][fn.timerID])
			timers[label][fn.timerID] = window.setInterval(handler,interval);
		
		this.global.push( element );
		
	},
	remove: function(element, label, fn) {
		var timers = jQuery.data(element, this.dataKey), ret;
		
		if ( timers ) {
			
			if (!label) {
				for ( label in timers )
					this.remove(element, label, fn);
			} else if ( timers[label] ) {
				if ( fn ) {
					if ( fn.timerID ) {
						window.clearInterval(timers[label][fn.timerID]);
						delete timers[label][fn.timerID];
					}
				} else {
					for ( var fn in timers[label] ) {
						window.clearInterval(timers[label][fn]);
						delete timers[label][fn];
					}
				}
				
				for ( ret in timers[label] ) break;
				if ( !ret ) {
					ret = null;
					delete timers[label];
				}
			}
			
			for ( ret in timers ) break;
			if ( !ret ) 
				jQuery.removeData(element, this.dataKey);
		}
	}
}
});
jQuery(window).bind("unload", function() {
	jQuery.each(jQuery.timer.global, function(index, item) {
		jQuery.timer.remove(item);
	});
});

/*
 * Tooltip script 
 * written by Alen Grakalic (http://cssglobe.com)
 * for more info visit http://cssglobe.com/post/1695/easiest-tooltip-and-image-preview-using-jquery
 */

this.tooltip = function(){		
	xOffset = 10;
	yOffset = 20;				
	$("a.tooltip").hover(function(e){											  
		this.t = this.title;
		this.title = "";									  
		$("body").append("<p id='tooltip'>"+ this.t +"</p>");
		$("#tooltip").css("top",(e.pageY - xOffset) + "px").css("left",(e.pageX + yOffset) + "px").fadeIn("fast");		
    },
	function(){
		this.title = this.t;		
		$("#tooltip").remove();
    });	
	$("a.tooltip").mousemove(function(e){
		$("#tooltip").css("top",(e.pageY - xOffset) + "px").css("left",(e.pageX + yOffset) + "px");
	});			
};

$(document).ready(function(){
	tooltip();
});