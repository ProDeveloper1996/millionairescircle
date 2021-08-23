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
        $this->orderDefault = "category_id";
        XPage::XPage ($object);
        
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->javaScripts = $this->GetJavaScript ();
        $this->mainTemplate = "./templates/categories.tpl";
        $this->pageTitle = "E-Shop Categories";
        $this->pageHeader = "E-Shop Categories";

        $total = $this->db->GetOne ("Select Count(*) From {$this->object}");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ADDLINK" => "<a href='{$this->pageUrl}?ocd=new' title='Add a category'><img src='./images/add.png' border='0' /></a>",
            
            "HEAD_ID" => $this->Header_GetSortLink ("category_id", "ID"),
            "HEAD_TITLE" => $this->Header_GetSortLink ("title", "Title"),
            "HEAD_LEVEL" => $this->Header_GetSortLink ("m_level", "Member Level"),
            
        );

        $bgcolor = "";
        if ($total > 0)
        {
            
            if ($this->orderBy == "") $this->orderBy = "category_id";
            
            $result = $this->db->ExecuteSql ("Select * From `{$this->object}` Order By {$this->orderBy} {$this->orderDir}");
            while ($row = $this->db->FetchInArray ($result))
            {
                $category_id = $row['category_id'];
                $title = $this->dec ($row['title']);
                $m_level = $row['m_level'];
                
                $m_level = ($m_level != "")? $this->selectLevels ($m_level) : "Not defined";
                
                $activeLink = "<a href='javascript:is_active(\"".$this->object."\", \"category_id\", ".$category_id.")'><img src='./images/active".$row['is_active'].".png' width='25' border='0' alt='Change activity status' title='Change activity status' /></a>";
                $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$category_id'><img src='./images/edit.png' width='25' border='0' alt='Edit' title='Edit' /></a>";
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$category_id' onClick=\"return confirm ('Do you really want to delete this category?');\"><img src='./images/trash.png' width='25' border='0' alt='Delete' title='Delete' /></a>";
                
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_ID" => $category_id,
                    "ROW_TITLE" => $title,
                    "ROW_LEVEL" => $m_level,
                    "ROW_ACTIVELINK" => $activeLink,
                    "ROW_DELLINK" => $delLink,
                    "ROW_EDITLINK" => $editLink,
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
    
    
    //--------------------------------------------------------------------------
    function fill_form ($opCode = "insert", $source = FORM_EMPTY)
    {
        $this->mainTemplate = "./templates/category_details.tpl";
        $id = $this->GetGP ("id");
        
        $count_levels = $this->db->GetOne ("Select Count(*) From `types`", 0);

        $arrLevels = array ();

        switch ($source)
        {
            case FORM_FROM_DB:

                $row = $this->db->GetEntry ("Select * From {$this->object} Where category_id='$id'", $this->pageUrl);
                $title = "<input type='text' name='title' value='".$row["title"]."' maxlength='250' style='width: 500px;'>";
                //$description = "<textarea style='width: 500px; height: 200px;' name='description'>".$row["description"]."</textarea>";
                $description = $this->FCKeditor( "description", htmlspecialchars_decode( $row["description"] ) );
                
                $arrLevels = explode (";", $row["m_level"]);
                

            break;

            case FORM_FROM_GP:

                
                $title = "<input type='text' name='title' value='".$this->GetGP ("title")."' maxlength='250' style='width: 500px;'>";
                //$description = "<textarea style='width: 500px; height: 200px;' name='description'>".$this->GetGP ("description")."</textarea>";
                $description = $this->FCKeditor( "description", htmlspecialchars_decode( $this->GetGP ("description") ) );
                
                for ($i = 1; $i <= $count_levels; $i++)
                {
                    $k = $this->GetGP ("level$i", 0);
                    $arrLevels [] = $k;    
                }
                
                
            break;
            case FORM_EMPTY:
            default:
                
                $title = "<input type='text' name='title' value='' maxlength='250' style='width: 500px;'>";
                //$description = "<textarea style='width: 500px; height: 200px;' name='description'></textarea>";
                $description = $this->FCKeditor( "description", '' );
                
                for ($i = 1; $i <= $count_levels; $i++)
                {
                    $arrLevels [] = 0;    
                }
                
                break;
        }
        
        $m_level = $this->getMLevel ($arrLevels);

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            
            "MAIN_TITLE" => $title,
            "MAIN_TITLE_ERROR" => $this->GetError ("title"),
            
            "MAIN_DESCRIPTION" => $description,
            "MAIN_DESCRIPTION_ERROR" => $this->GetError ("description"),
            
            "MAIN_LEVEL" => $m_level,
            
            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_ID" => $id,
            "MAIN_OCD" => $opCode,
        );
    }
    
    //--------------------------------------------------------------------------
    function selectLevels ($string)
    {
         $arrLevels = explode (";", $string);
         $toRet = "";
         foreach ($arrLevels as $i=>$type_id)
         {
            $title = $this->db->GetOne ("Select title From `types` Where order_index='$type_id'", "");
            $toRet .= $title.", ";
         }
         
         if ($toRet != "") $toRet = substr ($toRet, 0, -2);
         
         return  $toRet;
         
    }
    
    //--------------------------------------------------------------------------
    function getMLevel ($arrLevels)
    {
        $count_levels = $this->db->GetOne ("Select Count(*) From `types`", 0);
        $toRet = "<table border='0' cellspacing='0' cellpadding='2'>";
        for ($i = 1; $i <= $count_levels; $i++)
        {
            $title = $this->db->GetOne ("Select title From `types` Where order_index='$i'", ""); 
            
            $checked = (in_array ($i, $arrLevels))? "checked" : "";
            
            $toRet .= "<tr><td>".$title . "</td><td><input type='checkbox' name='level$i' value='1' $checked> </td></tr>";
        }
        
        $toRet .= "</table>"; 
        return $toRet;
    }
    
    //--------------------------------------------------------------------------
    function ocd_new ()
    {
        $this->pageTitle = "E-Shop Categories";
        $this->pageHeader = "<a href='{$this->pageUrl}'>E-Shop Categories</a> / New Category";
        $this->fill_form ("insert", FORM_EMPTY);
    }
    
    //--------------------------------------------------------------------------
    function ocd_insert ()
    {
        $this->pageTitle = "E-Shop Categories";
        $this->pageHeader = "<a href='{$this->pageUrl}'>E-Shop Categories</a> / New Category";

        $title = $this->enc ($this->GetValidGP ("title", "Title", VALIDATE_NOT_EMPTY));
        $description = $this->enc ($this->GetValidGP ("description", "Description", VALIDATE_NOT_EMPTY));
        
        
        
        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("insert", FORM_FROM_GP);
        }
        else
        {
            $m_level = "";
            
            $count_levels = $this->db->GetOne ("Select Count(*) From `types`", 0);
            for ($i = 0; $i <= $count_levels - 1; $i++)
            {
                $d = $i + 1;
                $res = $this->GetGP ("level$d", 0);
                if ($res == 1) $m_level .= $d.";";    
                        
            }
            if ($m_level != "") $m_level = substr ($m_level, 0, -1);
            
            $this->db->ExecuteSql ("Insert into {$this->object} (title, description, m_level, is_active) values ('$title', '$description', '$m_level', '0')");
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_edit ()
    {
        $this->pageTitle = "E-Shop Categories";
        $this->pageHeader = "<a href='{$this->pageUrl}'>E-Shop Categories</a> / Edit Category";
        $this->fill_form ("update", FORM_FROM_DB);
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        $this->pageTitle = "E-Shop Categories";
        $this->pageHeader = "<a href='{$this->pageUrl}'>E-Shop Categories</a> / Edit Category";
        $id = $this->GetGP ("id");
        
        $title = $this->enc ($this->GetValidGP ("title", "Title", VALIDATE_NOT_EMPTY));
        $description = $this->enc ($this->GetValidGP ("description", "Description", VALIDATE_NOT_EMPTY));

        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("update", FORM_FROM_GP);
        }
        else
        {
            $m_level = "";
            
            $count_levels = $this->db->GetOne ("Select Count(*) From `types`", 0);
            for ($i = 0; $i <= $count_levels - 1; $i++)
            {
                $d = $i + 1;
                $res = $this->GetGP ("level$d", 0);
                if ($res == 1) $m_level .= $d.";";    
                        
            }
            if ($m_level != "") $m_level = substr ($m_level, 0, -1);
            
            $this->db->ExecuteSql ("Update {$this->object} Set `title`='$title', `m_level`='$m_level', `description`='$description' Where `category_id`='$id'");
            $this->Redirect ($this->pageUrl);
        }
    }
    
    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id", 0);
        $this->db->ExecuteSql ("Delete From {$this->object} Where `category_id`='$id'");
        $this->Redirect ($this->pageUrl);
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

$zPage = new ZPage ("categories");

$zPage->Render ();

?>