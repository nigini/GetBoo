<?php
//include("../gettext.php");
include('../../config.inc.php');
header('Content-Type: text/javascript');
?>

function deleteBookmark(ele, item){
	var link = $(ele).parents("li.treeItem");
	var confirmDelete = "<span class='confirm'><?php echo T_("Delete") . "?";?> <a href=\"#\" onclick=\"deleteBookmarkConfirmed(this, " + item + ", \'\'); return false;\"><?php echo T_("Yes");?></a> - <a href=\"#\" onclick=\"deleteCancelled(this); return false;\"><?php echo T_("No");?></a></span>";
	if (link.eq(0).children("span.confirm").length < 1)
	{
		link.eq(0).append(confirmDelete);
	}
}

function deleteFolder(ele, item){
	var link = $(ele).parents("li.treeItem");
	var confirmDelete = " <span class='confirm' style='padding-left: 4px'><?php echo T_("Delete") . "?";?> <a href=\"#\" onclick=\"deleteFolderConfirmed(this, " + item + ", \'\'); return false;\"><?php echo T_("Yes");?></a> - <a href=\"#\" onclick=\"deleteCancelled(this); return false;\"><?php echo T_("No");?></span>";
	if (link.eq(0).children("span.confirm").length < 1)
	{
		$(ele).after(confirmDelete);
	}
}

function deleteCancelled(ele) {
$(ele).parents('span.confirm').remove();
}

function deleteBookmarkConfirmed(ele, item) {
$.ajax({
type: 'GET',
url: 'ajax/deletebook.php',
data: { 'bookID': item },
success: function(msg) {
$(ele).parents('li.treeItem').eq(0).remove();
}
});
}

function deleteFolderConfirmed(ele, item) {
$.ajax({
type: 'GET',
url: 'ajax/deletefolder.php',
data: { 'folderID': item },
success: function(msg) {
$(ele).parents('li.treeItem').eq(0).remove();
}
});
}