<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>FFmpeg Terminal</title>
<link rel="stylesheet" href="<?php echo URL; ?>assets/css/style.css">
<link href='https://fonts.googleapis.com/css?family=Source+Code+Pro:200' rel='stylesheet' type='text/css'>
</head>
<body>
<div class="container">
	<div class="window">
		<div class="handle">
			<span class="title"></span>
		</div>
		<div class="terminal"></div>
	</div>
	
	<div class="progress">
		<div class="bar">5%</div>
	</div>
	
</div>
<script src="<?php echo URL; ?>assets/js/jquery.min.js"></script>

<script>
$(document).ready(function() {
		"use strict";
		
		var title = $(".title");
		var terminal = $(".terminal");
		var prompt = "➜";
		var path = "~";

		var commandHistory = [];
		var historyIndex = 0;

		var command = "";
		var doProgress;
			
		  function SetProgressStart() {
			$('.progress').show();
			doProgress = setInterval(
			  showProgress, 2000);
		  }
		  
		  function showProgress() {
			$.getJSON('<?php echo URL; ?>progress.php',
			  function (data) {
				if (data.progress == "done")
				{
				  clearInterval(doProgress);
					terminal.append('Operation Done.');
				}
				else if (data.progress == "error_audio")
				{
					clearInterval(doProgress);
					terminal.append('The target video is not include audio, please select a video that has the audio to perform this operation.');
				}
				else if (data.progress == "unknown_error")
				{
					clearInterval(doProgress);
					terminal.append('Unknown Error !');
				}
				else
				{
				  $('.bar').css('width', data.progress + '%' );
				  $('.bar').html(data.progress + '%');
				}
				
			  });
		  }
		  
		function clear() {
			terminal.text("");
		}

		function terminalHelp() {
			$.ajax({
				type:'POST',
				url:'<?php echo URL; ?>commands.php',
				data: { terminal_help: '1' }
			})
			.done(function(data){
				terminal.append(data);
				displayPrompt();
			})
			.fail(function(data){
				terminal.append(data);
			})
		}
		
		function getLogs() {
			$.ajax({
				type:'POST',
				url:'<?php echo URL; ?>commands.php',
				data: { log: '1' }
			})
			.done(function(data){
				// console.log(data);
				terminal.append(data);
				displayPrompt();
			})
			.fail(function(data){
				terminal.append(data);
			})
		}

		function help() {
			$.ajax({
				type:'POST',
				url:'<?php echo URL; ?>commands.php',
				data: { help: '1' }
			})
			.done(function(data){
				setTimeout(function () {
					getLogs();
				}, 800);
			})
			.fail(function(data){
				terminal.append(data);
			})
		}

		function ffmpegCommands(command) {
			SetProgressStart();
			$.ajax({
				type:'POST',
				url:'<?php echo URL; ?>commands.php',
				data: { commands: command }
			})
			.done(function(data){
				setTimeout(function () {
					getLogs();
				}, 800);
			})
			.fail(function(data){
				setTimeout(function () {
					getLogs();
				}, 800);
			})
		}

		function terminalCommands(command) {
			$.ajax({
				type:'POST',
				url:'<?php echo URL; ?>commands.php',
				data: { terminal_commands: command }
			})
			.done(function(data){
				terminal.append(data);
			})
			.fail(function(data){
				terminal.append(data);
			})
		}
		
			function displayPrompt() {
					terminal.append("<span class=\"prompt\">" + prompt + "</span> ");
					terminal.append("<span class=\"path\">" + path + "</span> ");
			}

			function erase(n) {
					command = command.slice(0, -n);
					terminal.html(terminal.html().slice(0, -n));
			}

			function clearCommand() {
					if (command.length > 0) {
							erase(command.length);
					}
			}

			function appendCommand(str) {
					terminal.append(str);
					command += str;
			}
					
				function processCommand() {
				
					if(command=='clear')
					{
						clear();
					}
					else if(command=='log')
					{
						getLogs();
					}
					else if(command=='__help')
					{
						terminalHelp();
					}
					else if(command=='help')
					{
						help();
					}
					else {

						var term_find = command.indexOf("__");
						
						if(term_find =='0') {
							terminal.append("Please Wait...\n");
							terminalCommands(command);
						} 
						else {
						
							var find = command.indexOf("ffmpeg");
							
							if(find =='0') {
								ffmpegCommands(command);
							}
							else {
								terminal.append("error: command not found: " + command + "\n");
							}
						
						}
					}
					
					commandHistory.push(command);
					historyIndex = commandHistory.length;
					command = "";
			}

$(document).keydown(function(e) {
		e = e || window.event;
		var keyCode = typeof e.which === "number" ? e.which : e.keyCode;

		if (keyCode === 8 && e.target.tagName !== "INPUT" && e.target.tagName !== "TEXTAREA") {
				e.preventDefault();
				if (command !== "") {
						erase(1);
				}
		}

		if (keyCode === 38 || keyCode === 40) {
				if (keyCode === 38) {
						historyIndex--;
						if (historyIndex < 0) {
								historyIndex++;
						}
				} else if (keyCode === 40) {
						historyIndex++;
						if (historyIndex > commandHistory.length - 1) {
								historyIndex--;
						}
				}
				var cmd = commandHistory[historyIndex];
				if (cmd !== undefined) {
						clearCommand();
						appendCommand(cmd);
				}
		}
});

$(document).keypress(function(e) {
		e = e || window.event;
		var keyCode = typeof e.which === "number" ? e.which : e.keyCode;

		switch (keyCode) {
				case 13:
						{
							terminal.append("\n");
							processCommand();
							displayPrompt();
							break;
						}
				default:
						{
							appendCommand(String.fromCharCode(keyCode));
						}
		}
});

// Set the window title
title.text("Enter FFmpeg Command");

// Display Start Msg
terminal.append("For helping Terminal Commands, Type '__help' and hit Enter.\n");
terminal.append("And For helping FFmpeg commands, Type 'help' and hit Enter.\n"); displayPrompt();

});

</script>

</body>
</html>