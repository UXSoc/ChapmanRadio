<?php namespace ChapmanRadio;

/*
 
 xml2array.php
 

 THIS IS A NEW VERSION OF THE FUNCTION
 
 which handles attributes better.
 
 special thanks to: Ashok dot 893 at gmail dot com 26-Apr-2010 05:52
 http://www.php.net/manual/en/function.xml-parse.php
 
 */

function xml2array($data){
	$xmlObj = @simplexml_load_string($data);
	if(!$xmlObj) return array();
	$arrXml = @xml2array_objectsIntoArray($xmlObj);
	if(!$arrXml) return array();
	return $arrXml;
}

function xml2array_objectsIntoArray($arrObjData, $arrSkipIndices = array()) {
	$arrData = array();
	
	// if input is object, convert into array
	if (is_object($arrObjData)) {
		$arrObjData = get_object_vars($arrObjData);
	}
	
	if (is_array($arrObjData)) {
		foreach ($arrObjData as $index => $value) {
			if (is_object($value) || is_array($value)) {
				$value = xml2array_objectsIntoArray($value, $arrSkipIndices); // recursive call
			}
			if (in_array($index, $arrSkipIndices)) {
				continue;
			}
			$arrData[$index] = $value;
		}
	}
	return $arrData;
}