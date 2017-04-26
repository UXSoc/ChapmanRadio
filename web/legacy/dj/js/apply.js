function updateContinueShowButton() {
	if($('#showid').val()) {
		$('#continueshowsubmit').val('Continue '+shows[$('#showid').val()]);
		$('#continueshowsubmit').prop('disabled', false);
		}
	else {
		$('#continueshowsubmit').val('Continue');
		$('#continueshowsubmit').prop('disabled', true);
		}
	}

function checkcboxes(){
	var problem = false;
	$('#apply-checkboxes input[type=checkbox]').each(function(i, e){
		if(!$(e).is(':checked')) {
			$('#submitbutton').prop('disabled', true);
			problem = true;
			return;
			}
		});
	if(!problem) $('#submitbutton').prop('disabled', false);
	}
	
function checkAcceptBoxes(){
	$('#newshowsubmittbutton').prop('disabled', $('#accept1').is(':checked') && $('#accept2').is(':checked') ? false : true);
	}
	
function checkAppCheckBoxes() {
	var arr = ['newshow','existingshow','codj'];
	for(var index in arr) {
		x = arr[index];
		if($('#'+x).is(':checked'))
			$('#'+x+'div').slideDown();
		else
			$('#'+x+'div').slideUp();
		}
	}

$(document).ready(function(){
	$('#showname').watermark('Enter a Show Name');
	$('#code').watermark('Enter your Code');
	$('#showname').watermark('Show Name');
	$('#email').watermark('email@address.com');	
	updateContinueShowButton();
	checkcboxes();
	checkAcceptBoxes();
	checkAppCheckBoxes();
	});