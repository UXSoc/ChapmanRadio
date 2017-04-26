var g = new Object();
g.self = "/staff/giveaways";
g.timer = null;
g.edit = function(giveawayid) {
	g.load("edit",giveawayid);
};

g.pic = function(giveawayid) {
	g.load("pic",giveawayid);
};

g.remove = function(giveawayid) {
	if(confirm("You are about to permanently delete this giveaway. Are you sure?")) {
		if(confirm("Seriously, you're about to delete this giveaway forever. Continue?")) {
			g.post(g.self, {"DELETE_GIVEAWAY":"true", "giveawayid":giveawayid});
		}
	}
};

g.post = function(path, params, method) {
	method = method || "post"; // Set method to post by default, if not specified.
	
	// The rest of this code assumes you are not using a library.
	// It can be made less wordy if you use one.
	var form = document.createElement("form");
	form.setAttribute("method", method);
	form.setAttribute("action", path);
	
	for(var key in params) {
		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", key);
		hiddenField.setAttribute("value", params[key]);
		form.appendChild(hiddenField);
	}
	
	document.body.appendChild(form);
	form.submit();
};

g.onOpen = function(generate, giveawayid) {
	if(!$("#generate"+generate+"-giveawayid"+giveawayid)[0])
		setTimeout(function(){ g.onOpen(generate, giveawayid); }, 100);
	else
		$("#generate"+generate+"-giveawayid"+giveawayid).load(g.self, {generate:generate, giveawayid:giveawayid});
};

g.load = function(generate,giveawayid) {
	Shadowbox.open({
		content:    "<div id='generate"+generate+"-giveawayid"+giveawayid+"'><div style='padding:160px 200px;text-align:center;margin:auto;color:#FFF'>Loading...</div></div>",
		player:     "html",
		title:      "Edit Giveaway",
		height:     570,
		width:      840,
		enableKeys : false,
		options: { enableKeys: false }
		});
	if(g.timer) { clearTimeout(g.timer); g.timer = null; }
	g.onOpen(generate, giveawayid); // check once per second if the div is ready to load
};