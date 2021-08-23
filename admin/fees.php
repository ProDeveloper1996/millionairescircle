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
        $st = $this->GetGP ("st", "");
        $message = "";
        if ($st == "ok") $message = "<span class='message'>The settings were successfully updated.</span>";
        if ($st == "cl") $message = "<span class='message'>All the fees were successfully reset.</span>";
        if ($st == "no") $message = "<span class='error'>You should fill in all fields.</span>";

        $this->mainTemplate = "./templates/fees.tpl";
        $this->pageTitle = "Fees Page";
        $this->pageHeader = "Fees Page";

        $total = $this->db->GetOne ("Select Count(*) From `types` Where cost>0", 0);
        $main_names = "";

        $symb = '$';
        if ( $this->db->GetSetting("Commissions_Value") == 2 ) $symb = '%';

        if ($total > 0)
        {
            $scale = 100 / $total;
            $main_names = "<table width='100%' border='0' cellspacing='0' cellpadding='0'><tr>";
            $result = $this->db->ExecuteSql ("Select title From `types` Where cost>0 Order By order_index Asc");
            while ($row = $this->db->FetchInArray ($result))
            {
                $title = $this->dec ($row ["title"]);
                $main_names .= "<td width='$scale%' align='center'> Fee paid from $title member</td>";
            }
            $this->db->FreeSqlResult ($result);
            $main_names .= "</td></table>";
        }

        $result = $this->db->ExecuteSql ("Select * From `types` Where depth>0 Order By order_index Asc");
        $content = "";

        while ($row = $this->db->FetchInArray ($result))
        {
            $title = $this->dec ($row ["title"]);
            $order_index = $row ["order_index"];

            $paid_levels = $row ["depth"];
            $content .= "<tr><td class='w_border'>to $title member</td><td class='w_border'>";

            $content .= "<form name='ff' method='POST' enctype='multipart/form-data'>";

            $m_in_content = "<table width='100%' border='0' cellspacing='0' cellpadding='0'>";
            //$e_in_content = $m_in_content;
            for ($i = 1; $i <= $paid_levels; $i += 1)
            {

                $m_in_content .= "<tr>";
                //$e_in_content .= "<tr>";
                $m_in_in_content = "";
                //$e_in_in_content = "";

                $result1 = $this->db->ExecuteSql ("Select * From `types` Where cost>0 Order By order_index Asc");
                while ($row1 = $this->db->FetchInArray ($result1))
                {
                    $j = $row1 ["order_index"];

                    $title2 = $this->dec ($row1 ["title"]);

                    $fee_member = $this->db->GetOne ("Select fee_member From `fees` Where to_order_index='$order_index' And plevel='$i' And from_order_index='$j'", "0.00");
                    //$fee_sponsor = $this->db->GetOne ("Select fee_sponsor From `fees` Where to_order_index='$order_index' And plevel='$i' And from_order_index='$j'", "0.00");

                    $m_text = $symb."<input type='text' name='".$i."mfee".$j."' value='$fee_member' maxlength='10' style='width: 50px;'> <img src='./images/question.png' title='This amount will get the \"$title\" member on the \"$i\" level of matrix from \"$title2\" member'>";
                    //$e_text = $symb."<input type='text' name='".$i."efee".$j."' value='$fee_sponsor' maxlength='10' style='width: 50px;'> <img src='./images/question.png' title='This amount will get the \"$title2\" member on the \"$i\" level of matrix from \"$title\" personally sponsored member'>";

                    $m_in_in_content .= "<td align='center'>on Level $i deep: $m_text</td>";
                    //$e_in_in_content .= "<td align='center'>Level $i: $e_text</td>";
                }
                $this->db->FreeSqlResult ($result1);

                $m_in_content .= $m_in_in_content."</tr>";
                //$e_in_content .= $e_in_in_content."</tr>";
            }
            $m_in_content .= "</table>";
            //$e_in_content .= "</table>";

            $content .= $m_in_content."</td><td class='w_border'>";
            //$content .= $e_in_content."</td><td class='w_border' align='center'>";

            $content .= ($paid_levels > 0)? "<input type='hidden' name='ocd' value='update'><input type='hidden' name='to_order_index' value='$order_index'><input class='some_btn' type='submit' value='Update'></td></tr></form>" : "&nbsp;</td></tr></form>";

        }
        $this->db->FreeSqlResult ($result);

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_MESSAGE" => $message,
            "MAIN_ACTION" => $this->pageUrl,
            "HEAD_TITLE" => "Title",
            "HEAD_FEE" => "Fees paid to upliners depending on Stage and Levels",
            "HEAD_SPON" => "Sponsor Fee",
            "HEAD_NAMES" => $main_names,
            "MAIN_CONTENT" => $content,
        );
    }

    //------------------------------------------------------------------------------
    function ocd_update ()
    {
        $to_order_index = $this->GetGP ("to_order_index", 0);
        $paid_levels = $this->db->GetOne ("Select depth From `types` Where order_index='$to_order_index'", 0);

        $result = $this->db->ExecuteSql ("Select * From `types` Where cost>0 Order By order_index Asc");

        while ($row = $this->db->FetchInArray ($result))
        {
            $order_index = $row ["order_index"];

            for ($i = 1; $i <= $paid_levels; $i += 1)
            {
                $m_fee = $this->GetValidGP ($i."mfee$order_index", "fee", VALIDATE_NUMERIC_POSITIVE);
                $e_fee = 0;//$this->GetValidGP ($i."efee$order_index", "fee", VALIDATE_NUMERIC_POSITIVE);
                if ($this->errors['err_count'] > 0)
                {
                    $this->Redirect ($this->pageUrl."?st=no");
                }
                else
                {
                    $this->db->ExecuteSql ("Delete From `fees` Where to_order_index='$to_order_index' And from_order_index='$order_index' And plevel='$i'");
                    $this->db->ExecuteSql ("Insert Into `fees` (to_order_index, from_order_index, plevel, fee_member, fee_sponsor) Values ('$to_order_index', '$order_index', '$i', '$m_fee', '$e_fee')");
                }
            }
        }
        $this->db->FreeSqlResult ($result);

        $this->Redirect ($this->pageUrl."?st=ok");

    }

    //------------------------------------------------------------------------------
    function ocd_clear_fee ()
    {
        $this->db->ExecuteSql ("Truncate table `fees`");
        $this->Redirect ($this->pageUrl."?st=cl");
    }

}
//------------------------------------------------------------------------------

$zPage = new ZPage ("fees");

$zPage->Render ();

?>