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
        $this->mainTemplate = "./templates/download.tpl";
                
        $this->pageTitle = "Download Page";
        $this->pageHeader = "Download Page";


        $total = $this->db->GetOne ("Select COUNT(*) From `products` Where `product_id` IN (Select `product_id` From `payins` Where `member_id`='".$this->member_id."')", 0);
        
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
        );
        
        if ($total > 0)
        {
            $k = 0;
            $result = $this->db->ExecuteSql ("Select * From `products` Where `product_id` IN (Select `product_id` From `payins` Where `member_id`='".$this->member_id."')", 0);
            while ($row = $this->db->FetchInArray ($result))
            {
                $k++;
                $id = $row['product_id'];
                
                $title = $this->dec ($row['title']);
                $description = nl2br ($this->dec ($row['description']));
                
                if ($row['photo'] != "")
                {
                    $photoUrl = "../data/products/small_".$this->dec ($row['photo']).".jpg";
                }
                else
                {   
                    $photoUrl = "../data/products/nophoto.jpg";
                }
                $photo = "<a href='./download.php?ocd=down&id=$id'><img src='$photoUrl' alt='Download' title='Download' class='img_w_d_board' />";
                
                $price = ($row['price'] > 0)? "$".$row['price'] : "FREE!!!";
                
                $up = "<td style='width:25%;' align='center'>";
                $down = "</td>";
                
                if ($k == 5)
                {
                    $up = "</tr><tr style='height:10px;'><td colspan='4'></tr><tr valign='top'><td style='width:25%;' align='center'>";
                    $down = "</td>";
                    $k = 1;
                }
                
                $this->data ['PRODUCTS_ROW'][] = array (
                    
                    "ROW_UP" => $up,
                    "ROW_DOWN" => $down,
                    "ROW_PHOTO" => $photo,
                    "ROW_TITLE" => $title,
                    "ROW_PRICE" => $price,
                    );
            }
            $this->db->FreeSqlResult ($result);
        }
        else
        {
            $this->data ['PRODUCTS_EMPTY'][] = array (
                "_" => "_"
            );
        }
        
    }
    
    //--------------------------------------------------------------------------
    function ocd_down ()
    {
        $product_id = $this->GetGP ("id", "");
        
        $total = $this->db->GetOne ("Select COUNT(*) From `payins` Where `product_id`='$product_id' And `product_id`!=0 And `member_id`='".$this->member_id."'", 0);
        
        if ($total == 0) $this->Redirect ("./download.php");
        
        $file = $this->db->GetOne ("Select `file` From `products` Where `product_id`='$product_id'", "");
        $ext = getExtension ($file);
            
//        $title = $this->dec ($this->db->GetOne ("Select `title` From `products` Where `product_id`='$product_id'", ""));
        $title = "Product";
                
        $pathSite = $this->db->GetSetting ("PathSite");
        $file_show = $this->member_id.$product_id."_".$title.".".$ext;
            
        $total = $pathSite ."/data/pfiles/". $file; 
	      Header ( "Content-Type: application/octet-stream"); 
	      Header ( "Content-Length: ".filesize($total)); 
	      Header( "Content-Disposition: attachment; filename=$file_show"); 
	
        readfile($total);
        
        
    }   
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("download");

$zPage->Render ();

?>