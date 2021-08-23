<?php
echo 'Document root: '.$_SERVER['DOCUMENT_ROOT'].'<br>';
echo 'Psysical Path to the script: '.$_SERVER['SCRIPT_FILENAME'].'<br>';
echo 'Script name: '.$_SERVER['SCRIPT_NAME'];

require_once ("./includes/config.php");
require_once ("./includes/xpage_member.php");
require_once ("./includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        XPage::XPage ($object, false);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $message = '
            line1<br>line2<br>
            <b>line3</b>
        ';
        $this->emailHeader .= 'MIME-Version: 1.0' . "\r\n";
        $this->emailHeader .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

        sendMail ('ruslan.sarachan@gmail.com', 'test email', $message, $this->emailHeader);
    }
}
$zPage = new ZPage ("PayPal");

$zPage->RunController ();

?>