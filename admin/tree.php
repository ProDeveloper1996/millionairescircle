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
		 GLOBAL $dict;
        XPage::XPage ($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        GLOBAL $dict;
        $this->mainTemplate = "./templates/tree.tpl";

        $id = $this->GetGP ("id", 1);

        $matrix_type = $this->db->GetSetting ("cycling", 0);

       $this->pageHeader = 'Overall Tree' ;

        $this->pageTitle = 'Overall Tree';

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
        );


        //$matrix_type = $this->db->GetSetting ("cycling", 0);
        if ($matrix_type == 1) // cycling
        {
            $level = $this->GetGP ("level", 1);
            $content = "";
            $links = "";
            $result = $this->db->ExecuteSql ("Select * From `types` Order By order_index ASC");
            while ($row = $this->db->FetchInArray ($result))
            {
                $order_index = $row['order_index'];
                $title = $this->dec ($row['title']);
                $class = ($order_index == $level)? "active" : "";
                //$links .= "&nbsp;&nbsp;<a class='$class' href='{$this->pageUrl}?id=$id&level=$order_index'>$title</a>";
                $links .= '<li role="presentation" class="'.$class.'"><a href="'.$this->pageUrl.'?id='.$id.'&level='.$order_index.'" >'.$title.'</a></li>';
            }

            $content = $this->matrix_tree_cycling($id, $level);//matrix_tree_admin_set ($id, $level);

            $this->data = array (
                "MAIN_HEADER" => $this->pageHeader,
                "MAIN_CONTENT" => $content,
                "MAIN_ID" => $id,
                "MAIN_LINKS" => $links,
            );
        }
        if ($matrix_type == 0) // forced
        {
            $links = '';
            $content = $this->matrix_tree_forced ($id);
            $this->data["MAIN_CONTENT"] = $content;
            $this->data["MAIN_LINKS"] = $links;
        }

    }


// ============================================
/*  TREE cycling */
    function matrix_tree_cycling($host_id=1, $m_level=1){
        GLOBAL $dict;
        $count = $this->db->GetOne ("Select Count(*) From `places` Where member_id='$host_id' And m_level='$m_level'", 1);
        $width = $this->db->GetOne ("Select width From `types` Where order_index='$m_level'");
        $depth = $this->db->GetOne ("Select depth From `types` Where order_index='$m_level'");
        $content = "";

        $result = $this->db->ExecuteSql("Select `place_id` From `places` Where `member_id`='$host_id' And `m_level`='$m_level' Order by place_id Asc");
        $i = 0;
        while ($row = $this->db->FetchInArray($result))
        {
            $i++;
            $place_id = $row['place_id'];
            $cont = "";
            $content .= "<h3 style='text-align:center;'>Matrix #".$i."</h3>";
            $content .= $this->get_tree_matrix_cycling($host_id, $m_level, $place_id, $width, $depth, $i);

        }

        return $content;
    }

    function get_tree_matrix_cycling($host_id, $m_level, $place_id, $width, $depth){
        $content = "<table cellpadding='0' cellspacing='2' align='center' width='100%'>";

        $member = $this->db->GetEntry ("Select * From `members` Where member_id='$host_id'");
        $name = decU ($member ["first_name"] . " " . $member ["last_name"]);
        //$mm_level = $member ["m_level"];
        //$mm_level = $this->db->GetOne ("Select title From `types` Where `order_index`='$mm_level'", "");
        //$reentry = $this->db->GetOne ("Select `reentry` From `places` Where `place_id`='$place_id'");

        $content .= "<tr><td style='text-align:center;'><b>$name</b><br />ID:$host_id</td></tr><tr><td>";

        $content .= $this->cycling_getMembers($host_id, $place_id, $width, $depth, 0);

        $content .= "</td></tr></table>";

        return $content;
    }

    function cycling_getMembers($host_id, $place_id, $width, $depth, $sts,  $reentry=1, $arrow=false){
        $sts++;
        $count_ref = $this->db->GetOne ("Select Count(*) From `places` Where `referrer_place_id`='$place_id'", 0);
        $percent = 100 / $width;
        $to_ret = "<table style='height:50px;' cellpadding='0' cellspacing='2' align='center' width='100%'><tr>";
        if ($count_ref > 0)
        {
            $i=0;
            $result = $this->db->ExecuteSql ("Select * From `places` Where `referrer_place_id`='$place_id'");
            while ($row = $this->db->FetchInArray ($result))
            {
                $new_place_id = $row['place_id'];
                $member_id = $row['member_id'];
                $m_level = $row['m_level'];
                $reentry = $row['reentry'];
                //$member_info = getMemberInfoAdmin ($member_id, $reentry, $host_id);
                $member = $this->db->GetEntry ("Select * From `members` Where member_id='$member_id'");
                //$member_info = '<a class="getmatrix" id="m'.$member_id.'" data-reentry="'.$reentry.'" data-level="'.$m_level.'" data-place="'.$new_place_id.'" href="#">'.decU ($member ["first_name"] . " " . $member ["last_name"]) . "</a>";
                //$member_info .= '<span class="fa-stack fa-1" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus."><i class="fa fa-circle-thin fa-stack-2x"></i><i class="fa fa-info"></i></span>';
                //$member_info .= "<br><a href='/?ref=$member_id' target='_blank'>ref link</a>";
                $member_info = $this->getMemberInfo ($member_id, $reentry, $host_id, $new_place_id);

                $arrowHtml = '';
                if ($arrow) $arrowHtml = "<i class='fa fa-long-arrow-down'></i>";

                $to_ret .= "<td style='text-align:center;width:$percent%' valign='top' border=0>$arrowHtml<div style='font-size:10px;min-height:50px;border: 1px solid;border-radius: 30px;padding: 5px 10px 0 10px;line-height: 13px;border-color: #E0DCDC;margin: 0 2px;'>$member_info</div>";
                $i++;
                if ($sts < $depth)
                {
                    $to_ret .= $this->cycling_getMembers ($host_id, $new_place_id, $width, $depth, $sts,  $reentry, true);
                }

                $to_ret .= "</td>";
            }
            $this->db->FreeSqlResult ($result);
        }
        if ($count_ref < $width)
        {
            $number = $width - $count_ref;
            for ($i = 1; $i <= $number; $i += 1)
            {
                $to_ret .= "<td style='text-align:center;width:$percent%' valign='top'><div style='font-size:10px;min-height:50px;border: 1px solid;  border-radius: 30px;border-color: #E0DCDC;'><img src='./images/grey_man.png' border='0' alt='Vacant Place' title='Vacant Place' /></div>";
                if ($sts < $depth)
                {
                    $to_ret .= $this->cycling_getMembers ($host_id, -1, $width, $depth, $sts);
                }
                $to_ret .= "</td>";
            }
        }
        $to_ret .= "</tr></table>";

        return $to_ret;


    }
