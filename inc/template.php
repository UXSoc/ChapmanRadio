<?php namespace ChapmanRadio;

class Template {

	private static $sitename = "Chapman Radio";
	private static $title = "";
	private static $body = "";
	private static $bodystart = "";
	private static $bodytitle = "";
	private static $bodyclass = "";
	private static $bodyalert = "";
	private static $heading = "";
	private static $prehead = [];
	private static $head = array();
	
	private static $scripts = array();
	private static $styles = array();
	
	private static $meta = array();
	private static $meta_keywords = "";
	private static $meta_description = "";
	
	private static $section = "";
	private static $template = "inc/templates/default.template.html";
	private static $headerimg = "/img/logo/default.png";
	
	private static $notify = "";
	private static $alert = "";
	
	private static $problem = false;
	private static $bootstrapping = false;
	
	public static function Finalize($xtra='') {
	
		if(isset($_GET['template'])) setcookie('template', $_GET['template'], 0, '/');
		
		if(Template::$template == "inc/templates/default.template.html" && isset($_REQUEST['template']))
			Template::$template = "inc/templates/".$_REQUEST['template'].".template.html";
		
		if(!file_exists(PATH . Template::$template)) Template::$template = "inc/templates/default.template.html";
		
		if(!self::$bootstrapping) self::$prehead[] = "<link rel='stylesheet' href='/css/prebootstrap.css' type='text/css' />\n";
		else self::$bodyclass .= "bootstrapping";
		
		/* get the proper template file*/
		$html = file_get_contents(PATH . Template::$template);
		
		// seasonal
		self::$headerimg = "/img/logo/grad2016.png";
		//self::Css("/css/snow/snow.css");
		
		if(isset($_GET['fb_banner_preview']))
			self::Style(".box942 img { width: 831px; height: 315px; }");
		
		/* logo */
		// if(isset($_GET['logo'])) setcookie('logo', $_GET['logo'], 0, '/');
		
		if(isset($_REQUEST['logo'])){
			switch($_REQUEST['logo']){ 
				case 'fall':
					Template::$headerimg = "/img/logo/logo_fall.png";
					break;
				case 'xmas':
					Template::$headerimg = "/img/logo/logo_xmas.png";
					break;
				case 'christmas':
					Template::$headerimg = "/img/logo/christmas.png";
					break;
				case 'spring':
					Template::$headerimg = "/img/logo/logo_spring.png";
					break;
				case 'halloween':
					Template::$headerimg = "/img/logo/halloween.png";
					break;
				case 'halloween2015':
					Template::$headerimg = "/img/logo/halloween2015.png";
					break;
				case 'thanksgiving':
					Template::$headerimg = "/img/logo/thanksgiving.png";
					break;
				case 'ham':
					Template::$headerimg = "/img/logo/ham.png";
					break;
				default:
					Template::$headerimg = "/img/logo/default.png";
					break;
				}
			}
		
		/* create parts of the page */
		Template::$head[] = Template::analytics();
		
		
		/* standard replaces */
		$html = str_replace('[%html_title%]', Template::$title, $html);
		$html = str_replace('[%html_prehead%]', implode("", Template::$prehead) , $html);
		$html = str_replace('[%og:url%]', "http://chapmanradio.com".$_SERVER['PHP_SELF'], $html);
		$html = str_replace('[%header_url%]', Template::$headerimg, $html);
		$html = str_replace('[%session%]', Template::generateUserSession(), $html);
		$html = str_replace('[%navbar%]', Template::generateSiteNavigation(Template::$section) , $html);
		$html = str_replace('[%usernav%]', Template::generateUserNavigation(), $html);
		$html = str_replace('[%body_alert%]', Template::$bodyalert, $html);
		$html = str_replace('[%html_body%]', Template::$heading.Template::$body.$xtra, $html);
		$html = str_replace('[%body_start%]', Template::$bodystart, $html);
		$html = str_replace('[%body_class%]', Template::$bodyclass, $html);
		$html = str_replace('[%body_title%]', Template::$bodytitle ? Template::$bodytitle : Template::$title, $html);
		$html = str_replace('{PATH}', PATH, $html);
		$html = str_replace('[%year%]', date('Y'), $html);
		$html = str_replace('[%metadescription%]', Template::$meta_description, $html);
		$html = str_replace('[%metakeywords%]', Template::$meta_keywords, $html);
		
		$load_avg = sys_getloadavg();
		$load_mem = number_format (memory_get_usage() / 1048576,  2);
		$load_mem_peek = number_format (memory_get_peak_usage() / 1048576,  2);  
		$load_time = number_format(microtime(true)-$GLOBALS['__cr_global_startime'], 2);
		$load_queries = DB::GetQueryCount();
		
		$HTTP_USER_AGENT = Request::GetFrom($_SERVER, 'HTTP_USER_AGENT');
		$REMOTE_ADDR = Request::ClientAddress();
		
		$footer_load_avg = "<div>Using {$load_time} {$load_mem} {$load_mem_peek} {$load_queries} Load {$load_avg[0]} {$load_avg[1]} {$load_avg[2]}</div>";
		
		$html = str_replace('[%footer%]', Template::$alert.Template::notification().$footer_load_avg, $html);
		
		// Do head last in case anything here added to head
		$html = str_replace('[%html_head%]', implode("", Template::$head) , $html);
		
		/* calculate render time */
		/* output */
		return $html;
//		print "\n<!-- Client: {$REMOTE_ADDR} Agent: {$HTTP_USER_AGENT} -->\n";
//		exit;
	}
	
