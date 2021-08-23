<?php
//require_once ("ErrorHandler.php");

function d($msg = '', $stop=true, $caption='', $return=false){
    $arrParentInfo = debug_backtrace();
    if (is_null($msg) || is_bool($msg)) $msg = '<span style="color:#0F2FE2">'.var_export($msg,true).'</span>';
    $str='<pre>';
    $str.='---- Debug Info <span style="color: #0F2FE2;">'.$caption.'</span> -- <span style="font-size: 10px;">'.date("Y-m-d H:i:s").'</span> -- <span style="font-size: 10px;color: #A29E9E;">'.str_replace($_SERVER['DOCUMENT_ROOT'], '', $arrParentInfo[0]['file']).' ('.$arrParentInfo[0]['line'].')</span> ----<br>';
    $str.=print_r($msg, true);
    $str.='<br>';
    $str.='-------------------------------------------<br>';
    $str.='</pre>';
    if ( $return ) return $str;
    echo $str;
    if ( $stop ) exit();
}

//------------------------------------------------------------------------------
function getStatus($member_id)
{

    global $db;

    $cycling = $db->GetSetting("cycling", 1);
    $m_level = $db->GetOne("Select `m_level` From `members` Where `member_id`='$member_id'");

    if (($cycling == 1 And $m_level > 0) Or ($cycling == 0 And $m_level > 1)) {
        return "active";
    } else {
        return "inactive";
    }
}

//------------------------------------------------------------------------------
function getTextAdContent($id)
{
    global $db;
    $row = $db->GetEntry("Select * From `text_ads` Where text_ad_id='$id'");
    $id = $row['text_ad_id'];
    $title = decU($row['title']);
    $description1 = decU($row['description1']);
    $description2 = decU($row['description2']);
    $url = decU($row['url']);
    $show_url = $row['show_url'];

    $toRet = "";
    $title = "<a href='$url' target='_blank' class='tadsTitle'>$title</a>";
    $toRet .= $title;

    if ($description1 != "") {
        $toRet .= "<br /><span class='tadsDescr1'>" . $description1 . "</span>";
    }
    if ($description2 != "") {
        $toRet .= "<br /><span class='tadsDescr2'>" . $description2 . "</span>";
    }
    if ($show_url == 1) {
        $toRet .= "<br /><a href='$url' target='_blank' class='tadsURL'>$url</a>";
    }
    return $toRet;


}

//------------------------------------------------------------------------------
function getTextAdContentShow($id)
{
    global $db;
    $row = $db->GetEntry("Select * From `text_ads` Where text_ad_id='$id'");
    $id = $row['text_ad_id'];
    $title = decU($row['title']);
    $description1 = decU($row['description1']);
    $description2 = decU($row['description2']);
    $url = decU($row['url']);
    $show_url = $row['show_url'];

    $toRet = "";
    $title = "<a href='$url' target='_blank' onMouseover='clearTimeout(movedownvar1)' onMouseout='movedown1 ();' class='tadsTitle'>$title</a>";
    $toRet .= $title;

    if ($description1 != "") {
        $toRet .= "<br /><span class='tadsDescr1'>" . $description1 . "</span>";
    }
    if ($description2 != "") {
        $toRet .= "<br /><span class='tadsDescr2'>" . $description2 . "</span>";
    }
    if ($show_url == 1) {
        $toRet .= "<br /><a href='$url' target='_blank' onMouseover='clearTimeout(movedownvar1)' onMouseout='movedown1 ();' class='tadsURL'>$url</a>";
    }
    return $toRet;


}

//------------------------------------------------------------------------------
function selectCategory($value)
{

    global $db;
    $toRet = "<select name='category_id' style='width:400px;'>";

    $result = $db->ExecuteSql("Select * From `categories` Order By `title` Asc");
    while ($row = $db->FetchInArray($result)) {
        $category_id = $row ["category_id"];
        $title = decU($row ["title"]);
        $selected = ($category_id == $value) ? "selected" : "";
        $toRet .= "<option value='" . $category_id . "' $selected>" . $title . "</option>";
    }
    $db->FreeSqlResult($result);
    $toRet .= "</select>";

    return $toRet;

}

//------------------------------------------------------------------------------
function selectCategoryMain($value)
{
    global $db;
    $toRet = "<select name='category_id' style='width:400px;'>";

    $selected = ($value == 0) ? "selected" : "";
    $toRet .= "<option value='0' $selected>Show all products</option>";

    $selected = ($value == -1) ? "selected" : "";
    $toRet .= "<option value='-1' $selected>Show all products without category</option>";

    $result = $db->ExecuteSql("Select * From `categories` Order By `title` Asc");
    while ($row = $db->FetchInArray($result)) {
        $category_id = $row ["category_id"];

        $title = decU($row ["title"]);
        $selected = ($value == $category_id) ? "selected" : "";
        $toRet .= "<option value='" . $category_id . "' $selected>" . $title . "</option>";

    }
    $db->FreeSqlResult($result);
    $toRet .= "</select>";

    return $toRet;
}


//view matrix levels------------------------------------------------------------
//------------------------------------------------------------------------------
function find_number_members_level($member_id, $i)
{
    global $db;
    $k = 1;
    $c = array();
    $b = array();
    $mem_exit = array();
    $c[] = $member_id;
    $depth = $db->GetOne("Select depth From `matrixes` Where matrix_id=1");

    while ($k <= $depth) {
        foreach ($c as $each) {
            $total = $db->GetOne("Select Count(*) From `matrix` Where referrer_id='$each'");
            if ($total > 0) {
                $result = $db->ExecuteSql("Select * From `matrix` Where referrer_id='$each' Order by member_id Asc");
                while ($row = $db->FetchInArray($result)) {
                    $b[] = $row ['member_id'];
                }
                $db->FreeSqlResult($result);
            }
        }
        if ($i == $k) return count($b);

        $mem_exit[] = count($b);
        $c = array();
        $c = $b;
        $b = array();
        $k += 1;
    }
    return $mem_exit;
}

//------------------------------------------------------------------------------
function getMembers($member_id, $i)
{

    global $db;
    $k = 1;
    $c = array();
    $b = array();
    $mem_exit = array();
    $c[] = $member_id;
    $depth = $db->GetOne("Select depth From `matrixes` Where matrix_id=1");

    while ($k <= $depth) {
        foreach ($c as $each) {
            $total = $db->GetOne("Select Count(*) From `matrix` Where referrer_id='$each'");
            if ($total > 0) {
                $result = $db->ExecuteSql("Select * From `matrix` Where referrer_id='$each' Order by member_id Asc");
                while ($row = $db->FetchInArray($result)) {
                    $b[] = $row ['member_id'];
                }
                $db->FreeSqlResult($result);
            }
        }
        if ($i == $k) return $b;

        $mem_exit[] = count($b);
        $c = array();
        $c = $b;
        $b = array();
        $k += 1;
    }
    return $mem_exit;
}

