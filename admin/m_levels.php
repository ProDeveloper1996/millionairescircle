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
        XPage::XPage ($object);
        $this->pageTitle = "Levels";
        $this->pageHeader = "Levels";
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->javaScripts = $this->GetJavaScript ();

        $current_member_id = $this->GetGP ("c_m_id", 0);
        
        $name = $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From members Where member_id='$current_member_id'");
        $depth = $this->db->GetOne ("Select depth From `matrixes` Where matrix_id=1");
        
        $content = "<table bgcolor='#E6E6FA' cellpadding='4' cellspacing='0' class='w_border' width='100%'>";
        
        for ($i = 1; $i <= $depth; $i++)
        {
            $count_level = find_number_members_level ($current_member_id, $i);
            
            if ($count_level == 0) break;
            
            $content .= "<tr><td align='center' class='w_border'><span class='signs_s'>Level $i : </span> <span class='signs'>$count_level members </span></td></tr>";
            $content .= "<tr><td class='w_border' align='center'><div id='resultik$i'><a href='javascript:open_level($current_member_id, $i)'><img src='./images/open_down.png' border='0' align='bottom' alt='Show Details' title='Show Details' /></div></td></tr>";

        }
        if ($i == 1) $content .= "<tr><td class='w_border' align='center'><span class='signs_s'>No levels</span></td></tr>"; 
        $content .= "</table>";
        
        $this->mainTemplate = "./templates/m_levels.tpl";

        $matrix = "<a href='forced_matrix.php?id=$current_member_id'><img alt='See Matrix' title='See Matrix' src='./images/matrix_icon.png' valign='middle' border='0'></a>";

        $back = ($current_member_id == 1)? "" : "<a href='m_levels.php?c_m_id=1'><img alt='Back To the First Member' title='Back To the First Member' src='./images/icon_up.png' valign='middle' border='0' class='img_w_board_c'></a>";
        
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_CONTENT" => $content,
            "MAIN_NAME" => $name,
            "MAIN_MATRIX" => $matrix, 
            "MAIN_BACK" => $back,
        );
    }
    
    //--------------------------------------------------------------------------
    function GetJavaScript ()
    {
        return <<<_ENDOFJS_
        <script language='JavaScript' src='../js/is_active.js'></script>
_ENDOFJS_;
    }
    
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("memb_matrix");

$zPage->Render ();

?>