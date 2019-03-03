<?php
/*
 * FFmpeg Terminal Config
 * @Author: Pedram Asbaghi
 * @Email: Pedroxam@gmail.com
 * @Date: 03/2019
*/

//Disable Errors
error_reporting(0);

//No Limit Time
set_time_limit(0);

//Output Files Path
$store = 'store/';

//Log Path
$log = './log.txt';

//FFmpeg Installation Path
if (substr(php_uname(), 0, 7) == "Windows")
{
	 //windows ( make sure this file is exists)
	$ffmpeg  = dirname( __FILE__ ) . '/ext/ffmpeg.exe';
}
else {
	 //linux ( mke sure this path is true)
	$ffmpeg  = '/usr/bin/ffmpeg';
}

/*========================================
	## Do Not Edit Below Lines ##
========================================*/
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$url = str_replace('index.php','',$url);

define('URL', $url);
define('LOG_PATH', $log);
define('FFMPEG_PATH', $ffmpeg);
define('STORE_PATH', $store);
define('FFmepgTerminal', true);