//CYCLING MATRIX----------------------------------------------------------------
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
function getSSSReferrer($places, $m_level)
{
    global $db;

    $my_array = array();
    for ($i = 1; $i <= 1000; $i++) {
        $my_array = array();
        foreach ($places as $place_id) {
            $result = $db->ExecuteSql("Select `place_id` From `places` Where referrer_place_id='$place_id' And m_level='$m_level' Order By `place_id` Asc");
//            print "Select `place_id` From `places` Where referrer_place_id='$place_id' And m_level='$m_level' Order By `place_id` Acs<br />";
            while ($row = $db->FetchInArray($result)) {
                $place_id = $row ['place_id'];
                $is_closed = $db->GetOne("Select Count(*) From `matrices_completed` Where `place_id`='$place_id'", 0);
                if ($is_closed == 0) return $place_id;
                $my_array [] = $place_id;
            }
            $db->FreeSqlResult($result);
        }
        $places = array();
        if (count($places) == 0) break;
        $places = $my_array;
    }
    return 0;

}
//in_matrix(9,1,1); d();
//in_matrix(15, 1,1); d();
//------------------------------------------------------------------------------
function in_matrix($member_id, $enroller_id, $m_level)
{
    global $db;
    $width = $db->GetOne("Select width From `types` Where order_index='$m_level'", 0);
    $depth = $db->GetOne("Select depth From `types` Where order_index='$m_level'");
    $total_to_complete = (pow($width, ($depth + 1)) - 1) / ($width - 1) - 1;
    //if ( $depth<$width ) $total_to_complete = (pow($width, ($depth + 1)) - 1) / ($width - 1) - 1;
    //else $total_to_complete = $width * $depth;
    $count_all_levels = $db->GetOne("Select Count(*) From `types`", 0);

    $start_referrer_place_id = $db->GetOne("Select MIN(place_id) From `places` Where `member_id`='$enroller_id' And m_level='$m_level' And place_id NOT IN (Select place_id From `matrices_completed`)", 0);
    if ($start_referrer_place_id == 0) $start_referrer_place_id = $db->GetOne("Select MIN(place_id) From `places` Where `member_id`='$member_id' And m_level='$m_level' And place_id NOT IN (Select place_id From `matrices_completed`)", 0);

    if ($start_referrer_place_id == 0) {

        $pplaces = array();
        $result = $db->ExecuteSql("Select `place_id` From `places` Where `member_id`='$enroller_id' And m_level='$m_level'");


        while ($row = $db->FetchInArray($result)) {
            $place_id = $row ['place_id'];
            $result2 = $db->ExecuteSql("Select `place_id` From `places` Where referrer_place_id='$place_id' And m_level='$m_level' Order By `place_id` Asc");

            while ($row2 = $db->FetchInArray($result2)) {
                $is_closed = $db->GetOne("Select Count(*) From `matrices_completed` Where `place_id`='" . $row2 ['place_id'] . "'", 0);
                if ($is_closed == 0) {
                    $start_referrer_place_id = $row2 ['place_id'];
                    break;
                }
                $pplaces [] = $row2 ['place_id'];
            }
            $db->FreeSqlResult($result2);
            if ($start_referrer_place_id != 0) break;
        }
        $db->FreeSqlResult($result);

        if ($start_referrer_place_id == 0) $start_referrer_place_id = getSSSReferrer($pplaces, $m_level);
    }

    if ($start_referrer_place_id == 0) $start_referrer_place_id = $db->GetOne("Select MIN(place_id) From `places` Where m_level='$m_level' And place_id NOT IN (Select place_id From `matrices_completed`)", 0);

    $places = array();
    $count = $db->GetOne("Select Count(*) From `places` Where referrer_place_id='$start_referrer_place_id' And referrer_place_id>0", 0);

    if ($count < $width) {
        $referrer_place_id = $start_referrer_place_id;
    } else {
        $result = $db->ExecuteSql("Select place_id From `places` Where referrer_place_id='$start_referrer_place_id' And m_level='$m_level'");
        while ($row = $db->FetchInArray($result)) {
            $places[] = $row ['place_id'];
        }
        $db->FreeSqlResult($result);
        sort($places);
        $referrer_place_id = find_cycling_referrer($width, $places, $m_level);
    }

    $count_entrances = $db->GetOne("Select Count(*) From `places` Where `member_id`='$member_id' And `m_level`='$m_level'", 0) + 1;

/*
    $count_entrances = $db->GetOne ("Select Count(*) From `places` Where `member_id`='$member_id' And `m_level`='$m_level'", 0) + 1;
    if ($count_entrances>=1){
        $referrer_place_id = $db->GetOne ("Select MIN(place_id) From `places` Where m_level='$m_level' And cl<".$width, 0);
    }
    $db->ExecuteSql ("Update `places` Set `cl`=`cl`+1 Where `place_id`='$referrer_place_id'");
*/    
    $db->ExecuteSql("Insert Into `places` (member_id, referrer_place_id, m_level, z_date, reentry) Values ('$member_id', '$referrer_place_id', '$m_level', '" . time() . "', '$count_entrances')");

    $place_id = $db->GetInsertID();

    // checking if it closes some matrix...
    for ($i = 1; $i <= $depth; $i++) {
        $top_place_id = $db->GetOne("Select `referrer_place_id` From `places` Where `place_id`='$place_id' And `m_level`='$m_level'", 0);
        if ($top_place_id == 0) break;
        $place_id = $top_place_id;
    }

    if ($top_place_id > 0) {
        $count_under_place_id = array();
        $count_under_place_id = getUnderTopPlaces($top_place_id, $depth, $m_level);
        //matrix is completed

        if (Count($count_under_place_id) == $total_to_complete) {
            $hoster_id = $db->GetOne("Select `member_id` From `places` Where `place_id`='$top_place_id'", 0);
            $is_there = $db->GetOne("Select Count(*) From `matrices_completed` Where `place_id`='$top_place_id'", 0);
            if ($is_there != 0) return;
            $db->ExecuteSql("Insert Into `matrices_completed` (place_id, z_date) Values ('$top_place_id', '" . time() . "')");

            //payment for completed matrix
            $sum_to_cash = $db->GetOne("Select host_fee From `types` Where order_index='$m_level'", 0);
            $count_matrices = $db->GetOne("Select Count(*) From `places` Where `m_level`='$m_level' And `member_id`='$hoster_id' And `place_id`<=$top_place_id", 0);
            if ($sum_to_cash > 0) {
                $db->ExecuteSql("Insert Into `cash` (amount, type_cash, from_id, to_id, cash_date, descr, payment_id) Values ('$sum_to_cash', '$m_level', '$count_matrices', '$hoster_id', '" . time() . "', 'For completed matrix', '0')");

                //sending notification email
                $row = $db->GetEntry("Select * From `emailtempl` Where `emailtempl_id`='13'", "");
                if ($row ["is_active"] == 1) {
                    $SiteTitle = $db->GetSetting("SiteTitle");
                    $ContactEmail = $db->GetSetting("ContactEmail");
                    $header = "From: $SiteTitle <$ContactEmail>\r\n";

                    $first_name = decU($db->GetOne("Select first_name From `members` Where member_id='$hoster_id'"));
                    $last_name = decU($db->GetOne("Select last_name From `members` Where member_id='$hoster_id'"));
                    $email = $db->GetOne("Select email From `members` Where member_id='$hoster_id'");
                    $username = $db->GetOne("Select username From `members` Where member_id='$hoster_id'");

                    $matrix_title = $db->GetOne("Select title From `types` Where order_index='$m_level'", "");

                    $subject = decU($row ["subject"]);
                    $message = decU($row ["message"]);
                    $subject = preg_replace("/\[SiteTitle\]/", $SiteTitle, $subject);

                    $message = preg_replace("/\[SiteTitle\]/", $SiteTitle, $message);
                    $message = preg_replace("/\[FirstName\]/", $first_name, $message);
                    $message = preg_replace("/\[LastName\]/", $last_name, $message);
                    $message = preg_replace("/\[Username\]/", $username, $message);
                    $message = preg_replace("/\[Amount\]/", $sum_to_cash, $message);
                    $message = preg_replace("/\[Matrix\]/", $matrix_title, $message);

                    sendMail($email, $subject, $message, $header);
                }
            }

            // Pay bonus to enroller
            $enroller_id = $db->GetOne("Select enroller_id From `members` Where member_id='$hoster_id'", 0);
            if ($enroller_id > 0) {
                $sum_to_enr = $db->GetOne("Select enr_fee From `types` Where order_index='$m_level'", 0);
                if ($sum_to_enr > 0) {
                    $db->ExecuteSql("Insert Into `cash` (amount, type_cash, from_id, to_id, cash_date, descr, payment_id) Values ('$sum_to_enr', '$m_level', '$hoster_id', '$enroller_id', '" . time() . "', 'To sponsor', '0')");

                    //sending notification email
                    $row = $db->GetEntry("Select * From `emailtempl` Where `emailtempl_id`='14'", "");
                    if ($row ["is_active"] == 1) {
                        $SiteTitle = $db->GetSetting("SiteTitle");
                        $ContactEmail = $db->GetSetting("ContactEmail");
                        $header = "From: $SiteTitle <$ContactEmail>\r\n";

                        $first_name = decU($db->GetOne("Select first_name From `members` Where member_id='$enroller_id'"));
                        $last_name = decU($db->GetOne("Select last_name From `members` Where member_id='$enroller_id'"));
                        $email = $db->GetOne("Select email From `members` Where member_id='$enroller_id'");
                        $username = $db->GetOne("Select username From `members` Where member_id='$enroller_id'");

                        $matrix_title = $db->GetOne("Select title From `types` Where order_index='$m_level'", "");

                        $subject = decU($row ["subject"]);
                        $message = decU($row ["message"]);
                        $subject = preg_replace("/\[SiteTitle\]/", $SiteTitle, $subject);

                        $message = preg_replace("/\[SiteTitle\]/", $SiteTitle, $message);
                        $message = preg_replace("/\[FirstName\]/", $first_name, $message);
                        $message = preg_replace("/\[LastName\]/", $last_name, $message);
                        $message = preg_replace("/\[Username\]/", $username, $message);
                        $message = preg_replace("/\[Amount\]/", $sum_to_enr, $message);
                        $message = preg_replace("/\[Matrix\]/", $matrix_title, $message);
                        $message = preg_replace("/\[RefID\]/", $hoster_id, $message);

                        sendMail($email, $subject, $message, $header);

                    }

                }

            }

            $admin_fee = $db->GetOne("Select admin_fee From `types` Where order_index='$m_level'", 0);
            if ($admin_fee >0) $db->ExecuteSql("Insert Into `cash` (amount, type_cash, from_id, to_id, cash_date, descr, payment_id) Values ('-$admin_fee', '$m_level', '1', '$hoster_id', '" . time() . "', 'Admin fee', '0')");

            // Re-cycling on this level
            in_matrix($hoster_id, $enroller_id, $m_level);
            // Deduct cash for re-cycling
            $sum_minus_cash = $db->GetOne("Select cost From `types` Where order_index='$m_level'", 0);
            if ($sum_minus_cash > 0) $db->ExecuteSql("Insert Into `cash` (amount, type_cash, from_id, to_id, cash_date, descr, payment_id) Values ('-$sum_minus_cash', '$m_level', '$count_matrices', '$hoster_id', '" . time() . "', 'For re-cycling', '0')");

            in_matrix($hoster_id, $enroller_id, $m_level);
            // Deduct cash for re-cycling
            $sum_minus_cash = $db->GetOne("Select cost From `types` Where order_index='$m_level'", 0);
            if ($sum_minus_cash > 0) $db->ExecuteSql("Insert Into `cash` (amount, type_cash, from_id, to_id, cash_date, descr, payment_id) Values ('-$sum_minus_cash', '$m_level', '$count_matrices', '$hoster_id', '" . time() . "', 'For re-cycling', '0')");

            

            //next level re-entry
            $next_level = $m_level + 1;
            // проверяем открыто ли место у хостера 
            $checkNewPlace = $db->GetOne("Select Count(*) From `places` Where `member_id`='$hoster_id' And `m_level`='$next_level'", 0);
            if (/*$checkNewPlace == 0 &&*/ $next_level <= $count_all_levels) {
                $member_level = $db->GetOne("Select `m_level` From `members` Where member_id='$hoster_id'", 0);
                if ($member_level < $next_level) $db->ExecuteSql("Update `members` Set `m_level`='$next_level' Where `member_id`='$hoster_id'");

                $count_matrices = $db->GetOne("Select Count(*) From `places` Where `m_level`='$next_level' And `member_id`='$hoster_id'", 0) + 1;

                //  Deduct cash for re-cycling
                $sum_minus_cash = $db->GetOne("Select cost From `types` Where order_index='$next_level'", 0);
                if ($sum_minus_cash > 0) $db->ExecuteSql("Insert Into `cash` (amount, type_cash, from_id, to_id, cash_date, descr, payment_id) Values ('-$sum_minus_cash', '$next_level', '$count_matrices', '$hoster_id', '" . time() . "', 'For re-cycling', '0')");

                // Re-cycling on the next level
                in_matrix($hoster_id, $enroller_id, $next_level);
            }

        }

    }

    return;
}

//------------------------------------------------------------------------------
function getUnderTopPlaces($top_place_id, $depth, $m_level)
{
    global $db;
    $k = 1;
    $c = array();
    $b = array();
    $mem_exit = array();
    $c[] = $top_place_id;


    while ($k <= $depth) {
        foreach ($c as $each) {
            $total = $db->GetOne("Select Count(*) From `places` Where `referrer_place_id`='$each' And `m_level`='$m_level'");
            if ($total > 0) {
                $result = $db->ExecuteSql("Select `place_id` From `places` Where `referrer_place_id`='$each' And `m_level`='$m_level' Order by `place_id` Asc");
                while ($row = $db->FetchInArray($result)) {
                    $b[] = $row ['place_id'];
                }
                $db->FreeSqlResult($result);
            }
        }
        $mem_exit = array_merge($mem_exit, $b);

        $c = array();
        $c = $b;
        $b = array();
        $k++;

    }
    return $mem_exit;

}

//------------------------------------------------------------------------------
function find_cycling_referrer($width, $places, $m_level)
{
    global $db;
    $tot_min = $width;
    $places_new = array();
    $referrer_id = 0;

    foreach ($places as $each) {
        $total_ref = $db->GetOne("Select Count(Distinct place_id) From `places` Where referrer_place_id='$each' And `m_level`='$m_level'", 0);
        if ($total_ref == 0) return $each;
        if ($total_ref < $width) {
            if ($total_ref < $tot_min) {
                $tot_min = $total_ref;
                $referrer_id = $each;
            }
        } else {
            $result = $db->ExecuteSql("Select Distinct place_id From `places` Where referrer_place_id='$each' And `m_level`='$m_level'");
            while ($row = $db->FetchInArray($result)) {
                $places_new[] = $row ['place_id'];
            }
            $db->FreeSqlResult($result);
        }
    }

    if ($referrer_id > 0) return $referrer_id;
    sort($places_new);
    $referrer_id = find_cycling_referrer($width, $places_new, $m_level);
    return $referrer_id;
}


//View of the matrix From Admin Area--------------------------------------------
//------------------------------------------------------------------------------
function matrix_tree_admin_set($host_id, $m_level)
{
    global $db, $dict;
    $cycling = $db->GetSetting("cycling", 1);
    switch ($cycling) {
        case 1:
            $count = $db->GetOne("Select Count(*) From `places` Where member_id='$host_id' And m_level='$m_level'", 1);
            $width = $db->GetOne("Select width From `types` Where order_index='$m_level'");
            $depth = $db->GetOne("Select depth From `types` Where order_index='$m_level'");
            $content = "";

            $result = $db->ExecuteSql("Select `place_id` From `places` Where `member_id`='$host_id' And `m_level`='$m_level' Order by place_id Asc");
            $i = 0;
            while ($row = $db->FetchInArray($result)) {
                $i++;
                $place_id = $row['place_id'];
                $cont = "";
                $content .= "<h3 style='text-align:center;'>{$dict['MX_Title']} #" . $i . "</h3>";
                $content .= matrix_tree_admin_cycl($host_id, $m_level, $place_id, $width, $depth) . "<br />";

            }
            break;

        case 0:
            $depth = $db->GetOne("Select depth From `matrixes` Where matrix_id=1");
            $a = $host_id;
            $content = "";
            $level = 0;
            $content = matrix_tree_admin_forc($a, $content, $host_id, $level, $depth);
            break;
    }
    return $content;
}

//------------------------------------------------------------------------------
function matrix_tree_admin_cycl($host_id, $m_level, $place_id, $width, $depth)
{
    global $db;

    $content = "<table class='b_border' style='background-color:#EEEEEE;' cellpadding='0' cellspacing='2' align='center' width='100%'>";


    $member = $db->GetEntry("Select * From `members` Where member_id='$host_id'");
    $name = decU($member ["first_name"] . " " . $member ["last_name"]);
    $mm_level = $member ["m_level"];


    $mm_level = $db->GetOne("Select title From `types` Where `order_index`='$mm_level'", "");
    $reentry = $db->GetOne("Select `reentry` From `places` Where `place_id`='$place_id'");

    $content .= "<tr><td style='text-align:center;'><b>$name (R#$reentry)</b><br />ID:$host_id<br />Level:$mm_level</td></tr><tr><td>";

    $content .= get_width_ref($host_id, $place_id, $width, $depth, 0);

    $date_complete = $db->GetOne("Select `z_date` From `matrices_completed` Where place_id='$place_id'", 0);
    $content .= ($date_complete > 0) ? "</td></tr><tr><td class='f_border'><b>Status:</b> Completed " . date("d M Y H:i", $date_complete) . "</td><tr>" : "</td></tr><tr><td><b>Status:</b> Incompleted</td><tr>";
    $content .= "</table>";

    return $content;
}


