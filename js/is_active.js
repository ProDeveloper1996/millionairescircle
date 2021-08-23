var xmlHttp
var str1 = 0;
function is_active (table, field, id)
{ 
    xmlHttp=GetXmlHttpObject()
    if (xmlHttp==null)
    {
        alert ("Browser does not support HTTP Request")
        return
    } 
    var url="is_active.php"
    url=url+"?table="+table
    url=url+"&field="+field
    url=url+"&id="+id
    url=url+"&sid="+Math.random()
    str1 = id
    xmlHttp.onreadystatechange=stateChanged
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function is_menu (table, field, id)
{ 
    xmlHttp=GetXmlHttpObject()
    if (xmlHttp==null)
    {
        alert ("Browser does not support HTTP Request")
        return
    } 
    var url="is_menu.php"
    url=url+"?table="+table
    url=url+"&field="+field
    url=url+"&id="+id
    url=url+"&sid="+Math.random()
    str1 = "a" + id
    xmlHttp.onreadystatechange=stateChanged
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function ip_check (table, field, id)
{ 
    xmlHttp=GetXmlHttpObject()
    if (xmlHttp==null)
    {
        alert ("Browser does not support HTTP Request")
        return
    } 
    var url="ip_check.php"
    url=url+"?table="+table
    url=url+"&field="+field
    url=url+"&id="+id
    url=url+"&sid="+Math.random()
    str1 = "a" + id
    xmlHttp.onreadystatechange=stateChanged
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function is_replica (table, field, id)
{ 
    xmlHttp=GetXmlHttpObject()
    if (xmlHttp==null)
    {
        alert ("Browser does not support HTTP Request")
        return
    } 
    var url="is_replica.php"
    url=url+"?table="+table
    url=url+"&field="+field
    url=url+"&id="+id
    url=url+"&sid="+Math.random()
    str1 = "b" + id
    xmlHttp.onreadystatechange=stateChanged
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function open_level (member_id, level)
{ 
    
    xmlHttp=GetXmlHttpObject()
    if (xmlHttp==null)
    {
        alert ("Browser does not support HTTP Request")
        return
    } 
    var url="open_level.php"
    url=url+"?member_id="+member_id
    url=url+"&level="+level
    url=url+"&sid="+Math.random()
    str1 = level
    xmlHttp.onreadystatechange=stateChanged
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function close_level (member_id, level)
{ 
    
    xmlHttp=GetXmlHttpObject()
    if (xmlHttp==null)
    {
        alert ("Browser does not support HTTP Request")
        return
    } 
    var url="close_level.php"
    url=url+"?member_id="+member_id
    url=url+"&level="+level
    url=url+"&sid="+Math.random()
    str1 = level
    xmlHttp.onreadystatechange=stateChanged
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function stateChanged()
{ 
    if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
    { 
        id = "resultik" + str1
        document.getElementById(id).innerHTML=xmlHttp.responseText
    }
}

function GetXmlHttpObject()
{ 
    var objXMLHttp=null
    if (window.XMLHttpRequest)
    {
        objXMLHttp=new XMLHttpRequest()
    }
    else if (window.ActiveXObject)
    {
        objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
    }
    return objXMLHttp
}

