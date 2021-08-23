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
            $this->db->ExecuteSql ("Update `$table` Set is_a_replica=(1-is_a_replica) Where $field=$id");
            $is_active = $this->db->GetOne ("Select is_a_replica From `$table` Where $field='$id'", 0);
            echo "<a href='javascript:is_replica(\"".$table."\", \"".$field."\", ".$id.")'><img src='./images/replica".$is_active.".png' width='25' border='0' title='Change replicated site activity status'></a>";
        }
        exit ();
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("replica");

$zPage->Render ();

?>