//------------------------------------------------------------------------------
function get_width_ref($host_id, $place_id, $width, $depth, $sts)
{
    global $db;

    $sts++;
    $count_ref = $db->GetOne("Select Count(*) From `places` Where `referrer_place_id`='$place_id'", 0);
    $percent = 100 / $width;
    $to_ret = "<table style='height:50px;' cellpadding='0' cellspacing='2' align='center' class='b_border' width='100%'><tr>";
    if ($count_ref > 0) {
        $result = $db->ExecuteSql("Select * From `places` Where `referrer_place_id`='$place_id'");
        while ($row = $db->FetchInArray($result)) {
            $new_place_id = $row['place_id'];
            $member_id = $row['member_id'];
            $reentry = $row['reentry'];

            $member_info = getMemberInfoAdmin($member_id, $reentry, $host_id);


            $to_ret .= "<td style='text-align:center;width:$percent%' border=1><div style='height:50px;'>$member_info</div>";
            if ($sts < $depth) {
                $to_ret .= get_width_ref($host_id, $new_place_id, $width, $depth, $sts);
            }

            $to_ret .= "</td>";
        }
        $db->FreeSqlResult($result);
    }
    if ($count_ref < $width) {
        $number = $width - $count_ref;
        for ($i = 1; $i <= $number; $i += 1) {
            $to_ret .= "<td style='text-align:center;width:$percent%'><div style='height:50px;'><img src='./images/grey_man.png' border='0' alt='Vacant Place'></div>";
            if ($sts < $depth) {
                $to_ret .= get_width_ref($host_id, -1, $width, $depth, $sts);
            }
            $to_ret .= "</td>";
        }
    }
    $to_ret .= "</tr></table>";

    return $to_ret;

}

//------------------------------------------------------------------------------
function getMemberInfoAdmin($member_id, $reentry, $host_id)
{
    global $db;
    $is_dead = $db->GetOne("Select `is_dead` From `members` Where member_id='$member_id'", 1);
    if ($is_dead == 0) {
        $member = $db->GetEntry("Select * From `members` Where member_id='$member_id'");
        $name = decU($member ["first_name"] . " " . $member ["last_name"]);
        $mm_level = $member ["m_level"];
        $loc_enroller = $member ["enroller_id"];

        $mm_level = $db->GetOne("Select title From `types` Where `order_index`='$mm_level'", "");

        $add = "Id:$member_id, spon: $loc_enroller";

//            	print "$loc_enroller  == $host_id <br />";

        $add2 = ($loc_enroller == $host_id) ? " <b>(R#$reentry)</b>" : " (R#$reentry)";
        $toRet = "<a class='menu' href='memb_matrix.php?id=$member_id'>$name</a>$add2<br />$add<br />Level:$mm_level<br />";
    } else {
        $toRet = "<img src='./images/red_man.gif' border='0' alt='Removed member' title='Removed member' />";
    }
    return $toRet;
}

//------------------------------------------------------------------------------
function matrix_tree_admin_forc($a, $content, $id, $level, $depth, $full = -1)
{
    global $db;
    $total = $db->GetOne("Select Count(*) From `matrix` Where referrer_id='$a'");
    $sql = ($full == -1) ? "" : " And a.host_id='$id' And a.matrix_id='$full' ";
    if ($total > 0) {
        $result = $db->ExecuteSql("Select * From `matrix` a, `members` b Where a.member_id=b.member_id And a.referrer_id='$a' $sql Order by a.member_id Asc");
        while ($row = $db->FetchInArray($result)) {
            $enroller_id = $row['enroller_id'];
            $m_level = $row['m_level'];
            $m_level_title = $db->GetOne("Select title From `types` Where order_index='$m_level'", "No level");
            $level += 1;
            $w = ($depth - $level) * 120;
            $content .= "<table cellpadding='0' cellspacing='0' align='center' class='n_border'><tr height='5'><td></td></tr></table>";
            $content .= "<table cellpadding='0' cellspacing='0' align='center' class='n_border'>";
            $content .= ($row['enroller_id'] == $id) ? "<tr><td bgcolor='#EBEEF0' align='center' width='110' valign='middle' class='n_border'><b>" . $row['first_name'] . "<br />" . $row['last_name'] . "</b><br />" : "<tr><td bgcolor='#EBEEF0' align='center' width='110' valign='middle' class='n_border'>" . $row['first_name'] . "<br />" . $row['last_name'] . "<br />";
            $content .= "<a class='super_small' href='forced_matrix.php?id=" . $row['member_id'] . "'>(ID:" . $row['member_id'] . ")</a><br />";
            $enroller_id = ($enroller_id > 0) ? $enroller_id : "sys";
            $content .= "<span class='super_small'>Enr.id=" . $enroller_id . "</span><br />";
            $content .= "<span class='super_small'>Level=" . $m_level_title . "</span>";
            $content .= "</td>";
            $content .= "<td width='$w' align='left' border='0' valign='middle' nowrap>";
            $a = $row['member_id'];
            if ($level <= $depth - 1) {
                $content = matrix_tree_admin_forc($a, $content, $id, $level, $depth, $full);
                $level -= 1;
            } else {
                $level -= 1;
            }
            $content .= "</td></tr></table>";
            $content .= "<table cellpadding='0' cellspacing='0' align='center' class='n_border'><tr height='5'><td></td></tr></table>\r\n";
        }
        $db->FreeSqlResult($result);
    }
    return $content;
}


//View of the matrix From Member Area--------------------------------------------
//------------------------------------------------------------------------------
function matrix_tree_member_set($host_id, $m_level)
{
    global $db, $dict;
    $cycling = $db->GetSetting("cycling", 1);
    switch ($cycling) {
        case 1:
            $count = $db->GetOne("Select Count(*) From `places` Where member_id='$host_id' And m_level='$m_level'", 1);
            $width = $db->GetOne("Select width From `types` Where order_index='$m_level'");
            $depth = $db->GetOne("Select depth From `types` Where order_index='$m_level'");
            $content = "";

            $result = $db->ExecuteSql("Select `place_id` From `places` Where `member_id`='$host_id' And `m_level`='$m_level' Order by place_id Asc");
            $i = 0;
            while ($row = $db->FetchInArray($result)) {
                $i++;
                $place_id = $row['place_id'];
                $cont = "";
                $content .= "<h4 style='text-align:center;'>{$dict['MX_Title']} #" . $i . "</h4>";
                $content .= matrix_tree_member_cycl($host_id, $m_level, $place_id, $width, $depth) . "<br />";

            }
            break;

        case 0:
            $depth = $db->GetOne("Select depth From `matrixes` Where matrix_id=1");
            $a = $host_id;
            $content = "";
            $level = 0;

            $paid_levels = $db->GetOne("Select depth From `types` Where order_index='$m_level'", 0);

            $content = matrix_tree_member_forc($a, $content, $host_id, $level, $depth, $paid_levels);
            break;
    }
    return $content;
}

//------------------------------------------------------------------------------
function matrix_tree_member_forc($a, $content, $id, $level, $depth, $paid_levels, $full = -1)
{
    global $db, $dict;
    $total = $db->GetOne("Select Count(*) From `matrix` Where referrer_id='$a'");
    $sql = ($full == -1) ? "" : " And a.host_id='$id' And a.matrix_id='$full' ";
    if ($total > 0) {
        $result = $db->ExecuteSql("Select * From `matrix` a, `members` b Where a.member_id=b.member_id And a.referrer_id='$a' $sql Order by a.member_id Asc");
        while ($row = $db->FetchInArray($result)) {
            $enroller_id = $row['enroller_id'];
            $m_level = $row['m_level'];
            $m_level_title = $db->GetOne("Select title From `types` Where order_index='$m_level'", "No level");
            $level += 1;
            $w = ($depth - $level) * 120;
            $content .= "<table cellpadding='0' cellspacing='0' align='center'><tr height='5'><td></td></tr></table>";
            $content .= "<table cellpadding='0' cellspacing='0' align='center'>";

//            $bg_color = ($level <= $paid_levels)? "#eed75c" : "#bbdeef";
            $bg_color = "#4b596b";

            $content .= ($row['enroller_id'] == $id) ? "<tr><td bgcolor='$bg_color' style='text-align:center;' width='110' valign='middle' class='all_b_border'><b>" . $row['first_name'] . "<br />" . $row['last_name'] . "</b><br />" : "<tr><td bgcolor='$bg_color' style='text-align:center;' width='110' valign='middle' class='all_b_border'>" . $row['first_name'] . "<br />" . $row['last_name'] . "<br />";
            $content .= "<a href='contact.php?s=" . $row['member_id'] . "'><img src='./images/mail.png' border='0' alt='Email to member'></a>&nbsp;<span class='super_small'>(ID:" . $row['member_id'] . ")</span><br />";
            $enroller_id = ($enroller_id > 0) ? $enroller_id : "sys";
            $content .= "<span class='super_small'>Enr.id=" . $enroller_id . "</span><br />";
            $content .= "<span class='super_small'>{$dict['MX_Level']}:" . $m_level_title . "</span>";
            $content .= "</td>";
            $content .= "<td width='$w' align='left' border='0' valign='middle' nowrap>";
            $a = $row['member_id'];
            if ($level <= $depth - 1) {
                $content = matrix_tree_member_forc($a, $content, $id, $level, $depth, $paid_levels, $full);
                $level -= 1;
            } else {
                $level -= 1;
            }
            $content .= "</td></tr></table>";
            $content .= "<table cellpadding='0' cellspacing='0' align='center' class='n_border'><tr height='5'><td></td></tr></table>\r\n";
        }
        $db->FreeSqlResult($result);
    }
    return $content;
}

//------------------------------------------------------------------------------
function matrix_tree_member_cycl($host_id, $m_level, $place_id, $width, $depth)
{
    global $db, $dict;

    $mm_level = $db->GetOne("Select m_level From `members` Where member_id='$host_id'", "");
    if ($mm_level >= $m_level) {
        $content = "<table style='border:1px solid #cccccc;' cellpadding='2' cellspacing='2' align='center' width='100%'>";

        $member = $db->GetEntry("Select * From `members` Where member_id='$host_id'");
        $name = decU($member ["first_name"] . " " . $member ["last_name"]);
        $mm_level = $member ["m_level"];
        $mm_level = $db->GetOne("Select title From `types` Where `order_index`='$mm_level'", "");
        $reentry = $db->GetOne("Select `reentry` From `places` Where `place_id`='$place_id'");

        $content .= "<tr><td style='border:1px solid #cccccc;text-align:center;background-color:#004FAB;color:white'>$name (R#$reentry)<br />ID:$host_id<br />{$dict['MX_Level']}:$mm_level</td></tr><tr><td>";
        $content .= get_width_ref_member($host_id, $place_id, $width, $depth, 0);
        $date_complete = $db->GetOne("Select `z_date` From `matrices_completed` Where place_id='$place_id'", 0);
        $content .= ($date_complete > 0) ? "</td></tr><tr><td style='background-color:#9ED900; color:white'>{$dict['MX_Status']}: <b>{$dict['MX_Completed']} " . date("d M Y H:i", $date_complete) . "</b></td><tr>" : "</td></tr><tr><td style='background-color:#004FAB; color:white;'>{$dict['MX_Status']}: <b>{$dict['MX_Incompleted']}</b></span></td><tr>";
        $content .= "</table>";


    } else {
        $content = "<table class='c_border' style='background-color:#76889d;' cellpadding='0' cellspacing='4' align='center' width='100%'>";
        $content .= "<tr></td></tr><tr><td class='c_border'><b>&nbsp;Status:</b> <span class='answer' style='color:#ffffff;'>Not entered yet</span></td><tr>";
        $content .= "</table>";
    }


    return $content;
}

//------------------------------------------------------------------------------
function get_width_ref_member($host_id, $place_id, $width, $depth, $sts)
{
    global $db;
    $sts++;
    $count_ref = $db->GetOne("Select Count(*) From `places` Where `referrer_place_id`='$place_id'", 0);
    $percent = 100 / $width;
    $to_ret = "<table style='height:50px;' cellpadding='0' cellspacing='2' align='center' class='c_border' width='100%'><tr>";
    if ($count_ref > 0) {
        $result = $db->ExecuteSql("Select * From `places` Where `referrer_place_id`='$place_id'");
        while ($row = $db->FetchInArray($result)) {
            $new_place_id = $row['place_id'];
            $member_id = $row['member_id'];
            $reentry = $row['reentry'];

            $member_info = getMemberInfo($member_id, $reentry, $host_id);

            $to_ret .= "<td style='text-align:center;width:$percent%' border=1><div style='font-size:10px;height:80px;'>$member_info</div>";

            if ($sts < $depth) {
                $to_ret .= get_width_ref_member($host_id, $new_place_id, $width, $depth, $sts);
            }

            $to_ret .= "</td>";
        }
        $db->FreeSqlResult($result);
    }
    if ($count_ref < $width) {
        $number = $width - $count_ref;
        for ($i = 1; $i <= $number; $i += 1) {
            $to_ret .= "<td style='text-align:center;width:$percent%'><div style='height:80px;'><img src='./images/grey_man.png' border='0' alt='Vacant Place' title='Vacant Place' /></div>";
            if ($sts < $depth) {
                $to_ret .= get_width_ref_member($host_id, -1, $width, $depth, $sts);
            }
            $to_ret .= "</td>";
        }
    }
    $to_ret .= "</tr></table>";

    return $to_ret;

}

