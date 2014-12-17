<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('file_get_contents_curl') ){
	function file_get_contents_curl( $url ){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

		$data = curl_exec($ch);
		curl_close($ch);
	 
		return $data;
	}
}