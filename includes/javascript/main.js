function sf() {
	var oLogin = document.getElementById('login_usrname');
	if(oLogin != null) oLogin.focus();
	else
	{
		var oSearch = document.getElementById('search_box');
		if(oSearch != null) oSearch.focus();
	}
}
function activateTags(cBox)
{
	if(cBox.checked)
		document.getElementById("tags").readOnly = false;	
	else
		document.getElementById("tags").readOnly = true;
}