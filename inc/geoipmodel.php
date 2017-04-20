<?php namespace ChapmanRadio;

class GeoipModel extends \Sinopia\BaseModel {
	
	public $ip;
	public $countrycode;
	public $country;
	public $region;
	public $city;
	public $zip;
	
	public $latitude;
	public $longitude;
	public $timezone;
	public $lastupdate;
	
	public static $DataTable = "geoip";
	public static $DataMap = [
		"id" => "geoip_ip",
		"countrycode" => "geoip_countrycode",
		"country" => "geoip_country",
		"region" => "geoip_region",
		"city" => "geoip_city",
		"zip" => "geoip_zip",
		"latitude" => "geoip_latitude",
		"longitude" => "geoip_longitude",
		"timezone" => "geoip_timezone",
		"lastupdate" => "geoip_lastupdate"
		];
	
	public function __setup(){
		$this->ip = inet_ntop($this->id);
		}
	
	public function Lookup() {
		$ip = $this->ip;
		$regex = '/^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/'; 
		if(!preg_match($regex, $ip, $matches)) return array("error"=>"Invalid IP Address");
		
		$key = Site::$ipinfodb_apikey;
		$url = "http://api.ipinfodb.com/v3/ip-city/?key=$key&ip=$ip&format=json";
		
		$data = file_get_contents($url);
		if(!$data) return array("error"=>"Failed to queried external ipinfodb.com database");
		$data = json_decode($data,true);
		
		if(!$data) return array("error"=>"Failed to decode ipinfodb.com database response as JSON");
		if(@$data["statusCode"] != "OK") return array("error"=>"It appears that ipinfodb.com returned an invalid result for the ip address listed");
		
		$ip = @$data["ipAddress"];
		if(!preg_match($regex, $ip, $matches)) return array("error"=>"Invalid IP Address returned from ipinfodb.com ($data[ipAddress]) - this is super irregular!");
		
		return [
			'countrycode' => $data['countryCode'],
			'country' => $data['countryName'],
			'region' => $data['regionName'],
			'city' => $data['cityName'],
			'zip' => $data['zipCode'],
			'latitude' => $data['latitude'],
			'longitude' => $data['longitude'],
			'timezone' => $data['timeZone'],
			];
		}
	
	}