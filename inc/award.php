<?php namespace ChapmanRadio;

class Award {
	public static $awards = array(
		'showoftheweek' => array("Show of the Week", "/img/icons/showoftheweek50.png"),
		'bestshowofthesemester' => array("Show of the Semester","/img/icons/brightstar50.png"),
		'highestpeak' => array("Highest Listenership Peak","/img/icons/stats50.png"),
		'highestaverage' => array("Highest Average Listeners","/img/icons/stats50.png"),
		'bestworldshow' => array("Best World Show","/img/icons/world50.png"),
		'bestmixingshow' => array("Best Mixing Show","/img/icons/turntable50.png"),
		'bestloudshow' => array("Best Loud Show","/img/icons/loudguitar50.png"),
		'bestoldiesshow' => array("Best Oldies Show","/img/icons/djwithfro50.png"),
		'bestsportsshow' => array("Best SportsController Show","/img/icons/soccer50.png"),
		'besthiphopshow' => array("Best Hip Hop show","/img/icons/turntable50.png"),
		'bestindieshow' => array("Best Indie show","/img/icons/indie50.png"),
		'besttalkshow' => array("Best Talk Show","/img/icons/oldmic50.png"),
		'besttop40show' => array("Best Top 40 Show","/img/icons/speaker50.jpg"),
		'beststaffshow' => array("Best Staff Show","/img/icons/djwithfro50.png"),
		'bestnewshow' => array("Best New Show","/img/icons/new50.png"),
		'bestelectronicshow' => array("Best Electronic Show","/img/icons/vinyl50.jpg"),
		'bestlivemixingshow' => array("Best Live Mixing Show","/img/icons/vinyl50.jpg"),
		'mostimprovedshow' => array("Most Improved Show","/img/icons/headphones50.jpg"),
		'mostpromisingdj' => array("Most Promising DJ","/img/icons/headphones50.jpg"),
		'djofthesemester' => array("DJ of the Semester","/img/icons/djwithfro50.png"),
		'seniorshowofthesemester' => array("Senior Show of the Semester","/img/icons/djwithfro50.png"),
		'promograndprize' => array("Promo Contest - Grand Prize", "/img/icons/showoftheweek50.png"),
		'promolistenership' => array("Promo Contest - Listenership", "/img/icons/showoftheweek50.png"),
		'promocreativity' => array("Promo Contest - Creativity", "/img/icons/showoftheweek50.png"),
		'promomostcreativeflyer' => array("Promo Contest - Creative Flyer", "/img/icons/showoftheweek50.png"),
		'promosocialmediamaven' => array("Promo Contest - Social Media", "/img/icons/showoftheweek50.png")
		);
	
	public static function name($type) {
		$a = Award::$awards;
		if(isset($a[$type])) return $a[$type][0];
		else return "an Award";
	}
	
	public static function icon($type) {
		$a = Award::$awards;
		if(isset($a[$type])) return $a[$type][1];
		else return "/img/icons/showoftheweek50.png";
	}
	
	public static function date($type, $time) {
		if(!is_numeric($time)) $time = strtotime($time);
		switch($type) {
			case 'showoftheweek':
				return date("F jS, Y", $time);
			default:
				return date("m", $time) < 8 ? 'Spring '.date("Y",$time) : 'Fall '.date("Y",$time);
			}
		}
	}