//------------------------------------------------------------------------------
function getMemberInfo($member_id, $reentry, $host_id)
{
    global $db;
    $is_dead = $db->GetOne("Select `is_dead` From `members` Where member_id='$member_id'", 1);
    if ($is_dead == 0) {
        $member = $db->GetEntry("Select * From `members` Where member_id='$member_id'");
        $name = decU($member ["first_name"] . " " . $member ["last_name"]);
        $mm_level = $member ["m_level"];
        $loc_enroller = $member ["enroller_id"];

        $mm_level = $db->GetOne("Select title From `types` Where `order_index`='$mm_level'", "");

        $add = "Id:$member_id, spon: $loc_enroller";
        $add2 = ($loc_enroller == $host_id) ? "<b>$name (R#$reentry)</b>" : "$name (R#$reentry)";

        $toRet = "$add2<br /><a href='contact.php?s=$member_id'><img src='./images/mail.png' border='0' alt='Email to member' title='Email to member' /></a><br />$add<br /><span class='super_small'>Level:$mm_level</span>";
    } else {
        $toRet = "<img src='./images/red_man.gif' border='0' alt='Removed member' title='Removed member' />";
    }
    return $toRet;
}

//FORCED MATRIX-----------------------------------------------------------------
//------------------------------------------------------------------------------
function in_forced_matrix($member_id, $enroller_id)
{
    global $db;
    $width = $db->GetOne("Select width From `matrixes` Where matrix_id=1", 0);
    $members = array();
    $count = $db->GetOne("Select Count(*) From `matrix` Where referrer_id='$enroller_id'", 0);
    $nowDate = time();
    if ($count < $width) {
        $referrer_id = $enroller_id;
    } else {
        $result = $db->ExecuteSql("Select member_id From `matrix` Where referrer_id='$enroller_id'", 0);
        while ($row = $db->FetchInArray($result)) {
            $members[] = $row ['member_id'];
        }
        $db->FreeSqlResult($result);
        sort($members);
        $referrer_id = find_referrer($width, $members, $enroller_id);
    }
    $db->ExecuteSql("Insert Into `matrix` (host_id, referrer_id, member_id, z_date) Values ('$enroller_id', '$referrer_id', '$member_id', '$nowDate')");

    return;

}

//------------------------------------------------------------------------------
function find_referrer($width, $members, $enroller_id)
{
    global $db;
    $tot_min = $width;
    $members_new = array();
    $referrer_id = 0;

    foreach ($members as $each) {
        $total_ref = $db->GetOne("Select Count(Distinct member_id) From `matrix` Where referrer_id='$each'", 0);
        if ($total_ref == 0) return $each;
        if ($total_ref < $width) {
            if ($total_ref < $tot_min) {
                $tot_min = $total_ref;
                $referrer_id = $each;
            }
        } else {
            $result = $db->ExecuteSql("Select Distinct member_id From `matrix` Where referrer_id='$each'");
            while ($row = $db->FetchInArray($result)) {
                $members_new[] = $row ['member_id'];
            }
            $db->FreeSqlResult($result);
        }
    }

    if ($referrer_id > 0) return $referrer_id;
    sort($members_new);
    $referrer_id = find_referrer($width, $members_new, $enroller_id);
    return $referrer_id;
}

//------------------------------------------------------------------------------
function out_matrix($member_id, $enroller_id)
{
    global $db;
    $matrix_mode = $db->GetSetting("matrix_mode", 2);

    switch ($matrix_mode) {
        case 2:

            $db->ExecuteSql("Update `matrix` Set host_id=1 Where host_id='$member_id'");
            $db->ExecuteSql("Update `members` Set enroller_id=1 Where enroller_id='$member_id'");

            break;

        case 3:

            $new_host = $db->GetOne("Select enroller_id From `members` Where member_id='$member_id'", 0);
            $db->ExecuteSql("Update `matrix` Set host_id='$new_host' Where host_id='$member_id'");
            $db->ExecuteSql("Update `members` Set enroller_id='$new_host' Where enroller_id='$member_id'");

            break;
    }

    $count = $db->GetOne("Select Count(*) From `matrix` Where referrer_id='$member_id'", 0);
    if ($count > 0) {
        $ref_id = $db->GetOne("Select referrer_id From `matrix` Where member_id='$member_id'", 0);
        $result = $db->ExecuteSql("Select member_id From `matrix` Where referrer_id='$member_id' Order by member_id Asc");
        while ($row = $db->FetchInArray($result)) {
            $mem_id[] = $row['member_id'];
        }
        $db->FreeSqlResult($result);
        $db->ExecuteSql("Delete From `matrix` Where member_id='$member_id'");
        re_matrix($mem_id, $ref_id);
        return;
    } else {
        $db->ExecuteSql("Delete From `matrix` Where member_id='$member_id'");
        return;
    }
}

//------------------------------------------------------------------------------
function re_matrix($mem_id, $ref_id)
{
    global $db;
    $mem_next = array();
    $min_id = MIN($mem_id);

    $db->ExecuteSql("Update `matrix` Set referrer_id='$ref_id' Where member_id='$min_id'");
    $count = $db->GetOne("Select Count(*) From `matrix` Where referrer_id='$min_id'", 0);
    if ($count > 0) {
        $result = $db->ExecuteSql("Select Distinct member_id From `matrix` Where referrer_id='$min_id' Order by member_id Asc");
        while ($row = $db->FetchInArray($result)) {
            $mem_next[] = $row['member_id'];
        }
        $db->FreeSqlResult($result);
    }

    foreach ($mem_id as $each) {
        if ($each != $min_id) $db->ExecuteSql("Update `matrix` Set referrer_id='$min_id' Where member_id='$each'");
    }
    if (Count($mem_next) > 0) re_matrix($mem_next, $min_id);
    return;
}

//------------------------------------------------------------------------------
function getNumberDownlines($member_id, $downline)
{
    global $db;
    $depth = $db->GetOne("Select depth From `matrixes` Where matrix_id=1", 0);
    $k = 1;
    $c = array();
    $b = array();
    $mem_exit = array();
    $c[] = $member_id;

    while ($k <= $depth) {
        foreach ($c as $each) {
            $total = $db->GetOne("Select Count(*) From `matrix` Where referrer_id='$each'");
            if ($total > 0) {
                $result = $db->ExecuteSql("Select * From `matrix` Where referrer_id='$each' Order by member_id Asc");
                while ($row = $db->FetchInArray($result)) {
                    $b[] = $row ['member_id'];
                }
                $db->FreeSqlResult($result);
            }
        }
        $mem_exit = array_merge($mem_exit, $b);
        $c = array();
        $c = $b;
        $b = array();
        $k += 1;
    }
    return $mem_exit;
}


//------------------------------------------------------------------------------

function getPayFormCode($member_id, $amount, $processor_id, $product, $level_id)
{
    global $db;

    $sql = $db->GetEntry("Select * From `currency` Where id='" . $db->GetSetting("currency") . "'", "");
    $currency_name = $sql['name'];
    $currency = $sql['symbol'];

    $result = $db->ExecuteSql("Select * From `processors` Where processor_id='$processor_id' And is_active=1");
    if ($row = $db->FetchInArray($result)) {
        $proc_keyname = $row["code"];
        $account_id = $row["account_id"];
        $routine_url = $row["routine_url"];
    } else {
        return "Error: you cannot pay now.";
    }

    $SiteUrl = $db->GetOne("Select value From settings Where keyname='SiteUrl'");
    $SiteTitle = $db->GetOne("Select value From settings Where keyname='SiteTitle'");
    $adminEmail = $db->GetOne("Select value From settings Where keyname='ContactEmail'");

    $cancel_url = $SiteUrl . "member/payment_res.php?res=no";
    $success_url = $SiteUrl . "member/payment_res.php?res=ok";

    $description = $SiteTitle . " " . $product;

//    $description = ($product != "Product Payment")? "$SiteTitle Membership Fee" : "$SiteTitle Membership Fee";

    $code = '';
    switch ($proc_keyname) {
        case "alertpay":
            $code = "<form action='$routine_url' method='POST'> \r\n" .
                "<input type='hidden' name='ap_purchasetype' value='Item'> \r\n" .
                "<input type='hidden' name='ap_merchant' value='$account_id'> \r\n" .
                "<input type='hidden' name='ap_itemname' value='$product'> \r\n" .
                "<input type='hidden' name='ap_description' value='$description'> \r\n" .
                "<input type='hidden' name='ap_returnurl' value='$success_url'> \r\n" .
                "<input type='hidden' name='ap_cancelurl' value='$cancel_url'> \r\n" .
                "<input type='hidden' name='ap_quantity' value='1'> \r\n" .
                "<input type='hidden' name='ap_amount' value='$amount'> \r\n" .
                "<input type='hidden' name='ap_currency' value='$currency_name'> \r\n" .
                "<input type='hidden' name='apc_1' value='$product'> \r\n" .
                "<input type='hidden' name='apc_2' value='$member_id'> \r\n" .
                "<input type='hidden' name='apc_3' value='$level_id'> \r\n" .
                "<button type='submit' class='btn btn-form'><i class='fa fa-check'></i> Pay Now </button> \r\n" .
                "</form>";
            break;

        case "okpay":

            $code = "<form  method='post' action='$routine_url'>
            <input type='hidden' name='ok_receiver' value='$account_id' />
            <input type='hidden' name='ok_item_1_name' value='$product' />
            <input type='hidden' name='ok_currency' value='euro' />
            <input type='hidden' name='ok_item_1_type' value='service' />
            <input type='hidden' name='ok_item_1_price' value='$amount' />
            
            <input type='hidden' name='ok_item_1_custom_1_title' value='Member ID' />
            <input type='hidden' name='ok_item_1_custom_1_value' value='$member_id' />
            
            <input type='hidden' name='ok_item_1_custom_2_title' value='Level ID' />
            <input type='hidden' name='ok_item_1_custom_2_value' value='$level_id' />
            
            <input type='hidden' name='ok_item_1_custom_3_title' value='Product' />
            <input type='hidden' name='ok_item_1_custom_3_value' value='$product' />
            <button type='submit' class='btn btn-form'><i class='fa fa-check'></i> Pay Now </button>
            </form>";

            break;

        case "solidtrustpay":
            $notify = $SiteUrl . "notify/solidtrustpay.php";

            $code = "<form action='$routine_url' method='post'> \r\n
                <input type=hidden name='merchantAccount' value='$account_id' /> \r\n
                <input type=hidden name='amount' value='$amount' /> \r\n
                <input type=hidden name='item_id' value='$description' /> \r\n
                <input type=hidden name='notify_url' value='$notify' /> \r\n
                <input type=hidden name='return_url' value='$success_url' /> \r\n
                <input type=hidden name='cancel_url' value='$cancel_url' /> \r\n

                <input type=hidden name='user1' value='$member_id' /> \r\n
                <input type=hidden name='user2' value='$product' /> \r\n
                <input type=hidden name='user3' value='$level_id' />
                <button type='submit' class='btn btn-form'><i class='fa fa-check'></i> Pay Now </button> \r\n
                </form>";
            break;

        case "paypal":

            $notify = $SiteUrl . "notify/paypal.php";
            $code = "<form action='$routine_url' method='post'> \r\n
               <input type='hidden' name='cmd' value='_xclick'> \r\n
               <input type='hidden' name='business' value='$account_id'> \r\n
               <input type='hidden' name='amount' value='$amount'> \r\n
               <input type='hidden' name='item_number' value='$member_id'> \r\n
               <input type='hidden' name='custom' value='$level_id'> \r\n
               <input type='hidden' name='item_name' value='$product'> \r\n
               <input type='hidden' name='currency_code' value='$currency_name'> \r\n
               <input type='hidden' name='quantity' value='1'> \r\n
               <input type='hidden' name='undefined_quantity' value='0'> \r\n
               <input type='hidden' name='no_shipping' value='1'> \r\n
               <input type='hidden' name='rm' value='2'> \r\n
               <input type='hidden' name='notify_url' value='$notify'> \r\n
               <input type='hidden' name='return' value='$success_url'> \r\n
               <input type='hidden' name='cancel_return' value='$cancel_url'> \r\n
               <button type='submit' class='btn btn-form'><i class='fa fa-check'></i> Pay Now </button> \r\n
               </form>";
            break;

        case "perfectmoney":
            $notify = $SiteUrl . "notify/perfectmoney.php";
            $code = "<form action='$routine_url' method='POST'>
            <input type='hidden' name='PAYEE_ACCOUNT' value='$account_id'>
            <input type='hidden' name='PAYEE_NAME' value='$SiteTitle'>
            <input type='hidden' name='PAYMENT_AMOUNT' value='$amount'>
            <input type='hidden' name='PAYMENT_UNITS' value='$currency_name'>
            <input type='hidden' name='STATUS_URL' value='$notify'>
            <input type='hidden' name='STATUS_URL_METHOD' value='POST'>
            <input type='hidden' name='PAYMENT_URL' value='$success_url'>
            <input type='hidden' name='PAYMENT_URL_METHOD' value='POST'>
            <input type='hidden' name='NOPAYMENT_URL' value='$cancel_url'>
            <input type='hidden' name='NOPAYMENT_URL_METHOD' value='POST'>
            <input type='hidden' name='BAGGAGE_FIELDS' value='MEMBER_ID PRODUCT LEVEL_ID'>
            <input type='hidden' name='MEMBER_ID' value='$member_id'>
            <input type='hidden' name='PRODUCT' value='$product'>
            <input type='hidden' name='LEVEL_ID' value='$level_id'>
            <button type='submit' class='btn btn-form'><i class='fa fa-check'></i> Pay Now </button>
             </form>";


            break;

        case "egopay":
            $notify = $SiteUrl . "notify/egopay.php";
            $code = "
				<form action='$routine_url' method='post'> \r\n
					<input type='hidden' name='store_id' value='$account_id' />
					<input type='hidden' name='amount' value='$amount' />
					<input type='hidden' name='currency' value='$currency_name' />
					<input type='hidden' name='description' value='$product' />
					<input type='hidden' name='cf_1' value='$member_id' />
					<input type='hidden' name='cf_2' value='$level_id' />
					<input type='hidden' name='success_url' value='$success_url' />
					<input type='hidden' name='fail_url' value='$cancel_url' />
					<input type='hidden' name='callback_url' value='$notify' />
					<button type='submit' class='btn btn-form'><i class='fa fa-check'></i> Pay Now </button> \r\n
				</form>";
            break;

        case "bitaps":
            require_once("bitaps/config.php");
            $notify = $SiteUrl . "notify/bitaps.php";
            $currency_rate = $db->GetSetting("currency_rate", "0");

            $data = file_get_contents("https://bitaps.com/api/ticker/bitfinex");
            $respond = json_decode($data, true);
            $currency_rate = sprintf("%f", 1/($respond['usd']));

            $amount_BTC = sprintf("%f", $amount * $currency_rate);// + BTC_COMMIS;
            $label = $product;
            $class = new Bitcoin();
            $code = $class->printForm(
                array(
                    'account_id' => $account_id,
                    'amount' => $amount,
                    'amount_BTC' => $amount_BTC,
                    'm_level' => $level_id,
                    'member_id' => $member_id,
                    'label' => $label,
                    'type' => $product,
                    'notify' => $notify
                )
            );
            break;

        case "blockchain":
            $notify = $SiteUrl . "notify/blockchain.php";
            $currency_rate = $db->GetSetting("currency_rate", "0");

            $data = file_get_contents("https://bitaps.com/api/ticker/bitfinex");//coinbase
            $respond = json_decode($data, true);
            $currency_rate = sprintf("%f", 1/($respond['usd']));

            $amount_BTC = sprintf("%f", $amount * $currency_rate );
            $label = $product;
            require_once("blockchain/config.php");
            $class = new Bitcoin();
            $code = $class->printForm(
                array(
                    'amount' => $amount,
                    'amount_BTC' => $amount_BTC,
                    'm_level' => $level_id,
                    'member_id' => $member_id,
                    'label' => $label,
                    'type' => $product,
                    'notify' => $notify
                )
            );
            break;

        case "coinpayments":
            $notify = $SiteUrl . "notify/coinpayments.php";
            $code = "
				<form action='$routine_url' method='post'> \r\n
                    <input type=\"hidden\" name=\"cmd\" value=\"_pay\">
                    <input type=\"hidden\" name=\"reset\" value=\"1\">
                    <input type=\"hidden\" name=\"merchant\" value=\"$account_id\">
                    <input type=\"hidden\" name=\"currency\" value=\"$currency_name\">
                    <input type=\"hidden\" name=\"amountf\" value=\"$amount\">
                    <input type=\"hidden\" name=\"item_name\" value=\"$product\">		
                    <input type=\"hidden\" name=\"invoice\" value=\"$member_id\">		
                    <input type='hidden' name='custom' value='$level_id' />	
                    <input type=\"hidden\" name=\"success_url\" value=\"$success_url\">	
                    <input type=\"hidden\" name=\"cancel_url\" value=\"$cancel_url\">
                    <input type='hidden' name='ipn_url' value='$notify' />	
                    <input type='hidden' name='allow_currencies' value='LTC,BTC,ETH,TRX,BNB,DOGE,BCH' />	
                    <input type='hidden' name='want_shipping' value='0' />	
                    <button type='submit' class='btn btn-form'><i class='fa fa-check'></i> Pay Now </button> \r\n
				</form>";
            break;

    }
    return $code;
}

