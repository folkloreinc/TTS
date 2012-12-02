<?php

//Change path depending on your system
define('ESPEAK_PATH', '/usr/local/bin/speak');
define('LAME_PATH', '/usr/local/bin/lame');

//Available voices
$VOICES = array(
	'fr',
	'en'
);

//Parameters
$text = $_REQUEST['text'];
$voice = isset($_REQUEST['voice']) && in_array($_REQUEST['voice'],$VOICES) ? $_REQUEST['voice']:'en';

//Output mp3
header('Content-type: audio/mpeg;');
if(!isset($_REQUEST['text']) || empty($_REQUEST['text'])) die();
system(ESPEAK_PATH.' -v '.escapeshellarg($voice).' '.escapeshellarg($text).' --stdout | '.LAME_PATH.' --quiet - -');
exit();