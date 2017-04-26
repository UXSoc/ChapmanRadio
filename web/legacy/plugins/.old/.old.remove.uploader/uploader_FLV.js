/*
SeniorSlideShow Uploader 0.9 Build 3269
(C) David Tyler 2010
*/

import flash.external.ExternalInterface;
import flash.net.*;
import flash.events.*;
import flash.display.*;
import com.adobe.serialization.json.JSON;

stage.align = StageAlign.TOP_LEFT;
stage.scaleMode = StageScaleMode.NO_SCALE;
var param:Object = LoaderInfo(this.root.loaderInfo).parameters;
var fRef:FileReferenceList = new FileReferenceList();
var fQueue:Array = new Array();
var fItem:Object = new Object();
var aQueue:Object = new Object();
var allowedTypes:Array;
var scriptURL:URLRequest;

function $trigger(eName:String, ... args):void {
	var list:Array = ['"'+eName+'"'];
	if (args.length > 0) list.push(JSON.encode(args));
	ExternalInterface.call('jQuery("#'+param.ID+'").trigger('+list.join(',')+')');
	}
function loadButton():void {
	if (param.buttonText) {
		browseBtn.empty.buttonText.text = unescape(param.buttonText);
		browseBtn.empty.alpha = 1;
		}
	if (param.buttonImg) {
		var btnLoader:Loader    = new Loader();
		var btnImage:URLRequest = new URLRequest(param.buttonImg);
		browseBtn.addChild(btnLoader);
		btnLoader.load(btnImage);
		browseBtn.empty.alpha = 0;
		}
	ExternalInterface.call('jQuery("#' + param.ID + '").attr("width",' + param.width + ')');
	ExternalInterface.call('jQuery("#' + param.ID + '").attr("height",' + param.height + ')');
	if (param.rollover) {
		browseBtn.addEventListener(MouseEvent.ROLL_OVER, function (event:MouseEvent):void {
			event.currentTarget.y = -param.height;
			});
		browseBtn.addEventListener(MouseEvent.ROLL_OUT, function (event:MouseEvent):void {
			event.currentTarget.y = 0;
			});
		browseBtn.addEventListener(MouseEvent.MOUSE_DOWN, function (event:MouseEvent):void {
			event.currentTarget.y = -(param.height * 2);
			});
		}
	browseBtn.buttonMode = true;
	browseBtn.useHandCursor = true;
	browseBtn.mouseChildren = false;
	allowedTypes = [];
	if (param.fileDesc && param.fileExt) {
		var fileDescs:Array = param.fileDesc.split('|');
		var fileExts:Array = param.fileExt.split('|');
		for (var n = 0; n < fileDescs.length; n++) {
			allowedTypes.push(new FileFilter(fileDescs[n], fileExts[n]));
			}
		}
	}
function objSize(obj:Object):Number {
	var i:int = 0;
	for (var item in obj) {
		i++;
		}
	return i;
	}
function getIndex(ID:String):Number {
	var index:int;
	for (var n:Number = 0; n < fQueue.length; n++) {
		if (fQueue[n].ID == ID) {
			return n;
			}
		}
	return -1;
	}
function genID(len:Number):String {
	var chars:Array = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
	var ID:String = '';
	var index:Number;
	for (var n:int = 0; n < len; n++) {
		ID += chars[Math.floor(Math.random() * 25)];
		}
	return ID;
	}
function fFinish(ID){
	fQueue.splice(getIndex(ID), 1);
	delete aQueue[ID];
	if(fQueue.length == 0){
		$trigger('sss_uploader_event_finished', {});
		}
	else{
		$trigger('sss_uploader_event_looping', {});
		sss_uploader_start();
		}
	}

loadButton();
browseBtn.addEventListener(MouseEvent.CLICK, function():void {
	if(objSize(aQueue) == 0){
		if (!allowedTypes){
			fRef.browse();
			}
		else {
			fRef.browse(allowedTypes);
			}
		}
	});
fRef.addEventListener(Event.SELECT, function(event:Event):void {
	var ID:String = '';
	for (var n:Number = 0; n < fRef.fileList.length; n++) {
		ID = genID(20);
		fItem = new Object();
		fItem.file = fRef.fileList[n];
		fItem.ID = ID;
		fQueue.unshift(fItem);
		$trigger('sss_uploader_event_select', ID, fItem.file);
		}
	sss_uploader_start();
	});

