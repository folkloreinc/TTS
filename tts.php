<?php


//Change path depending on your system
define('SOX_PATH', '/usr/bin/sox');
define('LAME_PATH', '/usr/local/bin/lame');

//Programs
$PROGRAMS = array(

	//Say
	'say' => array(
		'path' => '/usr/bin/say -v %s -o %s',
		'format' => 'aiff',
		'voices' => array(
			'Alex',
			'Bruce',
			'Fred',
			'Kathy',
			'Vicki',
			'Victoria',
			'Sebastien',
			'Felix',
			'Julie',
			'Thomas',
			'Virginie'
		)
	),

	//espeak
	'espeak' => array(
		'path' => '/usr/local/bin/speak -v %s -w %s',
		'format' => 'wav',
		'voices' => array(
			'fr',
			'en'
		)
	)
);

//Mix method
$METHODS = array('mix', 'concatenate', 'sequence', 'mix-power', 'merge', 'multiply');

//Set headers
if(!isset($_REQUEST['debug']) || $_REQUEST['debug'] != 'true') {
	header('Content-type: audio/mpeg;');
} else {
	ini_set('display_errors',1);
	error_reporting(E_ALL);
}

//Check parameters
if(!isset($_REQUEST['text']) || empty($_REQUEST['text'])) die();
if(!isset($_REQUEST['program']) || empty($_REQUEST['program'])) die();
if(!isset($PROGRAMS[$_REQUEST['program']])) die();


//Parameters
$program = $PROGRAMS[$_REQUEST['program']];
if(isset($_REQUEST['mix']) && !empty($_REQUEST['mix'])) {
	$mix =  in_array($_REQUEST['mix'],$METHODS) ? $_REQUEST['mix']:$METHODS[0];
} else {
	$mix =  null;
}


//Multiple files
if($mix) {

	$texts = is_array($_REQUEST['text']) ? $_REQUEST['text']:array($_REQUEST['text']);
	$voices = is_array($_REQUEST['voice']) ? $_REQUEST['voice']:array($_REQUEST['voice']);

	$commandParts = array();
	$tmpPaths = array();
	for($i = 0; $i < sizeof($texts); $i++) {
		$tmpPath = tempnam('/tmp','ttsphp_').'.'.$program['format'];
		$voice = isset($voices[$i]) && in_array($voices[$i],$program['voices']) ? $voices[$i]:$program['voices'][0];
		$command = sprintf($program['path'], escapeshellarg($voice), escapeshellarg($tmpPath), escapeshellarg($texts[$i]));
		exec($command);
		$tmpPaths[] = $tmpPath;
		$commandParts[] = sprintf('-v 1 %s',escapeshellarg($tmpPath));
	}

	system(SOX_PATH.' --combine '.escapeshellarg($mix).' '.implode(' ',$commandParts).' -t mp3 -');

	foreach($tmpPaths as $path) {
		unlink($path);
	}

} else {

	$text = $_REQUEST['text'];
	$voice = isset($_REQUEST['voice']) && in_array($_REQUEST['voice'],$program['voices']) ? $_REQUEST['voice']:$program['voices'][0];

	$tmpPath = tempnam('/tmp','ttsphp_').'.'.$program['format'];
	$command = sprintf($program['path'], escapeshellarg($voice), escapeshellarg($tmpPath), escapeshellarg($text));
	exec($command);

	system(LAME_PATH.' --quiet '.escapeshellarg($tmpPath).' -');

	unlink($tmpPath);

}