//------------------------------------------------------------------------------

function getPayFormCodeAdmin($id, $member_id, $amount, $processor_id, $account_id)
{
    global $db;

    $SiteUrl = $db->GetOne("Select value From settings Where keyname='SiteUrl'");
    $SiteTitle = $db->GetOne("Select value From settings Where keyname='SiteTitle'");
    $adminEmail = $db->GetOne("Select value From settings Where keyname='ContactEmail'");

    $name = $db->GetOne("Select CONCAT(first_name, ' ', last_name) From `members` Where member_id='$member_id'");

    $this_url = $SiteUrl . "admin/cash_out.php";
    $return = $SiteUrl . "admin/cash_out.php";
    $description = $SiteTitle . " Cash Out";
    $sql = $db->GetEntry("Select * From `currency` Where id='" . $db->GetSetting("currency") . "'", "");
    $currency_name = $sql['name'];
    $currency = $sql['symbol'];
    $product = "CashOut";

    $proc_keyname = $db->GetOne("Select code From `processors` Where processor_id='$processor_id'");
    $routine_url = $db->GetOne("Select routine_url From `processors` Where processor_id='$processor_id'");

    $code = '';
    switch ($proc_keyname) {
        case "alertpay":
            $code = "<form action='$routine_url' method='POST'> \r\n" .
                "<input type='hidden' name='ap_purchasetype' value='Item'> \r\n" .
                "<input type='hidden' name='ap_merchant' value='$account_id'> \r\n" .
                "<input type='hidden' name='ap_itemname' value='$product'> \r\n" .
                "<input type='hidden' name='ap_description' value='$description'> \r\n" .
                "<input type='hidden' name='ap_returnurl' value='$return'> \r\n" .
                "<input type='hidden' name='ap_cancelurl' value='$this_url'> \r\n" .
                "<input type='hidden' name='ap_quantity' value='1'> \r\n" .
                "<input type='hidden' name='ap_amount' value='$amount'> \r\n" .
                "<input type='hidden' name='ap_currency' value='$currency_name'> \r\n" .
                "<input type='hidden' name='apc_1' value='$product'> \r\n" .
                "<input type='hidden' name='apc_2' value='$id'> \r\n" .
                "<input type='submit' value='Pay Now - $currency$amount' class='some_btn'> \r\n" .
                "</form>";
            break;

        case "okpay":

            $code = "<form  method='post' action='$routine_url'>
            <input type='hidden' name='ok_receiver' value='$account_id' />
            <input type='hidden' name='ok_item_1_name' value='$product' />
            <input type='hidden' name='ok_currency' value='euro' />
            <input type='hidden' name='ok_item_1_type' value='service' />
            <input type='hidden' name='ok_item_1_price' value='$amount' />
            
            <input type='hidden' name='ok_item_1_custom_1_title' value='ID' />
            <input type='hidden' name='ok_item_1_custom_1_value' value='$id' />
            
            <input type='hidden' name='ok_item_1_custom_3_title' value='Product' />
            <input type='hidden' name='ok_item_1_custom_3_value' value='$product' />
            
            <input type='submit' value=' Pay Now ' class='some_btn'></form>";

            break;

        case "solidtrustpay":
            $notify = $SiteUrl . "notify/solidtrustpay.php";

            $code = "<form action='$routine_url' method='post'> \r\n
                <input type=hidden name='merchantAccount' value='$account_id' /> \r\n
                <input type=hidden name='amount' value='$amount' /> \r\n
                <input type=hidden name='item_id' value='$description' /> \r\n
                <input type=hidden name='notify_url' value='$notify' /> \r\n
                <input type=hidden name='return_url' value='$return' /> \r\n
                <input type=hidden name='cancel_url' value='$this_url' /> \r\n

                <input type=hidden name='user1' value='$id' /> \r\n
                <input type=hidden name='user2' value='$product' /> \r\n
                <input type='submit' value='Pay Now - $currency$amount' class='some_btn'> \r\n
                </form>";
            break;


        case "paypal":

            $notify = $SiteUrl . "notify/paypal.php";
            $code = "<form action='$routine_url' method='post'> \r\n
               <input type='hidden' name='cmd' value='_xclick'> \r\n
               <input type='hidden' name='business' value='$account_id'> \r\n
               <input type='hidden' name='amount' value='$amount'> \r\n
               <input type='hidden' name='item_number' value='$id'> \r\n
               <input type='hidden' name='custom' value='$id'> \r\n
               <input type='hidden' name='item_name' value='$product'> \r\n
               <input type='hidden' name='currency_code' value='$currency_name'> \r\n
               <input type='hidden' name='quantity' value='1'> \r\n
               <input type='hidden' name='undefined_quantity' value='0'> \r\n
               <input type='hidden' name='no_shipping' value='1'> \r\n
               <input type='hidden' name='rm' value='2'> \r\n
               <input type='hidden' name='notify_url' value='$notify'> \r\n
               <input type='hidden' name='return' value='$return'> \r\n
               <input type='hidden' name='cancel_return' value='$this_url'> \r\n
               <input type='submit' value=\" Pay Now - $currency$amount \" class='some_btn' /> \r\n
               </form>";
            break;

        case "perfectmoney":
            $notify = $SiteUrl . "notify/perfectmoney.php";
            $code = "<form action='$routine_url' method='POST'>
            <input type='hidden' name='PAYEE_ACCOUNT' value='$account_id'>
            <input type='hidden' name='PAYEE_NAME' value='$SiteTitle'>
            <input type='hidden' name='PAYMENT_AMOUNT' value='$amount'>
            <input type='hidden' name='PAYMENT_UNITS' value='EUR'>
            <input type='hidden' name='STATUS_URL' value='$notify'>
            <input type='hidden' name='STATUS_URL_METHOD' value='POST'>
            <input type='hidden' name='PAYMENT_URL' value='$return'>
            <input type='hidden' name='PAYMENT_URL_METHOD' value='POST'>
            <input type='hidden' name='NOPAYMENT_URL' value='$this_url'>
            <input type='hidden' name='NOPAYMENT_URL_METHOD' value='POST'>
            <input type='hidden' name='BAGGAGE_FIELDS' value='MEMBER_ID PRODUCT'>
            <input type='hidden' name='MEMBER_ID' value='$id'>
            <input type='hidden' name='PRODUCT' value='$product'>
            <input type='submit' value=\" Pay Now \" class='some_btn'>
             </form>";
            break;

        case "egopay":
            $code = "
				<form action='https://www.egopay.com/payments/pay/$amount/USD/$account_id/$product' method='get'> \r\n
					<input type='submit' value=\" Pay Now - $currency$amount \" class='some_btn' /> \r\n
				</form>";
            break;

    }
    return $code;
}

