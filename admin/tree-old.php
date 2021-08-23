<?php

require_once ("../includes/config.php");
require_once ("../includes/xtemplate.php");
require_once ("../includes/xpage_admin.php");
require_once ("../includes/utilities.php");

//--------------------------------------------------------------------------
function getResultTree ($member_id)
{
		global $db;
		$toRet = "<div class='tree_content'>";
		if ($member_id == 0) return $toRet;
		$username =  $db->GetOne ("Select `username` From `members` Where `member_id`='$member_id'", "");
		$div = "<div class='closed' id='id_".$member_id."' onclick='closeid ($member_id)'></div>";
		$toRet .= "<ul>\r\n<li>".$div.$username;
		$toRet  .= getOneTree ($member_id, true)."\r\n</li>\r\n</ul></div>";
		return $toRet;
}

//--------------------------------------------------------------------------
function getOneTree ($member_id, $last = false)
{
	global $db;

		$total =  $db->GetOne ("Select COUNT(*) From `members` Where `enroller_id`='$member_id' And `m_level`>0 And `is_active`=1 And `is_dead`=0", 0);
		$toRet = ($last)? "<ul style='display:none;' id='$member_id'>" : "<ul id='$member_id' class='yes' style='display:none;'>";

        $result = $db->ExecuteSql ("Select * From `members` Where `enroller_id`='$member_id' And `m_level`>0 And `is_active`=1 And `is_dead`=0");
		$k = 0;
		while ($row = $db->FetchInArray ($result))
		{
			$k++;
			$c_member_id = $row ["member_id"];
			$c_username = $row ["username"];
			$count =  $db->GetOne ("Select COUNT(*) From `members` Where `enroller_id`='$c_member_id' And `m_level`>0 And `is_active`=1 And `is_dead`=0", 0);

			if ($count > 0)
			{
				$div = "<div class='closed' id='id_".$c_member_id."' onclick='closeid ($c_member_id)'></div>";
			}
			else
			{
				$div = ($k == $total)? "<div class='ugol'></div>" : "<div class='ugol2'></div>";
			}
			$toRet .= "<li>".$div.$c_member_id." : ".$c_username;
			if ($count > 0)
			{
				$last = ($k == $total)? true : false;
				$toRet .= getOneTree ($c_member_id, $last);
			}
			$toRet .= "</li>\r\n";
    	}
    	$toRet .= "\r\n</ul>\r\n";
    	$db->FreeSqlResult ($result);

	return $toRet;
}

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
        $this->javaScripts = $this->GetJavaScript ();

        $this->mainTemplate = "./templates/tree.tpl";
        $this->pageTitle = "Overall Tree";
        $this->pageHeader = "Overall Tree";
        
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "RESULT" => getResultTree (1),
        );
    }
    
    //--------------------------------------------------------------------------
    function GetJavaScript ()
    {
        return <<<_ENDOFJS_
        <script language='JavaScript' charset='windows-1251' type='text/javascript' src='/js/jquery-1.7.1.min.js'></script>
		<script>
			function expand ()
			{
			    var object = "#result ul";
			    $(object).show();
			    	
			    $('.closed').removeClass("closed").addClass("opened");
			}
		</script>
_ENDOFJS_;
    }
    
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("tree");

$zPage->Render ();

?>