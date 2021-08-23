var xmlHttp
var str1 = 0;
function open_month(month, year)
{ 
    xmlHttp=GetXmlHttpObject()
    if (xmlHttp==null)
    {
        alert ("Browser does not support HTTP Request")
        return
    } 
    var url="open.php"
    url=url+"?month="+month
    url=url+"&year="+year
    url=url+"&sid="+Math.random()
    str1 = month
    xmlHttp.onreadystatechange=stateChanged
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function close_month(month, year)
{ 
    xmlHttp=GetXmlHttpObject()
    if (xmlHttp==null)
    {
        alert ("Browser does not support HTTP Request")
        return
    } 
    var url="close.php"
    url=url+"?month="+month
    url=url+"&year="+year
    url=url+"&sid="+Math.random()
    str1 = month
    xmlHttp.onreadystatechange=stateChanged
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function open_country (country, startdate, finishdate)
{ 
    xmlHttp=GetXmlHttpObject()
    if (xmlHttp==null)
    {
        alert ("Browser does not support HTTP Request")
        return
    } 
    var url="open.php"
    url=url+"?country="+country
    url=url+"&startdate="+startdate
    url=url+"&finishdate="+finishdate
    url=url+"&sid="+Math.random()
    str1 = country
    xmlHttp.onreadystatechange=stateChanged
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function close_country (country, startdate, finishdate)
{ 
    xmlHttp=GetXmlHttpObject()
    if (xmlHttp==null)
    {
        alert ("Browser does not support HTTP Request")
        return
    } 
    var url="close.php"
    url=url+"?country="+country
    url=url+"&startdate="+startdate
    url=url+"&finishdate="+finishdate
    url=url+"&sid="+Math.random()
    str1 = country
    xmlHttp.onreadystatechange=stateChanged
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

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