<?php namespace ChapmanRadio;

class Evals {
	public static function categories($merge = true) {
		$good = array(
			"greattransition" => array("icon"=>"/img/icons/tags50.png","label"=>"Great Transition","description"=>"Music and/or talk transitioned smoothly, usually by use of a tag, sweeper, or crossfade","type"=>"button"),
			"promopsa" => array("icon"=>"/img/icons/newspaper_48.png","label"=>"Promo / PSA","description"=>"Read a promo or Public Service Announcement (PSA)","type"=>"button"),
			"goodbed" => array("icon"=>"/img/icons/bed50.png","label"=>"Great Music Bed","description"=>"Played music in the background while talking","type"=>"button"),
			"nicetalk" => array("icon"=>"/img/icons/oldmic50.png","label"=>"Pro Speech Delivery","description"=>"Clear speech delivery and great tone of voice","type"=>"button"),
			"goodcomment" => array("icon"=>"/img/icons/chat50.png","label"=>"Custom Comment","description"=>"Write a custom positive comment in your evaluation","type"=>"comment"),
			);
		$bad = array(
			"deadair" => array("icon"=>"/img/icons/deadair50.png","label"=>"Dead Air","description"=>"Broadcast was silent (no music or talk) for any time - Even as little as 1 second is dead air","type"=>"button"),
			"needsabed" => array("icon"=>"/img/icons/bedbw50.png","label"=>"Needs a Music Bed","description"=>"Plain speech should be accompanied by a music bed: instrumental audio that is played in the background under talking","type"=>"button"),
			"poorlevels" => array("icon"=>"/img/icons/mixer50.png","label"=>"Poor Levels","description"=>"Music and/or talk is <b>too loud or too soft</b> and needs improvement","type"=>"button"),
			"profanity" => array("icon"=>"/img/icons/explicit50.jpg","label"=>"Profanity","description"=>"Uncensored explicit language was used","type"=>"button"),
			"badcomment" => array("icon"=>"/img/icons/chatbw50.png","label"=>"Custom Comment","description"=>"Write a custom comment about an area of improvement","type"=>"comment"),
			 );
		if($merge) return array_merge($good, $bad);
		else return array("good"=>$good,"bad"=>$bad);
		}
	}