	public static function SetPageTitle($title){
		Template::$title = $title." - ".Template::$sitename;
		if(!Template::$bodytitle) Template::$bodytitle = $title;
		}
	
	public static function SetBodyTitle($title){
		Template::$bodytitle = $title;
		}
	
	public static function SetPageTemplate($template){
		Template::$template = "inc/templates/$template.template.html";
		}
	
	public static function SetBodyHeading($heading, $subheader = ""){
		if($subheader == "") Template::$heading = "<div class='cr-page-title'>$heading</div>";
		else Template::$heading = "<div class='cr-page-title'>$heading: $subheader</div>";
		}
	
	public static function AddPageHeading($heading){
		self::Add("<div class='cr-page-heading'>$heading</div>");
		}
	
	public static function AddBodyStartContent($content){
		Template::$bodystart .= $content;
		}
	
	public static function SetBodyClass($class){
		self::$bodyclass = $class;
		}
	
	public static function AddToBodyHeading($content){
		Template::$heading .= $content;
		}
	
	public static function AddBodyHeading($heading, $subheader){
		Template::AddBodyContent("<h2 class='page_heading'>$heading: $subheader</h2>");
		}
	
	public static function SetPageSection($section){
		Template::$section = $section;
		}
	
	public static function AddBodyAlert($content){
		Template::$bodyalert .= $content;
		}
	
	public static function Add($content){
		Template::$body .= $content;
		}
	
	public static function AddBodyContent($content){
		Template::$body .= $content;
		}
	
	
	public static function AddInlineError($content){
		Template::AddBodyContent("<div class='ingos-error'>$content</div>");
		}
	
	public static function AddInlineSuccess($content){
		Template::AddBodyContent("<div class='ingos-success'>$content</div>");
		}
	
	public static function AddInlineNotice($content){
		Template::AddBodyContent("<div class='ingos-notice'>$content</div>");
		}
	
	
	public static function AddCoujuError($content){
		Template::AddBodyContent("<div class='couju-error'>$content</div>");
		}
		
	public static function AddCoujuNotice($content){
		Template::AddBodyContent("<div class='couju-notice'>$content</div>");
		}
	
	public static function AddCoujuInfo($content){
		Template::AddBodyContent("<div class='couju-info'>$content</div>");
		}
	
	public static function AddCoujuSuccess($content){
		Template::AddBodyContent("<div class='couju-success'>$content</div>");
		}
		
	public static function AddCoujuDebug($content){
		Template::AddBodyContent("<div class='couju-debug'>$content</div>");
		}
	
	
	public static function AddAlertSuccess($content){
		self::Add("<div class='alert alert-success'>$content</div>");
		}
	
	public static function AddAlertInfo($content){
		self::Add("<div class='alert alert-info'>$content</div>");
		}
	
	public static function AddAlertWarning($content){
		self::Add("<div class='alert alert-warning'>$content</div>");
		}
	
	public static function AddAlertError($content){
		self::Add("<div class='alert alert-danger'>$content</div>");
		}
	
	
	public static function AddPanelError($heading = "", $content = "", $footer = ""){
		self::AddPanel($heading, $content, $footer, "panel-danger");
		}
		
