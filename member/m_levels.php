<?php

require_once ("../includes/config.php");
require_once ("../includes/xtemplate.php");
require_once ("../includes/xpage_member.php");
require_once ("../includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
			GLOBAL $dict;
			
        $this->javaScripts = $this->GetJavaScript ();

        $member_id = $this->member_id;
        
        $name = $this->db->GetOne ("Select CONCAT(first_name, ' ', last_name) From members Where member_id='$member_id'");
        $this->pageTitle = $dict['ML_pageTitle'];
        $this->pageHeader = $dict['MX_pageTitle'] . ' / ' . '<a href="./tree_matrix.php">'.$dict['MX_Tree'].'</a>' ;
        
        $depth = $this->db->GetOne ("Select depth From `matrixes` Where matrix_id=1");
        
        $content = "<table bgcolor='#49586a' cellpadding='4' cellspacing='0' class='b_border' width='100%' style='margin-top:10px;'>";
        
        for ($i = 1; $i <= $depth; $i++)
        {
            $count_level = find_number_members_level ($member_id, $i);
            
            if ($count_level == 0) break;
            
            $content .= "<tr><td align='center' class='b_border'><span class='signs_s'>Level $i : </span> <span class='signs'>$count_level {$dict['ML_members']} </span></td></tr>";
            $content .= "<tr><td class='b_border' align='center'><div id='resultik$i'><a href='javascript:open_level($member_id, $i)'><img src='./images/open_down.gif' border='0' align='bottom' alt='{$dict['ML_ShowDetails']}' /></div></td></tr>";

        }
        if ($i == 1) $content .= "<tr><td class='b_border' align='center'><span class='signs_s'>{$dict['ML_Nolevels']}</span></td></tr>"; 
        $content .= "</table>";
        
        $this->mainTemplate = "./templates/m_levels.tpl";

        $matrix = "<a href='matrix.php'><img alt='{$dict['ML_ShowMatrixTree']}' title='{$dict['ML_ShowMatrixTree']}' src='./images/matrix_icon.png' valign='middle' border='0'></a>";

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_CONTENT" => $content,
            "MAIN_NAME" => $name,
            "MAIN_MATRIX" => $matrix, 
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