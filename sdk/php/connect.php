<?php

  /// User supplied Options
$AUTH_TOKEN = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXX';

  /// Create a query string

function mkQueryString($arr, $key="", $recurs=1) {
	if ($recurs >= 10)
		throw Exception('Maximum Recursions');
	$ret = "";		
	foreach ($arr as $k=>$v) {
		$k = urlencode($k);
		if (is_array($v)) {
			$ret .= mkQueryString($v, $k, ++ $recurs);
			continue;
		}
		$k = ($key) ? urlencode($key).'[]' : urlencode($k);
	    	$v = urlencode($v);
		$ret .= "$k=$v&";
	}
	return $ret;
}

  /// Object to make queries against DNS.com's API

class DNSDOTCOM_API {
	var $_auth_token   = "";
	var $_sandbox = False;
	function __construct($auth_token, $sandbox=False) {
		$this->_auth_token = strtoupper($auth_token);
		$this->_sandbox = $sandbox;
	}

	function getURL() {
		if ($this->_sandbox) {
			return "http://sandbox.dns.com/api/";
		}
		return "https://www.dns.com/api/";
	}

	function __call($cmd, $args) {
		$url = $this->getURL().$cmd.'/?';
	     $args['AUTH_TOKEN'] = $this->_auth_token;
		$url .= mkQueryString($args);

		$json_string = file_get_contents($url);
		return json_decode($json_string);
	}
};



  /// Execute Commands

$obj = new DNSDOTCOM_API($AUTH_TOKEN, True);

$result = $obj->createDomain(array(
							'domain' => 'example.com',
							'mode'   => 'advanced'
							));

print_r($result);
?>
