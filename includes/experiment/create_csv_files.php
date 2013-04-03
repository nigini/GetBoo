<?php
/* This script converts data from http://arvindn.livejournal.com/116137.html to 4 CSV files.
 * (Actually this can work with other data that uses the JSON format used by Arvind!)
 * The output files have the format that maps the exact sequence of columns in the following GetBoo
 * database tables: favourites, tags, tags_added, and tags_books.
 * This allows the importing data task much less timely expensive because we can use the LOAD DATA 
 * INFILE MySQL tool.
 */
	
	function replace_unicode_escape_sequence($match)
	{
		return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
	}

	function convert_to_utf8($str)
	{
		$str = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $str);
		return $str;
	}

	function clean_tag($tag)
	{
		$tag = convert_to_utf8($tag);
		$tag = trim($tag);
		$tag = str_replace(array(" ","/","-"), "_", $tag);
		$tag = str_replace(array("\"",","), "", $tag);
		//$tag = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $tag);
		$tag = strtolower($tag);
		return $tag;
	}


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
			$tags_data = array(); //TAG->ID
			$url_data = array(); //URL->(['ID']->ID,['TAGS']->(T1,T2,T3))
			$count_book = 0;
			$count_tag = 0;
			while(!feof($file_handle))
			{
				$book_id = "";
				$book_data = fgets($file_handle);
				$json_obj = json_decode($book_data);
				$date_str = date('Y-m-d H:i:s', strtotime($json_obj->updated));
				$url_str = $json_obj->link;
				$url_tags = array();
				
				//FIND TAGS TO BE ADDED TO A BOOKMARK
				$url_exists = array_key_exists($url_str,$url_data);
				if(!$url_exists)
				{
					$book_id = $count_book+=1;
				  foreach($json_obj->tags as $tag)
					{
						$tag = clean_tag($tag->term);
						if(array_search($tag,$url_tags)===False)
						{
							$url_tags[]=$tag;
						}
					}	
					$url_data[$url_str] = array('ID'=>$book_id,'TAGS'=>$url_tags);
				}
				else
				{
					$url_local_data = $url_data[$url_str];
					$book_id = $url_local_data['ID'];
					$book_tags = $url_local_data['TAGS'];
					foreach($json_obj->tags as $tag)
					{
						$tag = clean_tag($tag->term);
						if(array_search($tag,$book_tags)===False and array_search($tag,$url_tags)===False)
						{
							$url_tags[]=$tag;
							$book_tags[]=$tag;
						}
					}
					$url_data[$url_str]['TAGS']=$book_tags;
				}
				//ADD NEW TAGS TO A BOOKMARK
				foreach($url_tags as $tag)
				{
					$tag_id = "";
					if(!array_key_exists($tag,$tags_data))
					{
						$tag_id = $count_tag+=1;
						$tags_data[$tag] = $tag_id;
						$tag_entry = "\"" . $tag_id . "\",\"" . $tag . "\",\"" . $date_str . "\"\n";
						fwrite($tags_output, $tag_entry);
					}
					else
					{
						$tag_id = $tags_data[$tag];
					}
					$relation_entry = "\"" . $book_id . "\",\"" . $tag_id . "\",\"" . $date_str . "\"\n";
					fwrite($relations_output, $relation_entry);
				}
				//ADD BOOKMARK IF IT IS NEW
				if(!$url_exists)
				{
					$book_title = str_replace("\n", "", $json_obj->title);
					$book_title = convert_to_utf8($book_title);
					$book_line = "\"" . $book_id . "\",\"" . $user_name . "\",\"" . $book_title . "\",\"" . 
						0 . "\",\"" . $url_str . "\",\"" . "NULL" . "\",\"" . $date_str . "\",\"" . 
						"0000-00-00 00:00:00"  . "\",\"" . "0000-00-00 00:00:00" . "\"\n";
					fwrite($books_output, $book_line);
					$pub_entry = "\"" . $book_id . "\",\"" . $date_str . "\"\n"; 
					fwrite($pub_books_output, $pub_entry);
				}
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
