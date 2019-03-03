<?php
/*========================================
	## FFmpeg Terminal Config ##
========================================*/

//Disable Errors
// error_reporting(0);
error_reporting(E_ALL);
set_time_limit(0);

//Enter Username Here
$username = 'admin';

//Enter Password Here
$password = 'admin';

//Store Files Path
$store = 'store/';

//Log Path
$log = './log.txt';

//FFmpeg Path 
if (substr(php_uname(), 0, 7) == "Windows")
{
	 //windows ( no need to change )
	$ffmpeg  = dirname( __FILE__ ) . '/ext/ffmpeg.exe';
}
else {
	 //linux ( If the conversion operation did not work, edit this path )
	$ffmpeg  = '/usr/bin/ffmpeg';
}

/*========================================
	## Do Not Edit Below Lines ##
========================================*/
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$url = str_replace('index.php','',$url);

define('URL', $url);
define('USERNAME', $username);
define('PASSWORD', $password);
define('FFMPEG_PATH', $ffmpeg);
define('STORE_PATH', $store);
define('LOG_PATH', $log);
define('AccessVD', true);