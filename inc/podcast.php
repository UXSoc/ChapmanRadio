<?php namespace ChapmanRadio;

class Podcast {
	
	public static function generate($showid = 0) {
		if($showid <= 0) return false;
		$show = ShowModel::FromId($showid);
		if(!$show) return false;
		// let's setup the basic output
		$rss = file_get_contents(PATH."inc/templates/podcast.template.rss");
		
		if($show->seasons_csv) {
			$seasons = explode(",", $show->seasons_csv);
			foreach($seasons as $key => $val) $seasons[$key] = preg_replace("/\\D/","",$val);
			if($seasons[0] == end($seasons)) $copyright = "&#xA9; ".$seasons[0]; 
			else $copyright = "&#xA9; ".$seasons[0]."-".end($seasons);
			}
		else $copyright = "&#xA9; ".date('Y');
		
		$name = htmlspecialchars($show->name);
		$description = htmlspecialchars($show->description);
		
		$rss = str_replace("[%title%]", $name, $rss);
		$rss = str_replace("[%link%]", $show->permaurl, $rss);
		$rss = str_replace("[%copyright%]", $copyright, $rss);
		$rss = str_replace("[%subtitle%]", "{$name} on ChapmanRadio.com", $rss);
		$rss = str_replace("[%author%]", $show->GetDjNamesCsv(), $rss);
		$rss = str_replace("[%description%]", $description, $rss);
		$rss = str_replace("[%explicit%]", ($show->explicit) ? "Yes" : "No", $rss);
		$rss = str_replace("[%imageurl%]", $show->Img(310, false, false), $rss);
		
		$category = $show->podcastcategory;
		if($show->podcastcategory) {
			if(strpos($category,">") !== false) {
				list($cat, $subcat) = explode(">",$category);
				$cat = trim($cat);
				$subcat = trim($subcat);
				$category = "<itunes:category text=\"$cat\"><itunes:category text=\"$subcat\" /></itunes:category>";
			}
			else $category = "<itunes:category text=\"".trim($category)."\"></itunes:category>";
		} else $category = "<itunes:category text=\"Music\" />";
		$rss = str_replace("[%category%]", $category, $rss);
		
		// get the items
		$items = "";
		$mp3s = $show->GetRecordings();
		$count = 0;
		foreach($mp3s as $mp3id => $mp3) {
			$mp3 = new RecordingModel($mp3);
			if(!$mp3->Exists()) continue;
			//if(!$mp3['label']) continue;
			$count ++;
			$items .= "<item>\n";
			$title = htmlentities($mp3->label ?: "Untitled Episode");
			$items .= "\t<title>$title</title>\n";
			$items .= "\t<itunes:author>".$show->GetDjNamesCsv()."</itunes:author>\n";
			$description = $mp3->description ? htmlspecialchars(html_entity_decode($mp3->description, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8') : date("F jS, Y", $mp3->timestamp);
			$subtitle = substr($description,0,255);
			$items .= "\t<itunes:subtitle>$description</itunes:subtitle>\n";
			$items .= "\t<itunes:summary>$description</itunes:summary>\n";
			$items .= "\t<enclosure url=\"".$mp3->PubUrl("podcast")."\" length=\"".$mp3->filesize()."\" type=\"audio/mpeg\" />\n";
			$items .= "\t<guid>http://guid.chapmanradio.com/mp3/{$mp3->id}</guid>\n";
			$items .= "\t<pubDate>".date('r', $mp3->timestamp)."</pubDate>\n";
			$duration = mp3::duration($mp3->url);
			$items .= "\t<itunes:duration>$duration</itunes:duration>\n";
			$items .= "</item>\n";
			}
		if($count) DB::Query("UPDATE shows SET podcastenabled=1 WHERE showid='$showid'");
		$rss = str_replace("[%items%]", $items, $rss);
		//file_put_contents(PATH."podcast/$showid.rss", $rss);
		return $rss;
		}
	
	public static $categories = array(
		'Arts' => array(
			'Arts'=>'Arts',
			'Arts > Design' => 'Design',
			'Arts > Fashion &amp; Beauty' => 'Fashion &amp; Beauty',
			'Arts > Food' => 'Food',
			'Arts > Literature' => 'Literature',
			'Arts > Performing Arts' => 'Performing Arts',
			'Arts > Visual Arts' => 'Visual Arts'
			),
		'Business' => array(
			'Business'=>'Business',
			'Business > Business News' => 'Business News',
			'Business > Careers' => 'Careers',
			'Business > Investing' => 'Investing',
			'Business > Management &amp; Marketing' => 'Management &amp; Marketing',
			'Business > Shopping' => 'Shopping'
			),
		'Comedy' => array(
			'Comedy'=>'Comedy'
			),
		'Education' => array(
			'Education'=>'Education',
			'Education > Education Technology' => 'Education Technology',
			'Education > Higher Education' => 'Higher Education',
			'Education > K-12' => 'K-12',
			'Education > Language Courses' => 'Language Courses',
			'Education > Training' => 'Training'
			),
		'Games &amp; Hobbies' => array(
			'Games &amp; Hobbies'=>'Games &amp; Hobbies',
			'Games &amp; Hobbies > Automotive' => 'Automotive',
			'Games &amp; Hobbies > Aviation' => 'Aviation',
			'Games &amp; Hobbies > Hobbies' => 'Hobbies',
			'Games &amp; Hobbies > Other Games' => 'Other Games',
			'Games &amp; Hobbies > Video Games' => 'Video Games',),
		'Government &amp; Organizations' => array(
			'Government &amp; Organizations'=>'Government &amp; Organizations',
			'Government &amp; Organizations > Local' => 'Local',
			'Government &amp; Organizations > National' => 'National',
			'Government &amp; Organizations > Non-Profit' => 'Non-Profit',
			'Government &amp; Organizations > Regional' => 'Regional',),
		'Health' => array(
			'Health'=>'Health',
			'Health > Alternative Health' => 'Alternative Health',
			'Health > Fitness &amp; Nutrition' => 'Fitness &amp; Nutrition',
			'Health > Self-Help' => 'Self-Help',
			'Health > Sexuality' => 'Sexuality',),
		'Kids &amp; Family' => array(
			'Kids &amp; Family'=>'Kids &amp; Family',),
		'Music' => array(
			'Music'=>'Music',),
		'News &amp; Politics' => array(
			'News &amp; Politics'=>'News &amp; Politics',),
		'Religion &amp; Spirituality' => array(
			'Religion &amp; Spirituality'=>'Religion &amp; Spirituality',
			'Religion &amp; Spirituality > Buddhism' => 'Buddhism',
			'Religion &amp; Spirituality > Christianity' => 'Christianity',
			'Religion &amp; Spirituality > Hinduism' => 'Hinduism',
			'Religion &amp; Spirituality > Islam' => 'Islam',
			'Religion &amp; Spirituality > Judaism' => 'Judaism',
			'Religion &amp; Spirituality > Other' => 'Other',
			'Religion &amp; Spirituality > Spirituality' => 'Spirituality',),
		'Science &amp; Medicine' => array(
			'Science &amp; Medicine'=>'Science &amp; Medicine',
			'Science &amp; Medicine > Medicine' => 'Medicine',
			'Science &amp; Medicine > Natural Sciences' => 'Natural Sciences',
			'Science &amp; Medicine > Social Sciences' => 'Social Sciences',),
		'Society &amp; Culture' => array(
			'Society &amp; Culture'=>'Society &amp; Culture',
			'Society &amp; Culture > History' => 'History',
			'Society &amp; Culture > Personal Journals' => 'Personal Journals',
			'Society &amp; Culture > Philosophy' => 'Philosophy',
			'Society &amp; Culture > Places &amp; Travel' => 'Places &amp; Travel',),
		'SportsController &amp; Recreation' => array(
			'SportsController &amp; Recreation'=>'SportsController &amp; Recreation',
			'SportsController &amp; Recreation > Amateur' => 'Amateur',
			'SportsController &amp; Recreation > College &amp; High School' => 'College &amp; High School',
			'SportsController &amp; Recreation > Outdoor' => 'Outdoor',
			'SportsController &amp; Recreation > Professional' => 'Professional',),
		'Technology' => array(
			'Technology'=>'Technology',
			'Technology > Gadgets' => 'Gadgets',
			'Technology > Tech News' => 'Tech News',
			'Technology > Podcasting' => 'Podcasting',
			'Technology > Software How-To' => 'Software How-To',),
		);
	
	}