	public static function AddPanelSuccess($heading = "", $content = "", $footer = ""){
		self::AddPanel($heading, $content, $footer, "panel-success");
		}
		
	public static function AddPanelNotice($heading = "", $content = "", $footer = ""){
		self::AddPanel($heading, $content, $footer, "panel-warning");
		}
	
	public static function AddPanelDebug($heading = "", $content = "", $footer = ""){
		self::AddPanel($heading, $content, $footer, "panel-primary");
		}
		
	public static function AddPanelInfo($heading = "", $content = "", $footer = ""){
		self::AddPanel($heading, $content, $footer, "panel-info");
		}
	
	public static function AddPanel($heading, $content, $footer, $panel_class = "panel-default"){
		if($heading != "") $heading = "<div class='panel-heading'><h3 class='panel-title'>{$heading}</h3></div>";
		if($footer != "") $footer = "<div class='panel-footer'>{$footer}</div>";
		self::Add("<div class='panel $panel_class'>{$heading}<div class='panel-body'>{$content}</div>{$footer}</div>");
		}
	
	
	public static function AddStaffAlert($content){
		if(Session::IsStaff()) Template::AddBodyAlert("<div class='couju-debug -centered'>You're on staff! $content</div>");
		}
	
	public static function ReplaceBodyContent($needle, $replace){
		Template::$body = str_replace($needle, $replace, Template::$body);
		}
	
	public static function SetMetaDescription($description){
		Template::$meta_description = $description;
		}
	
	public static function AddMetaKeywords($keywords){
		Template::$meta_keywords = $keywords . "," . Template::$meta_keywords;
		}
	
	public static function AddMeta($meta){
		Template::$head[] = $meta;
		}
	
	public static function formtable() {
		Template::css("/css/formtable.css");
		}
	
	public static function alert($msg, $type='error', $overwrite=true) {
		global $config;
		$style = "cursor:default;position:absolute;width:100%;height:40px;left:0;top:-30px;padding:2px 0px;";
		switch($type) {
			case 'notify':
				$style .= "background:#648026;color:#CACC4F;border-bottom:1px solid #CACC4F;";
				break;
			default:
				$style .= "background:#93060F;color:#FFEEEE;border-bottom:1px solid #CB1E21;";
				break;
		}
		$code = "<div style=\"$style\" id='template_alert'>$msg</div>";
		Template::script('$(document).ready(function(){$("#template_alert").mouseenter(function(){$(this).stop().animate({top:0})}).mouseleave(function(){$(this).stop().animate({top:-30})})});');
		if($overwrite) $config['alert'] = $code;
		else $config['alert'] .= $code;
		}
	
	public static function generateUserSession() {
		
		if(!Session::HasUser()) return "<a href='/login'>Login</a>";

		$user = Session::GetCurrentUser();
		
		if(!$user){ // wtf?
			mail('webmaster@chapmanradio.com', '[CHAPMANRADIO] PHP WTF', Session::GetCurrentUserId().print_r($user, true));
			return "<a href='/login'>Login</a>"; 
			}
			
		return "Hey {$user->fname} | <a href='/dj/profile'>My Profile</a> | <a href='/logout'>Logout</a>";
		}
	
	public static function generateSiteNavigation($active) {
		$navbarItems = array(
			"/home" => 'Home',
			"/schedule" => 'Schedule',
			"/topshows" => 'Top Shows',
			"/mostplayed" => 'Most Played',
			"/events" => 'Events',
			"/giveaways" => 'Giveaways',
			"/shows" => 'Shows',
			"/sports" => 'SportsController',
			"//chapmanradio.tumblr.com" => 'Blog',
			"/contact" => 'Contact',
			);
		$ret = '';
		foreach($navbarItems as $url => $name){
			$target = (substr($url, 0, 2) == "//") ? "_blank" : "";
			$ret .= "<td ".(($url == $active) ? 'class="active"' : '')."><a target='{$target}' href='$url'>$name</a></td>";
			//$ret .= "<li ".(($url == $active) ? 'class="active"' : '')."><a href='$url'>$name</a></li>";
			}
		return $ret;
		}
	