function sss_uploader_setting(setName:String, setValue){
	if(setValue == null) param[setName] = setValue;
	loadButton();
	return param[setName];
	}
function sss_uploader_start(){
	if (param.script.substr(0,1) != '/' && param.script.substr(0,4) != 'http') param.script = param.pagepath + param.script;
	scriptURL = new URLRequest(param.script);
	var vars = new URLVariables();
	scriptURL.method = URLRequestMethod.POST;
	if (param.scriptData) vars.decode(unescape(param.scriptData));
	if (param.fileExt) vars.fileext = unescape(param.fileExt);
	vars.folder = unescape(param.folder);
	scriptURL.data = vars;
	for (var n:int = fQueue.length - 1; n > -1; n--) {
		if (objSize(aQueue) < parseInt(param.simUploadLimit)) {
			if (fQueue[n].file) {
				var ID:String = fQueue[n].ID;
				fQueue[n].file.addEventListener(Event.OPEN, function(event:Event):void {
					$trigger('sss_uploader_event_init', ID, event.currentTarget);
					});
				fQueue[n].file.addEventListener(ProgressEvent.PROGRESS, function(event:ProgressEvent):void {
					var percentage:Number = Math.round((event.bytesLoaded / event.bytesTotal) * 100);
					$trigger('sss_uploader_event_progress', ID, {'percentage': percentage});
					});
				fQueue[n].file.addEventListener(DataEvent.UPLOAD_COMPLETE_DATA, function comHand(event:DataEvent):void {
					if(event.data != '1'){
						$trigger('sss_uploader_event_error', ID, {'type' : event.data});
						}
					else{
						$trigger('sss_uploader_event_progress', ID, {'percentage': 100});
						$trigger('sss_uploader_event_complete', ID, {});
						}
					fFinish(ID);
					event.currentTarget.removeEventListener(DataEvent.UPLOAD_COMPLETE_DATA, comHand);
					});
				fQueue[n].file.addEventListener(HTTPStatusEvent.HTTP_STATUS, function(event:HTTPStatusEvent):void {
					$trigger('sss_uploader_event_error', ID, {'type' : 'HTTP', 'info' : event.status});
					fFinish(ID);
					});
				fQueue[n].file.addEventListener(IOErrorEvent.IO_ERROR, function(event:IOErrorEvent):void {
					$trigger('sss_uploader_event_error', ID, {'type' : 'Connection (I/O)', 'info' : event.text});
					fFinish(ID);
					});
				fQueue[n].file.addEventListener(SecurityErrorEvent.SECURITY_ERROR, function(event:SecurityErrorEvent):void {
					$trigger('sss_uploader_event_error', ID, {'type' : 'Security', 'info' : event.text});
					fFinish(ID);
					});
				
				if (param.sizeLimit && fQueue[n].file.size > parseInt(param.sizeLimit)) {
					$trigger('sss_uploader_event_error', ID, {'type' : 'File Size', 'info' : param.sizeLimit });
					fFinish(ID);
					}
				else {
					fQueue[n].file.upload(scriptURL, param.fileDataName, false);
					aQueue[ID] = true;
					}
				}
			}
		else {
			break;
			}
		}
	return;
	}
function sss_uploader_cancel(ID:String){
	var index:int = getIndex(ID);
	if(index != -1){
		var fileObj:Object = new Object();
		if (fQueue[index].file) {
			fileObj = fQueue[index].file;
			fQueue[index].file.cancel();
			}
		fFinish(ID);
		}
	$trigger('sss_uploader_event_cancel', ID, {});
	return;
	}
function sss_uploader_clear(){
	if(fQueue.length > 0){
		for (var n:Number = 0; n < fQueue.length; n++){
			sss_uploader_cancel(fQueue[n].ID);
			}
		}
	$trigger('sss_uploader_event_cleared');
	return;
	}

ExternalInterface.addCallback('sss_uploader_commute_setting', sss_uploader_setting);
ExternalInterface.addCallback('sss_uploader_commute_start', sss_uploader_start);
ExternalInterface.addCallback('sss_uploader_commute_cancel', sss_uploader_cancel);
ExternalInterface.addCallback('sss_uploader_commute_clear', sss_uploader_clear);