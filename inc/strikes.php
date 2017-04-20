<?php namespace ChapmanRadio;

class Strikes {
	
	public static function check($userid = 0, $season = "", $debug=false) {
		
		// is this userid valid?
		$userid = intval($userid);
		if($userid <= 0) return false;
		
		$user = UserModel::FromId($userid);
		if(!$user) return false;
		
		// which season is it?
		if(!$season || $season=="") $season = Season::Current();

		// what shows is this user in?
		$queries = array();
		$queries[] = "userid='$userid'";
		
		$shows = $user->GetShows();
		foreach($shows as $show) $queries[] = "showid='".$show['showid']."'";
		
		// okay, let's get all attendance records for this user, or for this users shows
		$results = DB::getAll("SELECT * FROM attendance WHERE season='$season' AND ( ".implode($queries, ' OR ').")");
		if($debug) echo "Found ".count($results)." attendance records for user for user #$userid<br />\n";
		$workshopAbsences = 0;
		$showAbsences = 0;
		$tardies = 0;
		foreach($results as $att) {
			switch($att['type']) {
				case "workshop":
					if($att['status'] == 'absent') $workshopAbsences++;
					break;
				case "show":
					if($att['status'] == 'absent') $showAbsences++;
					if($att['status'] == 'present' && $att['late'] > Site::$TardyGrace) $tardies++;
					break;
				case "event":
					break;
			}
		}
		
		if($debug) echo "&nbsp;&nbsp;"."User has $showAbsences showAbsences, $workshopAbsences workshopAbsences, and $tardies tardies"."<br />\n";
		
		// do they have enough strikes for each reason?
		$nowd = date("Y-m-d H:i:s");

		$showStrikes = 0;
		$tardyStrikes = 0;
		$workshopStrikes = 0;
		
		foreach(array('show_absence', 'show_tardies', 'workshop_absence') as $reason) {
			
			// $strikes is the number of strike for a given reason
			$strikes = 0; 
			
			// $current is current number of strikes in the database
			extract(DB::GetFirst("SELECT count(*) as current FROM strikes WHERE reason='$reason' AND userid='$userid' AND season='$season'"));
			
			switch($reason) {
				case 'show_absence':
					$showStrikes = $strikes = floor( $showAbsences / Site::$ShowAbsencesPerStrike );
					break;
					
				case 'show_tardies':
					$tardyStrikes = $strikes = floor( $tardies / Site::$TardiesPerStrike ); 
					break;
					
				case 'workshop_absence':
					$workshopStrikes = $strikes = floor( $workshopAbsences / Site::$WorkshopAbsencesPerStrike );
					break;
				}
			
			/* Ideally there are the same number of strikes in the db as we just calculated ... until something changes */
			for($i = 0; $i < ($strikes - $current); $i++) {
				if($debug) echo "&nbsp;&nbsp;"."Inserting strike! ($reason) (current: $current; target: ".($strikes - $current).")<br />\n";
				DB::Insert("strikes", array("userid" => $userid, "assignedon" => $nowd, "reason" => $reason, "season" => $season));
				}
			for($i = 0; $i < ($current - $strikes); $i++) {
				if($debug) echo "&nbsp;&nbsp;"."Deleting strike ($reason) (current: $current; target: ".($current - $strikes).")<br />\n";
				DB::Query("DELETE FROM strikes WHERE userid='$userid' AND reason='$reason' AND season='$season' LIMIT 1");
				}
			}
		
		if($debug) echo "&nbsp;&nbsp;"."User has $showStrikes showStrikes, $workshopStrikes workshopStrikes, and $tardyStrikes tardyStrikes"."<br />\n";
		
		$totalStrikes = $showStrikes + $tardyStrikes + $workshopStrikes;

		// Cancel any shows this user is a DJ for
		$cancelled = $totalStrikes >= 3 ? 1 : 0;
		if($cancelled){
			if($debug) echo "&nbsp;&nbsp;"."User has 3 strikes. Canceling show.<br />\n";
			DB::Query("UPDATE shows SET status='cancelled' WHERE userid1=$userid OR userid2=$userid OR userid3=$userid OR userid5=$userid");
			}
			
		if($debug) echo "<br />\n";
		
		return array(
			"workshopStrikes"=>$workshopStrikes,
			"showStrikes"=>$showStrikes,
			"tardyStrikes"=>$tardyStrikes,
			"totalStrikes"=>$totalStrikes,
			"workshopAbsences"=>$workshopAbsences,
			"showAbsences"=>$showAbsences,
			"tardies"=>$tardies
			);
	}
	
	public static function Overview($userid){
		$check = Strikes::check($userid);
		$tardytardies = $check['tardies'] == 1 ? "tardy" : "tardies";
		$showAbsencesS = $check['showAbsences'] == 1 ? "" : "s";
		$workshopAbsencesS = $check['workshopAbsences'] == 1 ? "" : "s";
		$strikesS = $check['totalStrikes'] == 1 ? "" : "s";
		return "
			<table class='formtable' cellspacing='0'>
			<tr class='evenRow'><td>Show Absences<br /><small style='color:#757575'>".Site::$ShowAbsencesPerStrike." show absence = 1 strike</small></td><td><b>$check[showAbsences]</b> show absence$showAbsencesS</td></tr>
			<tr class='oddRow'><td>Workshop Absences<br /><small style='color:#757575'>".Site::$WorkshopAbsencesPerStrike." workshop absences = 1 strike</small></td><td><b>$check[workshopAbsences]</b> workshop absence$workshopAbsencesS</td></tr>
			<tr class='evenRow'><td>Tardies<br /><small style='color:#757575'>".Site::$TardiesPerStrike." tardies = 1 strike</small></td><td><b>$check[tardies]</b> $tardytardies</td></tr>
			<tr class='oddRow'><td colspan='2' style='text-align:center'>You have <b>$check[totalStrikes]</b> strike$strikesS</td></tr>
			</table>";
		}
	
	}