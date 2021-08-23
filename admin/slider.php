<?php

require_once ("../includes/config.php");
require_once ("../includes/xtemplate.php");
require_once ("../includes/xpage_admin.php");
require_once ("../includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{

    //--------------------------------------------------------------------------
    function ZPage ($object='')
    {
        //$this->orderDefault = "product_id";
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        
        //$this->javaScripts = $this->GetJavaScript ();
        $this->mainTemplate = "./templates/slider.tpl";
        $this->pageTitle = "Slider";
        $this->pageHeader = "Slider";
/*        
	$dir    = $_SERVER['DOCUMENT_ROOT'].'/data/slider/';
	$files = scandir($dir);
	$fileNames = [];
	foreach($files as $fileName)
	{
		if (is_file($dir.$fileName)) $fileNames[] = ['ROW_NAME' => $fileName];
	}
*/
        //$total = count($fileNames);
        
        $result = $this->db->ExecuteSql ("Select * From `slider` ");
        while ($row = $this->db->FetchInArray ($result))
        {
                $fileNames[] = [
                    'ROW_NAME' => $row['image'],
                    'ROW_TEXT' => $this->dec( $row['text'])
                ];
        }

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            //"MAIN_ADDLINK" => "<a href='{$this->pageUrl}?ocd=new' alt='Add file' title='Add file'><img src='./images/add.png' border='0' /></a>",

            "EDITOR" => $this->FCKeditor( "text", '' ),
            
            "LIST_ROW" => $fileNames,
            //"DIR_NAME" => $dir,
            "MAIN_TIME" => $this->db->GetSetting("Carousel_autoplayTimeout", "1000")

        );

        if (isset($_GET['add'])) 
        {
            $this->data['LIST_ROW'] = [];
            $this->data['ADD'] = ['-'=>'-'];
        }
        else if (isset($_GET['edit'])) 
        {
            $this->data['LIST_ROW'] = [];
            $this->data['EDIT'] = ['-'=>'-'];
            $row = $this->db->GetEntry ("Select * From slider Where image='{$_GET['edit']}' ", $this->pageUrl);
            $this->data['EDIT']['EDIT_ID'] = $row['id'];
            $this->data["EDITOR"] = $this->FCKeditor( "text", htmlspecialchars_decode($row['text']) );
        }




    }




    //--------------------------------------------------------------------------
    function ocd_add ()
    {
        
        //$this->pageTitle = "Products";
        //$this->pageHeader = "<a href='{$this->pageUrl}'>Products</a> / New Product";
        
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
                $whitelist = array('png',"jpg","jpeg","gif");

                if (is_uploaded_file ($tmp_name) && in_array($ext, $whitelist) )
                {
                    $new_name = $symbs.".".$ext;
                    if (is_uploaded_file ($tmp_name))
                    {
                        $physical_path = $this->db->GetSetting ("PathSite");
                        move_uploaded_file ($tmp_name, $physical_path."data/slider/".$new_name);
                        $cmd = "chmod 666 ".$physical_path."data/slider/".$new_name;
                        @exec ($cmd, $output, $retval);
                        @chmod ($physical_path."data/slider/".$new_name, 0644);

                        $text = $this->enc ($this->GetGP ("text"));

                        $this->db->ExecuteSql ("Insert into slider (text, image) values ('$text', '$new_name')");

                    }
                    
                }
            }
            $this->Redirect ($this->pageUrl);

    }

    //--------------------------------------------------------------------------
    function ocd_edit ()
    {
        
        //$this->pageTitle = "Products";
        //$this->pageHeader = "<a href='{$this->pageUrl}'>Products</a> / New Product";

             $row = $this->db->GetEntry ("Select * From slider Where id='".$this->GetGP ("id")."' ", $this->pageUrl);
            $text = $this->enc ($this->GetGP ("text"));
            $this->db->ExecuteSql ("Update slider Set text='$text' where id='{$row['id']}' ");
        
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
                $whitelist = array('png',"jpg","jpeg","gif");

                if (is_uploaded_file ($tmp_name) && in_array($ext, $whitelist) )
                {
                    $new_name = $symbs.".".$ext;
                    if (is_uploaded_file ($tmp_name))
                    {
                        $physical_path = $this->db->GetSetting ("PathSite");
                        move_uploaded_file ($tmp_name, $physical_path."data/slider/".$new_name);
                        $cmd = "chmod 666 ".$physical_path."data/slider/".$new_name;
                        @exec ($cmd, $output, $retval);
                        @chmod ($physical_path."data/slider/".$new_name, 0644);
                        
                        $this->db->ExecuteSql ("Update slider Set image='$new_name' where id='{$row['id']}' ");

                        if ( (file_exists ($physical_path."data/slider/".$row['image']))) unlink ($physical_path."data/slider/".$row['image']);
                    }
                    
                }
            }
            $this->Redirect ($this->pageUrl);

    }


    //--------------------------------------------------------------------------
    function ocd_del ()
    {
        $id = $this->GetGP ("id", '');
        
        $physical_path = $this->db->GetSetting ("PathSite");
        if ( (file_exists ($physical_path."data/slider/".$id))) unlink ($physical_path."data/slider/".$id);
        $this->db->ExecuteSql ("Delete From `slider` Where image='$id' ");
        $this->Redirect ($this->pageUrl);
    }

    //--------------------------------------------------------------------------
    function ocd_update()
    {
        $time = $this->GetGP ("time", 3000);
        $this->db->SetSetting ("Carousel_autoplayTimeout", $time);

        $this->Redirect ($this->pageUrl);
    }


}

//------------------------------------------------------------------------------

$zPage = new ZPage ('');

$zPage->Render ();

?>
