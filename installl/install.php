<?php

require_once ("../includes/utilities.php");
require_once ("../includes/dumper.php");

// Determine path to php binaries and ability to run shell commands
$phpPath = "";
$result = array ();
exec ("whereis php", $result, $retval);
if ($retval == 0)
{
    $execSupport = "<span style='color: #00AA00'>Success</span>";
    foreach ($result as $line)
    {
        $paths = split (" ", $line);
        foreach ($paths as $path)
        {
            if (strstr ($path, "bin")) {
                $phpPath = $path;
                break;
            }
        }
    }
}
else {
    $execSupport = "<span style='color: #AA0000'>Not supported</span>";
}
// libraries support

if (getParam ("ocd") == "install") {

    $dbhost = getParam ("dbhost", "");
    if ($dbhost == "") {
        $dbhost_error = "You should specify 'Hostname'.";
    }

    $dbname = getParam ("dbname", "");
    if ($dbname == "") {
        $dbname_error = "You should specify 'Database Name'.";
    }

    $dbuser = getParam ("dbuser", "");
    if ($dbuser == "") {
        $dbuser_error = "You should specify 'Database Username'.";
    }

    $dbpasswd = getParam ("dbpasswd", "");
    if ($dbpasswd == "") {
        $dbpasswd_error = "You should specify 'Database Password'.";
    }

    if ($dbhost_error != "" Or $dbname_error != "" Or $dbuser_error != "" Or $dbpasswd_error != "") {
    $output = $checkInfo = "";
    $output .= "<form method='POST'><table width='100%' border='0' cellspacing='1' cellpadding='0'>
            <tr><td height='5' colspan='2'></td></tr>
            <tr>
                <td colspan='2' align='center'><h3>Database Configuration</h3></td>
            </tr>
            <tr><td height='10' colspan='2'></td></tr>
            <tr>
                <td width='50%' align=right>Database Server Hostname: &nbsp;</td>
                <td width='50%'><input type='text' name='dbhost' value='$dbhost'></td>
            </tr>
            <tr>
                <td></td>
                <td><span class='error'>$dbhost_error</span></td>
            </tr>
            <tr>
                <td width='50%' align='right'>Your Database Name: &nbsp;</td>
                <td width='50%'><input type='text' name='dbname' value='$dbname'></td>
            </tr>
            <tr>
                <td></td>
                <td><span class='error'>$dbname_error</span></td>
            </tr>
            <tr>
                <td width='50%' align='right'>Database Username: &nbsp;</td>
                <td width='50%'><input type='text' name='dbuser' value='$dbuser'></td>
            </tr>
            <tr>
                <td></td>
                <td><span class='error'>$dbuser_error</span></td>
            </tr>
            <tr>
                <td width='50%' align='right'>Database Password: &nbsp;</td>
                <td width='50%'><input type='password' name='dbpasswd' value='$dbpasswd'></td>
            </tr>
            <tr>
                <td></td>
                <td><span class='error'>$dbpasswd_error</span></td>
            </tr>
            <tr><td height='10' colspan='2'></td></tr>
            <tr>
                <td colspan='2' align='center'><input type='submit' class='some_btn' value=' INSTALL '></td>
            </tr>
            <tr><td height='30' colspan='2'></td></tr>
        </table>
        <input type='hidden' name='ocd' value='install'>
        </form>";

    $checkInfo = "<table width='100%' border='0' cellspacing='0' cellpadding='2' align='center'>
        <tr><td colspan='3' align='center'><H3>Server software compatibility</H3></td></tr>
        <tr><td height='10' colspan='3'></td></tr>
        <tr>
            <td nowrap><b style='font-size:10px;'>Ability to run shell commands from PHP : </b> &nbsp;</td>
            <td> $execSupport &nbsp; </td>
            <td> &nbsp; </td>
        </tr>
        <tr><td height='30' colspan='3'></td></tr></table>";

    }
    else {
        $db_exist = checkDBAccess ($dbhost, $dbname, $dbuser, $dbpasswd);

        if ($db_exist != "") {
            $message = "<span class='error'>Some problem appeared with db connection: $db_exist <br>Please make sure that entered datas are valid.</span>";

            $output = $checkInfo = "";
            $output .= "<form method='POST'><table width='100%' border='0' cellspacing='1' cellpadding='0'>
            <tr><td height='5' colspan='2'></td></tr>
            <tr><td colspan='2' align='center'>$message</td></tr>
            <tr><td height='5' colspan='2'></td></tr>
            <tr>
                <td colspan='2' align='center'><h3>Database Configuration</h3></td>
            </tr>
            <tr><td height='10' colspan='2'></td></tr>
            <tr>
                <td width='50%' align=right>Database Server Hostname: &nbsp;</td>
                <td width='50%'><input type='text' name='dbhost' value='$dbhost'></td>
            </tr>
            <tr>
                <td></td>
                <td><span class='error'>$dbhost_error</span></td>
            </tr>
            <tr>
                <td width='50%' align='right'>Your Database Name: &nbsp;</td>
                <td width='50%'><input type='text' name='dbname' value='$dbname'></td>
            </tr>
            <tr>
                <td></td>
                <td><span class='error'>$dbname_error</span></td>
            </tr>
            <tr>
                <td width='50%' align='right'>Database Username: &nbsp;</td>
                <td width='50%'><input type='text' name='dbuser' value='$dbuser'></td>
            </tr>
            <tr>
                <td></td>
                <td><span class='error'>$dbuser_error</span></td>
            </tr>
            <tr>
                <td width='50%' align='right'>Database Password: &nbsp;</td>
                <td width='50%'><input type='password' name='dbpasswd' value='$dbpasswd'></td>
            </tr>
            <tr>
                <td></td>
                <td><span class='error'>$dbpasswd_error</span></td>
            </tr>
            <tr>
                <td colspan='2' align='center'><input type='submit' class='some_btn' value=' Install '></td>
            </tr>
            <tr><td height='30' colspan='2'></td></tr>
            </table>
            <input type='hidden' name='ocd' value='install'>
            </form>";

            $checkInfo = "<table width='100%' border='0' cellspacing='0' cellpadding='2' align='center'>
            <tr><td colspan='3' align='center'><H3>Server software compatibility</H3></td></tr>
            <tr><td height='10' colspan='3'></td></tr>
            <tr>
                <td nowrap><b style='font-size:10px;'>Ability to run shell commands from PHP : </b> &nbsp;</td>
                <td> $execSupport &nbsp; </td>
                <td> &nbsp; </td>
            </tr>
            <tr><td height='30' colspan='3'></td></tr></table>";

        }
        else {
            $config_file = "../includes/config.php";
            set_chmod_for_install ($config_file);

            $contents = "";
            $handle = fopen ($config_file, "w");
            $contents = fread ($handle, 4096);

$contents = '<?php

define ("DbHost", "'.$dbhost.'");
define ("DbName", "'.$dbname.'");
define ("DbUserName", "'.$dbuser.'");
define ("DbUserPwd", "'.$dbpasswd.'");

define ("LANG", "en");

?>';
            fwrite ($handle, $contents);
            fclose ($handle);
            $message = "<b><span class='message'>Config file for your copy of Builder was successfully installed.</span></b>";


            $connect = mysql_connect ($dbhost, $dbuser, $dbpasswd);
            mysql_select_db ($dbname);

            $sitePath = substr ($_SERVER["SCRIPT_FILENAME"], 0, -19);
            $dumper = new Dumper ($dbname, $sitePath."db/");

            $file_name = $dumper->restore ("mlmBuilder.sql.gz");
            if ($file_name != "") {
                $message .= "<br><span class='message'>Database was successfully installed.</span>";
                $message .= "<br><span class='message'>Please don't forget to delete `Install` folder.</span>";
            }


            $domain_name = $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"];

            $domain_name = pathinfo ($domain_name);
            $domain_name = $domain_name["dirname"]; 
            $domain_name = substr ($domain_name, 0, -8);

            $siteUrl = getAbsoluteLink ($domain_name) . "/";
            $sql1 = "Update `settings` Set value='$siteUrl' Where keyname='SiteURL'";

            mysql_query($sql1, $connect);

            // Set path to site
            $sitePath = substr ($_SERVER["SCRIPT_FILENAME"], 0, -19);
            $sql2 = "Update `settings` Set value='$sitePath' Where keyname='PathSite'";
            mysql_query($sql2, $connect);
            
            $sql3 = "Update `members` Set reg_date=UNIX_TIMESTAMP() Where member_id='1'";
            mysql_query($sql3, $connect);

            mysql_close ($connect);

            $output = $checkInfo = "";
            $output .= "<table width='100%' border='0' cellspacing='3' cellpadding='0'>
                    <tr><td height='5'></td></tr>
                    <tr><td align='center'> $message </td></tr>
                    <tr><td height='5'></td></tr>
                    <tr>
                        <td align='center'><input type='button' class='button' value=' Finish ' onClick=\"window.location.href='../admin/activation.php'\"></td>
                    </tr>
                    <tr><td height='30'></td></tr></table>";
        }
    }
}
else {
    $output = "";
    $output .= "<form method='POST'><table width='100%' border='0' cellspacing='3' cellpadding='0'>
            <tr><td height='5' colspan='2'></td></tr>
            <tr>
                <td colspan='2' align='center'><h3>Database Configuration</h3></td>
            </tr>
            <tr><td height='10' colspan='2'></td></tr>
            <tr>
                <td width='50%' align=right>Database Server Hostname: &nbsp;</td>
                <td width='50%'><input type='text' name='dbhost' value='localhost'></td>
            </tr>
            <tr>
                <td width='50%' align='right'>Your Database Name: &nbsp;</td>
                <td width='50%'><input type='text' name='dbname' value=''></td>
            </tr>
            <tr>
                <td width='50%' align='right'>Database Username: &nbsp;</td>
                <td width='50%'><input type='text' name='dbuser' value=''></td>
            </tr>
            <tr>
                <td width='50%' align='right'>Database Password: &nbsp;</td>
                <td width='50%'><input type='password' name='dbpasswd' value=''></td>
            </tr>
            <tr><td height='10' colspan='2'></td></tr>
            <tr>
                <td colspan='2' align='center'><input type='submit' class='some_btn' value=' INSTALL '></td>
            </tr>
            <tr><td height='30' colspan='2'></td></tr>
        </table>
        <input type='hidden' name='ocd' value='install'>
        </form>";

    $checkInfo = "";
    $checkInfo = "<table width='100%' border='0' cellspacing='0' cellpadding='1' align='center'>
        <tr><td colspan='3' align='center'><H3>Server software compatibility</H3></td></tr>
        <tr><td height='10' colspan='3'></td></tr>
        <tr>
            <td nowrap>Ability to run shell commands from PHP :</td>
            <td> $execSupport &nbsp; </td>
            <td> &nbsp; </td>
        </tr>
        <tr><td height='30' colspan='3'></td></tr></table>";
}

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title>MLM Builder : MLM Builder Installation</title>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO 8859-1">
    <meta http-equiv="content-language" content="en">
    <link href="../admin/templates/styles.css" type="text/css" rel="stylesheet" />
</head>

<body>


<table width='100%' height='100%' cellpadding="0" cellspacing="0" border='0' align='center'>
    <tr>
        <td style='vertical-align:middle;'>


            <table width="400" cellpadding="0" cellspacing="0" border='0' align='center'>
                <tr>
                    <td background="./admin/images/logo_login.gif" class='lr_border' align='right'>
                        <span class='signs_b' style='color:#ffffff;'>Installation Wizard</span> &nbsp;
                    </td>
                </tr>
                <tr style='height:60px;'>
                    <td class='lrb_border' bgcolor='#e7e7e7' style='padding:5px;'>
                        <table style='width:100%;' border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    Before clicking on `Install` button please make sure:
                                    <ul>
                                    <li>You created the database.
                                    <li>The 'data' folder has `777` access permission level (full rights). 
                                    <li>The './includes/config.php' file has `777` access permission level (full rights).
                                    </ul> 
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php print ($checkInfo); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php print ($output); ?>
                                </td>
                            </tr>
                          </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>


</body>
</html>