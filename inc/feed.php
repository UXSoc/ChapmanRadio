<?php namespace ChapmanRadio;

require_once BASE."/lib/snoopy.php";

class Feed {
	public function rss($cachename, $numresults=-1) {
		// get data (live or from cache)
		
		if(Cache::expired($cachename)) {
//				echo "$cachename cache expired<br />";
			$snoopy = new Snoopy;
			switch($cachename) {
				case 'twitter':
					$snoopy -> fetch("http://twitter.com/statuses/user_timeline/22593584.rss");
					break;
				case 'tumblr':
					$snoopy -> fetch("http://chapmanradio.tumblr.com/rss");
					break;
				case 'facebook':
					$snoopy -> fetch("http://www.facebook.com/feeds/page.php?format=atom10&id=121173777829");
					//$snoopy -> fetch("http://www.facebook.com/feeds/notifications.php?id=100000227407355&viewer=100000227407355&key=ff9949c1ea&format=rss20");
					break;
				case 'football':
					$snoopy -> fetch("http://www.chapmanathletics.com/sports/fball/2010-11/schedule?print=rss");
					break;
				case 'baseball':
					$snoopy -> fetch("http://www.chapmanathletics.com/sports/bsb/2009-10/schedule?print=rss");
					break;
				case 'basketball':
					$snoopy -> fetch("http://www.chapmanathletics.com/sports/mbkb/2010-11/schedule?print=rss");
					break;
				default:
					if(DEBUG)
						die('Feed:rss() called with unrecognized cachename: '.$cachename);
					else
						return "";
			}
			$dat = $snoopy -> results;
			Cache::update($cachename, $dat);
		}
		else
			$dat = Cache::data($cachename);
		// parse twitter feed
		require_once PATH."inc/xml2array.php";
		$raw = xml2array($dat);
		$none = "<li class='data'>No Recent Results</li>";
		if(!isset($raw['rss']))
			return $none;
		$feed = $raw['rss']['channel']['item'];
		$total = count($feed);
		if($numresults == -1)
			$numresults = $total;
		if($total == 0)
			return $none;
		if(!isset($feed[0])) // if there is only one <item> in rss, then it doesnt list it as an array. this fixes that
			$feed = array($feed);
		$ret = "";
		for($i = 0; $i < $numresults && isset($feed[$i]); $i++) {
			$message = $feed[$i]['title'];
			if($cachename == 'twitter')
				$message = substr($message, strlen('chapman_radio: '));
			else if($cachename == 'facebook')
				$message = str_replace('on your Wall', 'on Chapman Radioman\'s Wall', $message);
			$date = date("F jS", strtotime($feed[$i]['pubDate']));
			$link = htmlentities($feed[$i]['link']);
			$ret .= "<li class='data'><a href='$link'><span class='date'>on $date</span><span class='message'>$message</span></a></li>";
		}
		return $ret;
	}
}