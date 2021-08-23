<?php

//$path_to_site = $_SERVER['argv'][1];
//if (substr ($path_to_site, -1) == "/") $path_to_site = substr ($path_to_site, 0, -1);

require_once ("../includes/config.php");
require_once ("../includes/xpage_admin.php");
require_once ("../includes/utilities.php");

$subject = decU ($db->GetSetting ("subjectMail"));
$message_origin = decU ($db->GetSetting ("messageMail"));

$SiteTitle = $db->GetSetting ("SiteTitle");
$SiteUrl = $db->GetSetting ("SiteUrl");
$ContactEmail = $db->GetSetting ("ContactEmail");

$header  = "From: $SiteTitle <$ContactEmail>\r\n";

$result = $db->ExecuteSql ("Select * From `members`, `selected` Where members.member_id=selected.member_id");
while ($row = $db->FetchInArray ($result))
{
    $member_id = $row['member_id'];

    $first_name = decU ($row['first_name']);
    $last_name = decU ($row['last_name']);
    $email = $row['email'];
    $username = $row['username'];
    $enroller_id = $row['enroller_id'];

    $ref_link = $SiteUrl."?spon=".$member_id;
    $message = $message_origin;
    

    $message = preg_replace ("/\[SiteTitle\]/", $SiteTitle, $message);
    $message = preg_replace ("/\[SiteUrl\]/", $SiteUrl, $message);
    $message = preg_replace ("/\[FirstName\]/", $first_name, $message);
    $message = preg_replace ("/\[LastName\]/", $last_name, $message);
    $message = preg_replace ("/\[ID\]/", $member_id, $message);
    $message = preg_replace ("/\[Username\]/", $username, $message);
    $message = preg_replace ("/\[Email\]/", $email, $message);
    $message = preg_replace ("/\[RefLink\]/", $ref_link, $message);
    $message = preg_replace ("/\[SponsorID\]/", $enroller_id, $message);
    
    sendMail ($email, $subject, $message, $header);
    $db->ExecuteSql ("Insert Into `tester` (description) values ('$message')");
    
}
?>