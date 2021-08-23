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
        $month = $this->GetGP("month", 0);
        $year = $this->GetGP("year", 0);
        if ($month > 0 And $year > 0)
        {
            $result = "<a href='javascript:open_month($month, $year)'><img src='./images/open_down.png' border='0' align='bottom' title='Show Details' /></a>";
            print $result;
            exit ();
        }

        $country = $this->GetGP("country", "");
        $startdate = $this->GetGP("startdate", 0);
        $finishdate = $this->GetGP("finishdate", 0);
        if ($country != "" And $startdate > 0 And $finishdate > 0)
        {
            $result = "<a href='javascript:open_country($country, $startdate, $finishdate)'><img src='./images/open_down.png' border='0' align='bottom' title='Hide details' /></a>";
            print $result;
            exit ();
        }
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("close");

$zPage->Render ();

?>