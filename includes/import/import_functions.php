<?php
/* This script contains auxiliar functions to import bookmarks from different sources and formats.
 */

	/** 
	 * This function reads the JSON data in the format returned by the CiteULike export calls (e.g. 
	 * www.citeulike.org/json/user/USER_NAME) and uses the "article_id" to compose the URL to the 
	 * CiteULike article. If the URL already exists the tags are updated but the counter is not 
	 * incremented. 
	 *
	 * @param resource $data_file File resource handler.
	 * @param string $username The user who is importing bookmarks.
	 * @return int The number of imported bookmarks.
	 */
	function import_citeulike_json_file(&$data_file, $username)
	{
		//TODO(nigini): check if username exists!
		include('includes/bookmarks.php');
		include('includes/tags_functions.php');
		$import_count = 0;
		$article_str = '';
		while(!feof($data_file))
		{
			$token = fgets($data_file);
			if($token[0] == '{')
			{
				$article_str = '{';
			}
			else
			{
				$article_str .= substr($token,0,-1);
			}
			if($token[0]=='}')
			{
				if($token[1]==',')
				{
					$article_str = substr($article_str,0,-1);
				}
				$article_data = json_decode($article_str);
				$result = import_citeulike_json($article_data, $username);
				if($result == True)
				{
					$import_count += 1;
				}
			}
		}
		return $import_count;
	}

	/** 
	 * This function creates bookmarks from JSON data in the format returned by the CiteULike export 
	 * calls (e.g. www.citeulike.org/json/user/USER_NAME). It receives an article entry and uses the 
	 * "article_id" to compose the bookmark URL. If the URL already exists the tags are updated but 
	 * the return is False. 
	 *
	 * @param string $artcile_data A string containing the JSON data for a unique Citeulike article.
	 * @param string $username The user who is importing bookmarks.
	 * @return boolean True if the bookmark does not exists and was successfully added.
	 */
	function import_citeulike_json($article_data, $username)
	{
		$url_str = $article_data->article_id;
		$title_str = $article_data->title;
		$date_str = $article_data->date;
		if(array_key_exists('tags',$article_data))
		{
			$tags_str = implode(' ',$article_data->tags);
		}
		else
		{
			$tags_str = '';
		}
		$old_bookmark = b_url_exist($url_str, $username);
		if($old_bookmark['exists'] == True)
		{
			storeTags($old_bookmark['bId'], $tags_str);
			return False;
		}
		else
		{
			$result = add_bookmark($username, $title_str, 0, $url_str, '', $tags_str, True, $date_str);
			return $result['success'];
		}
	}
?>
