<?php
	function usage()
	{
		echo('php run_import.php FILE_TYPE FILE_PATH USER_NAME 2> /dev/null' . PHP_EOL);
		echo('FILE_TYPEs supported: citeulike' . PHP_EOL);
	}

	if($argc != 4) 
	{
		echo('Invalid arguments! Try something like this:' . PHP_EOL);
		usage();
		exit(1);
	}
	$file_type = $argv[1];
	$file_name = $argv[2];
	$user_name = $argv[3];
	$file_handle = @fopen($file_name, "r");

	if($file_handle)
	{
		$count = 0;
		include('import_functions.php');
		if($file_type == 'citeulike')
		{
			$count = import_citeulike_json_file($file_handle, $user_name);
		}
		else
		{
			echo('FILE_TYPE not supported! Usage:' . PHP_EOL);
			usage();
			exit(1);
		}
		echo('IMPORTED: ' . $count . PHP_EOL);
		exit(0);
	}
?>
