<?php

class PPCommunicate{

	var $API_ENDPOINT, $API_VERSION, $API_USERNAME, $API_PASSWORD, $API_SIGNATURE;

	function populate_credentials(){
		$this->API_ENDPOINT		= 'https://api-3t.sandbox.paypal.com/nvp';
		$this->API_VERSION		= '72';
		$this->API_USERNAME		= 'sell_1266966509_biz_api1.hotmail.com';
		$this->API_PASSWORD		= '1266966516';
		$this->API_SIGNATURE	= 'AuV-Biu2JVNtNCtTd3N0TOtCjpkWA5CpczZUhVesmeEvHsY5v1L-DcH-';
	}

	function __construct(){
		$this->populate_credentials();
	}

	function request($api_method, $vars=array()){
		if(is_array($vars) && !empty($vars)){
			$request_string = $this->request_string($api_method, $vars);
			$response_string = $this->curl($request_string);

			$request = array();
			$response = array();

			parse_str($request_string, $request);
			parse_str($response_string, $response);

			/*pre($request);
			pre($response);*/

			return $response;
		}
	}

	private function request_string($api_method, $vars=array()){
		$required_args = array(
			'METHOD' => $api_method,
			'VERSION' => $this->API_VERSION,
			'PWD' => $this->API_PASSWORD,
			'USER' => $this->API_USERNAME,
			'SIGNATURE' => $this->API_SIGNATURE
		);
		$vars = array_merge($vars, $required_args);
		$request_string = '';
		foreach($vars as $k => $v){
			$request_string .= $request_string ? '&' : '';
			$request_string .= "$k=" . urlencode($v);
		}
		return $request_string;
	}

	private function curl($request_string){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->API_ENDPOINT);
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request_string);
		$response_string = curl_exec($curl);
		if(curl_errno($curl)){
			$curl_error = curl_error($curl);
			$curl_errno = curl_errno($curl);
		}
		return $response_string;
	}
}