/*  END TREE cycling */

function getMemberInfo ($member_id, $reentry, $host_id, $new_place_id='')
{
        $is_dead = $this->db->GetOne ("Select `is_dead` From `members` Where member_id='$member_id'", 1);
        if ($is_dead == 0)
        {
                $member = $this->db->GetEntry ("Select * From `members` Where member_id='$member_id'");
                $name = decU ($member ["first_name"] . " " . $member ["last_name"]);
                $mm_level = $member ["m_level"];
                $loc_enroller = $member ["enroller_id"];

                $mm_level = $this->db->GetOne ("Select title From `types` Where `order_index`='$mm_level'", "");

                $add = ($reentry!=''?"Reentry:$reentry<br>":'');
                $add .= "Id:$member_id<br>spon: $loc_enroller";
                $name = ($loc_enroller == $host_id)? "<b>$name</b>" : "$name";

                //$toRet = "$add2<br /><a href='contact.php?s=$member_id'><img src='./images/mail.png' border='0' alt='Email to member' title='Email to member' /></a><br />$add<br /><span class='super_small'>Level:$mm_level</span>";
                $popover = "$add<br>Level:$mm_level";
                $toRet = '<a class="getmatrix" id="m'.$member_id.'" data-reentry="'.$reentry.'" data-level="'.$member ["m_level"].'" data-place="'.$new_place_id.'" href="#">'.$name."</a>";
                $toRet .= '<br>'.$popover;
                //$toRet .= '<span class="fa-stack fa-1" data-container="body" data-toggle="popover" data-trigger="hover" data-html=true data-placement="right" data-content="'.$popover.'"><i class="fa fa-circle-thin fa-stack-2x"></i><i class="fa fa-info"></i></span>';
                //$toRet .= "<br><a href='contact.php?s=$member_id'><img src='./images/mail.png' border='0' alt='Email to member' title='Email to member' /></a>";


        }
        else
        {
                $toRet = "<img src='./images/red_man.gif' border='0' alt='Removed member' title='Removed member' />";
        }
        return $toRet;
}

