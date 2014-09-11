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
$noise = '0.0'.(isset($_REQUEST['noise']) && $_REQUEST['noise'] >= 0 ? $_REQUEST['noise']:rand(1,5));
$pitch = isset($_REQUEST['pitch']) && (int)$_REQUEST['pitch'] != 0 ? $_REQUEST['pitch']:0;
$speed = isset($_REQUEST['speed']) && (int)$_REQUEST['speed'] != 0 ? $_REQUEST['speed']:1;
$tempo = isset($_REQUEST['tempo']) && (int)$_REQUEST['tempo'] != 0 ? $_REQUEST['tempo']:1;

if(!preg_match('/^https?\:\/\//',$url)) {
    die('WRONG URL');
}

$tmpPath = tempnam('/tmp','ttsphonephp_').'.wav';

$command1 = 'curl %s | '.SOX_PATH.' -t mp3 - -t wav %s pitch %s speed %s tempo %s vol -3dB mcompand "0.005,0.1 -47,-40,-34,-34,-17,-33" 100 "0.003,0.05 -47,-40,-34,-34,-17,-33" 400 "0.000625,0.0125 -47,-40,-34,-34,-15,-33" 1600 "0.0001,0.025 -47,-40,-34,-34,-31,-31,-0,-30" 6400 "0,0.025 -38,-31,-28,-28,-0,-25" vol 15dB highpass 22 highpass 22';
$command1 = sprintf($command1, $url, $tmpPath, $pitch, $speed, $tempo);
exec($command1);

$command2 = 'sox %s -p synth whitenoise vol %s | sox -m %s - -t wav - | '.LAME_PATH.' -b 72 --quiet - -';
$command2 = sprintf($command2, $tmpPath, $noise, $tmpPath);
system($command2);

unlink($tmpPath);
