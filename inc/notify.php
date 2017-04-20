<?php namespace ChapmanRadio;

class Notify {
	
	public static function error($to, $subject, $body, $headers=array(), $note="An Error occurred") {
		DB::Insert("notifications", array(
			'timestamp' => time(),
			'to'=> $to,
			'subject'=> $subject,
			'body'=> $body,
			'headers'=> $headers,
			'success'=> 0,
			'note' => $note));
		Log::Error('MAIL', $note);
		self::mail("webmaster@chapmanradio.com", "Mail Error", $body);
		}
	
	public static function show($showid, $subject, $content, $headers=array()) {
		$show = DB::GetFirst("SELECT userid1, userid2, userid3, userid4, userid5 FROM shows WHERE showid='$showid'");
		if(!$show) Notify::error("?", $subject, $content, $headers, "Show #$showid does not exist");
		else {
			$emails = array();
			for($i = 1;$i <= 5;$i++) {
				$userid = $show['userid'.$i];
				if(!$userid) continue;
				$user = DB::GetFirst("SELECT name,email FROM users WHERE userid='$userid'");
				if(!$user) {
					Notify::error("?", $subject, $content, $headers, "$user[name] with the userid #$userid does not exist in the users table");
					}
				else {
					extract($user);
					if(!$user['email']) Notify::error("$user[name]", $subject, $content, $headers, "$user[name] userid #$userid does not have an email address in the database");
					else if(!preg_match("/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}\$/", trim($email))) {
						Notify::error("$user[name]", $subject, $content, $headers, "$user[name] userid #$userid has an invalid email address, $user[email]");
						}
					else {
						$emails[] = trim($email);
						}
					}
				}
			foreach($emails as $email) {
				Notify::mail($email, $subject, $content, $headers);
				}
			}
		}
	
	public static function mail($to, $subject, $content, $headers=array()) {
		// are they blacklisted?
		$blacklist = DB::GetFirst("SELECT * FROM blacklist WHERE email='$to' AND status='blocked'");
		if($blacklist) return "Chapman Radio is not allowed to send emails to <b>$to</b> by user request.";
		// get template file for body
		$body = @file_get_contents(BASE."/templates/onlinenotification.html") or die("missing template file for online notification");
		// replace content, then header styles
		$body = str_replace("[%content%]", $content, $body);
		$body = str_replace("<h2>", "<h2 style=\"color:#09456B;text-transform:uppercase;font-weight:bold;font-size:22px;margin-bottom:2px;\">", $body);
		$body = str_replace("<h3>", "<h3 style=\"border-bottom:1px solid #CCC;margin-bottom:2px;\">", $body);
		// prepare the headers
		$headers[] = "Content-type: text/html";
		$headers[] = "From: \"Chapman Radio\" <notifications@chapmanradio.com>";
		$headers[] = "Reply-to: webmaster@chapmanradio.com";
		$headers = implode("\r\n", $headers);
		// try to send it
		$success = mail($to, $subject, $body, $headers, '-f notification-bounces@chapmanradio.com') ? 1 : 0;
		
		DB::Insert("notifications", array(
			'timestamp' => time(),
			'to'=> $to,
			'subject'=> $subject,
			'body'=> $body,
			'headers'=> $headers,
			'success'=> $success));
		
		return $success ? "" : "The email failed to send. Please try again later or email webmaster@chapmanradio.com for help.";
		}
	
	public static function emaillist($listname="", $subject="", $content="") {
		if(!$subject || !$content) return array();
		$result = DB::GetAll("SELECT email FROM emaillists WHERE listname='$listname'");
		$content .= "<p><small>This email was sent to all recipients of the <a href='https://chapmanradio.com/staff/emaillists'>staff email list</a> <b><tt>$listname</tt></b>. You are on the email list <tt>$listname</tt>, which is why you received this email.</small></p>";
		$ret = array();
		foreach($result as $row){
			extract($row);
			$ret[] = Notify::mail($email, $subject, $content);
			}
		return $ret;
		}
	
	}