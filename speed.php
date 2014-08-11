<?php


//Change path depending on your system
define('SOX_PATH', '/usr/bin/sox');
define('LAME_PATH', '/usr/bin/lame');

//Set headers
if(!isset($_REQUEST['debug']) || $_REQUEST['debug'] != 'true') {
	header('Content-type: audio/mpeg;');
} else {
	ini_set('display_errors',1);
	error_reporting(E_ALL);
}

$url = isset($_REQUEST['url']) ? $_REQUEST['url']:null;
$speed = isset($_REQUEST['speed']) && (float)$_REQUEST['speed'] > 0 ? (float)$_REQUEST['speed']:4;
$reverb = isset($_REQUEST['reverb']) && (integer)$_REQUEST['reverb'] >= 0 ? (integer)$_REQUEST['reverb']:80;

if(!preg_match('/^https?\:\/\//',$url) || filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
	die();
}

system('curl '.escapeshellarg($url).' | '.SOX_PATH.' -t mp3 - -t wav - speed '.escapeshellarg($speed).' reverb '.escapeshellarg($reverb).' echo 0.8 0.9 1000.0 0.3 1800.0 0.25 | '.LAME_PATH.' -b 72 --quiet - -');