	public static function generateUserNavigation(){
		$nav = "";
		$dj = Session::HasUser();
		$staff = Session::isStaff();
		
		if($dj){
			$nav .= Template::userNavBar("DJ", array(
				"/dj/live" => "DJ Live",
				"/dj/shows" => "My Shows",
				"/dj/profile" => "My Profile",
				"/dj/grades" => "My Grades",
				"/dj/attendance" => "My Attendance",
				"/dj/genre" => "My Genre",
				"/dj/evals" => "Evals",
				"/dj/stats" => "Stats",
				"/dj/news" => "Class News",
				"/calendar" => 'Calendar',
				"/dj/downloads" => "Downloads"
				), 'dj-bar');
			}
		
		if($staff)
			$nav .= Template::userNavBar("Staff", array(
				"http://kb.chapmanradio.com" => "KB",
				"/staff/attendance" => "Attendance",
				"/staff/features" => "Features",
				"/staff/genrecontent" => "Genres",
				"/staff/users" => "Users",
				"/staff/shows" => "Shows",
				"/staff/promos" => "Promos",
				"/staff/events" => "Events",
				"/staff/giveaways" => "Giveaways",
				"/staff/schedule" => "Schedule",
				"/staff/awards" => "Awards",
				"/staff/evals" => "Evals",
				"/staff/sitins" => "Sit-ins",
				"/staff/classnews" => "Class News",
				"..." => array(
					"/staff/staff" => "Staff",
					"/staff/grades" => "Grade Management",
					"/staff/log" => "Edit Log",
					"/staff/emaillists" => "Email Alerts",
					"/staff/alterations" => "Schedule Alterations",
					"/staff/cancelledshows" => "Cancelled Shows",
					"/staff/loginissues" => "Login Attempts",
					"/staff/strikes" => "Strikes",
					"/staff/aliases" => "URL Aliases",
					"/staff/listens" => "Listen Log",
					"/staff/quizquestions" => "Quizes",
					"/staff/errors" => "Error Log",
					"/staff/timestamp" => "Timestamp Utility",
					"/staff/recordings" => "Recording Log",
					"/staff/sms" => "Livechat History",
					"/staff/turntableshows" => "Turntable Shows",
					"/staff/advanced" => "Advanced"
					)
				), 'staff-bar');
			
		$nav .= Template::livestreams();
		return $nav;
		}
	
	public static function analytics(){
		return "<script type='text/javascript'> var _gaq = _gaq || []; _gaq.push(['_setAccount', 'UA-34475663-1']); _gaq.push(['_trackPageview']); (function() { var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s); })(); </script>";
		}
	
	public static function userNavBar($title, $data, $class = ''){
		$ret = "<li class='title'><span>$title &raquo;</span></li>";
		
		foreach($data as $href => $text){
			$active = (($_SERVER['PHP_SELF'] == $href || $_SERVER['PHP_SELF'] == $href.".php") ? 'active' : "");
			if(is_array($text)){
				// if(Session::GetCurrentUserId() != 571) continue;
				$ret .= "<li class='dropdown'><a>$href</a><ul>";
				foreach($text as $dhref => $dtext) $ret .= "<li class='{$active}'><a href='$dhref'>$dtext</a></li>";
				$ret .= "</ul></li>";
				}
			else{
				$target = (substr($href, 0, 2) == "//") ? "_blank" : "";
				$ret .= "<li class='{$active}'><a target='{$target}' href='$href'>$text</a></li>";
				}
			}
		
		return "<div class='usernav subnavbar $class'><ul>" . $ret . "</ul></div>";
		}
	
	public static function IncludeCSS($url) { self::CSS($url); }
	public static function CSS($url) {
		if(isset(self::$styles[$url])) return;
		self::$styles[$url] = true;
		self::$head[] = "<link rel='stylesheet' type='text/css' href='$url' />\n";
		}
	
	public static function IncludeStyle($code) { self::Style($code); }
	public static function Style($code) {
		self::$head[] = "<style type='text/css'>\n$code</style>\n";
		}
	
	public static function IncludeJS($url) { self::JS($url); }
	public static function JS($url) {
		if(isset(self::$scripts[$url])) return;
		self::$scripts[$url] = true;
		self::$head[] = "<script type='text/javascript' src='$url'></script>\n";
		}
	
