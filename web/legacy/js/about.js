/*

js/about.js

by Adam Borecki

*/

about = new Object();

about.defaultWidth = 240;
about.defaultHeight = 50;

about.show = function(id, pic, width, height) {
	$("#"+id).stop().css({height:'auto',position:'absolute',zIndex:10}).animate({marginLeft:-50,width:400});
	$("#"+id+" img").each(function(){ this.src = pic; $(this).stop().animate({width:width,height:height},function(){$("#"+id).css({zIndex:1});}) });
};

about.hide = function(id) {
	$("#"+id).stop().animate({width:about.defaultWidth,marginLeft:0});
	$("#"+id+" img").each(function(){ $(this).stop().animate({width:50,height:50},function(){$("#"+id).css({position:'relative',zIndex:0,height:about.defaultHeight});}); });	
};