//------------------------------------------------------------------------------
//payUpline ($id, $thisTime, $level_id, '-1');
function payUpline($member_id, $txnID, $level_id, $processor_id)
{

    global $db;

    $cycling = $db->GetSetting("cycling", 1);
    $amount = ($cycling == 1) ? $db->GetOne("Select entrance_fee From `matrixes` Where matrix_id='2'", 0) : $db->GetOne("Select cost From `types` Where order_index='$level_id'", 0);
    $SiteTitle = $db->GetSetting("SiteTitle");
    $thisTime = time();
    $ContactEmail = $db->GetSetting("ContactEmail");
    $header = "From: $SiteTitle <$ContactEmail>\r\n";

    $first_name = decU($db->GetOne("Select first_name From `members` Where member_id='$member_id'"));
    $last_name = decU($db->GetOne("Select last_name From `members` Where member_id='$member_id'"));
    $email = $db->GetOne("Select email From `members` Where member_id='$member_id'");
    $username = $db->GetOne("Select username From `members` Where member_id='$member_id'");

    //sending notification email regarding successfull payment
    $row = $db->GetEntry("Select * From `emailtempl` Where `emailtempl_id`='11'", "");
    if (!isPreLaunch() && $row ["is_active"] == 1 ) {
        $subject = decU($row ["subject"]);
        $message = decU($row ["message"]);
        $subject = preg_replace("/\[SiteTitle\]/", $SiteTitle, $subject);

        $processor = decU($db->GetOne("Select name From `processors` Where processor_id='$processor_id'", "Manual"));

        $message = preg_replace("/\[SiteTitle\]/", $SiteTitle, $message);
        $message = preg_replace("/\[FirstName\]/", $first_name, $message);
        $message = preg_replace("/\[LastName\]/", $last_name, $message);
        $message = preg_replace("/\[Username\]/", $username, $message);
        $message = preg_replace("/\[Amount\]/", $amount, $message);
        $message = preg_replace("/\[Processor\]/", $processor, $message);

        sendMail($email, $subject, $message, $header);

    }

    switch ($cycling) {
        case "1":
            $m_level = $db->GetOne("Select m_level From `members` Where member_id='$member_id'", 0);

            if ($m_level == 0) {
                $db->ExecuteSql("Update `members` Set m_level=1 Where member_id='$member_id'");
                $m_level = 1;
            }
            $enroller_id = $db->GetOne("Select enroller_id From `members` Where member_id='$member_id'", 0);

            $db->ExecuteSql("Insert into `payins` (member_id, amount, z_date, processor, transaction_id, description) Values ('$member_id', '$amount', '$thisTime', '$processor_id','$txnID', 'Member payment')");

            $sponsor_amount = $db->GetSetting("sponsor_amount", 0);
            $sponsor_quant = $db->GetSetting("sponsor_quant", 0);

            if ($sponsor_amount > 0 And $sponsor_quant > 0 And $enroller_id > 0) {
                $db->ExecuteSql("Insert into `sponsor_bonus` (member_id, sponsored_id, is_bonus, z_date) Values ('$enroller_id', '$member_id', 0, 0)");
                $quant_enr = $db->GetOne("Select Count(*) From `sponsor_bonus` Where member_id='$enroller_id' And is_bonus=0", 0);
                if ($quant_enr == $sponsor_quant) {
                    if ( $db->GetSetting("SPONSOR_VALUE") == 2 ) {
                        $sponsor_amount = sprintf("%01.2f", ($amount/100)*$sponsor_amount);
                    }
                    $db->ExecuteSql("Insert Into `cash` (amount, type_cash, from_id, to_id, cash_date, descr, payment_id) Values ('$sponsor_amount', '2', '$member_id', '$enroller_id', '$thisTime', 'Sponsor Bonus', '0')");
                    $db->ExecuteSql("Update `sponsor_bonus` Set is_bonus=1, z_date='$thisTime' Where member_id='$enroller_id' And is_bonus=0", 0);

                    //sending notification email
                    $row = $db->GetEntry("Select * From `emailtempl` Where `emailtempl_id`='15'", "");
                    if ($row ["is_active"] == 1) {
                        $first_name = decU($db->GetOne("Select first_name From `members` Where member_id='$enroller_id'"));
                        $last_name = decU($db->GetOne("Select last_name From `members` Where member_id='$enroller_id'"));
                        $email = $db->GetOne("Select email From `members` Where member_id='$enroller_id'");
                        $username = $db->GetOne("Select username From `members` Where member_id='$enroller_id'");

                        $subject = decU($row ["subject"]);
                        $message = decU($row ["message"]);
                        $subject = preg_replace("/\[SiteTitle\]/", $SiteTitle, $subject);

                        $message = preg_replace("/\[SiteTitle\]/", $SiteTitle, $message);
                        $message = preg_replace("/\[FirstName\]/", $first_name, $message);
                        $message = preg_replace("/\[LastName\]/", $last_name, $message);
                        $message = preg_replace("/\[Username\]/", $username, $message);
                        $message = preg_replace("/\[Amount\]/", $sponsor_amount, $message);

                        sendMail($email, $subject, $message, $header);

                    }
                }
            }

//            $db->ExecuteSql ("Insert Into `matrices` (member_id, m_level, date_create) Values ('$member_id', '$m_level', '".time ()."')");
//            $matrix_id = $this->db->GetInsertID ();
//            $sum_cost = $db->GetOne ("Select cost From `types` Where order_index='$m_level'", 0);
//            $db->ExecuteSql ("Insert Into `cash` (amount, type_cash, from_id, to_id, cash_date, descr, payment_id) Values ('-$sum_cost', '$m_level', '1', '$member_id', '$thisTime', 'For level 1', '0')");

            in_matrix($member_id, $enroller_id, $m_level);

            break;

        case "0":
            $enroller_id = $db->GetOne("Select enroller_id From `members` Where member_id='$member_id'", 0);
            $title_level = $db->GetOne("Select title From `types` Where order_index='$level_id'", 0);

            $db->ExecuteSql("Update `members` Set m_level='$level_id' Where member_id='$member_id'");

            if ( !isPreLaunch() )
            {
                $db->ExecuteSql("Update `members` Set quant_pay=quant_pay + 1 Where member_id='$member_id'");

                $db->ExecuteSql("Insert into `payins` (member_id, amount, z_date, processor, transaction_id, description) Values ('$member_id', '$amount', '$thisTime', '$processor_id','$txnID', 'Payment for $title_level level')");
                $payment_id = $db->GetInsertID();

                //payment of sponsor bonus
                $pay_spon_bonus = $db->GetOne("Select Count(*) From `sponsor_bonus` Where member_id='$enroller_id' And sponsored_id='$member_id'", 0);
                if ($pay_spon_bonus == 0) {
                    $sponsor_amount = $db->GetSetting("sponsor_amount", 0);
                    $sponsor_quant = $db->GetSetting("sponsor_quant", 0);
                    if ($sponsor_amount > 0 And $sponsor_quant > 0 And $enroller_id > 0) {
                        if ( $db->GetSetting("SPONSOR_VALUE") == 2 ) {
                            $sponsor_amount = sprintf("%01.2f", ($amount/100)*$sponsor_amount);
                        }
                        $db->ExecuteSql("Insert into `sponsor_bonus` (member_id, sponsored_id, is_bonus, z_date) Values ('$enroller_id', '$member_id', 0, 0)");
                        $quant_enr = $db->GetOne("Select Count(*) From `sponsor_bonus` Where member_id='$enroller_id' And is_bonus=0", 0);
                        if ($quant_enr == $sponsor_quant) {
                            $db->ExecuteSql("Insert Into `cash` (amount, type_cash, from_id, to_id, cash_date, descr, payment_id) Values ('$sponsor_amount', '2', '$member_id', '$enroller_id', '$thisTime', 'Sponsor Bonus', '0')");
                            $db->ExecuteSql("Update `sponsor_bonus` Set is_bonus=1, z_date='$thisTime' Where member_id='$enroller_id' And is_bonus=0", 0);

                            //sending notification email
                            $row = $db->GetEntry("Select * From `emailtempl` Where `emailtempl_id`='15'", "");
                            if ($row ["is_active"] == 1) {
                                $first_name = decU($db->GetOne("Select first_name From `members` Where member_id='$enroller_id'"));
                                $last_name = decU($db->GetOne("Select last_name From `members` Where member_id='$enroller_id'"));
                                $email = $db->GetOne("Select email From `members` Where member_id='$enroller_id'");
                                $username = $db->GetOne("Select username From `members` Where member_id='$enroller_id'");

                                $subject = decU($row ["subject"]);
                                $message = decU($row ["message"]);
                                $subject = preg_replace("/\[SiteTitle\]/", $SiteTitle, $subject);

                                $message = preg_replace("/\[SiteTitle\]/", $SiteTitle, $message);
                                $message = preg_replace("/\[FirstName\]/", $first_name, $message);
                                $message = preg_replace("/\[LastName\]/", $last_name, $message);
                                $message = preg_replace("/\[Username\]/", $username, $message);
                                $message = preg_replace("/\[Amount\]/", $sponsor_amount, $message);

                                sendMail($email, $subject, $message, $header);

                            }
                        }
                    }
                }

                //payment of commissions
                $depth = $db->GetOne("Select depth From `matrixes` Where matrix_id='1'");
                $m_real_id = $member_id;
                for ($i = 1; $i <= $depth; $i += 1) {
                    $referrer_id = $db->GetOne("Select referrer_id From `matrix` Where member_id='$member_id'", 0);
                    if ($referrer_id > 0) {
                        $descr = "From $title_level member (#$m_real_id) to ";
                        $sql = "Select ";
                        $ref_level = $db->GetOne("Select m_level From `members` Where member_id='$referrer_id'", 0);
                        $ref_title_level = $db->GetOne("Select title From `types` Where order_index='$ref_level'", 0);
                        $descr .= $ref_title_level;
                        //if ($referrer_id == $enroller_id) {
                        //    $sql .= "fee_sponsor ";
                        //    $descr .= " sponsor";
                        //} else {
                            $sql .= "fee_member ";
                            $descr .= " referrer";
                        //}
                        $sql .= "From `fees` Where to_order_index='$ref_level' And from_order_index='$level_id' And plevel='$i'";

                        $fee = $db->GetOne($sql, "0.00");

                        if ($fee > 0) {
                            if ( $db->GetSetting("Commissions_Value") == 2 ) {
                                $fee = sprintf("%01.2f", ($amount/100)*$fee);
                            }

                            $db->ExecuteSql("Insert into cash (amount, type_cash, from_id, to_id, cash_date, descr, payment_id) values ('$fee', 1, '$m_real_id', '$referrer_id', '" . time() . "', '$descr', '$payment_id')");

                            //matching bonus
                            if ($db->GetSetting("matching_bonus") == 1) {
                                $matcAmount = sprintf("%01.2f", ($fee/100)*$db->GetSetting("matching_bonus_value"));
                                $referrer_enroller_id = $db->GetOne("Select enroller_id From `members` Where member_id='$referrer_id'", 0);
                                if ($db->GetOne("Select count(matrix_id) From `matrix` Where member_id=$referrer_enroller_id ", 0) > 0) {
                                    $descr = "Matching Bonus from #$m_real_id payment";
                                    $db->ExecuteSql("Insert into cash (amount, type_cash, from_id, to_id, cash_date, descr, payment_id) values ($matcAmount, 1, '$m_real_id', '$referrer_enroller_id', '" . time() . "', '$descr', '$payment_id')");
                                }
                            }

                            //sending notification email regarding successfull payment
                            $row = $db->GetEntry("Select * From `emailtempl` Where `emailtempl_id`='12'", "");
                            if ($row ["is_active"] == 1) {
                                $first_name = decU($db->GetOne("Select first_name From `members` Where member_id='$referrer_id'"));
                                $last_name = decU($db->GetOne("Select last_name From `members` Where member_id='$referrer_id'"));
                                $email = $db->GetOne("Select email From `members` Where member_id='$referrer_id'");
                                $username = $db->GetOne("Select username From `members` Where member_id='$referrer_id'");

                                $subject = decU($row ["subject"]);
                                $message = decU($row ["message"]);
                                $subject = preg_replace("/\[SiteTitle\]/", $SiteTitle, $subject);

                                $message = preg_replace("/\[SiteTitle\]/", $SiteTitle, $message);
                                $message = preg_replace("/\[FirstName\]/", $first_name, $message);
                                $message = preg_replace("/\[LastName\]/", $last_name, $message);
                                $message = preg_replace("/\[Username\]/", $username, $message);
                                $message = preg_replace("/\[Amount\]/", $fee, $message);

                                sendMail($email, $subject, $message, $header);
                            }
                        }
                    } else {
                        break;
                    }
                    $member_id = $referrer_id;
                }
            }


            break;
    }


    return;
}

