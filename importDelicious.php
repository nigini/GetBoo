<?php
/***************************************************************************
Copyright (C) 2005-2008 GetBoo project
http://sourceforge.net/projects/getboo/
http://www.getboo.com/

This file is part of GetBoo.

GetBoo is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

GetBoo is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with GetBoo; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
***************************************************************************/
ob_start();
include('header.php'); ?>
<?php
	include('access.php');
	$access = checkAccess();
	if($access)
	{
		//Check if form submitted
		if ($_POST['submitted'])
		{
			if (!empty ($_FILES['import']['tmp_name']))
			{
				set_time_limit(100);
			    $user = new User();
				$username = $user->getUsername();
		        include("includes/bookmarks.php");
				$file = file ($_FILES['import']['tmp_name']);
				$depth = array();
			    $xml_parser = xml_parser_create();
			    xml_set_element_handler($xml_parser, "startElement", "endElement");
			
			    if (!($fp = fopen($_FILES['import']['tmp_name'], "r")))
			        die("<p class=\"error\">" . T_("Could not open XML input") . "</p>");
			
			    while ($data = fread($fp, 4096)) {
			        if (!xml_parse($xml_parser, $data, feof($fp))) {
			            die("<p class=\"error\">" . sprintf(T_("XML error: %s at line %d"),
			                xml_error_string(xml_get_error_code($xml_parser)),
			                xml_get_current_line_number($xml_parser)). "</p>");
			        }
			    }
			    xml_parser_free($xml_parser);
			    $_SESSION['success_msg'] = T_("The bookmarks have been imported from the XML file");
			    //Store the import date in table bookexportimport
			    include('conn.php');
				$Query = "INSERT INTO " . TABLE_PREFIX . "bookexportimport ( Name , Method , Time , IP ) values ('$username', 'ID', now(), '" . $_SERVER['REMOTE_ADDR'] ."')";
				//echo("$Query<br>\n");
				$dbResult = $dblink->query($Query);
				
			    header('Location: books.php');
			}
			else
			{
				$error = ("<p class=\"error\">" . T_("No file has been selected") . "</p>");
			}
		}
		include('bheader.php');
		if($error)
			echo($error);
?>
	<br><b><?php echo T_("Import from Delicious");?></b><br><br>
	<?php echo sprintf(T_("Here you can import your bookmarks from your <a href=\"%s\">del.icio.us</a> account.<br>Scuttle also supports the same API, so it can also be done with your Scuttle installation."), "http://del.icio.us");?><br>
	<br>
	<b><?php echo T_("Instructions");?></b><br>
	<ol>
    <li><?php echo sprintf(T_('Log in to the <a href="%s">export page at del.icio.us</a>, or the %s page from your Scuttle installation'),"http://del.icio.us/api/posts/all", "/api/posts/all"); ?>.</li>
    <li><?php echo T_('Save the resulting <abbr title="Extensible Markup Language">XML</abbr> file to your computer'); ?>.</li>
    <li><?php echo T_('Click Browse... to find this file on your computer'); ?>.</li>
    <?php /*TODO: Privacy?
    <li><?php echo T_('Select the default privacy setting for your imported bookmarks'); ?>.</li> */?>
    <li><?php echo T_('Click Import to start importing the bookmarks; it may take a minute'); ?>.</li>
	</ol>
	</div>
	<div class="content">
			<b><?php echo T_("Select the file you want to import your bookmarks from");?>:</b>
			<br><br>
			<form method="post" action="importDelicious.php" enctype="multipart/form-data">
				<input type="file" name="import" size="30" class="formtext" onfocus="this.select()"><br>
				<!--<span class="formsLabel"><?php echo T_("Remove duplicates");?></span>
				<input type="checkbox" name="duplicate"><br><br>-->
				<input type="submit" name="submitted" value="<?php echo T_("Import");?>" class="genericButton">
			</form>
		</div>
<?php }
function startElement($parser, $name, $attrs) {
    global $depth, $status, $tplVars, $username;

    if ($name == 'POST') {
        while(list($attrTitle, $attrVal) = each($attrs)) {
            switch ($attrTitle) {
                case 'HREF':
                    $bAddress = $attrVal;
                    break;
                case 'DESCRIPTION':
                    $bTitle = $attrVal;
                    break;
                case 'EXTENDED':
                    $bDescription = $attrVal;
                    break;
                case 'TIME':
                    $bDatetime = $attrVal;
                    break;
                case 'TAG':
                    $tags = strtolower($attrVal);
                    break;
            }
        }
        
       
		$result = b_url_exist($bAddress,$username);
        if($result['exists']) {
            $tplVars['error'] = T_('You have already submitted this bookmark.');
        } else {
            // Strangely, PHP can't work out full ISO 8601 dates, so we have to chop off the Z.
            $bDatetime = substr($bDatetime, 0, -1);

            // If bookmark claims to be from the future, set it to be now instead
            if (strtotime($bDatetime) > time()) {
                $bDatetime = date('Y-m-d H:i:s');
            }

            $resultArr = add_bookmark($username, $bTitle, MAIN_FID, $bAddress, $bDescription, $tags, true, $bDatetime);
            if ($resultArr['success'])
                $tplVars['msg'] = T_('Bookmark imported.');
            else
                $tplVars['error'] = T_('There was an error saving your bookmark. Please try again or contact the administrator.');
        }
    }
    $depth[$parser]++;
}

function endElement($parser, $name) {
    global $depth;
    $depth[$parser]--;
}

include('footer.php'); 
ob_end_flush();
?>