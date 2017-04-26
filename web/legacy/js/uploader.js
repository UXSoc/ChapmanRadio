$(document).ready(function(){
	$(document).bind('drop dragover', function (e) { e.preventDefault(); });
	$('.cr_uploader').each(function(i, e){ new CRUploader(e); });
	});
	
function CRUploaderEventInstance(event){
	return CRUploaderInstance($(event.currentTarget).parents('form'));
	}
	
function CRUploaderInstance(form){
	return $(form).data('CRUploader');
	}

function CRUploader(form){

	if($(form).attr('cr_uploader_loaded') != null) return;
	
	var f = this.form = $(form);
	
	f.attr('cr_uploader_loaded', true)
	
	this.id = f.attr('id');
	
	this.status = null;
	this.image = null;
	this.editimage = null;
	this.jcrop = null;
	this.key = null; 
	
	this.boundx = 0;
    this.boundy = 0;
	
	var instance = this;
	
	f.on('click', "input[name='cr_uploader_savecrop']", function(event){
		event.preventDefault();
		CRUploaderEventInstance(event).SaveCrop();
		});
	
	f.on('click', "input[name='cr_uploader_cancelcrop']", function(event){
		event.preventDefault();
		CRUploaderEventInstance(event).CancelCrop();
		});
		
	f.data('CRUploader', this);
	
	//return;
	
	f.fileupload({
		url: '/ajax/uploader.php',
		dataType: 'json',
		dropZone: null,
		
		submit: function (e, data){
			var instance = CRUploaderInstance(data.form);
			console.log(instance.id + ' - fileupload.submit');
			$(".cr_uploader_key", data.form).val('');
			$('input[type=file], input[type=submit]', data.form).prop('disabled', true);
			instance.SetStatus('Uploading ... ');
			},
		progress: function (e, data){
			var instance = CRUploaderInstance(data.form);
			console.log(instance.id + ' - fileupload.progress');
			instance.SetStatus('Uploading '+parseInt((data.loaded / data.total) * 100, 10)+'% ...');
			},
		fail: function (e, data){
			var instance = CRUploaderInstance(data.form);
			console.log(instance.id + ' - fileupload.fail');
			alert("Sorry, there was a problem with your upload\n\n Please try again");
			instance.SetStatus('Error during upload (failed)');
			},
		done: function (e, data){
			var instance = CRUploaderInstance(data.form);
			console.log(instance.id + ' - fileupload.done');
			if (data.result.result != "success"){
				alert("Sorry, there was a problem with your upload: \n\n"+data.result.message+"\n\n Please try again");
				instance.SetStatus('Error during upload (problem)!');
				}
			else {
				instance.SetStatus('Uploading 100% ...');
				$('.cr_uploader_key', instance.form).val(data.result.key);
				$('.cr_uploader_upload_buttons', instance.form).hide();
				$('.cr_uploader_crop_buttons', instance.form).show();
				instance.StartCrop(data);
				}
			}
		
		});
	
	// the fileupload has been created, hide the fallback upload button
	$("input[name='cr_uploader_submit']", f).hide();
	
	// add a status div
	f.append("<div class='cr_uploader_status'>"+this.id+"</div>");
	this.status = $('.cr_uploader_status', f);
	
	this.SetStatus = function(msg){
		console.log(this.id + ' - status - ' + msg);
		this.status.html(msg);
		}
	
	this.SaveCrop = function(){
		var instance = this;
		console.log(instance.id + ' - SaveCrop');
		instance.SetStatus('Saving ...');
		
		$('input[type=file], input[type=submit]', instance.form).prop('disabled', true);
		
		var crops = instance.currentCrop();
        if (crops == null) return;
		
		$("input[name='cr_uploader_crop_x']", instance.form).val(crops.x);
		$("input[name='cr_uploader_crop_y']", instance.form).val(crops.y)
		$("input[name='cr_uploader_crop_w']", instance.form).val(crops.w)
		$("input[name='cr_uploader_crop_h']", instance.form).val(crops.h);
		
		instance.DoSaveCrop();
		};
		
	this.DoSaveCrop = function(){
		var instance = this;
		jQuery.ajax({
            type: 'POST',
            url: '/ajax/uploader.php',
			dataType: 'json',
            data: instance.form.serialize(),
            success: function (data) { instance.PostSaveCrop(data); },
            error: function () {
                alert("Sorry, there was a problem with your upload. Please try again");
				}
			});
		};
		
	this.PostSaveCrop = function(data){
		var instance = this;
		if (data.result != "success"){
			alert("Sorry, there was a problem with your upload. Please try again");
			instance.SetStatus('Error during save!');
			return;
			}
		instance.SetStatus('Saved! Loading new image ... ');
		instance.image = new Image();
		instance.image.onload = function(event){
			instance.SetStatus('Saved!');
			instance.CancelCrop();
			};
		instance.image.src = data.file;
		},
	
	this.StartCrop = function(data){
		var instance = this;
		console.log(instance.id + ' - StartCrop');
		
		cropper = $('#'+instance.form.attr('data-cropper'));
		var editimage = new Image();
		editimage.onload = function(event){
			instance.SetStatus('Uploading complete. Ready to edit');
			cropper.attr('src', editimage.src).addClass('cropper').Jcrop({
				aspectRatio: instance.form.find("[name='cr_uploader_crop_aspect']").val(),
				bgOpacity: 0.2,
				allowSelect: false
				}, function () {
					instance.jcrop = this;
					var bounds = this.getBounds();
					instance.boundx = bounds[0];
					instance.boundy = bounds[1];
					instance.jcrop.setSelect([0, 0, bounds[0], bounds[1]]);
					instance.jcrop.focus();
				});
			};
		
		editimage.src = data.result.rawfile;
		instance.editimage = editimage;
		
		instance.image = new Image();
		instance.image.src = data.result.file;
		
		$('input[type=file], input[type=submit]', instance.form).prop('disabled', false);
		}
	
	this.currentCrop = function () {
        if (this.jcrop == null) return null;
        size = this.jcrop.tellSelect();
        bounds = this.jcrop.getBounds();
        return {
            x: (size.x / bounds[0]) * this.editimage.width,
            y: (size.y / bounds[1]) * this.editimage.height,
            w: (size.w / bounds[0]) * this.editimage.width,
            h: (size.h / bounds[1]) * this.editimage.height
			}
		};
	
	this.CancelCrop = function(){
		var instance = this;
		console.log(instance.id + ' - CancelCrop');
		
		if (instance.jcrop != null) instance.jcrop.destroy();
		instance.jcrop = null;
		
		cropper = '#'+instance.form.attr('data-cropper');
		var parent = $(cropper).parent();
		$(cropper).remove();
		parent.append('<img id='+instance.form.attr('data-cropper')+' src='+instance.image.src+' style=\'max-width: 310px;\' />');
		
		$('.cr_uploader_upload_buttons', instance.form).show();
		$('.cr_uploader_crop_buttons', instance.form).hide();
		$('input[type=file], input[type=submit]', instance.form).prop('disabled', false);
		
		};
	};
