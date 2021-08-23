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
        XPage::XPage ($object, false);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $m = $this->GetGP("month", 0);
        $endDateYear = $this->GetGP("year", 0);

        if ($m != 0 And $endDateYear != 0)
        {
            $days = getDays ($m, $endDateYear);
            $content2 = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='#FFF8DC' class='w_border'>";
            for ($i = 1; $i <= $days; $i++)
            {
                $startDate =  mktime (0, 0, 0, $m, $i, $endDateYear);
                $finishDate =  mktime (23, 59, 59, $m, $i, $endDateYear);

                $totalForPeriod = $this->db->GetOne ("Select Count(*) From stats_visitors Where thetime>".$startDate." And thetime<".$finishDate);
                $totalUnique = $this->db->GetOne ("Select Count(Distinct ipaddress) From stats_visitors Where thetime>".$startDate." And thetime<".$finishDate);
                $hits = $this->db->GetOne ("Select Count(*) From stats_views Where thetime>".$startDate." And thetime<".$finishDate);
                $quant = $this->db->GetOne ("Select Count(Distinct visitor_id) From stats_views Where thetime>".$startDate." And thetime<".$finishDate);

                $result = $this->db->ExecuteSql ("Select Distinct visitor_id From stats_views Where thetime>".$startDate." And thetime<".$finishDate);
                $s = 0;
                while ($row = $this->db->FetchInArray ($result))
                {
                    $s = $s + $this->db->GetOne ("Select Count(Distinct page) From stats_views Where visitor_id=".$row ['visitor_id']." And thetime>".$startDate." And thetime<".$finishDate);
                };
                $this->db->FreeSqlResult ($result);
                $pages = $s;

                $date =  mktime (0, 0, 0, $m, $i, $endDateYear);
                $date = date ("d", $date);
                $content2 .= "<tr><td align='center' class='w_border'>$date</td>";
                $content2 .= "<td style='width:149px;' align='center' class='w_border'>".$totalForPeriod."</td><td style='width:149px;' align='center' class='w_border'>".$totalUnique."</td><td style='width:149px;' align='center' class='w_border'>".$hits."</td><td style='width:149px;' align='center' class='w_border'>".$pages."</td></tr>";
            };
            $content2 .= "</table>";

            $open_month = $content2."<br><a href='javascript:close_month($m, $endDateYear)'><img src='./images/close_up.png' border='0' align='bottom' title='Hide details' /></a>";
            print $open_month;
            exit ();
        }

        $country = $this->GetGP("country", "");
        $startDate = $this->GetGP("startdate", 0);
        $finishDate = $this->GetGP("finishdate", 0);

        if ($country != "" And $startDate > 0 And $finishDate > 0)
        {
            $count = $this->db->GetOne ("Select Count(distinct city) From `stats_visitors` Where country='$country' And thetime>".$startDate." And thetime<".$finishDate, 0);
            if ($count > 0)
            {
                $result = $this->db->ExecuteSql ("Select distinct city From `stats_visitors` Where country='$country' And thetime>".$startDate." And thetime<".$finishDate." Order By city", 0);
                $content2 = "<table width='100%' cellspacing='0' cellpadding='1' bgcolor='#FFF8DC' class='w_border' border='0'>";
                $content2 .= "<tr bgcolor='#FAEBD7'><td class='w_border' align='center'><b>Cities</b></td><td class='w_border' align='center'><b>Visitors</b></td></tr>";
                while ($row = $this->db->FetchInArray ($result))
                {
                    $city = $row ["city"];
                    $count_city = $this->db->GetOne ("Select Count(*) From `stats_visitors` Where city='$city' And country='$country'", 0);
                    $content2 .= "<tr><td class='w_border' style='padding-left:5px;'>$city</td><td class='w_border' style='padding-left:5px;'>$count_city</td></tr>";
                }
                $this->db->FreeSqlResult ($result);
                $content2 .= "</table>";
            }

            $result = $content2."<br><a href='javascript:close_country($country, $startDate, $finishDate)'><img src='./images/close_up.png' border='0' align='bottom' title='Hide details' /></a>";
            print $result;
            exit ();

        }

    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("open_month");

$zPage->Render ();

?>