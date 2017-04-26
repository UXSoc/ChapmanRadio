/*
 
 js/subnav.js
 
 by adam borecki
 
 */

$(document).ready(function(){
				  
				 // $(".subcontent").css({"display":"none"});
				  count = 0;
				  $(".subcontent").each(function(){this.style.left = ((count++)*625)+"px";this.style.position='absolute';});
				  $(".subnav a").click(function(){subnav.load(this,this.href);return false;});
				  $(".subnav .active a").each(function(){subnav.load(this,this.href)});
				  if(subnav.cur == "") $(".subnav a:first").each(function(){subnav.load(this,this.href)});
				  
				  if(document.location.hash != "" && document.getElementById(document.location.hash)) subnav.load(document.getElementById(document.location.hash),'#'+document.location.hash);
				  
				  });

subnav = new Object();
subnav.cur = "";
subnav.curElem = null;
subnav.load = function(elem,href) {
	href = href.substring(href.indexOf('#'));
	if(href == subnav.cur) return;
	if(subnav.cur) {
		$(subnav.curElem).parent().removeClass('active');
	}
	subnav.curElem = elem;
	subnav.cur = href;
	$("#subcontentContainer").scrollTo(href, 750);
	$(subnav.curElem).parent().addClass('active');
}