//------------------------------------------------------------------------------
function payProduct($member_id, $txnID, $product_id, $processor_id)
{

    global $db;

    $amount = $db->GetOne("Select price From `products` Where product_id='$product_id'", 0);
    $title = decU($db->GetOne("Select title From `products` Where product_id='$product_id'", ""));
    $db->ExecuteSql("Insert into `payins` (member_id, amount, z_date, processor, transaction_id, description, product_id) Values ('$member_id', '$amount', '" . time() . "', '$processor_id','$txnID', 'Payment for #$product_id product ($title)', '$product_id')");

    $payment_id = $db->GetInsertID();

    $cycling = $db->GetSetting("cycling", 1);

    if ($cycling == 0) {

        //commissions calculation
        $depth = $db->GetOne("Select depth From `matrixes` Where matrix_id='1'");
        $m_real_id = $member_id;
        $enroller_id = $db->GetOne("Select enroller_id From `members` Where member_id='$member_id'", 0);
        $level_id = $db->GetOne("Select m_level From `members` Where member_id='$member_id'", 0);
        $title_level = $db->GetOne("Select title From `types` Where order_index='$level_id'", 0);
        for ($i = 1; $i <= $depth; $i += 1) {
            $referrer_id = $db->GetOne("Select referrer_id From `matrix` Where member_id='$member_id'", 0);
            if ($referrer_id > 0) {
                $descr = "Product payment commissions from $title_level member (#$m_real_id) to ";
                $sql = "Select ";
                $ref_level = $db->GetOne("Select m_level From `members` Where member_id='$referrer_id'", 0);
                $ref_title_level = $db->GetOne("Select title From `types` Where order_index='$ref_level'", 0);
                $descr .= $ref_title_level;
                if ($referrer_id == $enroller_id) {
                    $sql .= "fee_sponsor ";
                    $descr .= " sponsor";
                } else {
                    $sql .= "fee_member ";
                    $descr .= " referrer";
                }
                $sql .= "From `shop_fees` Where to_order_index='$ref_level' And from_order_index='$level_id' And plevel='$i'";

                $fee = $db->GetOne($sql, "0.00");

                $fee = $amount / 100 * $fee;
                $fee = sprintf("%01.2f", $fee);

                if ($fee > 0) {


                    $db->ExecuteSql("Insert into cash (amount, type_cash, from_id, to_id, cash_date, descr, payment_id) values ('$fee', 1, '$m_real_id', '$referrer_id', '" . time() . "', '$descr', '$payment_id')");

                    //sending notification email regarding successfull payment
                    $row = $db->GetEntry("Select * From `emailtempl` Where `emailtempl_id`='12'", "");
                    if ($row ["is_active"] == 1) {
                        $SiteTitle = $db->GetSetting("SiteTitle");
                        $ContactEmail = $db->GetSetting("ContactEmail");
                        $header = "From: $SiteTitle <$ContactEmail>\r\n";
                        $receiver = $db->GetEntry("Select * From `members` Where member_id='$referrer_id'");
                        $first_name = decU($receiver["first_name"]);
                        $last_name = decU($receiver["last_name"]);
                        $email = decU($receiver["email"]);
                        $username = decU($receiver["username"]);

                        $subject = decU($row ["subject"]);
                        $message = decU($row ["message"]);
                        $subject = preg_replace("/\[SiteTitle\]/", $SiteTitle, $subject);

                        $message = preg_replace("/\[SiteTitle\]/", $SiteTitle, $message);
                        $message = preg_replace("/\[FirstName\]/", $first_name, $message);
                        $message = preg_replace("/\[LastName\]/", $last_name, $message);
                        $message = preg_replace("/\[Username\]/", $username, $message);
                        $message = preg_replace("/\[Amount\]/", $fee, $message);

                        sendMail($email, $subject, $message, $header);
                    }
                }
            } else {
                break;
            }
            $member_id = $referrer_id;
        }
    } else {
        $level_id = $db->GetOne("Select m_level From `members` Where member_id='$member_id'", 0);
        $title_level = $db->GetOne("Select title From `types` Where order_index='$level_id'", 0);

        $enroller_id = $db->GetOne("Select enroller_id From `members` Where member_id='$member_id'", 0);
        if ($enroller_id > 0) {
            $m_real_id = $member_id;

            $descr = "Product payment commissions from $title_level member (#$m_real_id) to ";
            $sql = "Select ";
            $ref_level = $db->GetOne("Select m_level From `members` Where member_id='$enroller_id'", 0);
            $ref_title_level = $db->GetOne("Select title From `types` Where order_index='$ref_level'", 0);

            $descr .= $ref_title_level;
            $sql .= "fee_sponsor ";
            $descr .= " sponsor";
            $sql .= "From `shop_fees` Where to_order_index='$ref_level' And from_order_index='$level_id' And plevel='1'";

            $fee = $db->GetOne($sql, "0.00");
            $fee = $amount / 100 * $fee;
            $fee = sprintf("%01.2f", $fee);
            if ($fee > 0) {
                $db->ExecuteSql("Insert into cash (amount, type_cash, from_id, to_id, cash_date, descr, payment_id) values ('$fee', 1, '$m_real_id', '$enroller_id', '" . time() . "', '$descr', '$payment_id')");

                //sending notification email regarding successfull payment
                $row = $db->GetEntry("Select * From `emailtempl` Where `emailtempl_id`='12'", "");
                if ($row ["is_active"] == 1) {
                    $SiteTitle = $db->GetSetting("SiteTitle");
                    $ContactEmail = $db->GetSetting("ContactEmail");
                    $header = "From: $SiteTitle <$ContactEmail>\r\n";
                    $receiver = $db->GetEntry("Select * From `members` Where member_id='$enroller_id'");
                    $first_name = decU($receiver["first_name"]);
                    $last_name = decU($receiver["last_name"]);
                    $email = decU($receiver["email"]);
                    $username = decU($receiver["username"]);

                    $subject = decU($row ["subject"]);
                    $message = decU($row ["message"]);
                    $subject = preg_replace("/\[SiteTitle\]/", $SiteTitle, $subject);

                    $message = preg_replace("/\[SiteTitle\]/", $SiteTitle, $message);
                    $message = preg_replace("/\[FirstName\]/", $first_name, $message);
                    $message = preg_replace("/\[LastName\]/", $last_name, $message);
                    $message = preg_replace("/\[Username\]/", $username, $message);
                    $message = preg_replace("/\[Amount\]/", $fee, $message);

                    sendMail($email, $subject, $message, $header);
                }
            }

        }

    }

    return;

}

//------------------------------------------------------------------------------
function getActiveTicketSelect($value)
{
    if ($value == "") $value = 1;
    $toRet = "<select name='activ'>";
    if ($value == -1) $check = "selected"; else $check = "";
    $toRet .= "<option value='-1' $check>All tickets</option>";
    if ($value == 0) $check = "selected"; else $check = "";
    $toRet .= "<option value='0' $check>Closed tickets</option>";
    if ($value == 1) $check = "selected"; else $check = "";
    $toRet .= "<option value='1' $check>Open tickets</option>";
    return $toRet . "</select>";
}

//------------------------------------------------------------------------------

function getAbsoluteLink($link)
{
    $toRet = $link;
    if (strcasecmp(substr($link, 0, 7), "http://") != 0) $toRet = "http://" . $link;
    return $toRet;
}

//------------------------------------------------------------------------------

function getPureURL($url)
{
    $toRet = $url;
    if (strcasecmp(substr($url, 0, 7), "http://") == 0) $toRet = substr($url, 7);
    if (substr($toRet, -1) == "/") $toRet = substr($toRet, 0, -1);
    return $toRet;
}

//------------------------------------------------------------------------------

function getExtension($filename, $defValue = "")
{
    $toRet = explode(".", $filename);
    if (count($toRet) > 1) {
        $index = count($toRet) - 1;
        return $toRet[$index];
    } else {
        return $defValue;
    }
}

//------------------------------------------------------------------------------

function make_seed()
{
    list ($usec, $sec) = explode(' ', microtime());
    return (float)$sec + ((float)$usec * 100000);
}

//------------------------------------------------------------------------------

function getUnID($length)
{
    $toRet = "";
    $symbols = array();
    for ($i = 0; $i < 26; $i++)
        $symbols[] = chr(97 + $i);
    for ($i = 0; $i < 10; $i++)
        $symbols[] = chr(48 + $i);

    srand(make_seed());
    for ($i = 0; $i < $length; $i++)
        $toRet .= $symbols[rand(0, 35)];
    return $toRet;
}

//------------------------------------------------------------------------------

function getUserAgent($user_agent)
{
    $browser = "unknown";

    if (eregi("Opera", $user_agent)) $broser = "Opera";

    if (eregi("MSIE", $user_agent)) $browser = "MS Internet Explorer";

    if (eregi("Netscape", $user_agent)) $browser = "Netscape";

    if (eregi("Mozilla", $user_agent) and !eregi("MSIE", $user_agent)) $browser = "Mozilla";

    return $browser;
}

//------------------------------------------------------------------------------

function getJsString($value)
{
    $search = array("/'/", "/\"/", "/\r\n/", "/\n/");
    $replace = array("\'", "\\\"", " ", " ");
    return preg_replace($search, $replace, $value);
}

//------------------------------------------------------------------------------

function getMonthSelect($value = "", $name = "dateMonth", $straif = 0)
{
    GLOBAL $dict;

    if ($value == "" Or $value == 0) $value = date("m") + $straif;
    if ($value > 12) $value = $value - 12;
    if ($value < 1) $value = $value + 12;
    $toRet = "<select name='$name' class='form-control' >";
    foreach ($dict['Months'] as $key => $value1) {
        if ($value == $key) $check = "selected"; else $check = "";
        $toRet .= "<option value='$key' $check>$value1</option>";
    }
    /*
        if (LANG=='en'){
        if ($value == 1) $check = "selected"; else $check = "";
        $toRet .= "<option value='1' $check>January</option>";
        if ($value == 2) $check = "selected"; else $check = "";
        $toRet .= "<option value='2' $check>February</option>";
        if ($value == 3) $check = "selected"; else $check = "";
        $toRet .= "<option value='3' $check>March</option>";
        if ($value == 4) $check = "selected"; else $check = "";
        $toRet .= "<option value='4' $check>April</option>";
        if ($value == 5) $check = "selected"; else $check = "";
        $toRet .= "<option value='5' $check>May</option>";
        if ($value == 6) $check = "selected"; else $check = "";
        $toRet .= "<option value='6' $check>June</option>";
        if ($value == 7) $check = "selected"; else $check = "";
        $toRet .= "<option value='7' $check>July</option>";
        if ($value == 8) $check = "selected"; else $check = "";
        $toRet .= "<option value='8' $check>August</option>";
        if ($value == 9) $check = "selected"; else $check = "";
        $toRet .= "<option value='9' $check>September</option>";
        if ($value == 10) $check = "selected"; else $check = "";
        $toRet .= "<option value='10' $check>October</option>";
        if ($value == 11) $check = "selected"; else $check = "";
        $toRet .= "<option value='11' $check>November</option>";
        if ($value == 12) $check = "selected"; else $check = "";
        $toRet .= "<option value='12' $check>December</option>";
        }
        if (LANG=='ru'){
        if ($value == 1) $check = "selected"; else $check = "";
        $toRet .= "<option value='1' $check>ßíâàðü</option>";
        if ($value == 2) $check = "selected"; else $check = "";
        $toRet .= "<option value='2' $check>Ôåâðàëü</option>";
        if ($value == 3) $check = "selected"; else $check = "";
        $toRet .= "<option value='3' $check>Ìàðò</option>";
        if ($value == 4) $check = "selected"; else $check = "";
        $toRet .= "<option value='4' $check>Àïðåëü</option>";
        if ($value == 5) $check = "selected"; else $check = "";
        $toRet .= "<option value='5' $check>Ìàé</option>";
        if ($value == 6) $check = "selected"; else $check = "";
        $toRet .= "<option value='6' $check>Èþíü</option>";
        if ($value == 7) $check = "selected"; else $check = "";
        $toRet .= "<option value='7' $check>Èþëü</option>";
        if ($value == 8) $check = "selected"; else $check = "";
        $toRet .= "<option value='8' $check>Àâãóñò</option>";
        if ($value == 9) $check = "selected"; else $check = "";
        $toRet .= "<option value='9' $check>Ñåíòÿáðü</option>";
        if ($value == 10) $check = "selected"; else $check = "";
        $toRet .= "<option value='10' $check>Îêòÿáðü</option>";
        if ($value == 11) $check = "selected"; else $check = "";
        $toRet .= "<option value='11' $check>Íîÿáðü</option>";
        if ($value == 12) $check = "selected"; else $check = "";
        $toRet .= "<option value='12' $check>Äåêàáðü</option>";
        }
    */
    return $toRet . "</select>";
}

//------------------------------------------------------------------------------

