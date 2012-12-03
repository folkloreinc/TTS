<?php

//Change path depending on your system
define('ESPEAK_PATH', '/usr/local/bin/speak');
define('LAME_PATH', '/usr/local/bin/lame');
define('SOX_PATH', '/usr/bin/sox');


//Available voices
$VOICES = array('fr','en');

//Parameters
$text1 = $_REQUEST['text1'];
$voice1 = isset($_REQUEST['voice1']) && in_array($_REQUEST['voice1'],$VOICES) ? $_REQUEST['voice1']:'en';
$text2 = $_REQUEST['text2'];
$voice2 = isset($_REQUEST['voice2']) && in_array($_REQUEST['voice2'],$VOICES) ? $_REQUEST['voice2']:'en';

$tmpfname1 = tempnam("/tmp","tts").".wav";
$tmpfname2 = tempnam("/tmp","tts").".wav";

//Output mp3
if(!isset($_REQUEST['debug'])) {
	header('Content-type: audio/mpeg;');
} else {
	ini_set('display_errors',1);
	error_reporting(E_ALL);
}
exec(ESPEAK_PATH.' -v '.escapeshellarg($voice1).' -w '.escapeshellarg($tmpfname1).' '.escapeshellarg($text1));
exec(ESPEAK_PATH.' -v '.escapeshellarg($voice2).' -w '.escapeshellarg($tmpfname2).' '.escapeshellarg($text2));
system(SOX_PATH.' -m -v 1 '.escapeshellarg($tmpfname1).' -v 1 '.escapeshellarg($tmpfname2).' -t wav - | '.LAME_PATH.' --quiet - -');
exit();
