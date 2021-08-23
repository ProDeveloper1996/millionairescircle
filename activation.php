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

        $this->mainTemplate = "./templates/activation.tpl";
        $this->pageTitle = "Account activation";
        $this->pageHeader = "Account activation";
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $member_id = $this->GetID ('code');
        $ec = $this->GetGp ('ec');
        $mess = "";

        if ($ec == 'no') $mess = "<span class='error'>Sorry, but the details specified are not correct</span>";
        if ($ec == 'nom') $mess = "<span class='error'>Sorry, but no registered members with details specified</span>";

        $f_name = $this->db->GetOne("Select first_name from `members` Where member_id='$member_id'");
        $l_name = $this->db->GetOne("Select last_name from `members` Where member_id='$member_id'");
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "LASTNAME" => $l_name,
            "FIRSTNAME" => $f_name,
            "ID" => $member_id,
            "MESSAGE" => $mess,
            "USERNAME" => "<input type='text' name='username' value='' style='width:120px;'>",
            "PASSWORD" => "<input type='password' name='password' value='' style='width:120px;'>",
        );
    }

    //--------------------------------------------------------------------------
    function ocd_update ()
    {
        $code = $this->GetID ('i');

        $username = $this->enc($this->GetValidGP ("username", "Username", VALIDATE_USERNAME));
        $password = $this->GetValidGP ("password", "Password", VALIDATE_PASSWORD);
        $password_code = md5 ($password);
        if ($this->errors['err_count'] > 0)
        {
            $this->Redirect ("{$this->pageUrl}?ec=no&code=$code");
        }

        $member_id = $this->db->GetOne("Select member_id From `members` Where username='$username' And passwd='$password_code' And is_active=0", "0");

        if ($code == $member_id And $member_id > 0)
        {
            $is_active = $this->db->GetOne("Select is_active From `members` Where member_id='$member_id'", 0);
            if ($is_active == 1) $this->Redirect ("{$this->pageUrl}?ec=al&code=$code");

            $SiteTitle = $this->db->GetSetting ("SiteTitle");
            $SiteUrl = $this->db->GetSetting ("SiteUrl");
            $this->db->ExecuteSql("Update `members` Set is_active=1 Where member_id='$member_id'");
            $enroller_id = $this->db->GetOne ("Select enroller_id From `members` Where member_id='$member_id'");

            $cycling = $this->db->GetSetting ("cycling", 0);
            $first_name = $this->dec ($this->db->GetOne ("Select first_name From `members` Where member_id='$member_id'"));
            $last_name = $this->dec ($this->db->GetOne ("Select last_name From `members` Where member_id='$member_id'"));
            $email = $this->dec ($this->db->GetOne ("Select email From `members` Where member_id='$member_id'"));
            if ($cycling == 0)
            {
                $this->db->ExecuteSql ("Update `members` Set m_level='1', is_active=1 Where member_id='$member_id'");
                in_forced_matrix ($member_id, $enroller_id);
            }
            
            //sponsor notification
            $row = $this->db->GetEntry ("Select * From `emailtempl` Where `emailtempl_id`='3'", "");
            
            if ($row ["is_active"] == 1)
            {
                $SponsorFName = $this->dec ($this->db->GetOne ("Select first_name From `members` Where member_id='$enroller_id'"));
                $SponsorLName = $this->dec ($this->db->GetOne ("Select last_name From `members` Where member_id='$enroller_id'"));
                $SponsorUsername = $this->dec ($this->db->GetOne ("Select username From `members` Where member_id='$enroller_id'"));
                $SponsorEmail = $this->dec ($this->db->GetOne ("Select email From `members` Where member_id='$enroller_id'"));
                
                $subject = $this->dec ($row ["subject"]);
                $message = $this->dec ($row ["message"]);
                $subject = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $subject);
                
                $message = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $message);
                
                $message = preg_replace ("/\[SponsorFName\]/", $SponsorFName, $message);
                $message = preg_replace ("/\[SponsorLName\]/", $SponsorLName, $message);
                $message = preg_replace ("/\[SponsorUsername\]/", $SponsorUsername, $message);
                $message = preg_replace ("/\[SponsorEmail\]/", $SponsorEmail, $message);
                
                $message = preg_replace ("/\[FirstName\]/", $first_name, $message);
                $message = preg_replace ("/\[LastName\]/", $last_name, $message);
                $message = preg_replace ("/\[Username\]/", $username, $message);
                $message = preg_replace ("/\[Email\]/", $email, $message);
                
                sendMail ($SponsorEmail, $subject, $message, $this->emailHeader);
                
            }
            
            $row = $this->db->GetEntry ("Select * From `emailtempl` Where `emailtempl_id`='4'", ""); 
            if ($row ["is_active"] == 1)
            {
                
                $subject = $this->dec ($row ["subject"]);
                $message = $this->dec ($row ["message"]);
                $subject = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $subject);
                
                $RefLink = $SiteUrl."index.php?spon=".$member_id;
                
                $message = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $message);
                $message = preg_replace ("/\[FirstName\]/", $first_name, $message);
                $message = preg_replace ("/\[LastName\]/", $last_name, $message);
                $message = preg_replace ("/\[Username\]/", $username, $message);
                $message = preg_replace ("/\[Email\]/", $email, $message);
                $message = preg_replace ("/\[Password\]/", $password, $message);
                $message = preg_replace ("/\[RefLink\]/", $RefLink, $message);
                
                sendMail ($email, $subject, $message, $this->emailHeader);
                
            }

            $_SESSION['MemberID'] = $member_id;

            $this->Redirect ("./member/myaccount.php");
        }
        else
        {
            $member_id = $this->db->GetOne("Select member_id From `members` Where username='$username' And passwd='$password_code' And is_active=1", "0");
            if ($code == $member_id And $member_id > 0)
            {
                $_SESSION['MemberID'] = $member_id;
                $this->Redirect ("./member/myaccount.php");
            }
            
            $this->Redirect ("{$this->pageUrl}?ec=nom&code=$code");
        }

    }
}

//------------------------------------------------------------------------------

$zPage = new ZPage ("activate");

$zPage->Render ();

?>