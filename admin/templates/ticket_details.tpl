<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER}</td></tr>
</table>

<table width='100%' cellspacing='0' cellpadding='0' align='center'>
    <tr>
        <td>
        <table  cellspacing='0' cellpadding='5'>
            <tr>
                <td>Subject:</td>
                <td><h4>{MAIN_SUBJECT}</h4></td>
            </tr>            
            <tr>
                <td>Status:</td>
                <td>{MAIN_STATUS}</td>
            </tr>            
           
            <tr>
                <td>Last Update:</td>
                <td><b>{MAIN_LAST_UPDATE}</b></td>
            </tr>
            <tr>
                <td>Created On:</td>
                <td><b>{MAIN_DATE_CREATE}</b></td>
            </tr>
        </table>
        </td>
    </tr>
</table>

<table width='100%' cellspacing='0' cellpadding='3' >
    <tr>
        <td colspan="2"><h4>Correspondence</h4></td>
    </tr>
    <!-- BEGIN: TABLE_ROW -->
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td width="20%" rowspan="2"><span style='color:#000'><b>{ROW_FROM}</b></span></br><span class='hidden'>(Posted on {ROW_DATE_POST})</span></td>
    </tr>
    <tr bgcolor='{ROW_BGCOLOR}'>
        <td><b>{ROW_MESSAGE}</b></td>
    </tr>
    <!-- END: TABLE_ROW -->
</table>

<table width='100%' cellspacing='0' cellpadding='2' align='center'>
    <tr><td height='20'></td></tr>
</table>

<form name='form1' action='{MAIN_ACTION}' method='POST'>
<table width='100%' border='0' cellspacing='0' cellpadding='2' align='center'>
    <tr>
        <td width="20%" valign='top'><h4>Post Reply</h4></td>
        <td>{MAIN_MESSAGE} &nbsp;<span class='error'><div id='error'></div></span></td>
    </tr>
    <tr>
        <td colspan='3'>
            <input class='some_btn' type='submit' value=" Reply " onClick="return func ();"> &nbsp;
            <input class='some_btn' type='button' value=" Close " onClick="window.location.href='{MAIN_CANCEL_URL}'">
        </td>
    </tr>
</table>
<input type='hidden' name='ocd' value='{MAIN_OCD}'>
<input type='hidden' name='id' value='{MAIN_ID}'>
</form>

<table width='100%' border='0' cellspacing='0' cellpadding='0' align='center'>
    <tr><td height='12'></td></tr>
</table>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->