	public static function IncludeScript($code) { self::Script($code); }
	public static function Script($code) {
		self::$head[] = "<script type='text/javascript'>\n$code</script>\n";
		}
	
	public static function notify($title, $msg="", $type="info", $autohide=true) {
		// valid types: alert, error, info, question, & warning
		$autohide = $autohide ? 1 : 0;
		$s = '[%=%]';
		Template::$notify = "$title$s$msg$s$type$s$autohide";
		$_SESSION['notify'] = "$title$s$msg$s$type$s$autohide";
		}
	
	public static function notification() {
		$n = Template::$notify;
		if(!$n && isset($_SESSION['notify']) ) $n = $_SESSION['notify'];
		if(isset($_SESSION['notify']) && $_SESSION['notify'] == $n) unset($_SESSION['notify']);
		if(!$n) return "";
		list($title, $msg, $type, $autohide) = explode('[%=%]', $n);
		$code = "<div class='sessionNotification ".($autohide?"autohide":"")."'><div class='inner'><h2>$title</h2><img src='/img/icons/notify/$type.png' alt='' class='icon' /><div class='msg'>$msg</div></div></div>";
		return $code;
		}
	
	public static function Error($msg){
		// Log::Error('PHP TemplateError', $msg);
		
		$headers = "From: \"Chapman Radio\" <notifications@chapmanradio.com>\r\nReply-to: webmaster@chapmanradio.com\r\n";
		$success = mail("webmaster@chapmanradio.com", "TmplteError", $msg."\n".print_r($_SERVER, true), $headers);
		
		Template::Finalize("<div class='couju-error'>$msg</div><br /><p style='color:#757575'><br />If you have questions about this error, please contact webmaster@chapmanradio.com</p>");
		}
	
