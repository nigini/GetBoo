<?php
	if($argc != 4) 
	{
		echo('Invalid arguments! Try something like this: \n');
		echo('php run_import.php citeulike FILE_NAME USER_NAME 2> /dev/null \n');
		exit(1);
	}
	else
	{
		$file_name = $argv[2];
		$user_name = $argv[3];
		$file_handle = @fopen($file_name, "r");

		if($file_handle)
		{
			include('import_functions.php');
			$count = import_citeulike_json_file($file_handle, $user_name);
			echo('IMPORTED: ' . $count . '\n');
		}
	}
?>
