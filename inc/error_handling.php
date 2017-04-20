<?php
register_shutdown_function('__chapmanradio_error_handling'); 
function __chapmanradio_error_handling(){
	if(!isset($_SERVER['SERVER_NAME']) || $_SERVER['SERVER_NAME'] != "chapmanradio.com") return;
	if(is_null($e = error_get_last()) === false){
		if($e['type'] <= E_CORE_WARNING){
			try{
				if(class_exists('Log')) Log::Error('PHP Error', $e);
				else mail('webmaster@chapmanradio.com', "[CHAPMANRADIO] UNLOGGABLE", print_r($e, true)."\n\nServer:".print_r($_SERVER, true));
				}
			catch(Exception $e){
				// Well this sucks if we can't even log the error
				}
			}
		}
	}