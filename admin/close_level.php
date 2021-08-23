<?php

require_once ("../includes/config.php");
require_once ("../includes/xtemplate.php");
require_once ("../includes/xpage_admin.php");
require_once ("../includes/utilities.php");

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
        $member_id = $this->GetGP("member_id", 0);
        $level = $this->GetGP("level", 0);
        
        $content = "<div id='resultik$level'><a href='javascript:open_level($member_id, $level)'><img src='./images/open_down.png' border='0' align='bottom' alt='Show Details' title='Show Details' /></div></td></tr>";
        
        print ($content);
        exit ();
        
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("open_month");

$zPage->Render ();

?>