<!-- BEGIN: MAIN -->

<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>{HEADER_TITLE}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO 8859-1">
    <meta http-equiv="content-language" content="en">
    <link href="./css/styles.css" type="text/css" rel="stylesheet" />
    {HEADER_JAVASCRIPTS}
</head>

<body>
<div align="center">
<table width='100%' height='100%' border='0' cellspacing='0' cellpadding='0'>
<tr>
    <td valign='middle'>
             <table width="40%" cellpadding="7" cellspacing="0" border='0' align='center'>
                <tr>
                    <td colspan='2' align='center'><h4>{MAIN_HEADER}</h4></td>
                </tr>
                <tr style='height:60px;'>
                    <td>
            <form name='LoginForm' action='{PAGE_ACTION}' method='POST'>
            <table width='100%' border='0' cellspacing='5' cellpadding='0'>

                <tr><td colspan='2' height='10'></td></tr>
                <tr>
                    <td colspan='2' align='center'>{MAIN_MESSAGE}</td>
                </tr>
                <tr><td colspan='2' height='10'></td></tr>
                <tr>
                    <td width='50%' align='right'>{DICT.FP_EmailAddress}</td>
                    <td> {LOGIN_EMAIL}</td>
                </tr>
                <tr><td colspan='2' height='10'></td></tr>

                <tr>
                    <td>
                    </td>
                    <td>
                        <input type='submit' class='some_btn' value='{DICT.FP_Send}'>
                    </td>
                </tr>
                <tr>
                    <td colspan='2' height='20'></td>
                </tr>
            </table>
            <input type='hidden' name='ocd' value='remind'>
            </form>

                    </td>
                </tr>
            </table>

    </td>
</tr>
<tr>
    <td height='2%' valign='bottom'>

    <table width='100%' border='0' cellspacing='0' cellpadding='15'>
        <tr>
            <td align='center'>
                <span> Powered by &copy; RUNMLM Builder Script, 2004-{CURRENT_YEAR} </span> | <a href="mailto:support@runmlm.com">Email Us</a> | <a target='blank' href='http://runmlm.com/content.php?p_id=8'>Help Guide</a>
            </td>
        </tr>
    </table>
    </td>
</tr>
</table>

</div>
</body>
</html>