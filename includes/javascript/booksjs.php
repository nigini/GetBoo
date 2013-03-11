<?php
header('Content-Type: text/javascript');
?>

function checkurl(ele) {
    var address = ele.value;
    if (address != '') {
        if (address.indexOf(':') < 0) {
            address = 'http:\/\/' + address;
        }
        getTitle(address, null);
        ele.value = address;
    }
}


function getTitle(input, response)
{
    var title = document.getElementById('title');
    if (title.value == '') {
        title.style.backgroundImage = 'url(/images/progress.GIF)';
        if (response != null) {
            title.style.backgroundImage = 'none';
            title.value = response;
        } else if (input.indexOf('http') > -1) {
            loadXMLDoc('ajax/booktitle.php?url=' + input);
        } else {
            return false;
        }
    }
}

var xmlhttp;
function loadXMLDoc(url) {
    // Native
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = processStateChange;
        xmlhttp.open("GET", url, true);
        xmlhttp.send(null);
    // ActiveX
    } else if (window.ActiveXObject) {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        if (xmlhttp) {
            xmlhttp.onreadystatechange = processStateChange;
            xmlhttp.open("GET", url, true);
            xmlhttp.send();
        }
    }
}

function processStateChange() {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        response = xmlhttp.responseXML.documentElement;
        method = response.getElementsByTagName('method')[0].firstChild.data;
        result = response.getElementsByTagName('result')[0].firstChild.data;
        eval(method + '(\'\', result)');
    }
}