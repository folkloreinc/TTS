<?php


//Change path depending on your system
define('SOX_PATH', '/usr/bin/sox');

//Set headers
if(!isset($_REQUEST['debug']) || $_REQUEST['debug'] != 'true') {
	header('Content-type: audio/mpeg;');
} else {
	ini_set('display_errors',1);
	error_reporting(E_ALL);
}

$url = $_REQUEST['url'];
$speed = isset($_REQUEST['speed']) && (float)$_REQUEST['speed'] > 0 ? (float)$_REQUEST['speed']:10;

exec('curl '.escapeshellarg($url).' | '.SOX_PATH.' -t mp3 - -t mp3 - speed '.escapeshellarg($speed));