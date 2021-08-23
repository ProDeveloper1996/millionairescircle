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
    <table width='100%' border='0' cellspacing='0' cellpadding='10'>
        <tr>
            <td align="left"><a target="blank" href="http://www.runmlm.com/"><img src="images/runmlm_logo.png"></a></td>
        </tr>
    </table>
<table width='100%' height='90%' border='0' cellspacing='0' cellpadding='0'>
<tr>
    <td valign='middle'>
             <table width="25%" cellpadding="7" cellspacing="0" border='0' align='center'>
                <tr>
                    <td colspan='2' align='center'><h4>Adminstrator Login</h4></td>
                </tr>
                <tr style='height:60px;'>
                    <td>
                        <form name='login' method='POST' action='{MAIN_ACTION}'>
                        <table width="100%" cellpadding="5" cellspacing="0" border='0' align='center'>
                            <tr><td colspan='2' align='center'><span class='error'>{LOGIN_ERROR}</span></td></tr>
                            <tr>
                                <td colspan='2'>{LOGIN_USERNAME}</td>
                            </tr>
                            <tr><td colspan='2'>{LOGIN_PASSWORD}</td>
                            </tr>
                            <tr>
                                <td colspan='2'><input type='submit' value='Login' class='some_btn'></td>
                            </tr>
                            <tr>
                                <td  colspan='2' align='left'><a style="FONT-SIZE: 10px;" href="forgot_password.php">Forgot Password?</a></td>
                            </tr>
                        </table>
                        <input type='hidden' name='ocd' value='login'>
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
            <td align='center' style="FONT-SIZE: 12px;">Powered by &copy; <a target='blank' href='http://www.runmlm.com'>RUNMLM Builder Script</a>, 2004-{CURRENT_YEAR} | <a href="mailto:support@runmlm.com">Email Us</a> | <a target='blank' href='http://runmlm.com/content.php?p_id=8'>Help Guide</a>
            </td>
        </tr>
    </table>
    </td>
</tr>
</table>

</div>
</body>
</html>