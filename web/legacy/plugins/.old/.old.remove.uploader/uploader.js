/*! Client Side Multi-File Uploader
 * (C) David Tyler 2010
 * 
 * Requires SWFObject.js, mf_uploader.swf, Facebook.js, Backend
 * mf_uploader
 */
 
var mf_uploader = {
	_$: function(i){ return document.getElementById(i); },
	si: 'mf_uploader_selector',
	ui: 'mf_uploader_uploader',
	active: [],
	format_size: function(size){var e=['B','KB','MB','GB','TB'],i=0;while(size>1024){size=Math.round((size/1024)*100)/100;i++;}return size+e[i];},
	attach: function(z, options){
		jQuery(z).each(function(i, E){ E = jQuery(E);
			var s,d='',n;
			s = jQuery.extend({
				id : E.attr('id'),
				expressInstall : null,
				height : 30,
				width : 110,
				wmode : 'opaque',
				scriptAccess : 'sameDomain',
				fileDataName : 'Filedata',
				simUploadLimit : 1,
				queueID : false
				}, options);
			n = location.pathname.split('/'); n.pop(); s.pagepath = n.join('/')+'/';
			s.ID = s.id;
			s.buttonText = escape(s.buttonText);
			s.folder = escape(s.folder);
			if (s.scriptData){
				for (n in s.scriptData) d+='&'+n+'='+s.scriptData[n];
				s.scriptData = escape(d.substr(1));
				}
			E.css('display','none');
			E.after('<div id="mf_uploader_uploader_links" style="margin-bottom:5px;"><div id="' + E.attr('id') + 'Uploader"></div> <span style=" bottom:7px; font-size:15px; position:relative;"><a onclick="mf_uploader.clearPending(\'#mf_uploader_uploader\');"> Cancel Pending</a> | <a onclick="mf_uploader.clearCompleted(\'#mf_uploader_uploader\');">Clear Queue</a> |  <a onclick="mf_uploader.cg()">Change Category</a></span></div>');
			swfobject.embedSWF(s.uploader, s.id + 'Uploader', s.width, s.height, '9.0.24', s.expressInstall, s, {'quality':'high','wmode':s.wmode,'allowScriptAccess':s.scriptAccess});
			
			E.bind("mf_uploader_event_select", {'queueID': s.queueID}, function(event, ID, file){
				var fileName = (file.name.length > 30) ? (file.name.substr(0,30) + '...') : file.name;
				jQuery('#'+event.data.queueID).append('<div id="' + E.attr('id') + ID + '" class="mf_uploader_item"><div class="mf_uploader_cancel"><a onclick="mf_uploader.cancel(\'#'+E.attr('id')+'\', \''+ID+'\')"><img src="' + s.cancelImg + '" border="0" /></a></div><span class="mf_uploader_fileName">' + fileName + ' ('+mf_uploader.format_size(file.size)+')</span><span class="mf_uploader_status"></span><div class="mf_uploader_progress"><div id="' + E.attr('id') + ID + 'ProgressBar" class="mf_uploader_progressbar"></div></div></div>');
				});
			E.bind("mf_uploader_event_cancel", function(event, ID, data){
				if(jQuery("#" + E.attr('id') + ID + " .mf_uploader_status").text().indexOf('%') != -1){
					jQuery("#" + E.attr('id') + ID + " .mf_uploader_status").text(' - Canceled');
					}
				jQuery("#" + E.attr('id') + ID).fadeOut(1000, function(){ jQuery(this).remove() });
				});
			E.bind("mf_uploader_event_error", function(event, ID, data){
				jQuery("#" + E.attr('id') + ID + " .mf_uploader_status").text(" - " + data.type + " Error");
				jQuery("#" + E.attr('id') + ID).addClass('mf_uploader_error');
				});
			E.bind("mf_uploader_event_progress", function(event, ID, data){
				jQuery("#" + E.attr('id') + ID + "ProgressBar").css('width', data.percentage + '%');
				jQuery("#" + E.attr('id') + ID + " .mf_uploader_status").text(' - '+data.percentage+'%');
				});
			E.bind("mf_uploader_event_complete", function(event, ID, data){
				jQuery("#" + E.attr('id') + ID + " .mf_uploader_status").text(' - Completed');
				jQuery("#" + E.attr('id') + ID).fadeOut(1000, function(){ jQuery(this).remove() });
				});
			});
		},
	setting: function(z, settingName, settingValue, reset){
		var returnValue = false;
		jQuery(z).each(function(i, E){ E = jQuery(E);
			if (settingName == 'scriptData' && settingValue != null){
				if (reset){
					var scriptData = settingValue;
					}
				else {
					var scriptData = jQuery.extend(s.scriptData, settingValue);
					}
				var scriptDataString = '';
				for (var name in scriptData){
					scriptDataString += '&' + name + '=' + escape(scriptData[name]);
					}
				settingValue = scriptDataString.substr(1);
				}
			returnValue = mf_uploader._$(E.attr('id') + 'Uploader').mf_uploader_commute_setting(settingName, settingValue);
			});
		if (settingValue == null){
			if (settingName == 'scriptData'){
				var returnSplit = unescape(returnValue).split('&');
				var returnObj = new Object();
				for (var i = 0; i < returnSplit.length; i++){
					var iSplit = returnSplit[i].split('=');
					returnObj[iSplit[0]] = iSplit[1];
					}
				returnValue = returnObj;
				}
			}
		return returnValue;
		},
	start: function(z, ID){
		jQuery(z).each(function(i, E){ E = jQuery(E);
			mf_uploader._$(E.attr('id') + 'Uploader').mf_uploader_commute_start(ID, false);
			});
		},
	cancel: function(z, ID){
		jQuery(z).each(function(i, E){ E = jQuery(E);
			mf_uploader._$(E.attr('id') + 'Uploader').mf_uploader_commute_cancel(ID);
			});
		},
	clearPending: function(z){
		jQuery(z).each(function(i, E){ E = jQuery(E);
			mf_uploader._$(E.attr('id') + 'Uploader').mf_uploader_commute_clear();
			});
		},
	clearCompleted: function(z){
		jQuery(z).each(function(i, E){ E = jQuery(E);
			var q = mf_uploader._$(E.attr('id') + 'Uploader').mf_uploader_commute_setting('queueID');
			mf_uploader._$(E.attr('id') + 'Uploader').mf_uploader_commute_clear();
			jQuery('#'+q+' .mf_uploader_item').each(function(j, F){
				if(jQuery('#'+F.id+" .mf_uploader_status").text().indexOf('%') == -1){
					jQuery(F).fadeOut(500, function(){ jQuery(this).remove() });
					}
				});
			});
		},
	
	cg: function(){
		jQuery('.mf_uploader_child').removeAttr('disabled').slideUp(500);
		jQuery('#'+mf_uploader.si).removeAttr('disabled').val("0");
		jQuery('#'+mf_uploader.ui+'Uploader').hide();
		setTimeout(function(){ jQuery('#'+mf_uploader.ui+'_container').slideUp(500); }, 300);
		mf_uploader.active = [];
		},
	cd: function(f,c){
		c++;
		try{ mf_uploader.setting('#'+mf_uploader.ui, 'folder', f); }
		catch(e){
			if(typeof(console)!='undefined') console.log(e);
			if(c==10){ alert('Your browser does not appear to support this uploader. Upgrade your browser or try a different one. \n\nContact Us if this problem doesn\'t go away.'); }
			else{ setTimeout(function(){ mf_uploader.cd(f,c); }, 500); }
			}
		},
	change: function(e){
		var s = jQuery(e).attr('disabled', 'disabled').val().split('~'), f = '';
		mf_uploader.active.push(s[1]);
		if(s[0] == 'child'){
			jQuery.each(mf_uploader.active, function(i,v){ f+=v+'/'; });
			jQuery('#'+mf_uploader.ui+'_status').html('Uploading to '+f);
			jQuery('#'+mf_uploader.ui+'_container').slideDown(500);
			setTimeout(function(){ jQuery('#'+mf_uploader.ui+'Uploader').show(); }, 300);
			jQuery('body,html').delay(500).animate({ scrollTop: jQuery('#'+mf_uploader.ui+'_container').offset()['top'] }, 1000);
			mf_uploader.cd(f,0);
			}
		if(s[0] == 'parent'){
			jQuery('#'+s[2]).slideDown(500);
			}
		},
		
	zzz: function(){ alert('(C)David Tyler [Current Year]'); }
	};