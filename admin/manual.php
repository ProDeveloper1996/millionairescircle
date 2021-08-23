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
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $this->mainTemplate = "./templates/manual.tpl";

        $m = $this->GetGP ("m");
        $mess = "";
        if ($m == "no") $mess = "This member's ID was not found in the system.";
        if ($m == "nop") $mess = "This product's ID was not found in the system.";
        if ($m == "yes") $mess = "The payment was successfully registered";

        $this->pageTitle = "Manual Member Payment";
        $this->pageHeader = "Manual Member Payment";
        $forced_level = "";
        $cycling = $this->db->GetSetting ("cycling", 1);
        switch ($cycling)
        {
            case 0:

                $level = $this->selectLevel ();
                $forced_level = "<tr><td><span class='signs_b'>Choose the member level:</span></td><td>$level</td></tr><tr style='height=5px;'><td colspan='2'></td></tr>";

            break;
        }

        $id = "<input type='text' name='id' value='' maxlength='10' style='width: 120px; background: #FFFFFF;'>";
        $product = "<input type='text' name='product_id' value='' maxlength='10' style='width: 120px; background: #FFFFFF;'>";

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_HEADER2" => "E-Shop Product Manual Payment",
            "MAIN_MESS" => $mess,
            "MAIN_ID" => $id,
            "MAIN_PRODUCT" => $product,
            "FORCED_LEVEL" => $forced_level,
        );
    }

    //--------------------------------------------------------------------------
    function ocd_product ()
    {
        $member_id = $this->GetGP ("id", 0);
        $product_id = $this->GetGP ("product_id", 0);
        
        $total1 = $this->db->GetOne ("Select Count(*) From `members` Where member_id='$member_id' And is_active=1", 0);        
        $total2 = $this->db->GetOne ("Select Count(*) From `products` Where product_id='$product_id' And is_active=1", 0);
        
        if ($total1 > 0 And $total2 > 0)
        {
            payProduct ($member_id, time (), $product_id, '-1');
            $this->Redirect ($this->pageUrl."?m=yes");            
        }
        $this->Redirect ($this->pageUrl."?m=nop");
        
        
    }

    //--------------------------------------------------------------------------
    function ocd_pay ()
    {
        $id = $this->GetGP ("id");

        $cycling = $this->db->GetSetting ("cycling", 1);
        $thisTime = time ();
        switch ($cycling)
        {
            case 1:

                $total = $this->db->GetOne ("Select Count(*) From `members` Where member_id='$id' And m_level=0", 0);
                if ($total == 1)
                {
                    $enroller_id = $this->db->GetOne ("Select enroller_id From `members` Where member_id='$id'");
                    $enr_level = $this->db->GetOne ("Select m_level From `members` Where member_id='$enroller_id'");

                    if ($enr_level == 0)
                    {
                        $new_enroller_id = $this->db->GetOne ("Select member_id From `members` Where is_active=1 And m_level>0 Order By RAND() Limit 1", 1);
                        $this->db->ExecuteSql ("Update `members` Set enroller_id='$new_enroller_id' Where member_id='$id'");
                    }

                    payUpline ($id, $thisTime, '1', '-1');
                    $this->Redirect ($this->pageUrl."?m=yes");

                }
                else
                {

                    $this->Redirect ($this->pageUrl."?m=no");

                }

            break;
            case 0:

                $total = $this->db->GetOne ("Select Count(*) From `members` Where member_id='$id'");

                if ($total == 1)
                {

                    $m_level = $this->GetGP ("m_level", 0);
                    
                    if ($m_level > 0)
                    {
                        payUpline ($id, $thisTime, $m_level, '-1');
                        $this->Redirect ($this->pageUrl."?m=yes");
                    }
                    else
                    {
                        $this->Redirect ($this->pageUrl."?m=no");
                    }

                }
                else
                {

                    $this->Redirect ($this->pageUrl."?m=no");

                }

            break;
        }
    }
    //--------------------------------------------------------------------------
    function selectLevel ()
    {
        $all_levels = $this->db->GetOne ("Select Count(*) From `types`", 0);


        if ($all_levels > 0)
        {
            $toRet = "<select name='m_level' style='width:250px;'> \r\n";
            $result = $this->db->ExecuteSql ("Select * From `types` Where cost>0 Order By order_index ASC");
            while ($row = $this->db->FetchInArray ($result))
            {
                $order_index = $row ["order_index"];
                $title = $this->dec ($row ["title"]);
                $cost = $row ["cost"];
                $toRet .= "<option value='$order_index'>$title - \$$cost</option>";
            }
            $this->db->FreeSqlResult ($result);
        }
        else
        {
            $toRet = "No levels registered";
        }
        return $toRet."</select>\r\n";
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("manual");

$zPage->Render ();

?>