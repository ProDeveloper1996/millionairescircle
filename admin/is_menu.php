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
            $this->db->ExecuteSql ("Update `$table` Set in_menu=(1-in_menu) Where $field=$id");
            $in_menu = $this->db->GetOne ("Select in_menu From `$table` Where $field='$id'", 0);
            echo "<a href='javascript:is_menu(\"".$table."\", \"".$field."\", ".$id.")'><img src='./images/mactive".$in_menu.".png' width='25' border='0' title='Show in menu status'></a>";
        }
        exit ();
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("open_month1");

$zPage->Render ();

?>