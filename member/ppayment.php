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
        $this->mainTemplate = "./templates/ppayment.tpl";
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->pageTitle = "Payment Page";
        $this->pageHeader = "Payment Page";
        $member_id = $this->member_id;

        $prepayment = $this->GetGP ("prepayment", 0);
        if ($prepayment == 1)
        {
            $processor_id = $this->GetGP ("processor", 0);
            $this->SaveStateValue ("processor", $processor_id);
        }
        
        $id = $this->GetGP ("id", "");
        if ($id != "")
        {
            $this->SaveStateValue ("id", $id);    
        }
        $id = $this->GetStateValue ("id", "");
        
        if ($id == "" Or  !$this->isValidProduct ($id, $this->member_id)) $this->Redirect ("./overview.php");
        $product = $this->db->GetEntry ("Select * From `products` Where `product_id`='$id' And `is_active`='1'", "./overview.php");
        
        $amount = $product ["price"];
        $title = $this->dec ($product ["title"]);
        $description = nl2br ($this->dec ($product ["description"]));

        if ($product['photo'] != "")
        {
            $photoUrl = "../data/products/small_".$this->dec ($product['photo']).".jpg";
            $photo = "<a href='../data/products/".$this->dec ($product['photo']).".jpg' target='_blank'><img src='$photoUrl' alt='$title' title='$title' class='img_w_d_board' /></a>";
        }
        else
        {   
            $photoUrl = "../data/products/nophoto.jpg";
            $photo = "<img src='$photoUrl' alt='$title' title='$title' class='img_w_d_board' />";
        }
        $download_link = "";
        if ($amount == 0)
        {
            $amount = "FREE!!!";
            $download_link = "<a href='".$this->pageUrl."?ocd=download&id=$id'  class='smallLink'>Download</a>"; 
        }
        else
        {
            $amount = "$".$amount; 
        }
        
        $processor_id = $this->GetStateValue ("processor", '0');
        $prepayment = $this->GetGP ("prepayment", 0);
        
        $button = ($product ["price"] > 0)? "

    <div class='form-group'>
        <div class='row'>
            <label class='col-sm-4 control-label'>Select processor :</label>
            <div class='col-sm-8'>
                ".$this->select_processor ($processor_id)."
            </div>
        </div>    
    </div>      
            " : "";

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            
            "MAIN_ACTION" => $this->pageUrl,
            "AMOUNT" => $amount,
            "TITLE" => $title,
            "DESCRIPTION" => $description,
            "PHOTO" => $photo,
            "BUTTON" => $button,
            "DOWNLOAD" => $download_link,
        );

            if ($prepayment == 1)
            {
                
                $amount = $product ["price"];
                
                $product = "Product Payment";
                $this->SaveStateValue ("processor", $processor_id);
                $processor_title = $this->db->GetOne ("Select name From `processors` Where processor_id='$processor_id'", "Account cash balance");
                $processor_fee = $this->db->GetOne ("Select fee From `processors` Where processor_id='$processor_id'", "0.00");
                $full_sum_dig = $amount + $amount / 100 * $processor_fee;
                $full_sum_dig = sprintf ("%01.2f", $full_sum_dig);
                $full_sum = "\$".$amount." + \$".$amount." / 100% * ".$processor_fee."% = <b>\$".$full_sum_dig."</b>";
                
                $this->data ['PREPAYMENT'][] = array (
                        "ROW_ACTION" => "",
                        "ROW_AMOUNT" => $amount,
                        "ROW_PRODUCT" => $product,
                        "ROW_PRODUCT_NAME" => $title,
                        "ROW_FEE" => $processor_fee,
                        "ROW_FULL_SUM" => $full_sum,
                        "ROW_CODE" =>  ($processor_id >= 0)? getPayFormCode ($member_id, $full_sum_dig, $processor_id, $product, $id) : $this->getCashButton ($id),
                        "ROW_PROCESSOR" => $processor_title,
                    );
            }
            else
            {
                $this->data ['PREPAYMENT_NO'][] = array (
                        "NO_RIGHT" => "No need to pay more"
                        );
            }

    }
    
    //--------------------------------------------------------------------------
    function ocd_pay ()
    {
        $id = $this->GetGP ("product_id", "");
         
        $total_cash = $this->db->GetOne ("Select SUM(amount) From `cash` Where to_id='{$this->member_id}'", "0.00");
        $price = $this->db->GetOne ("Select `price` From `products` Where `product_id`='$id' And `is_active`='1'", "0");
        if ($price > 0 And $total_cash >= $price)
        {
            
            $this->db->ExecuteSql ("Insert Into `cash` (amount, type_cash, from_id, to_id, cash_date, descr, payment_id) Values ('-$price', '$id', '0', '".$this->member_id."', '".time ()."', 'Product payment #$id', '0')");
            payProduct ($this->member_id, time(), $id, '-2');
            
            $this->Redirect ("payment_res.php?res=ok");
        }
        $this->Redirect ($this->pageUrl);         
        
    }
    
    //--------------------------------------------------------------------------
    function getCashButton ($product_id)
    {
        $toRet = "<form action='' method='POST'>";
        $toRet .= "<input type='hidden' name='ocd' value='pay' />";
        $toRet .= "<input type='hidden' name='product_id' value='$product_id' />";
        $toRet .= "<input type='submit' value=' Pay Now ' class='some_btn' /></form>";
        return $toRet;
    }
    
    //--------------------------------------------------------------------------
    function isValidProduct ($id, $member_id)
    {
        $cid = $this->db->GetOne ("Select `category_id` From `products` Where `product_id`='$id'", "");
        $m_level_cat = $this->db->GetOne ("Select `m_level` From `categories` Where `category_id`='$cid'", "");
        $m_level = $this->db->GetOne ("Select `m_level` From `members` Where `member_id`='$member_id'", 0);
        
        $arrLevels = explode (";", $m_level_cat);
        
        if (in_array ($m_level, $arrLevels))
            return true;
        else
            return false;
        
    }

    //--------------------------------------------------------------------------
    function select_processor ($processor_id = 0)
    {
        $toRet = "<select name='processor' onChange='this.form.submit();'> \r\n";
        $toRet .= "<option value='0' >Choose processor</option>";

		$payp_fromcash = $this->db->GetSetting ("payp_fromcash", 0);
		if ($payp_fromcash == 1)
		{

        		$total_cash = $this->db->GetOne ("Select SUM(amount) From `cash` Where to_id='{$this->member_id}'", "0.00");
        		$id = $this->GetStateValue ("id", "");
        		$price = $this->db->GetOne ("Select `price` From `products` Where `product_id`='$id' And `is_active`='1'", "0");
		        
         		if ($price <= $total_cash) 
        		{
            		$selected = (0 == $processor_id)? "selected" : "";   
            		$toRet .= "<option value='-1' $selected>From my account cash balance (balance: $".$total_cash.")</option>";
        		}
        }
        
        $result = $this->db->ExecuteSql ("Select * From `processors` Where is_active=1 Order By name");
        while ($row = $this->db->FetchInArray ($result))
        {
            $id = $row['processor_id'];
            $fee = $row['fee'];
            $selected = ($id == $processor_id)? "selected" : "";
            $toRet .= "<option value='".$id."' $selected>".$row['name']." (fee: $fee%)</option>";
        }
        return $toRet."</select>\r\n";
    }
    
    //--------------------------------------------------------------------------
    function ocd_download ()
    {
        $product_id = $this->GetGP ("id", "");
        
        $price = $this->db->GetOne ("Select `price` From `products` Where `product_id`='$product_id'", "");
        
        if ($price > 0) $this->Redirect ($this->pageUrl);
        
        $file = $this->db->GetOne ("Select `file` From `products` Where `product_id`='$product_id'", "");
        $ext = getExtension ($file);
            
//        $title = $this->dec ($this->db->GetOne ("Select `title` From `products` Where `product_id`='$product_id'", ""));
        $title = "Product";
                
        $pathSite = $this->db->GetSetting ("PathSite");
        $file_show = $this->member_id.$product_id."_".$title.".".$ext;
            
        $total = $pathSite ."/data/pfiles/". $file; 
	      Header ( "Content-Type: application/octet-stream"); 
	      Header ( "Content-Length: ".filesize($total)); 
	      Header ( "Content-Disposition: attachment; filename=$file_show"); 
	
        readfile($total);
        
        
    }   
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("account");

$zPage->Render ();

?>