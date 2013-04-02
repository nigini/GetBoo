<?php
/* This script converts data from http://arvindn.livejournal.com/116137.html to 4 CSV files.
 * (Actually this can work with other data that uses the JSON format used by Arvind!)
 * The output files have the format that maps the exact sequence of columns in the following GetBoo
 * database tables: favourites, tags, tags_added, and tags_books.
 * This allows the importing data task much less timely expensive because we can use the LOAD DATA 
 * INFILE MySQL tool.
 */

	if($argc != 3) 
	{
		echo('Invalid arguments! Try something like this: \n');
		echo('php create_csv_files.php delicious-rss-1250k getboo_username 2> /dev/null \n');
		exit(1);
	}
	else
	{
		$file_name = $argv[1];
		$user_name = $argv[2];
		$file_handle = @fopen($file_name, "r");

		if($file_handle)
		{
			$books_output = @fopen("1_books_output.txt", "w");
			$tags_output = @fopen("2_tags_output.txt", "w");
			$relations_output = @fopen("3_relations_output.txt", "w");
			$pub_books_output = @fopen("4_pubbooks_output.txt", "w");
			require_once("../bookmarks.php");
			$tags_data = array();
			$count_book = 0;
			$count_tag = 1;
			while(!feof($file_handle))
			{
				$count_book++;
				$book_data = fgets($file_handle);
				$json_obj = json_decode($book_data);
				$date_str = date('Y-m-d H:i:s', strtotime($json_obj->updated));
				$tags_url = array();
				foreach($json_obj->tags as $tag)
				{
					$tag = trim($tag->term);
					$tag = str_replace(" ", "_", $tag);
					$tag = str_replace(array("\"",","), "", $tag);
					$tag = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $tag);
					$tag = strtolower($tag);
					if(array_search($tag,$tags_url)===False)
					{
						$tags_url[]=$tag;
						$tag_id = "";
						if(!array_key_exists($tag,$tags_data))
						{
							$tag_id = $count_tag++;
							$tags_data[$tag] = $tag_id;
							$tag_entry = "\"" . $tag_id . "\",\"" . $tag . "\",\"" . $date_str . "\"\n";
							fwrite($tags_output, $tag_entry);
						}
						else
						{
							$tag_id = $tags_data[$tag];
						}
						$relation_entry = "\"" . $count_book . "\",\"" . $tag_id . "\",\"" . $date_str . "\"\n";
						fwrite($relations_output, $relation_entry);
					}
				}
				$book_title = str_replace("\n", "", $json_obj->title);
				$book_line = "\"" . $count_book . "\",\"" . $user_name . "\",\"" . $book_title . "\",\"" . 
					0 . "\",\"" . $json_obj->link . "\",\"" . "NULL" . "\",\"" . $date_str . "\",\"" . 
					"0000-00-00 00:00:00"  . "\",\"" . "0000-00-00 00:00:00" . "\"\n";
				fwrite($books_output, $book_line);
				$pub_entry = "\"" . $count_book . "\",\"" . $date_str . "\"\n"; 
				fwrite($pub_books_output, $pub_entry);
			}
			fclose($file_handle);
			fclose($books_output);
			fclose($tags_output);
			fclose($relations_output);
			fclose($pub_books_output);
			exit(0);
		}
		else
		{
			echo("Could not read file " . $file_name);
			exit(1);
		}
	}
?>
