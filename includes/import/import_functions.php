<?php
/* This script contains auxiliar functions to import bookmarks from different sources and formats.
 */

	/** 
	 * Reads the bookmarks returned by CiteULike in JSON format and import into Getboo backend. The 
	 * "article_id" is used to compose the URL and only successfully added non existent URL is counted
	 * to the return value. Existent URLs will have its tags updated. 
	 *
	 * @param resource $data_file File resource handler.
	 * @param string $username The user who is importing bookmarks.
	 * @return int The number of imported bookmarks.
	 */
	function import_citeulike_json_file(&$data_file, $username)
	{
		//TODO(nigini): check if username exists!
		$import_count = 0;
		$article_str = '';
		while(!feof($data_file))
		{
			$token = fgets($data_file);
			if($token[0] == '{')
			{
				$article_str = '{';
			}
			else if($token[0]=='}')
			{
				$article_str .= '}'; 
				if(import_citeulike_json(json_decode($article_str), $username))
				{
					$import_count += 1;
				}
			}
			else
			{
				$article_str .= substr($token, 0, -1);
			}
		}
		return $import_count;
	}

	/** 
	 * Creates bookmarks into GetBoo from data returned by CiteULike in JSON format (e.g. 
	 * www.citeulike.org/json/user/USER_NAME). It receives an article entry and uses the "article_id" 
	 * to compose the bookmark URL. If the URL already exists the tags are updated but the return is 
	 * False. 
	 *
	 * @param string $artcile_data A string containing the JSON data for a unique Citeulike article.
	 * @param string $username The user who is importing bookmarks.
	 * @return boolean True if the bookmark does not exists and was successfully added.
	 */
	function import_citeulike_json($article_data, $username)
	{
		require_once('includes/bookmarks.php');
		require_once('includes/tags_functions.php');
		$url_str = "http://www.citeulike.org/article/" . $article_data->article_id;
		$title_str = $article_data->title;
		$date_str = $article_data->date;
		$tags_str = '';
		if(array_key_exists('tags', $article_data))
		{
			$tags = $article_data->tags;
			for ($tag_index = 0; $tag_index < count($tags); $tag_index++)
			{
				$tags[$tag_index] = clean_tag($tags[$tag_index]); 
			}
			$tags_str = implode(' ', $article_data->tags);
		}
		$old_bookmark = b_url_exist($url_str, $username);
		$added_bookmark = False;
		if($old_bookmark['exists'])
		{
			storeTags($old_bookmark['bId'], $tags_str);
		}
		else
		{
			$result = add_bookmark($username, $title_str, 0, $url_str, '', $tags_str, True, $date_str);
			$added_bookmark = $result['success'];
		}
		return $added_bookmark;
	}
?>
