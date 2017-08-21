<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use DateTime;
use DatePeriod;
use DateIntercal;


class getdataController extends Controller
{   
	private static $main_url = 'https://sandbox.bca.co.id'; 
	private static $client_id = 'd84b8c47-4f7d-4de3-b55c-9ee27045020b'; 
	private static $client_secret = '32deb748-b3d8-4df6-9ea5-67f44029706c'; 
	private static $api_key = '5f4749f0-8b9f-4148-ad1d-a12b955e7dae'; 
	private static $api_secret = '24d3e053-27be-4738-9c6f-04e3a8830e4d'; 
	private static $access_token = null;
	private static $signature = null;
	private static $timestamp = null;

	private function getToken(){
		$path = '/api/oauth/token';
		$headers = array(
			'Content-Type: application/x-www-form-urlencoded',
			'Authorization: Basic '.base64_encode(self::$client_id.':'.self::$client_secret));
		$data = array(
			'grant_type' => 'client_credentials'
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::$main_url.$path);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt_array($ch, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_POSTFIELDS => http_build_query($data),
		));
		$output = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($output,true);
		self::$access_token = $result['access_token'];
	}
	private function parseSignature($res){
		$explode_response = explode(',', $res);
		$explode_response_1 = explode(':', $explode_response[8]);
		self::$signature = trim($explode_response_1[1]);
	}
	private function parseTimestamp($res){
		$explode_response = explode(',', $res);
		$explode_response_1 = explode('Timestamp: ', $explode_response[3]);
		self::$timestamp = trim($explode_response_1[1]);
	}
	private function getSignature($url,$method,$data){
		$path = '/utilities/signature';
		$timestamp = date(DateTime::ISO8601);
		$timestamp = str_replace('+','.000+', $timestamp);
		$timestamp = substr($timestamp, 0,(strlen($timestamp) - 2));
		$timestamp .= ':00';
		$url_encode = $url;
		$headers = array(
			'Timestamp: '.$timestamp,
			'URI: '.$url_encode,
			'AccessToken: '.self::$access_token,
			'APISecret: '.self::$api_secret,
			'HTTPMethod: '.$method,
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::$main_url.$path);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt_array($ch, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_POSTFIELDS => http_build_query($data),
		));
		$output = curl_exec($ch);
		curl_close($ch);
		$this->parseSignature($output);
		$this->parseTimestamp($output);
	}
	public function index(){
		$this->getToken();

		$path = '/general/info-bca/atm?Radius=20&Count=3&Latitude=-6.1900718&SearchBy=Distance&Longitude=106.797190';
		$method = 'GET';
		$data = array();
		$this->getSignature($path, $method, $data);
		$headers = array(
			'X-BCA-Key: '.self::$api_key,
			'X-BCA-Timestamp: '.self::$timestamp,
			'Authorization: Bearer '.self::$access_token,
			'X-BCA-Signature: '.self::$signature,
			'Content-Type: application/json',
			'Origin: '.$_SERVER['SERVER_NAME']
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::$main_url.$path);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HTTPHEADER => $headers,
		));
		$output = curl_exec($ch); 
		curl_close($ch);
		echo $output;
	}

}
