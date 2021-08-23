<?php

require_once ("./includes/config.php");
require_once ("./includes/xtemplate.php");
require_once ("./includes/xpage_public.php");
require_once ("./includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        XPage::XPage ($object);

        $this->mainTemplate = "./templates/check_data.tpl";
        $this->pageTitle = "Account activation";
        $this->pageHeader = "Account activation";
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $member_id = $this->GetID ('i');
        $pin_code = $this->GetGp ('c');
        $ec = $this->GetGp ('ec');
        $mess = "";
        if ($ec == 'yes') $mess = "<span class='message'>Your ip address was successfully changed</span>";
        if ($ec == 'no') $mess = "<span class='message'>Sorry, but your ip address cannot be changed</span>";

        $f_name = $this->db->GetOne("Select first_name from members Where member_id='$member_id'");
        $l_name = $this->db->GetOne("Select last_name from members Where member_id='$member_id'");
        $ip_address = $this->GetServer ("REMOTE_ADDR", "unknown");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "LASTNAME" => $l_name,
            "FIRSTNAME" => $f_name,
            "ID" => $member_id,
            "MESSAGE" => $mess,
            "IP_ADDRESS" => "<input type='text' name='ip_address' value='$ip_address' style='width:120px;'>",
            "PIN_CODE" => "<input type='text' name='c' value='$pin_code' style='width:120px;'>",
            "USERNAME" => "<input type='text' name='username' value='' style='width:120px;'>",
            "PASSWORD" => "<input type='password' name='password' value='' style='width:120px;'>",
        );
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        $pin_code = $this->GetGp ('c');
        $ip_address = $this->GetGp ('ip_address');
        $username = $this->GetGp ('username');
        $password = md5 ($this->GetGp ('password'));

        $member_id = $this->db->GetOne("Select member_id From members Where username='$username' And passwd='$password'", "0");
        $pin_code_in_db = $this->db->GetOne("Select pin_code From members Where username='$username' And passwd='$password'", "0");


        if ($pin_code == $pin_code_in_db)
        {
            $this->db->ExecuteSql("Update members Set ip_address='$ip_address', pin_code='' Where username='$username' And passwd='$password'");

            $_SESSION['MemberID'] = $member_id;
            $this->Redirect ("./member/overview.php");
        }
        else
        {
            $this->Redirect ("{$this->pageUrl}?ec=no&c=$pin_code&i=$member_id");
        }

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "PIN_CODE" => "<input type='text' name='pin_code' value='$pin_code' style='width:120px;'>",
            "USERNAME" => "<input type='text' name='username' value='' style='width:120px;'>",
            "PASSWORD" => "<input type='password' name='password' value='' style='width:120px;'>",
        );
    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("activate");

$zPage->Render ();

?>