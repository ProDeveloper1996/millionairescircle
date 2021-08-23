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
        $this->mainTemplate = "./templates/eshop.tpl";
                
        $cid = $this->GetGP ("cid", "");
        if ($cid != "")
        {
            $this->SaveStateValue ("cid", $cid);    
        }
        $cid = $this->GetStateValue ("cid", "");
        
        if ($cid == "" Or  !$this->isValidCategory ($cid, $this->member_id)) $this->Redirect ("./overview.php");
        
        $category = $this->db->GetEntry ("Select * From `categories` Where `category_id`='$cid'", "./overview.php");
        
        $this->pageTitle = "E-Shop : ".$this->dec ($category ["title"]);
        $this->pageHeader = "E-Shop : ".$this->dec ($category ["title"]);

        $total = $this->db->GetOne ("Select COUNT(*) From `products` Where `category_id`='$cid'", 0);

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_CATEGORY_DESCRIPTION" => nl2br ($this->dec ($category ["description"])),
            "MAIN_PAGES" => $this->Pages_GetLinks ($total, $this->pageUrl."?"),
        );
        
        if ($total > 0)
        {
            $k = 0;
            $result = $this->db->ExecuteSql ("Select * From `products` Where `category_id`='$cid' And `is_active`=1 Order By `title` Asc", true);
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
                $photo = "<a href='./ppayment.php?id=$id'><img src='$photoUrl' alt='$title' title='$title' class='img_w_d_board' />";
                
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
    function isValidCategory ($cid, $member_id)
    {
        $m_level_cat = $this->db->GetOne ("Select `m_level` From `categories` Where `category_id`='$cid'", "");
        $m_level = $this->db->GetOne ("Select `m_level` From `members` Where `member_id`='$member_id'", 0);
        
        $arrLevels = explode (";", $m_level_cat);
        
        if (in_array ($m_level, $arrLevels))
            return true;
        else
            return false;
        
    }
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("eshop");

$zPage->Render ();

?>