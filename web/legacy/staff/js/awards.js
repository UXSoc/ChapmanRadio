award = new Object();
award.data = new Array();
award.edit = function(num) {
	award.editor(num);
	$('#awardlist').animate({left:-840});
	$('#editor').animate({left:100});
};
award.editor = function(num) {
	var dat = award.data[num];
	var html = "<br /><p><a href='javascript:award.back();'>&laquo; Go Back</a></p><table><tr><td><form method='post' action='"+document.location+"'><input type='hidden' name='awardid' value='"+num+"' /><table class='formtable tablesorter' cellspacing='0' cellpadding='0'><thead><th>property</th><th>value</th></thead><tbody>";
	var count = 0;
	for(x in dat) {
		rowclass = ++count % 2 == 0 ? 'evenRow' : 'oddRow';
		switch(x) {
			case 'awardid':
			case 'icon':
			case 'showhtml':
				count--;
				break;
			case 'awardedon':
				html += "<tr class='"+rowclass+"'><td>"+x+"</td><td><input name='award-"+x+"' value=\""+dat[x]+"\" /></td></tr>";
				break;
			default:
				html += "<tr class='"+rowclass+"'><td>"+x+"</td><td>"+dat[x]+"</td></tr>";
				break;
		}
	}
	rowclass = ++count % 2 == 0 ? 'evenRow' : 'oddRow';
	html += "<tr><td colspan='2' class='"+rowclass+"' style='text-align:center;'><input type='submit' name='SAVE_AWARD' value=' Save Changes ' /></td></tr></tbody></table></form></td><td><div style='width:320px;'>"+dat.showhtml+"<p><a href='/?show="+dat.showid+"' target='_blank'>&raquo; View Show Profile</a></div></td></tr></table><br /><form method='post' action='"+document.location+"'><p>Delete this award: <input type='submit' value=' Delete ' name='DELETE_AWARD' onclick='return confirm(\"Are you sure you want to permanently delete this award? This CANNOT be undone.\");' /><input type='hidden' name='awardid' value='"+num+"' /></p></form>";
	$('#editor').html(html);
};

award.back = function() {
	$('#awardlist').animate({left:0});
	$('#editor').animate({left:1000});
};