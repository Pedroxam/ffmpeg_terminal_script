<?php
/*
 * FFmpeg Terminal Command Excutables
 * @Author: Pedram Asbaghi
 * @Email: Pedroxam@gmail.com
 * @Date: 03/2019
*/

include 'config.php';

if(isset($_POST['log'])) {
	$log = getLogs();
	if(empty($log)) echo "Log is Empty.\n";
	exit($log);
}

if(isset($_POST['terminal_help'])) {
	echo trim(preg_replace('/\t+/', '', teminalHelp()));
	exit("\n");
}

if(isset($_POST['terminal_commands'])) {
	echo execTerminal($_POST['terminal_commands']);
	exit("\n");
}

if(isset($_POST['help'])) {
	ffmpegHelp();
	exit("\n");
}

if(isset($_POST['commands'])) {
	execFFmpeg($_POST['commands']);
	exit();
}

/*========================================
	##  Functions ##
========================================*/

/**
* Format Size
**/
function format_size($bytes)
{
	switch ($bytes) {
		case $bytes < 1024:
		$size = $bytes . " B";
		break;
		case $bytes < 1048576:
		$size = round($bytes / 1024, 2) . " KB";
		break;
		case $bytes < 1073741824:
		$size = round($bytes / 1048576, 2) . " MB";
		break;
		case $bytes < 1099511627776:
		$size = round($bytes / 1073741824, 2) . " GB";
		break;
	}
	if (!empty($size)) {
		return $size;
	} else {
		return "";
	}
}

/*
 * FFmpeg Help
 */
function ffmpegHelp() {
	
	$log = LOG_PATH;
	
	if (substr(php_uname(), 0, 7) == "Windows")
	{
		//windows
		pclose(popen("start /B " . FFMPEG_PATH . ' -help' . " 1> $log 2>&1", "r"));
	}
	else
	{
		//linux
		shell_exec(FFMPEG_PATH . ' -help' . " 1> $log 2>&1" );
	}
}

/*
 * Exec FFmpeg Command
 */
function execFFmpeg($commands) {
	
	$log = LOG_PATH;

	$command	= str_replace('ffmpeg', FFMPEG_PATH, $commands);

	if (substr(php_uname(), 0, 7) == "Windows")
	{
		//windows
		pclose(popen("start /B " . $command . " 1> $log 2>&1", "r"));
	}
	else
	{
		//linux
		shell_exec( $command . " 1> $log 2>&1" );
	}
	return true;
}

/*
 * Keyword Validator
 */
function is_key($in,$key) {
	if(strpos($in, $key) !== false) {
		return true;
	} else return false;
}

/*
 * Exec Teminal Commands
 */
function execTerminal($command) {
	
	$mkdir = '__mkdir';
	$list = '__list';
	$play = '__play';
	$shot = '__shot';
	$get  = '__get';
	
	if(is_key($command, $mkdir)) {
		return makeDir(str_replace($mkdir,'',$command));
	}
	
	if(is_key($command, $list)) {
		return getfiles(str_replace($list,'',$command));
	}
	
	if(is_key($command, $play)) {
		return playVideo(str_replace($play,'',$command));
	}
	
	if(is_key($command, $shot)) {
		return takeScreen(str_replace($shot,'',$command));
	}
	
	if(is_key($command, $get)) {
		return uploadFile(str_replace($get,'',$command));
	}
}


/*
 * Teminal Help
 */
function teminalHelp() {
	
	$text = "
		The commands are as follows:\n
		---------
		Clear terminal:\n
		clear\n
		---------
		Show last ffmpeg log:\n
		log\n
		---------
		Create folder:\n
		__mkdir 'FOLDER NAME'\n
		Example: __mkdir videos\n
		---------
		Remote Upload File:\n
		__get 'FILE URL' to 'FOLDER/FILE NAME'\n
		Example: __get http://example.com/video.avi to videos/new.avi\n
		Supported Files to upload: mp4|3gp|avi|mkv|mov
		---------
		View list of files in target folder:\n
		__list 'FOLDER NAME'\n
		Example: __list videos\n
		---------
		Play Video:\n
		__play 'FILE NAME'\n
		Example: __play videos/test.mp4\n
		---------
		Take Screen Shot of 5 seconds of video:\n
		__shot 'FILE NAME'\n
		Example: __shot videos/test.mp4\n
		---------
		You are Allowed to use public ffmpeg commands.
		Here is a simple example for convert video to 'webm' format:\n
		ffmpeg -i videos/input.mp4 videos/output.webm\n
		
		Fore More Example and information from ffmpeg commands, visit offical ffmpeg site:\n
		<a href='http://ffmpeg.org/ffmpeg.html' target='_blank'>http://ffmpeg.org/ffmpeg.html</a>
		---------
	";
	
	return $text;
}

/*
 * Show Log
 */
function getLogs() {
	return file_get_contents(LOG_PATH);
}

/*
 * Mke Directory
 */
function makeDir($name) {
	if(@mkdir($name,0777, true)){
		return "$name is maked.";
	} else 
		return "Please Enter Folder Name.\n";
}

/*
 * Get Files From Folder
 */
function getfiles($name) {
	
	$name = trim($name);
	$files = glob("$name/*.*");
	
	if(!count($files)) return "The are no files.\n";
	
	foreach ($files as $filename) {
		return "$filename size " . format_size(filesize($filename)) . "\n";
	}
}

/*
 * Play Video
 */
function playVideo($name) {
	return "<video src='$name' style=' width:20%;height:150px;margin:50px;' controls></video>";
}

/*
 * Take Screen Shot
 */
function takeScreen($name) {
	$image = uniqid() . basename($name) . '_.jpg';
	$second = '00:00:05';
	$cmd = "ffmpeg -i $name -an -ss $second -s 640x480 -vcodec mjpeg $image";
	execFFmpeg($cmd);
	sleep(4);
	return "<img src='$image' style='width:20%;margin:50px;'>";
	
}

/*
 * Remote Upload File
 */
function uploadFile($name) {
	
	sleep(.1);
	
	//find download url
	if(preg_match('/(https?:\/\/[^ ]+?(?:\.webm|\.mp4|\.3gp|\.avi|\.mkv|\.mov))/m', $name, $filename)) {
		$url = trim($filename[1]);
	}
	else return "Only full URLs are allowed with following Formats: (at the end of url)\n  mp4|3gp|avi|mkv|mov\n";
	
	//find save location
	if(preg_match('/to\s(.*)/m', $name, $location)) {
		$path = $location[1];
	}
	else return "Please enter the file save path correctly.\n";
	
	//put file
	$put = file_put_contents($path, file_get_contents($url));
	
	if($put) {
		return "$path Successfully uploaded.\n";
	}
	else
		return "Please check the URL.\n";
	
}