// ============================================
/*  TREE FORCED */
    function matrix_tree_forced($host_id=1){
        $width = $this->db->GetOne ("Select width From `matrixes` Where matrix_id=1");
        $depth = $this->db->GetOne ("Select depth From `matrixes` Where matrix_id=1");

        $content = "<table cellpadding='0' cellspacing='2' align='center' width='100%'>";

        $member = $this->db->GetEntry ("Select * From `members` Where member_id='$host_id'");
        $name = decU ($member ["first_name"] . " " . $member ["last_name"]);

        $content .= "<tr><td style='text-align:center;'><span class='question'>$name</span><br />ID:$host_id</td></tr><tr><td>";
        $content .= $this->matrix_tree_forced_getMembers ($host_id, $width, $depth, 0 );
        $content .= "</td></tr></table>";

        return $content;
    }

    function matrix_tree_forced_getMembers ($host_id, $width, $depth, $depthIndex ){
        $depthIndex++;
        $count_ref = $this->db->GetOne ("Select Count(*) From `matrix` Where `referrer_id`='$host_id'", 0);
        $percent = 100 / $width;
        $content = "<table style='height:50px;' cellpadding='0' cellspacing='2' align='center' width='100%'><tr>";
        if ($count_ref > 0)
        {
            $result = $this->db->ExecuteSql ("Select * From `matrix` Where `referrer_id`='$host_id'");
            while ($row = $this->db->FetchInArray ($result))
            {
                $member_id = $row['member_id'];

                $member = $this->db->GetEntry ("Select * From `members` Where member_id='$member_id'");
                //$member_info = '<a class="getmatrix" id="'.$member_id.'" href="#">'.decU ($member ["first_name"] . " " . $member ["last_name"]) . "</a>";
                //$member_info .= "<br><a href='/?ref=$member_id' target='_blank'>ref link</a>";
                $member_info = $this->getMemberInfo ($member_id, '', $host_id, 0);

                $content .= "<td style='text-align:center;width:$percent%;' valign='top' border=0 ><div style='font-size:10px;min-height:50px;border: 1px solid;border-radius: 30px;padding: 5px 10px 0 10px;line-height: 13px;border-color: #E0DCDC;'>$member_info</div>";

                if ($depthIndex < $depth) $content .= $this->matrix_tree_forced_getMembers($member_id, $width, $depth, $depthIndex );

                $content .= "</td>";
            }
            $this->db->FreeSqlResult ($result);
        }

        if ($count_ref < $width)
        {
            $number = $width - $count_ref;
            for ($i = 1; $i <= $number; $i += 1)
            {
                $content .= "<td style='text-align:center;width:$percent%' valign='top'><div style='font-size:10px;min-height:50px;border: 1px solid;  border-radius: 30px;border-color: #E0DCDC;'><img src='./images/grey_man.png' border='0' alt='Vacant Place' title='Vacant Place' /></div>";
                if ($depthIndex < $depth) {
                    //$content .= $this->matrix_tree_forced_getMembers (-1, $width, $depth, $depthIndex );
                }
                $content .= "</td>";
            }
        }

        $content .= "</tr></table>";

        return $content;
    }
/*  END TREE FORCED */

    function ocd_getmatrix(){
        $id = $this->GetGP ("id", 1);
        $matrix_type = $this->db->GetSetting ("cycling", 0);
        if ($matrix_type == 1) // cycling
        {
            $m_level = $this->GetGP ("level", 1);
            $reentry = $this->GetGP ("reentry", 1);
            $place_id = $this->GetGP ("place", 1);
            $width = $this->db->GetOne ("Select width From `types` Where order_index='$m_level'");
            $depth = $this->db->GetOne ("Select depth From `types` Where order_index='$m_level'");
            $content=$this->cycling_getMembers($id, $place_id, $width, $depth, 0);
        }
        if ($matrix_type == 0) // forced
        {
            $width = $this->db->GetOne ("Select width From `matrixes` Where matrix_id=1");
            $depth = $this->db->GetOne ("Select depth From `matrixes` Where matrix_id=1");
            
            $content = $this->matrix_tree_forced_getMembers ($id, $width, $depth, 0 );
        }
        exit($content);
    }


}

//------------------------------------------------------------------------------

$zPage = new ZPage ("memb_matrix");

$zPage->Render ();

?>