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
    function ocd_list()
    {
        GLOBAL $dict;
		  $this->javaScripts = $this->GetJavaScript ();
        
        $member_id = $this->member_id;
        $this->mainTemplate = "./templates/pages.tpl";
        $this->pageTitle = $dict['RS_pageTitle'];
        $this->pageHeader = $dict['RS_pageTitle'];

        $row = $this->db->GetEntry("Select replica,is_replica,is_a_replica From `members` Where member_id='$member_id'");
        if ($row['replica'] == "") {
	        $this->mainTemplate = "./templates/pages_start.tpl";

	        $siteUrl = $this->db->GetSetting ("SiteUrl");

	        $is_replica = ($row['is_replica'] == 1)? "checked" : "";
	        $is_replica = "<input type='checkbox' name='is_replica' value='1' $is_replica>";
	        
	        $is_a_replica = ($row['is_a_replica'] == 1)? $dict['RS_Authorized'] : $dict['RS_NotAuthorized'];
	        
	        $replica = $row['replica'];
	        $replica = "<input type='text' name='replica' value='$replica' style='width:100px;' maxlength='15'>";

	        $this->data = array (
	            "MAIN_HEADER" => $this->pageHeader,
                "MAIN_ACTION" => $this->pageUrl,
	            "ACCOUNT_IS_REPLICA" => $is_replica,
	            "ACCOUNT_IS_A_REPLICA" => $is_a_replica,
	            "ACCOUNT_REPLICA_URL" => $replica,
	            "ACCOUNT_SITE_URL" => $siteUrl,
	        );
        	
        }
        else{
	        $is_replica = ($row['is_replica'] == 1)? "checked" : "";
	        $is_replica = "<input type='checkbox' name='is_replica' value='1' $is_replica>";

	        $quant_replica = $this->db->GetSetting ("quant_replica", 0);
	        $total = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where member_id='$member_id'");
	        
	        $siteUrl = $this->db->GetSetting ("siteUrl");
	        $replica = $this->db->GetOne ("Select replica From `members` Where member_id='$member_id'", "");
	        $replica = ($replica != "")? $siteUrl."$replica"."/" : "";
            //$link_to_site = ($replica != "")? "<span class='question'>{$dict['RS_mess1']}</span><a href='$replica' class='smallLink' target='_blank'>$replica</a>" : $dict['RS_mess2'];  
            $link_to_site = ($replica != "")? "<a href='$replica' class='smallLink' target='_blank'>$replica</a>" : $dict['RS_mess2'];  
	        
	        $this->data = array (
	            "MAIN_HEADER" => $this->pageHeader,
	            "MAIN_ADDLINK" => ($total < $quant_replica)? "<a href='{$this->pageUrl}?ocd=new' title='{$dict['RS_add']}'><img src='./images/add.png' border='0'></a>" : "<img src='./images/add.png' border='0' alt='{$dict['RS_mess3']}'>",
	            "HEAD_ORDER" => $dict['RS_Order'],
	            "HEAD_NAME" => $dict['RS_Name'],
	            "HEAD_TITLE" => $dict['RS_Title'],
                "REPLICA" => $link_to_site,

	            "ACCOUNT_IS_REPLICA" => $is_replica,
	        );
	
	        $bgcolor = "";
	        if ($total > 0)
	        {
	            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Where member_id='$member_id' Order By order_index Asc");
	            while ($row = $this->db->FetchInArray ($result))
	            {
	                $id = $row['replica_id'];
	                $p_order = $row['order_index'];
	                $title = $row['title'];
	                $menu_title = $row['menu_title'];
	                if ($total == 1)
	                {
	                    $orderLink = "&nbsp;";    
	                }
	                elseif ($p_order == $total)
	                {
	                    $orderLink = "<a href='{$this->pageUrl}?ocd=up&id=$id'><img src='./images/arrow_up.png' align='absmiddle' width='12' border='0' alt='{$dict['RS_MoveUp']}' title='{$dict['RS_MoveUp']}' /></a>";
	                }
	                elseif ($p_order == 1)
	                {
	                     $orderLink = "<a href='{$this->pageUrl}?ocd=down&id=$id'><img src='./images/arrow_down.png' align='absmiddle' width='12' border='0' alt='{$dict['RS_MoveDown']}' title='{$dict['RS_MoveDown']}' /></a>";
	                }
	                else
	                {
	                    $orderLink = "<a href='{$this->pageUrl}?ocd=up&id=$id'><img src='./images/arrow_up.png' align='absmiddle' width='12' border='0' alt='{$dict['RS_MoveUp']}' title='{$dict['RS_MoveUp']}' /></a>";
	                    $orderLink .= "<br><a href='{$this->pageUrl}?ocd=down&id=$id'><img src='./images/arrow_down.png' align='absmiddle' width='12' border='0' alt='{$dict['RS_MoveDown']}' title='{$dict['RS_MoveDown']}' /></a>";
	                }
	                $activeLink = "<a href='javascript:is_active(\"".$this->object."\", \"replica_id\", ".$id.")'><img src='./images/active".$row['is_active'].".png' width='24' border='0' alt='{$dict['RS_Changeactivitystatus']}' title='{$dict['RS_Changeactivitystatus']}' /></a>";
	                $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$id'><img src='./images/edit.png' width='24' border='0' alt='{$dict['RS_Edit']}' title='{$dict['RS_Edit']}' /></a>";
	                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('{$dict['RS_Del']}');\"><img src='./images/trash.png' width='24' border='0' alt='{$dict['RS_Delete']}' title='{$dict['RS_Delete']}' /></a>";
	                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
	                $this->data ['TABLE_ROW'][] = array (
	                    "ROW_ORDER" => $p_order,
	                    "ROW_ID" => $id,
	                    "ROW_TITLE" => $title,
	                    "ROW_MENU" => $menu_title,
	                    "ROW_ORDERLINK" => $orderLink,
	                    "ROW_ACTIVELINK" => $activeLink,
	                    "ROW_EDITLINK" => $editLink,
	                    "ROW_DELLINK" => $delLink,
	                    "ROW_BGCOLOR" => $bgcolor,
	                );
	            }
	            $this->db->FreeSqlResult ($result);
	        }
	        else
	        {
	            $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
	            $this->data ['TABLE_EMPTY'][] = array (
	                "ROW_BGCOLOR" => $bgcolor
	            );
	        }
        }

    }

    //--------------------------------------------------------------------------
    function fill_form ($opCode = "insert", $source = FORM_EMPTY)
    {
        $this->mainTemplate = "./templates/page_details.tpl";
        $id = $this->GetGP ("id");
        $this->javaScripts = $this->GetJavaScript ();

        switch ($source)
        {
            case FORM_FROM_DB:

                $row = $this->db->GetEntry ("Select * From {$this->object} Where replica_id='$id'", $this->pageUrl);
                $title = "<input type='text' name='title' value='".$row["title"]."' maxlength='120' style='width: 300px;'>";
                $menu_title = "<input type='text' name='menu_title' value='".$row["menu_title"]."' maxlength='120' style='width: 300px;'>";
                $content = $this->FCKeditor( "content", $this->dec($row["content"]));//"<textarea rows='14' style='width: 450px; height: 340px;' id='content' name='content'>".$row["content"]."</textarea>";

            break;

            case FORM_FROM_GP:

                $title = "<input type='text' name='title' value='".$this->GetGP ("title")."' maxlength='120' style='width: 300px;'>";
                $menu_title = "<input type='text' name='menu_title' value='".$this->GetGP ("menu_title")."' maxlength='120' style='width: 300px;'>";
                $content = $this->FCKeditor( "content", $this->GetGP ("content") );//"<textarea rows='14' style='width: 450px; height: 340px;' id='content' name='content'>".$this->GetGP ("content")."</textarea>";

            break;

            case FORM_EMPTY:
            default:

                $title = "<input type='text' name='title' value='' maxlength='120' style='width: 300px;'>";
                $content = $this->FCKeditor( "content", '' );//"<textarea rows='14' style='width: 450px; height: 340px;' id='content' name='content'></textarea>";
                $menu_title = "<input type='text' name='menu_title' value='' maxlength='120' style='width: 300px;'>";

            break;
        }

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_TITLE" => $title,
            "MAIN_TITLE_ERROR" => $this->GetError ("title"),

            "MAIN_TITLE_MENU" => $menu_title,
            "MAIN_TITLE_MENU_ERROR" => $this->GetError ("menu_title"),

            "MAIN_CONTENT" => $content,

            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_ID" => $id,
            "MAIN_OCD" => $opCode,
        );
    }

    //--------------------------------------------------------------------------
    function ocd_addsite ()
    {
        GLOBAL $dict;
		  $this->pageTitle = $dict['RS_pageTitle1'];
        $this->pageHeader = $dict['RS_pageTitle1'];
//        $this->fill_form ("insert", FORM_EMPTY);

        $member_id = $this->member_id;
        $replica_admin = $this->db->GetSetting ("is_replica", 0);
         //$replica = $this->GetGP ("replica", "");
         $is_replica = $this->GetGP ("is_replica", 0);
     
         if ($replica_admin == 1)
         {
             $replica = $this->enc ($this->GetValidGP ("replica", $dict['RS_UrlofMySite'], VALIDATE_REPLICA));
             if ($this->errors['err_count'] == 0)
             {
                 $count = $this->db->GetOne ("Select Count(*) From `members` Where replica='$replica' And member_id<>'$member_id'", 0);
                 if ($count > 0) $this->SetError ("replica", $dict['RS_error']);
             }
         }
//debug($this->errors);
        if ($this->errors['err_count'] > 0)
        {
	        $this->mainTemplate = "./templates/pages_start.tpl";
            $siteUrl = $this->db->GetSetting ("SiteUrl");
            $is_replica = ($is_replica == 1)? "checked" : ""; 
            $is_a_replica = $this->db->GetOne ("Select is_a_replica From `members` Where member_id='$member_id'", 0);
            $is_a_replica = ($is_a_replica == 1)? $dict['RS_Authorized'] : $dict['RS_NotAuthorized'];
            $this->data = array (
                "MAIN_HEADER" => $this->pageHeader,
                "MAIN_ACTION" => $this->pageUrl,
                
                "ACCOUNT_IS_REPLICA" => "<input type='checkbox' name='is_replica' value='1' $is_replica>",
                "ACCOUNT_IS_A_REPLICA" => $is_a_replica,
                "ACCOUNT_REPLICA_URL" => "<input type='text' name='replica' value='$replica' style='width:100px;' maxlength='15'>",
                "ACCOUNT_REPLICA_ERROR" => $this->GetError ("replica"),
                "ACCOUNT_SITE_URL" => $siteUrl,
            );
        }
        else
        {
            if ($replica_admin == 1) $this->db->ExecuteSql ("Update `members` Set replica='$replica', is_replica='$is_replica', is_a_replica=1 Where member_id='$member_id'"); 
	        $this->ocd_list();
        }


    }

    //--------------------------------------------------------------------------
    function ocd_new ()
    {
        GLOBAL $dict;
		  $this->pageTitle = $dict['RS_pageTitle2'];
        $this->pageHeader = $dict['RS_pageTitle2'];
        $this->fill_form ("insert", FORM_EMPTY);
    }

    //--------------------------------------------------------------------------
    function ocd_insert ()
    {
        GLOBAL $dict;
        $member_id = $this->member_id;
        $quant_replica = $this->db->GetSetting ("quant_replica", 0);
        $total = $this->db->GetOne ("Select Count(*) From `{$this->object}` Where member_id='$member_id'");
        if ($quant_replica == $total) $this->Redirect ($this->pageUrl);
         
        $this->pageTitle = $dict['RS_pageTitle2'];
        $this->pageHeader = $dict['RS_pageTitle2'];
        
        $title = $this->enc ($this->GetValidGP ("title", $dict['RS_err1'], VALIDATE_NOT_EMPTY));
        $menu_title = $this->enc ($this->GetValidGP ("menu_title", $dict['RS_Title'], VALIDATE_NOT_EMPTY));
        $content = $this->enc ($this->GetGP ("content"));

        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("insert", FORM_FROM_GP);
        }
        else
        {
            if ($this->errors['err_count'] > 0)
            {
                $this->fill_form ("insert", FORM_FROM_GP);
            }
            else
            {
                $total = $this->db->GetOne ("Select Count(*) From {$this->object} Where member_id='$member_id'", 0) + 1;
                $this->db->ExecuteSql ("Insert into {$this->object} (title, menu_title, content, order_index, member_id, is_active) values ('$title', '$menu_title', '$content', '$total', '$member_id', 0)");
//                print "Insert into {$this->object} (title, menu_title, content, order_index, member_id, is_active) values ('$title', '$menu_title', '$content', '$total', '$member_id', 0)";
                $this->Redirect ($this->pageUrl);
            }
        }
    }

    //--------------------------------------------------------------------------
    function ocd_edit ()
    {
        GLOBAL $dict;
		  $this->pageTitle = $dict['RS_pageTitle3'];
        $this->pageHeader = $dict['RS_pageTitle3'];
        $this->fill_form ("update", FORM_FROM_DB);
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        GLOBAL $dict;
		  $this->pageTitle = $dict['RS_pageTitle3'];
        $this->pageHeader = $dict['RS_pageTitle3'];
        $id = $this->GetGP ("id");
        $member_id = $this->member_id;
        
        $owner = $this->db->GetOne ("Select member_id From `{$this->object}` Where replica_id='$id'", "");
        
        if ($owner != $member_id) $this-Redirect ($this->pageUrl);
        
        $title = $this->enc ($this->GetValidGP ("title", $dict['RS_err1'], VALIDATE_NOT_EMPTY));
        $menu_title = $this->enc ($this->GetValidGP ("menu_title", $dict['RS_Title'], VALIDATE_NOT_EMPTY));
        $content = $this->enc ($this->GetGP ("content"));

        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("update", FORM_FROM_GP);
        }
        else
        {

            $this->db->ExecuteSql ("Update {$this->object} Set title='$title', menu_title='$menu_title', content='$content' Where replica_id='$id'");
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_updatestatus ()
    {
        GLOBAL $dict;
		  $this->pageTitle = $dict['RS_pageTitle3'];
        $this->pageHeader = $dict['RS_pageTitle3'];
        $member_id = $this->member_id;

	      $this->db->ExecuteSql("Update members Set is_replica='".$this->GetGP('is_replica',0)."' Where member_id='$member_id'");
	      $this->Redirect($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_activate ()
    {
        $id = $this->GetGP ("id", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set is_active=(1-is_active) Where replica_id=$id");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id", 0);
        $member_id = $this->member_id;
        $p_order = $this->db->GetOne ("Select order_index From `{$this->object}` Where replica_id='$id'");
        $this->db->ExecuteSql ("Delete From {$this->object} Where replica_id='$id'");
        $this->db->ExecuteSql ("Update `{$this->object}` Set order_index=order_index-1 Where order_index>'$p_order' And member_id='$member_id'");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_down ()
    {
        $id = $this->GetGP ("id", 0);
        $member_id = $this->member_id;
        $number = $this->db->GetOne ("Select order_index From {$this->object} Where replica_id='$id'", 0);
        $number_next = $number + 1;
        $id_next = $this->db->GetOne ("Select replica_id From {$this->object} Where order_index='$number_next' And member_id='$member_id'", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set order_index=order_index+1 Where replica_id='$id'");
        $this->db->ExecuteSql ("Update {$this->object} Set order_index=order_index-1 Where replica_id='$id_next'");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_up ()
    {
        $id = $this->GetGP ("id", 0);
        $member_id = $this->member_id;
        $number = $this->db->GetOne ("Select order_index From {$this->object} Where replica_id='$id'", 0);
        $number_next = $number - 1;
        $id_next = $this->db->GetOne ("Select replica_id From {$this->object} Where order_index='$number_next' And member_id='$member_id'", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set order_index=order_index-1 Where replica_id='$id'");
        $this->db->ExecuteSql ("Update {$this->object} Set order_index=order_index+1 Where replica_id='$id_next'");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function GetJavaScript ()
    {
        return <<<_ENDOFJS_

        <script language='JavaScript' src='../admin/editor/scripts/innovaeditor.js'></script>
        <script language='JavaScript' src='../js/is_active.js'></script>
        <script>
/*
$(document).ready(function(){
		  	$('#content').show();
});
*/
		  </script>

_ENDOFJS_;
    }

}

//------------------------------------------------------------------------------

$zPage = new ZPage ("replicas");

$zPage->Render ();

?>