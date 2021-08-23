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
        GLOBAL $dict;
        $this->pageTitle = $dict['TF_pageTitle'];
        $this->pageHeader = $dict['TF_pageTitle'];
        
        $SiteUrl = $this->db->GetSetting ("SiteUrl");
        $SiteTitle = $this->db->GetSetting ("SiteTitle");
        
        $this->mainTemplate = "./templates/tellfriends.tpl";
        $member_id = $this->member_id;
        
        $sender_first_name = $this->dec($this->db->GetOne ("Select first_name From members Where member_id='$member_id'"));
        $sender_last_name = $this->dec($this->db->GetOne ("Select last_name From members Where member_id='$member_id'"));
        $sender_email = $this->db->GetOne ("Select email From members Where member_id='$member_id'");
        
        //$ref_link = "<input type='text' name='enroller_id' value='".$thisSiteUrl."index.php?spon=".$member_id."' style='width: 320px;'>";
        $ReferrerUrl = $this->db->GetSetting ("ReferrerUrl");
        $ref_id=$this->db->GetOne ("Select $ReferrerUrl From `members` Where member_id='$member_id'", 1);
        $ref_link = $SiteUrl."?ref=".$ref_id;
        
        $mess = "";
        if ($this->GetGP ("m", "") == "ok")
        {
            if ($this->GetGP ("k", 0) > 0)
            {
                $mess = "<span class='message'>{$dict['TF_mess1']}".$this->GetGP ("k", 0)."{$dict['TF_mess2']}</span>";
            }
            else
            {
                $mess = "<span class='error'>{$dict['TF_mess3']}</span>";
            }
            
        }
        
        $first_name = "First Name";
        $last_name = "Last Name";
        
        $row = $this->db->GetEntry ("Select * From `emailtempl` Where `emailtempl_id`='10'", "");
        $subject = $this->dec($row ["subject"]);
        $message = nl2br ($this->dec($row ["message"]));
        
        $subject = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $subject);
        $subject = preg_replace ("/\[SenderFirstName\]/", $sender_first_name, $subject);
        $subject = preg_replace ("/\[SenderLastName\]/", $sender_last_name, $subject);
        $subject = preg_replace ("/\[FirstName\]/", $first_name, $subject);
        $subject = preg_replace ("/\[LastName\]/", $last_name, $subject);
        
        $message = preg_replace ("/\[FirstName\]/", $first_name, $message);
        $message = preg_replace ("/\[LastName\]/", $last_name, $message);
        $message = preg_replace ("/\[SenderFirstName\]/", $sender_first_name, $message);
        $message = preg_replace ("/\[SenderLastName\]/", $sender_last_name, $message);
        $message = preg_replace ("/\[RefLink\]/", $ref_link, $message);
        $message = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $message);
        
        $this->data = array (
            
            "MAIN_HEADER" => $this->pageHeader,
            "MESS" => $mess,
            "SUBJECT" => $subject,
            "MESSAGE" => $message,            
            
        );
        
        $bgcolor = "";
        for ($i=1; $i<=10; $i+=1)
        {
            $first_name= "<input type='text' name='first_name".$i."' value='' style='width: 120px;'>";
            $last_name= "<input type='text' name='last_name".$i."' value='' style='width: 120px;'>";
            $email= "<input type='text' name='email".$i."' value='' style='width: 210px;'>";
            $bgcolor = ($bgcolor == "") ? "#E7E7E7" : "";
            $this->data ['FORM_ROW'][] = array (
                    "COL_NUMBER" => $i,
                    "COL_FIRST_NAME" => $first_name,
                    "COL_LAST_NAME" => $last_name,
                    "COL_EMAIL" => $email,
                    "COL_BGCOLOR" => $bgcolor,
                );
        }
                
    }
    
    //--------------------------------------------------------------------------
    function ocd_send ()
    {
        $member_id = $this->member_id;
        
        $SiteUrl = $this->db->GetSetting ("SiteUrl");
        $SiteTitle = $this->db->GetSetting ("SiteTitle");
                
        $sender_first_name = $this->dec($this->db->GetOne ("Select first_name From members Where member_id='$member_id'"));
        $sender_last_name = $this->dec($this->db->GetOne ("Select last_name From members Where member_id='$member_id'"));
        $sender_email = $this->db->GetOne ("Select email From members Where member_id='$member_id'");
        
        $ReferrerUrl = $this->db->GetSetting ("ReferrerUrl");
        $ref_id=$this->db->GetOne ("Select $ReferrerUrl From `members` Where member_id='$member_id'", 1);
        $ref_link = $SiteUrl."?ref=".$ref_id;
        $headers = "From: $sender_first_name $sender_last_name <$sender_email>\r\n";
        
        $row = $this->db->GetEntry ("Select * From `emailtempl` Where `emailtempl_id`='10'", "");
        $subject_origin = $this->dec($row ["subject"]);
        $message_origin = $this->dec($row ["message"]);
        
        $k = 0;
        for ($i=1; $i<=10; $i+=1)
        {
            $subject = $subject_origin;
            $message = $message_origin;
                
            $email = "email".$i;
            $first_name = "first_name".$i;
            $last_name = "last_name".$i;

            $email = $this->GetGP($email, "");
            $first_name = $this->GetGP($first_name, "");
            $last_name = $this->GetGP($last_name, "");

            if ($this->validEmail($email))
            {
                $k++;
                $subject = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $subject);
                $subject = preg_replace ("/\[SenderFirstName\]/", $sender_first_name, $subject);
                $subject = preg_replace ("/\[SenderLastName\]/", $sender_last_name, $subject);
                $subject = preg_replace ("/\[FirstName\]/", $first_name, $subject);
                $subject = preg_replace ("/\[LastName\]/", $last_name, $subject);
                  
                $message = preg_replace ("/\[FirstName\]/", $first_name, $message);
                $message = preg_replace ("/\[LastName\]/", $last_name, $message);
                $message = preg_replace ("/\[SenderFirstName\]/", $sender_first_name, $message);
                $message = preg_replace ("/\[SenderLastName\]/", $sender_last_name, $message);
                $message = preg_replace ("/\[RefLink\]/", $ref_link, $message);
                $message = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $message);
                
                sendMail ($email, $subject, $message, $headers);
                    
            }
        }
        $this->Redirect ("tellfriend.php?m=ok&k=$k");
        
    }
}
//------------------------------------------------------------------------------

$zPage = new ZPage ("tellfriend");

$zPage->Render ();

?>