<?php

  /// User supplied Options
$email    = 'xxxxx@xxxxx.xxx';
$password = 'xxxxxxxxxxxxxxx';

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
	var $_email   = "";
	var $_password= "";
	var $_sandbox = False;
	function __construct($email, $password, $sandbox=False) {
		$this->_email   = $email;
		$this->_password= $password;
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
	     $args['email'] = $this->_email;
		$args['password'] = $this->_password;
		$url .= mkQueryString($args);

		$json_string = file_get_contents($url);
		return json_decode($json_string);
	}
};



  /// Execute Commands

$obj = new DNSDOTCOM_API($email, $password, True);

$result = $obj->createDomain(array(
							'domain' => 'example.com',
							'mode'   => 'advanced'
							));

print_r($result);
?>