	public static function UnhandledException($e){
		Log::Error('PHP EX', $e);
		Template::Finalize("<div class='couju-error'>
			Opps! There was an error loading this page<br /><br />
			Error: ".$e->GetMessage()."<br /><br />
			This error has been logged and our staff have been notified.
			</div>");
		}
	
	public static function RequireLogin($description, $type = 'dj'){
		$user = Session::GetCurrentUser();
		if($user != NULL) {
			// force them to activate their account
			if(!$user->IsActivated()) {
				Template::notify("Inactive Account","Please renew your account with Chapman Radio for <b>".Season::name(Site::CurrentSeason())."</b>");
				$_SESSION['redirectPageName'] = $description;
				$_SESSION['redirect'] = $_SERVER['PHP_SELF'];
				header("Location: /activate");
				exit;
			}
			
			// do they have 3 strikes
			if(count($user->getStrikes()) >= 3 && $_SERVER["PHP_SELF"] != "/dj/attendance") {
				$user->DeniedLogin('account_suspended');
				Template::SetBodyHeading("Chapman Radio", "Account Suspended");
				$djname = $user->djname && $user->name != $user->djname ? $user->name."<br /><span class='genre'>".$user->djname."</span>" : $user->name;
				Template::css("/css/formtable.css");
				Template::AddBodyContent("<table class='formtable' cellspacing='0' style='width:424px;'><tr class='oddRow'><td><img src='".$user->img50."' /></td><td>$djname</td><td>".$user->email."<br />".$user->phone."<br />".$user->classclub."<td></tr></table>");
				Template::error("<p>You have <b>3 strikes</b>.</p><div style='text-align:left;'><p>Your account has been suspended due to violations in our <a href='/policies'>policies</a>.</p><p>View <a href='/dj/attendance'>my attendance</a> for more information.</p><p>Email <a href='attendance@chapmanradio.com'>attendance@chapmanradio.com</a> for help with your strikes.</p></div>");
			}
			
			// suspended account
			if($user->isSuspended){
				if($_SERVER["PHP_SELF"] != "/dj/suspended"){
					header("Location: /dj/suspended");
					exit;
					}
				}
			
			// force them to complete their quiz
			else if(true && !$user->HasQuizSeason(Site::CurrentSeason()) && $_SERVER["PHP_SELF"] != "/dj/quiz") {
				// If there are any current shows that the user is in, they need the quiz
				if(count($user->GetShowsInSeason(Site::CurrentSeason(), "AND status='accepted'")) > 1){
					Template::notify("Inactive Account", "Please take the quiz for <b>".Season::name(Season::current())."</b>");
					$_SESSION['redirectPageName'] = $description;
					$_SESSION['redirect'] = $_SERVER['PHP_SELF'];
					header("Location: /dj/quiz");
					exit;
					}
				}
			
			// Check permissions
			$hasPermission = false;
			switch($user->type) {
				case "dj": $hasPermission = $type == "dj"; break;
				case "staff": $hasPermission = $type == "dj" || $type == "staff"; break;
				case " ": $hasPermission = $type == "dj"; break;
				case "": $hasPermission = $type == "dj"; break;
				}
			if($hasPermission) return; // OK
			
			Template::notify("Permission Denied", "Sorry, but you <b>do not have sufficient privileges</b> to view <b>$description</b>","error");
			$_SESSION['redirectPageName'] = $description;
			header("Location: /denied");
			exit;
			}
		
		// not logged in, redirect
		$redirect = $_SERVER['PHP_SELF'];
		if(preg_match("/.php\$/", $redirect)) $redirect = substr($redirect, 0, strlen($redirect)-4); 
		$_SESSION['redirectPageName'] = $description;
		$_SESSION['redirect'] = $redirect;
		header("Location: /login?redirect=$redirect");
		exit;
		}
	
	public static function Maint(){
		if(isset($_GET['maint_override'])) return;
		Template::Finalize("<br /><div class='couju-debug' style='width:80%; margin: 0 auto;'>This area of Chapman Radio is currently offline for maintenance.</div>");
		}
	
	public static function shadowbox($options = "") {
		Template::css("/plugins/shadowbox/shadowbox.css");
		Template::js("/plugins/shadowbox/shadowbox.js");
		if($options) $options = "{".$options."}";
		Template::script("Shadowbox.init($options);");
		}
	
	public static function tablesorter() {
		Template::js("/plugins/tablesorter/jquery.tablesorter.min.js");
		Template::css("/plugins/tablesorter/blue/style.css");
		Template::style(".tablesorter tbody tr:nth-child(even) {background: #F4F4F4} .tablesorter tbody tr:nth-child(odd) {background: #E6E6E6}");
		Template::script("\$(document).ready(function(){\$('.tablesorter').tablesorter();});");
		}
	
	public static function bootstrap(){
		self::$bootstrapping = true;
		self::js("/js/bootstrap.min.js");
		self::css("/css/bootstrap.min.css");
		}
	
	public static function livestreams() {
		Template::js("/js/livestreams.js?G");
		$streams = Icecast::streams();
		
		if(!$streams && Session::HasUser())
			return "<div class='couju-notice'><a href='/contact' class='nounderline'>Chapman Radio's broadcast server is down. Please <u>contact a staff member &raquo;</u></a></div>";
		
		$showid = Schedule::HappeningNow();
		$show = ShowModel::FromId($showid);
		
		$djs = ($show!=null) ? $show->GetDjNamesCsv() : "";
		$href = ($show!=null) ? $show->permalink : "";
		
		return "
			<div id='livestream-radio' class='livestream' style='display:".(($show!=null)?"block":"none")."'>
				<div class='slide' id='livestream-radio-show'><a class='listenlivelink' href='/listenlive'><div class='cr-ls-container'><img alt='Current Show Logo' src='".(($show!=null)?$show->img64:"/img/defaults/64.png")."' /><div class='sideinfo'><span class='slidetitle'>CURRENT SHOW</span><span class='showname'>".(($show!=null)?$show->name:"")."</span><span class='showdetails'>{$djs}</span></div></div></a></div>
				<div class='slide' id='livestream-radio-nowplaying'><a class='listenlivelink' href='/listenlive'><div class='cr-ls-container'></div></a></div>
				<div class='slide' id='livestream-sports' style='display:".(isset($streams['sports'])?"table-cell":"none")."'><a class='listenlivelink' data-stream='sports' href='/listenlive?stream=sports'><div class='cr-ls-container'><img alt='SportsController Image' src='/img/chrome/sports_64.png' /><div class='sideinfo'><span class='slidetitle'>ALSO STREAMING</span><span class='showname'>Live SportsController Stream</span></div></div></a></div>
			</div>";
		}
	
	// checkthisout displays a random box, designed to grab this users attention and take them to a random page on the site
	public static function checkthisout($excludes=array()) {
		$options = array(
			"mostplayed" => "<div class='gloss' style='margin:30px auto;text-align:center;'><h2>Check this out</h2><table style='text-align:left;'><tr><td style='padding:6px 20px;'><a href='/mostplayed'><img src='/img/decor/whatsnew/mostplayed128.jpg' alt='' /></a></td><td><h3>Most Played</h3><p>Ever wonder which tracks are played most often on Chapman Radio?<br />Now you can find out which tunes are on the top of our charts.</p><p><a href='/mostplayed'>&raquo; Most Played</a></p></td></tr></table></div>",
			"listeners" => "<div class='gloss' style='margin:30px auto;text-align:center;'><h2>Check this out</h2><table style='text-align:left;'><tr><td style='padding:6px 20px;'><a href='/listeners'><img src='/img/decor/whatsnew/listeners128.jpg' alt='' /></a></td><td><h3>Listener Map</h3><p>Where are you when you listen to Chapman Radio?<br />People around the globe are tuning in to the hottest college radio station. Find out where they're listening.</p><p><a href='/listeners'>&raquo; Listener Map</a></p></td></tr></table></div>",
			"showoftheweek" => "",
			"trendingshow" => "",
			"iphoneapp"=>"<div class='gloss' style='margin:30px auto;text-align:center;'><h2>Check this out</h2><table style='text-align:left;'><tr><td style='padding:6px 20px;'><a href='/iphoneapp'><img src='/img/decor/whatsnew/iphone.png' alt='' /></a></td><td><h3>iPhone App</h3><p>Experience live, cutting edge radio broadcasts straight from Chapman. From the hottest music to the latest talk radio, Chapman Radio now presents you with quality college radio, even while you're on the go.</p><p><a href='/iphoneapp'>&raquo; iPhone App</a></p></td></tr></table></div>",
			"nowplaying"=>"<div class='gloss' style='margin:30px auto;text-align:center;'><h2>Check this out</h2><table style='text-align:left;'><tr><td style='padding:6px 20px;'><a href='/nowplaying'><img src='/img/decor/whatsnew/musicaudio.png' alt='' /></a></td><td><h3>Now Playing</h3><p>Find out more about what's playing on Chapman Radio right now, and check out which tracks have been played recently.</p><p><a href='/nowplaying'>&raquo; Now Playing</a></p></td></tr></table></div>",
		);
		if(is_string($excludes)) $excludes = array($excludes);
		if($excludes) foreach($excludes as $exclude) unset($options[$exclude]);
		$key = array_rand($options);
		$html = "";
		switch($key) {
			case "showoftheweek":
				$show = ShowModel::ShowOfTheWeek();
				if(!$show) return;
				$html = "<div class='gloss' style='margin:30px auto;text-align:center;'><h2>Check this out</h2><table style='text-align:left;'><tr><td style='padding:6px 20px;'><a href='".$show->permalink."'><img src='".$show->img310."' alt='' style='width:128px;' /></a></td><td><h3>Show of the Week</h3><p>Congratulations to our most recent show of the week winner: <b>".$show->name."</b></p><p><a href='".$show->permalink."'>&raquo; Find out More</a></p></td></tr></table></div>";
				break;
			case "trendingshow":
				// display a random of the top 10 shows
				$start = rand(0,9);
				$show = ShowModel::FromResult(DB::GetFirst("SELECT * FROM shows WHERE ranking!=0 ORDER BY ranking LIMIT $start,1"));
				if(!@$show) return;
				$start++;
				$html = "<div class='gloss' style='margin:30px auto;text-align:center;'><h2>Check this out</h2><table style='text-align:left;'><tr><td style='padding:6px 20px;'><a href='{$show->permalink}'><img src='{$show->img192}' alt='' style='width:128px;' /></a></td><td><h3>Trending Show</h3><p>Ranking in at #$start on our charts, <b>{$show->name}</b> is popular on Chapman Radio.</p><p><a href='{$show->permalink}'>&raquo; Find out More</a></p></td></tr></table></div>";
				break;
			default:
				$html = $options[$key];
		}
		Template::AddBodyContent($html);
		return "";
	}

	}

Template::AddMetaKeywords(Site::$MetaKeywords);
Template::SetMetaDescription(Site::$MetaDescription);

// if(!isset($_GET['dt'])) Template::Maint();

// Depreciated
function error($msg) { Template::Error($msg); }

