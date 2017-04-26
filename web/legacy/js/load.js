/*
 
 js/load.js

*/

load = new Object();

load.all = new Array();
load.cur = 0;
load.speed = 2000;

$(document).ready(function(){
	$("<div>", {id:'loadContainer',style:'display:none'}).appendTo("body");
	for(var x in load.all) {
		$("#loadContainer").append("<div id='loadChild"+x+"'></div>");
		load.sync(x);
		}
	});

load.create = function(dat) {
	// id and src are required as object literal
	var obj = new Object();
	obj.id = dat.id;
	obj.src = dat.url ? dat.url : dat.src;
	if(obj.src.indexOf("http") == -1) {
		obj.src = document.location.protocol + "//" + document.location.hostname + obj.src;
	}
	obj.interval = dat.interval ? dat.interval : 20*1000;
	obj.timer = null;
	obj.arg = dat.arg ? dat.arg : null;
	obj.lastResponse = "";
	load.all[++load.cur] = obj;
};

load.sync = function(num) {
	var obj = load.all[num];
	
	$("#loadContainer"+num).load(obj.src, obj.arg, function(response){
	if(response != obj.lastResponse) {
	if(response)
	 $("#"+obj.id).slideUp(function(){
	   $(this).html(response).slideDown();
	   });
	else
		$("#"+obj.id).slideUp();
	 load.all[num].lastResponse = response;
	}
	load.all[num].timer = setTimeout("load.sync("+num+")", obj.interval);
	});
};