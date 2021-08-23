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
        $this->orderDefault = "product_id";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        
        $this->javaScripts = $this->GetJavaScript ();
        $this->mainTemplate = "./templates/products.tpl";
        $this->pageTitle = "Products";
        $this->pageHeader = "Products";
        
        $filter = $this->GetGP ("filter");
        if ($filter == 1)
        {
            $search = $this->GetGP ("search", "");
            $current_category_id = $this->GetGP ("category_id", "");
            $this->SaveStateValue ("search", $search);
            $this->SaveStateValue ("current_category_id", $current_category_id);
        }
        
        $search = $this->GetStateValue ("search", "");
        $current_category_id = $this->GetStateValue ("current_category_id", 0);
        
        $sql = "";
        if ($search != "") $sql .= " And (`title` LIKE '%$search%' Or `description` LIKE '%$search%' ) ";
        if ($current_category_id == -1)  $sql .= " And `category_id`=0 ";
        if ($current_category_id > 0)  $sql .= " And `category_id`='$current_category_id' ";
        
        $total = $this->db->GetOne ("Select Count(*) From {$this->object} Where 1 $sql");
        
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ADDLINK" => "<a href='{$this->pageUrl}?ocd=new' alt='Add a category' title='Add a category'><img src='./images/add.png' border='0' /></a>",
            
            "HEAD_ID" => $this->Header_GetSortLink ("product_id", "ID"),
            "HEAD_TITLE" => $this->Header_GetSortLink ("title", "Title"),
            "HEAD_CATEGORY" => $this->Header_GetSortLink ("category_id", "Category"),
            "HEAD_PRICE" => $this->Header_GetSortLink ("price", "Price"),
            "HEAD_FILE" => $this->Header_GetSortLink ("file", "File"),
            
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
            
            "SEARCH" => $search,
            
            "MAIN_CAT_SELECT" => selectCategoryMain ($current_category_id),
            
        );

        $bgcolor = "";
        if ($total > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From {$this->object} Where 1 $sql Order By {$this->orderBy} {$this->orderDir}", true);
            while ($row = $this->db->FetchInArray ($result))
            {

                $id = $row['product_id'];
                $title = $this->dec ($row['title']);
                
                $category_id = $row['category_id'];
                $price = ($row['price'] > 0)? $row['price'] : "Free";
                
                $category = ($category_id > 0)? $this->dec ($this->db->GetOne ("Select `title` From `categories` Where `category_id`='$category_id'", "")) : "Not assigned"; 
                
                $file = "<a href='../data/pfiles/".$row['file']."' target='_blank'>".$row['file']."</a>&nbsp;";
                
                $activeLink = "<a href='javascript:is_active(\"".$this->object."\", \"product_id\", ".$id.")'><img src='./images/active".$row['is_active'].".png' width='25' border='0' alt='Change activity status' title='Change activity status' /></a>";
                $editLink = "<a href='{$this->pageUrl}?ocd=edit&id=$id'><img src='./images/edit.png' width='25' border='0' title='Edit' alt='Edit' /></a>";
                $delLink = "<a href='{$this->pageUrl}?ocd=del&id=$id' onClick=\"return confirm ('Do you really want to delete this product?');\"><img src='./images/trash.png' width='25' border='0' alt='Delete' title='Delete' /></a>";
                
                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                
                $this->data ['TABLE_ROW'][] = array (
                    
                    "ROW_ID" => $id,
                    "ROW_TITLE" => $title,
                    "ROW_CATEGORY" => $category,
                    "ROW_PRICE" => $price,
                    "ROW_FILE" => $file,
                    
                    "ROW_ACTIVELINK" => $activeLink,
                    "ROW_EDITLINK" => $editLink,
                    "ROW_DELLINK" => $delLink,
                    "ROW_BGCOLOR" => $bgcolor
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
        $this->mainTemplate = "./templates/product_details.tpl";
        $this->javaScripts = $this->GetJavaScript ();
        $id = $this->GetGP ("id");
        switch ($source)
        {
            case FORM_FROM_DB:
                $row = $this->db->GetEntry ("Select * From {$this->object} Where product_id=$id", $this->pageUrl);
                
                $title = $row["title"];
                //$description = $row["description"];
                $description = $this->FCKeditor( "description", htmlspecialchars_decode($row["description"] ) );
                $price = $row["price"];
                $category_id = $row["category_id"];
                
                $photo = "";
                if ($row["photo"] != "" And file_exists ("../data/products/".$row["photo"].".jpg"))
                {
                    $photo = "<a href='../data/products/".$row["photo"].".jpg' target='_blank'><img title='Enlarge' alt='Enlarge' border='0' src='../data/products/small_".$row["photo"].".jpg'></a>&nbsp;";
                    $photo .= "<a href='{$this->pageUrl}?ocd=delphoto&id=$id' onClick=\"return confirm ('Do you really want to delete this image?')\"><img src='./images/trash.png' border='0' alt='Delete' title='Delete' /></a>";

                }
                else
                {
                    $photo = "<input type='file' name='photo' value='' style='width: 320px;'>";
                }
                
                $file = "";
                if ($row["file"] != "" And file_exists ("../data/pfiles/".$row["file"]))
                {
                    $file = "<a href='../data/pfiles/".$row["file"]."' target='_blank'>".$row["file"]."</a>&nbsp;";
                    $file .= "<a href='{$this->pageUrl}?ocd=delfile&id=$id' onClick=\"return confirm ('Do you really want to delete this file?')\"><img src='./images/trash.png' border='0' alt='Delete' title='Delete' /></a>";

                }
                else
                {
                    $file = "<input type='file' name='file' value='' style='width: 320px;'>";
                }

                break;

            case FORM_FROM_GP:
                
                $title = $this->GetGP ("title");
                //$description = $this->GetGP ("description");
                $description = $this->FCKeditor( "description", htmlspecialchars_decode( $this->GetGP ("description") ) );
                $category_id = $this->GetGP ("category_id");
                $price = $this->GetGP ("price");
                
                $photo = "";
                $photo_file = $this->db->GetOne ("Select photo From `{$this->object}` Where product_id=$id", "");
                if ($photo_file != "" And file_exists ("../data/products/".$photo_file.".jpg") And $opCode == "update")
                {
                    $photo = "<a href='../data/products/".$photo_file.".jpg' target='_blank'><img title='Enlarge' alt='Enlarge' border='0' src='../data/products/small_".$photo_file.".jpg'></a>&nbsp;";
                    $photo .= "<a href='{$this->pageUrl}?ocd=delphoto&id=$id' onClick=\"return confirm ('Do you really want to delete this image?')\"><img src='./images/trash.png' border='0' alt='Delete' title='Delete' /></a>";
                }
                else
                {
                    $photo = "<input type='file' name='photo' value='' style='width: 320px;'>";
                }
                
                $file = "";
                $file_file = $this->db->GetOne ("Select file From `{$this->object}` Where product_id=$id", "");
                if ($photo_file != "" And file_exists ("../data/products/".$photo_file.".jpg") And $opCode == "update")
                {
                    $file = "<a href='../data/pfiles/".$photo_file.".jpg' target='_blank'>$photo_file</a>&nbsp;";
                    $file .= "<a href='{$this->pageUrl}?ocd=delfile&id=$id' onClick=\"return confirm ('Do you really want to delete this file?')\"><img src='./images/trash.png' border='0' alt='Delete' title='Delete' /></a>";
                }
                else
                {
                    $file = "<input type='file' name='file' value='' style='width: 320px;'>";
                }
                
                break;

            case FORM_EMPTY:
            default:
                
                $title = "";
                $price = "0.00";
                //$description = "";
                $description = $this->FCKeditor( "description", '' );
                $photo = "<input type='file' name='photo' value='' style='width: 320px;'>";
                
                $category_id = $this->GetStateValue ("current_category_id", 0);
                
                $file = "<input type='file' name='file' value='' style='width: 320px;'>";

                break;
        }

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            
            "MAIN_TITLE" => $title,
            "MAIN_TITLE_ERROR" => $this->GetError ("title"),
            
            "MAIN_DESCRIPTION" => $description,
            
            "MAIN_PRICE" => $price,

            "MAIN_PHOTO" => $photo,
            "MAIN_FILE" => $file,
            
            "MAIN_CATEGORY" => selectCategory ($category_id),
            
            "MAIN_CANCEL_URL" => $this->pageUrl,
            "MAIN_OCD" => $opCode,
            "MAIN_ID" => $id,
        );
    }
    
    //--------------------------------------------------------------------------
    function ocd_new ()
    {
        $this->pageTitle = "Products";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Products</a> / New Product";
        $this->fill_form ("insert", FORM_EMPTY);
    }

    //--------------------------------------------------------------------------
    function ocd_insert ()
    {
        
        $this->pageTitle = "Products";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Products</a> / New Product";
        
        $title = $this->enc ($this->GetValidGP ("title", "Title", VALIDATE_NOT_EMPTY));
        $price = $this->enc ($this->GetValidGP ("price", "Price", VALIDATE_FLOAT_POSITIVE));
        $description = $this->enc ($this->GetGP ("description"));
        $category_id = $this->enc ($this->GetGP ("category_id"));
        
        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("insert", FORM_FROM_GP);
        }
        else
        {
            
            $this->SaveStateValue ("current_category_id", $category_id);
            
            $this->db->ExecuteSql ("Insert into {$this->object} (title, description, price, category_id, is_active) Values ('$title', '$description', '$price', '$category_id', '0')");
            $id = $this->db->GetInsertID ();
            
            if (array_key_exists ("photo", $_FILES) and $_FILES['photo']['error'] < 3)
            {
                $symbs = getUnID (5);
                $oldname = $_FILES['photo']['name'];
                $tmp_name = $_FILES['photo']['tmp_name'];
                $short_name = $id."_".$symbs;
                
                $types = $_FILES['photo']['type'];
                $types_array = explode("/", $types);
                if ( $types_array [0] != "image" || strpos($_FILES['photo']['name'],'php')!==false ) {
                    $this->Redirect ($this->pageUrl);
                    exit();
                }
                $ext = getExtension ($oldname, "jpg");
                $whitelist = array("jpg","jpeg","gif","png");

                if (is_uploaded_file ($tmp_name) && in_array($ext, $whitelist) )
                {
                    $symbs = getUnID (5);
                    $oldname = $_FILES['photo']['name'];
                    $tmp_name = $_FILES['photo']['tmp_name'];
                    $short_name = $id."_".$symbs;
                    $new_name = $short_name;
                    $thumb_name = "small_".$new_name;
                    if (is_uploaded_file ($tmp_name))
                    {
                        $physical_path = $this->db->GetSetting ("PathSite");
                        move_uploaded_file ($tmp_name, $physical_path."data/products/".$new_name.".jpg");
                        $cmd = "chmod 666 ".$physical_path."data/products/".$new_name.".jpg";
                        @exec ($cmd, $output, $retval);
                        @chmod ($physical_path."data/products/".$new_name, 0644);
                        copy ($physical_path."data/products/".$new_name.".jpg", $physical_path."data/products/".$thumb_name.".jpg");
                        $cmd = "chmod 666 ".$physical_path."data/products/".$thumb_name.".jpg";
                        @exec ($cmd, $output, $retval);
                        @chmod ($physical_path."data/products/".$thumb_name.".jpg", 0644);
                        makeThumbnail ($physical_path."data/products/".$thumb_name.".jpg", 0);
                        makeThumbnail ($physical_path."data/products/".$new_name.".jpg", 1);
                        $this->db->ExecuteSql ("Update {$this->object} Set photo='$short_name' Where product_id='$id'");
                    }
                    
                }
            }
            
            if (array_key_exists ("file", $_FILES) and $_FILES['file']['error'] < 3)
            {
                $symbs = getUnID (20);
                $oldname = $_FILES['file']['name'];
                $tmp_name = $_FILES['file']['tmp_name'];
                
                $ext = getExtension ($oldname, "txt");
                
                $types = $_FILES['file']['type'];
                $types_array = explode("/", $types);
                if ( strpos($_FILES['file']['name'],'php')!==false ) {
                    $this->Redirect ($this->pageUrl);
                    exit();
                }
                $whitelist = array("jpg","jpeg","gif","png",'zip','txt','pdf');

                if (is_uploaded_file ($tmp_name) && in_array($ext, $whitelist) )
                {
                    $new_name = $id."_".$symbs.".".$ext;
                    if (is_uploaded_file ($tmp_name))
                    {
                        $physical_path = $this->db->GetSetting ("PathSite");
                        move_uploaded_file ($tmp_name, $physical_path."data/pfiles/".$new_name);
                        $cmd = "chmod 666 ".$physical_path."data/pfiles/".$new_name;
                        @exec ($cmd, $output, $retval);
                        @chmod ($physical_path."data/pfiles/".$new_name, 0644);
                        $this->db->ExecuteSql ("Update {$this->object} Set file='$new_name' Where product_id='$id'");
                    }
                    
                }
            }
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_edit ()
    {
        $this->pageTitle = "Products";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Products</a> / Edit Product";
        $this->fill_form ("update", FORM_FROM_DB);
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        $this->pageTitle = "Products";
        $this->pageHeader = "<a href='{$this->pageUrl}'>Products</a> / Edit Product";
        $id = $this->GetGP ("id");

        $title = $this->enc ($this->GetValidGP ("title", "Title", VALIDATE_NOT_EMPTY));
        $price = $this->enc ($this->GetValidGP ("price", "Price", VALIDATE_FLOAT_POSITIVE));
        $description = $this->enc ($this->GetGP ("description"));
        $category_id = $this->enc ($this->GetGP ("category_id"));
        
        $short_description = $this->enc ($this->GetGP ("short_description"));
        if ($this->errors['err_count'] > 0) {
            $this->fill_form ("update", FORM_FROM_GP);
        }
        else
        {
            $this->db->ExecuteSql ("Update {$this->object} Set `title`='$title', `description`='$description', `price`='$price', category_id='$category_id' Where `product_id`=$id");
            
            if (array_key_exists ("photo", $_FILES) and $_FILES['photo']['error'] < 3)
            {
                $symbs = getUnID (5);
                $oldname = $_FILES['photo']['name'];
                $tmp_name = $_FILES['photo']['tmp_name'];
                $short_name = $id."_".$symbs;
                
                if (is_uploaded_file ($tmp_name))
                {
                    $symbs = getUnID (5);
                    $oldname = $_FILES['photo']['name'];
                    $tmp_name = $_FILES['photo']['tmp_name'];
                    $short_name = $id."_".$symbs;
                    $new_name = $short_name;
                    $thumb_name = "small_".$new_name;

                    $types = $_FILES['photo']['type'];
                    $types_array = explode("/", $types);
                    if ( $types_array [0] != "image" || strpos($_FILES['photo']['name'],'php')!==false ) {
                        $this->Redirect ($this->pageUrl);
                        exit();
                    }
                    $ext = getExtension ($oldname, "jpg");
                    $whitelist = array("jpg","jpeg","gif","png");

                    if (is_uploaded_file ($tmp_name) && in_array($ext, $whitelist) )
                    {
                        $physical_path = $this->db->GetSetting ("PathSite");
                        move_uploaded_file ($tmp_name, $physical_path."data/products/".$new_name.".jpg");
                        $cmd = "chmod 666 ".$physical_path."data/products/".$new_name.".jpg";
                        @exec ($cmd, $output, $retval);
                        @chmod ($physical_path."data/products/".$new_name, 0644);
                        copy ($physical_path."data/products/".$new_name.".jpg", $physical_path."data/products/".$thumb_name.".jpg");
                        $cmd = "chmod 666 ".$physical_path."data/products/".$thumb_name.".jpg";
                        @exec ($cmd, $output, $retval);
                        @chmod ($physical_path."data/products/".$thumb_name.".jpg", 0644);
                        makeThumbnail ($physical_path."data/products/".$thumb_name.".jpg", 0);
                        makeThumbnail ($physical_path."data/products/".$new_name.".jpg", 1);
                        $this->db->ExecuteSql ("Update {$this->object} Set photo='$short_name' Where product_id='$id'");
                    }
                    
                }
            }
            
            if (array_key_exists ("file", $_FILES) and $_FILES['file']['error'] < 3)
            {
                $symbs = getUnID (20);
                $oldname = $_FILES['file']['name'];
                $tmp_name = $_FILES['file']['tmp_name'];
                
                $ext = getExtension ($oldname, "txt");
                
                $types = $_FILES['file']['type'];
                $types_array = explode("/", $types);
                if ( strpos($_FILES['file']['name'],'php')!==false ) {
                    $this->Redirect ($this->pageUrl);
                    exit();
                }
                $whitelist = array("jpg","jpeg","gif","png",'zip','txt','pdf');

                if (is_uploaded_file ($tmp_name) && in_array($ext, $whitelist) )
                {
                    $new_name = $id."_".$symbs.".".$ext;
                    if (is_uploaded_file ($tmp_name))
                    {
                        $physical_path = $this->db->GetSetting ("PathSite");
                        move_uploaded_file ($tmp_name, $physical_path."data/pfiles/".$new_name);
                        $cmd = "chmod 666 ".$physical_path."data/pfiles/".$new_name;
                        @exec ($cmd, $output, $retval);
                        @chmod ($physical_path."data/pfiles/".$new_name, 0644);
                        $this->db->ExecuteSql ("Update {$this->object} Set file='$new_name' Where product_id='$id'");
                    }
                    
                }
            }
            
            $this->Redirect ($this->pageUrl);
        }
    }

    //--------------------------------------------------------------------------
    function ocd_activate ()
    {
        $id = $this->GetGP ("id", 0);
        $this->db->ExecuteSql ("Update {$this->object} Set is_active=(1-is_active) Where product_id=$id");
        $this->Redirect ($this->pageUrl);
    }
    
    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id", 0);
        
        $filename = $this->db->GetOne ("Select photo From {$this->object} Where product_id='$id'");
        $physical_path = $this->db->GetSetting ("PathSite");
        if (($filename!= "") and (file_exists ($physical_path."data/products/".$filename.".jpg"))) unlink ($physical_path."data/products/".$filename.".jpg");
        if (($filename!= "") and (file_exists ($physical_path."data/products/small_".$filename.".jpg"))) unlink ($physical_path."data/products/small_".$filename.".jpg");
        
        $filename = $this->db->GetOne ("Select file From {$this->object} Where product_id='$id'");
        if (($filename!= "") and (file_exists ($physical_path."data/pfiles/".$filename))) unlink ($physical_path."data/pfiles/".$filename);
        
        
        $this->db->ExecuteSql ("Delete From {$this->object} Where product_id='$id'");
        
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_delphoto ()
    {
        $id = $this->GetGP ("id", 0);
        $filename = $this->db->GetOne ("Select photo From {$this->object} Where product_id='$id'");
        $physical_path = $this->db->GetSetting ("PathSite");
        if (($filename!= "") and (file_exists ($physical_path."data/products/".$filename.".jpg"))) unlink ($physical_path."data/products/".$filename.".jpg");
        if (($filename!= "") and (file_exists ($physical_path."data/products/small_".$filename.".jpg"))) unlink ($physical_path."data/products/small_".$filename.".jpg");
        $this->db->ExecuteSql ("Update {$this->object} Set photo='' Where product_id='$id'");
        $this->Redirect ($this->pageUrl."?ocd=edit&id=$id");
    }
    
    //--------------------------------------------------------------------------
    function ocd_delfile ()
    {
        $id = $this->GetGP ("id", 0);
        $filename = $this->db->GetOne ("Select file From {$this->object} Where product_id='$id'");
        $physical_path = $this->db->GetSetting ("PathSite");
        if (($filename!= "") and (file_exists ($physical_path."data/pfiles/".$filename))) unlink ($physical_path."data/pfiles/".$filename);
        $this->db->ExecuteSql ("Update {$this->object} Set file='' Where product_id='$id'");
        $this->Redirect ($this->pageUrl."?ocd=edit&id=$id");
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

$zPage = new ZPage ("products");

$zPage->Render ();

?>