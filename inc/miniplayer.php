<?php namespace ChapmanRadio;

	if(!function_exists('miniPlayer')) {
		function miniPlayer($mp3) {
			return  "<object type='application/x-shockwave-flash' data='/plugins/flashmp3player/player_mp3_maxi.swf' width='25' height='20'>
			<param name='movie' value='/plugins/flashmp3player/player_mp3_maxi.swf' />
			<param name='FlashVars' value='mp3=$mp3&amp;showslider=0&amp;width=25' />
			</object>";
		}
	}