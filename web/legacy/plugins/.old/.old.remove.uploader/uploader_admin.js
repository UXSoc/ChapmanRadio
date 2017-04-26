/*! Admin Side Multi-File Uploader
 * (C) David Tyler 2010
 */
jQuery(document).ready(function($){
	$('#sss_uploader_admin_tabs').tabs({
		ajaxOptions: {
			error: function(xhr, status, index, anchor) {
				$(anchor.hash).html("Unable to load images (network connection error)");
				}
			},
		idPrefix: 'category-',
		spinner: '<div>Loading&#8230;</div>',
		tabTemplate: '<a href="#{href}">#{label}</a>',
		show: function(ev, ui){
			/* Stuff to run when a tab link is clicked */
			window.location.hash = ui.tab.hash;
			active_tab = ui.tab.parentNode;
			$('.sss_uploader_child').each(function(i,e){
				var pretender = false;
				$(e.parentNode).find('li').each(function(j,f){
					if(this == active_tab){
						$(e.parentNode).addClass('sss_pretend_active').removeClass('ui-state-default');
						pretender = true;
						}
					});
				if(pretender == false){
					$(e.parentNode).removeClass('sss_pretend_active').addClass('ui-state-default');
					}
				});
			$('#sss_uploader_admin_tabs ul li.ui-state-default').find('ul').slideUp();
			$(active_tab).find('ul').slideDown();
			},
		load: function(ev, ui){
			/* Stuff to run when a tab content loads */
			$(".sss_uploader_ul").selectable({ filter: 'li' });
			$(".sss_uploader_ul li").draggable({
				refreshPositions:true,
				revert: 'invalid',
				cursor: 'move',
				opacity: 0.5,
				containment: '#page'
				});
			$('.sss_uploader_root li').droppable({
				accept: 'li',
				tolerance: 'pointer',
				hoverClass: 'sss_uploader_nav_droping',
				opacity: 0.5,
				over: function(ev, ui) { $(ev.target).animate({ paddingBottom: '80px' }, 500); },
				out: function(ev, ui) { $(ev.target).animate({ paddingBottom: '2px' }, 500); },
				drop: function(ev, ui) { sss_uploader_moving(ui.draggable, ev.target); }
				});
			$('.sss_uploader_middle a').attr('rel', 'fancybox').fancybox();
			$('.ui-state-active').droppable("destroy");
			$('.sss_uploader_admin_action_form').submit(function (e){
				e.preventDefault();
				var id = e.target['img_id'].value;
				// $('#'+id+'_li').prepend('<div class="sss_uploader_pending"></div>');
				if(e.target['sss_uploader_admin_action'].value == 'delete') $('#'+id+'_li').fadeOut('slow');
				if(e.target['sss_uploader_admin_action'].value == 'download') $('#'+id+'_li').fadeOut('slow');
				if(e.target['sss_uploader_admin_action'].value == 'destroy') $('#'+id+'_li').hide();
				$.post(window.location.href, $(e.target).serialize(), function(d){
					if(d!=1){
						if(d.indexOf('goto:')!=-1){
							alert(d.substr(5));
							window.location.href = d.substr(5);
							}
						else{
							alert('Unable to complete action (Server code '+d+')');
							}
						}
					// Success
					return false;
					});
				// Failure
				return false;
				});
			}
		});
	function sss_uploader_moving(img, target){
		var iimg = $(img[0]).hide().find('.sss_uploader_image');
		$(target).children('div').append(iimg);
		$(iimg).css({ height: '75px', opactiy: '1', margin: '0 auto' });
		jQuery.post(window.location.href, {
			sss_uploader_admin_action: 'move',
			img_id: img[0].id.substring(0,22),
			target: target.id },
			function(d){
				if(d!=1){
					alert('Uh Oh...The image didn\'t move properly (server returned error '+d+')');
					}
				});
		$(iimg).delay(500).animate({ opacity: 0 },700,function(){ $(target).animate({ paddingBottom: '2px' }, 200); $(this).hide(); });
		}
	});