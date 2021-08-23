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
        $table = $this->GetGP("table", "");
        $field = $this->GetGP("field", "");
        $id = $this->GetGP("id", 0);
        if ($table != "" And $field != "" And $id != 0)
        {
            $this->db->ExecuteSql ("Update `$table` Set ip_check=0 Where $field=$id");
            echo "&nbsp;";
        }
        exit ();
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("open_month");

$zPage->Render ();

?>