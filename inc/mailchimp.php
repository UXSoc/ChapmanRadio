<?php namespace ChapmanRadio;

/*

inc/mailchimp.php

requires "MCAPI.class.php" before being called

STANDAD USAGE:

$retval = Mailchimp::add("email@address.com", array("FNAME"=>$fname, "LNAME"=>$lname, "HOWFOUND"=>$howfound), $list_id);
if($retval)
echo "error: $retval";
else
echo "added";

*/

require_once BASE."/lib/mailchimp.php";

$mailchimp_api = new MCAPI("d447e06e695e3505740ef8ba5493dd57-us2"); // mailchimp api key

class Mailchimp {
	
	public function add($email, $merge_vars, $list_id, $sendwelcome = false) {
		global $mailchimp_api;
		// from http://apidocs.mailchimp.com/api/1.3/
		// syntax: listSubscribe(string apikey, string id, string email_address, array merge_vars, string email_type, boolean double_optin, boolean update_existing, boolean replace_interests, boolean send_welcome)
		$retval = $api->listSubscribe( $list_id, $email, $merge_vars, 'html', false, false, true, $send_welcome );
		if ($api->errorCode)
			return $api->errorMessage;
		else
			return '';
	}
	
	public function lists() {
		global $mailchimp_api;
		$ret = array();
		$lists = $mailchimp_api -> lists();
		foreach(@$lists['data'] as $data) {
			$ret[$data['id']] = htmlspecialchars($data['name'],ENT_COMPAT,"UTF-8");
		}
		return $ret;
	}
	
	public function picker($default) {
		$ret = "<select name='listid'><option value=''> - Pick a Mailchimp List - </option>";
		$lists = Mailchimp::lists();
		foreach($lists as $listid => $label) {
			$selected = $default == $listid ? "selected='selected'" : "";
			$ret .= "<option value='$listid' $selected>$label</option>";
		}
		$ret .= "</select>";
		return $ret;
	}

	}