function getYearSelect($value = "", $name = "dateYear", $table = "", $field = "")
{
    global $db;
    $toRet = "<select name='$name' class='form-control' >";
    if ($value == "" Or $value == 0) $value = date("Y");
    $start = date("Y") - 3;
    if ($value < $start) $start = $value - 1;
    if ($table != "" And $field != "") {
        $start = $db->GetOne("Select Min($field) From $table");
        $start = date("Y", $start);
    }

    for ($i = $start; $i <= (date("Y") + 3); $i++) {
        if ($value == $i) $check = "selected"; else $check = "";
        $toRet .= "<option value='$i' $check> $i </option>";
    }

    return $toRet . "</select>";
}

//------------------------------------------------------------------------------

function getDays($month, $year)
{
    switch ($month) {
        case 1:
            $days = 31;
            break;
        case 2:
            $days = (floor($year / 4) == $year / 4) ? 29 : 28;
            break;
        case 3:
            $days = 31;
            break;
        case 4:
            $days = 30;
            break;
        case 5:
            $days = 31;
            break;
        case 6:
            $days = 30;
            break;
        case 7:
            $days = 31;
            break;
        case 8:
            $days = 31;
            break;
        case 9:
            $days = 30;
            break;
        case 10:
            $days = 31;
            break;
        case 11:
            $days = 30;
            break;
        case 12:
            $days = 31;
            break;
        default:
            $days = 30;
    }
    return $days;
}


//------------------------------------------------------------------------------

function getDaySelect($value = "", $name = "dateDay")
{
    if ($value == "" Or $value == 0) $value = date("d");
    $toRet = "<select name='$name' class='form-control' >";

    for ($i = 1; $i < 32; $i++) {
        if ($value == $i) $check = "selected"; else $check = "";
        if (strlen($i) == 1) $i = "0" . $i;
        $toRet .= "<option value='$i' $check> $i </option>";
    }

    return $toRet . "</select>";
}

//------------------------------------------------------------------------------

function sendMail($email, $subject, $message, $header = "")
{
    global $db;

    $header .= 'MIME-Version: 1.0' . "\r\n";
    $header .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

    $useSMTPAutorisation = $db->GetOne("Select value From settings Where keyname='UseSMTPAutorisation'");
    if ($useSMTPAutorisation == 1) {

        $SMTPServer = $db->GetOne("Select value From settings Where keyname='SMTPServer'");
        $SMTPDomain = $db->GetOne("Select value From settings Where keyname='SMTPDomain'");
        $SMTPUserName = $db->GetOne("Select value From settings Where keyname='SMTPUserName'");
        $SMTPPassword = $db->GetOne("Select value From settings Where keyname='SMTPPassword'");
        $contactEmail = $db->GetOne("Select value From settings Where keyname='ContactEmail'");

        // SMTP connection
        $handle = @fsockopen($SMTPServer, 25);
        @fputs($handle, "EHLO $SMTPDomain\r\n");

        // SMTP authorization
        @fputs($handle, "AUTH LOGIN\r\n");
        @fputs($handle, base64_encode($SMTPUserName) . "\r\n");
        @fputs($handle, base64_encode($SMTPPassword) . "\r\n");

        // Send out the e-mail
        @fputs($handle, "MAIL FROM:<$contactEmail>\r\n");
        @fputs($handle, "RCPT TO:<$email>\r\n");
        @fputs($handle, "DATA\r\n");
        if ($header != "") @fputs($handle, $header);

        @fputs($handle, "To: $email\r\n");
        @fputs($handle, "Subject: $subject\r\n");
        @fputs($handle, "$message\r\n");
        @fputs($handle, ".\r\n");

        // Close connection to SMTP server
        @fputs($handle, "QUIT\r\n");
    } else {
        @mail($email, $subject, $message, $header);
    }
    return true;
}

//------------------------------------------------------------------------------

function makeThumbnail($nameFull, $size)
{
    global $db;
    $quality = '100';
    $info = getimagesize($nameFull);
    if ($size == 1) {
        $logoMaxWidth = $db->GetSetting("PhotoBigMaxWidth");
        $logoMaxHeight = $db->GetSetting("PhotoBigMaxHeight");
    } else if ($size == 0) {
        $logoMaxWidth = $db->GetSetting("PhotoSmallMaxWidth");
        $logoMaxHeight = $db->GetSetting("PhotoSmallMaxHeight");
    } else if ($size == 2) {
        $logoMaxWidth = 40;
        $logoMaxHeight = 40;
    }
    if ($info[0] > $logoMaxWidth or $info[1] > $logoMaxHeight) {
        $im = imagecreatefromjpeg($nameFull);
        $k1 = $logoMaxWidth / imagesx($im);
        $k2 = $logoMaxHeight / imagesy($im);
        $k = $k1 > $k2 ? $k2 : $k1;
        $w = intval(imagesx($im) * $k);
        $h = intval(imagesy($im) * $k);
        $im1 = imagecreatetruecolor($w, $h);
        imagecopyresampled($im1, $im, 0, 0, 0, 0, $w, $h, imagesx($im), imagesy($im));
        imagejpeg($im1, $nameFull, $quality);
        imagedestroy($im);
        imagedestroy($im1);
        return true;
    }
}

//------------------------------------------------------------------------------

function getParam($str, $defValue = "")
{
    $toRet = $defValue;
    if (array_key_exists($str, $_GET)) $toRet = $_GET [$str];
    elseif (array_key_exists($str, $_POST)) $toRet = $_POST [$str];
    return $toRet;
}

//------------------------------------------------------------------------------

function checkDBAccess($host, $name, $username, $password)
{
    $toRet = "";
    $connect = @mysql_connect($host, $username, $password);
    if ($connect == false) {
        $toRet = "Cannot establish connection to MySQL database.";
    } else {
        if (!@mysql_select_db($name, $connect)) $toRet = "Cannot select DB &quot;$name&quot;.";
        mysql_close($connect);
    }

    return $toRet;
}

//------------------------------------------------------------------------------
function set_chmod_for_install($file)
{
    @chmod($file, 0777);
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        @chmod($file, 0777);
        $cmd = "chmod 777" . $file;
        @exec($cmd, $output, $retval);
    }
}

function decU($value)
{
    $search = array("/&amp;/", "/&lt;/", "/&gt;/", "/&#039;/");
    $replace = array("&", "<", ">", "'");
    return preg_replace($search, $replace, $value);
}

//------------------------------------------------------------------------------

function sitestatistics()
{
    global $db;

    $agent = isset($_SERVER["HTTP_USER_AGENT"]) ? htmlspecialchars($_SERVER["HTTP_USER_AGENT"]) : '';
    $language = isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]) ? htmlspecialchars($_SERVER["HTTP_ACCEPT_LANGUAGE"]) : '';
    $t = explode(";", $language);
    $language = $t[0];
    $tmp = GetIP();
    $ip = $tmp['ip'];
    $proxy = $tmp['proxy'];
    $date = date("Y-m-d");
    $time = date("H:i:s");

    $referer = isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : '';
    $page = htmlspecialchars("http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

    $res = $db->GetEntry("SELECT * FROM stat_counter WHERE `date`='$date' ", false);
    $un_page = $res['page'] + 1;
    $un_ip = $res['un_ip'];
    $un_browser = $res['un_browser'];

    $un_ip_sl = 0;
    $res = $db->GetOne("SELECT count(`id`) FROM stat_log WHERE `ip`='$ip' and `date`='$date' ", 0);
    if ($res > 0) $un_hits = $un_ip;
    else {
        $un_ip_sl = 1;
        $un_ip++;
    }

    $un_browser_sl = 0;
    $y = "stat_" . $_SERVER["HTTP_HOST"];
    $y = str_replace('.', '_', $y);
    if (!isset($_COOKIE[$y])) {
        $un_browser_sl = 1;
        $un_browser++;
        @setcookie($y, $un_browser);
    }

    $res = $db->GetEntry("SELECT `time`,`session` FROM stat_log WHERE `ip`='$ip' and `date`='$date' ORDER BY `time` DESC ", false);
    $yy = mktime(date("H"), date("i"), date("s"));
    $t = explode(":", $res['time']);
    if (count($t) > 1) {
        $yyy = mktime($t[0], $t[1], $t[2]);
        $ss = $yy - $yyy;
    } else $ss = 300;
    if ($ss > 240)
        $sess = $db->GetOne("SELECT max(session) FROM stat_log ", 0) + 1;
    else
        $sess = $res['session'];

    $res = $db->GetOne("SELECT count(`id`) FROM stat_counter WHERE `date`='$date' ", 0);
    if ($res == 0)
        $db->ExecuteSql("Insert Into `stat_counter` (`id`,`page`,`un_ip`,`un_browser`,`date`) VALUES (NULL,'1','$un_ip_sl','$un_browser_sl','$date') ");
    else
        $db->ExecuteSql("Update `stat_counter` Set `page`='$un_page',`un_ip`='$un_ip',`un_browser`='$un_browser' Where `date`='$date' ");

    $db->ExecuteSql("Insert Into `stat_log` (`id`, `date`, `time`, `ip`, `proxy`, `page`, `referer`, `language`, `agent`, `un_browser`, `un_ip`, `session`, `screen`,`get`,`post`) VALUES (NULL,'$date','$time','$ip','$proxy','$page','$referer','$language','$agent','$un_browser_sl','$un_ip_sl','$sess','','" . addslashes(serialize($_GET)) . "','" . addslashes(serialize($_POST)) . "') ");

}

function GetIP()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    $proxy = "";
    if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $proxy = $ip;
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    $result['ip'] = $ip;
    $result['proxy'] = $proxy;
    return $result;
}

function UniKey($numAlpha = 8, $only_low = false)
{
    if ($only_low) $listAlpha = 'abcdefghijklmnopqrstuvwxyz0123456789';
    else $listAlpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return str_shuffle(substr(str_shuffle($listAlpha), 0, $numAlpha));
}

function GetTimePreLaunch()
{
    GLOBAL $db;
    $time = $db->GetSetting("PRE_LAUNCH_DATE", 0) - time();
    return ($time>0?$time:0);
}
function isPreLaunch()
{
    GLOBAL $db;
    //return (bool)$db->GetSetting("PRE_LAUNCH", false);
    return (bool)( (bool)$db->GetSetting("PRE_LAUNCH", false)&&!isPreLaunchEnd() );

}
function isPreLaunchEnd()
{
    return (GetTimePreLaunch()>0?false:true);

}
function checkPreLaunchEnd()
{
    GLOBAL $db;
    if ( $db->GetSetting("PRE_LAUNCH_DATE", 0)>0 && isPreLaunchEnd() )
    {
        $db->SetSetting("PRE_LAUNCH", 0);
        $db->SetSetting("PRE_LAUNCH_DATE", 0);
        $db->SetSetting("PaymentMode", 1);
        $db->SetSetting("PaymentModeDate", time());
        $db->SetSetting ("after_launch", time());


        $template = $db->GetEntry("Select * From `emailtempl` Where `emailtempl_id`='18'", "");
        $SiteTitle = $db->GetSetting("SiteTitle");
        $SiteUrl = $db->GetSetting("SiteUrl");
        $ContactEmail = $db->GetSetting("ContactEmail");
        $header = "From: $SiteTitle <$ContactEmail>\r\n";

        $result = $db->ExecuteSql("Select email, first_name From `members` WHERE prelaunch_norif=1 ");
        while ($row = $db->FetchInArray($result)) {
            if ($template["is_active"] == 1) {
                $first_name = decU($row['first_name']);
                $last_name = decU($row['last_name']);
                //$username = decU($row['username']);

                $subject = decU($row ["subject"]);
                $message = decU($row ["message"]);
                $subject = preg_replace("/\[SiteTitle\]/", $SiteTitle, $subject);

                $message = preg_replace("/\[SiteTitle\]/", $SiteTitle, $message);
                $message = preg_replace("/\[FirstName\]/", $first_name, $message);
                $message = preg_replace("/\[LastName\]/", $last_name, $message);
                //$message = preg_replace("/\[Username\]/", $username, $message);
                $message = preg_replace("/\[SiteUrl\]/", $SiteUrl, $message);

                sendMail($row['email'], $subject, $message, $header);
            }
        }
    }
}
function statusAfterLaunch()
{
    GLOBAL $db;
/*
0- не активный
1- активный
2- прошел
 */
    $status = 0;
    if ( time()-$db->GetSetting("after_launch") < $db->GetSetting("time_after_launch") ) $status = 1;
    if ( time()-$db->GetSetting("after_launch") > $db->GetSetting("time_after_launch") ) $status = 2;
    
    return $status;
}

function getInboxBadge($member_id)
{
    GLOBAL $db;
    $total = $db->GetOne("Select Count(*) From messages Where is_deleted=0 And to_member_id='$member_id' and member_id='$member_id' ", 0);
    return ($total>0?'<span class="badge">'.$total.'</span>':'');
}


?>