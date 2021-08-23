<?php
require_once ("../includes/config.php");
require_once ("../includes/xtemplate.php");
require_once ("../includes/xpage_admin.php");
require_once ("../includes/dumper.php");
require_once ("../includes/utilities.php");

class ZPage extends XPage
{

    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        XPage::XPage ($object);
        $this->mainTemplate = "./templates/backup.tpl";
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->pageTitle = "Back up of your DB";
        $this->pageHeader = "Back up of your DB";
        $ec = $this->GetGP ("ec", "");
        $fn = $this->GetGP ("fn", "");
        $main_message = "";

        if ($ec == "restore_error") $main_message = "<span class='error'>Error: The file of recovery copy has not been selected.</span>";
        if ($ec == "restore_ok") $main_message = "<span class='message'>The DB has been successfully recovered from the file '$fn'.</span>";
        if ($ec == "backup_ok") $main_message = "<span class='message'>The recovery copy of DB has been created. File - '$fn'.</span>";
        if ($ec == "delete_error") $main_message = "<span class='error'>Error: The file of recovery copy has not been selected.</span>";
        if ($ec == "delete_ok") $main_message = "<span class='message'>The file '$fn' has been successfully removed.</span>";
        if ($ec == "reset") $main_message = "<span class='message'>Your Data Base was successfully reset.</span>";

        $total = $this->db->GetOne ("Select Count(*) From {$this->object}");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_MESSAGE" => $main_message,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_OCD" => "reset",
            "MAIN_ADDLINK" => "<a href='{$this->pageUrl}?ocd=backup' title='Create backup copy' onClick=\"return confirm ('Now the copy of your DB will be created.');\"><img src='./images/add.png' border='0'></a>",
            "HEAD_TITLE" => "<b>Title_Data_Time</b>",
        );

        $appRoot = $this->db->GetSetting ("PathSite");
        $urlRoot = $this->db->GetSetting ("SiteUrl");

        $dumper = new Dumper (DbName, $appRoot."data/backups/");

        $files = array ();
        $files = $dumper->file_select ();

        array_shift ($files);
        $total = count ($files);

        $bgcolor = "";
        if ($total > 0)
        {
            foreach ($files as $file=>$title)
            {

                $recoverLink = "<a href='{$this->pageUrl}?ocd=restore&file=$file' onClick=\"return confirm ('Do you really want to recover your DB? The current DB will be deleted!');\"><img src='./images/infinity1.png' width='25' border='0' title='Recover database from this file' title='Recover database from this file' /></a>";
                $delLink = "<a href='{$this->pageUrl}?ocd=delete&file=$file' onClick=\"return confirm ('Do you really want to delete this copy of your DB?');\"><img src='./images/trash.png' width='25' border='0' alt='Delete' title='Delete' /></a>";
                $downLink = "<a href='".$urlRoot."data/backups/".$file."'><img src='./images/down.png' width='25' border='0' alt='Download this file' title='Download this file' /></a>";

                $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
                $this->data ['TABLE_ROW'][] = array (
                    "ROW_TITLE" => $title,
                    "ROW_RECOVERLINK" => $recoverLink,
                    "ROW_DOWNLINK" => $downLink,
                    "ROW_DELLINK" => $delLink,
                    "ROW_BGCOLOR" => $bgcolor
                );
            }
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
    function ocd_reset ()
    {
			$ContactEmail = $this->db->GetSetting ("ContactEmail");
        	$SiteTitle = $this->db->GetSetting ("SiteTitle");
        	$SerialCode = $this->db->GetSetting ("SerialCode");  
        	$StartDate = $this->db->GetSetting ("StartDate");
        	$SiteUrl = $this->db->GetSetting ("SiteUrl");
			$PathSite = $this->db->GetSetting ("PathSite");
			
			$folders = array ($PathSite."data/aptools/", 
			$PathSite."data/csv/", 
			$PathSite."data/images/", 
			$PathSite."data/lands/", 
			$PathSite."data/news/", 
			$PathSite."data/pfiles/", 
			$PathSite."data/pictures/",
			$PathSite."data/products/",
			$PathSite."data/ptools/",
			$PathSite."data/testimonials/");
			
			foreach ($folders as $dir)
			{
					if ($handle = opendir($dir))
					{
		    				while (false !== ($file = readdir($handle))) 
		    				{ 
		        					if ($file != "." && $file != ".." && $file != "index.php") 
		        					{ 
		            						unlink ($dir.$file); 
		        					} 
		    				}
		    				closedir($handle); 
					}  
			
			}
			
        			
        	$dumper = new Dumper (DbName, $PathSite."db/");
        	
        	$file_name = $dumper->restore ("mlmBuilder.sql.gz");
        	
        	if ($file_name != "") 
        	{
        			
        			$this->db->SetSetting ("ContactEmail", $ContactEmail);
        			$this->db->SetSetting ("SiteTitle", $SiteTitle);
        			$this->db->SetSetting ("SerialCode", $SerialCode);
        			$this->db->SetSetting ("StartDate", $StartDate);
        			$this->db->SetSetting ("SiteUrl", $SiteUrl);
        			
        			$this->db->SetSetting ("PathSite", $PathSite);
        			
            		$this->Redirect ($this->pageUrl."?ec=restore_ok&fn=mlmBuilder.sql.gz");
            		
        	}
        	else
        	{
            		$this->Redirect ($this->pageUrl."?ec=restore_error");
        	}
			
    }

    
    //--------------------------------------------------------------------------
    function ocd_backup ()
    {
        $appRoot = $this->db->GetSetting ("PathSite");
        $dumper = new Dumper (DbName, $appRoot."data/backups/");
        $file_name = $dumper->backup ();
        $this->Redirect ($this->pageUrl."?ec=backup_ok&fn=$file_name");

    }

    //--------------------------------------------------------------------------
    function ocd_restore ()
    {
        $file = $this->GetGP ("file");

        $appRoot = $this->db->GetSetting ("PathSite");
        $dumper = new Dumper (DbName, $appRoot."data/backups/");

        $file_name = $dumper->restore ($file);
        if ($file_name != "") {
            $this->Redirect ($this->pageUrl."?ec=restore_ok&fn=$file_name");
        }
        else
        {
            $this->Redirect ($this->pageUrl."?ec=restore_error");
        }
    }

    //--------------------------------------------------------------------------
    function ocd_delete ()
    {
        $file = $this->GetGP ("file");

        $physical_path = $this->db->GetSetting ("PathSite");
        if ($file != "" and file_exists ($physical_path."data/backups/".$file))
        {
            unlink ($physical_path."data/backups/".$file);
            $this->Redirect ($this->pageUrl."?ec=delete_ok&fn=$file");
        }
        else
        {
            $this->Redirect ($this->pageUrl."?ec=delete_error");
        }
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("backup");

$zPage->Render ();

?>