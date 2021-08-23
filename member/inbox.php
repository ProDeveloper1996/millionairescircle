<?php

require_once("../includes/config.php");
require_once("../includes/xtemplate.php");
require_once("../includes/xpage_member.php");
require_once("../includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage($object)
    {
        GLOBAL $dict;
        $this->mainTemplate = "./templates/inbox.tpl";
        $this->pageTitle = $dict['INB_pageTitle'];
        $this->pageHeader = $dict['INB_pageTitle'];

        $this->folders = ['inbox', 'sent', 'deleted', 'new'];
        $this->orderDefault = "date";
        $this->orderDirDefault = 'desc';

        XPage::XPage($object);
    }

    //--------------------------------------------------------------------------
    function ocd_list($data = null)
    {
        GLOBAL $dict;

        $folder = $this->GetGP('f', 'inbox');
        if (!in_array($folder, $this->folders)) $this->Redirect('myaccount.php');

        $this->data = array(
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "HEAD_SUBJECT" => 'Subject',
            "HEAD_DATE" => $this->Header_GetSortLink("date", 'Date'),

            "TO_ERROR" => $this->GetError("to"),
            "SUBJECT_ERROR" => $this->GetError("subject"),
            "ATTACH_ERROR" => $this->GetError("attach"),

        );


        $this->data['ACTIVE_' . strtoupper($folder)] = 'active';

        switch ($folder) {
            case 'inbox':
                $this->data['HEAD_MEMBER'] = $this->Header_GetSortLink("member", 'From');
                $this->data['TABLE'] = ['-' => '-'];
                $this->data['TABLE']['FORM_TYPE'] = 'inbox';
                $this->getInbox();
                break;
            case 'sent':
                $this->data['HEAD_MEMBER'] = $this->Header_GetSortLink("member", 'To');
                $this->data['TABLE'] = ['-' => '-'];
                $this->data['TABLE']['FORM_TYPE'] = 'sent';
                $this->getSent();
                break;
            case 'deleted':
                $this->data['HEAD_MEMBER'] = 'To/From';
                $this->data['TABLE'] = ['-' => '-'];
                $this->data['TABLE']['FORM_TYPE'] = 'deleted';
                $this->getDeleted();
                break;
            case 'new':
                $this->data['FORMNEW'] = ['-' => '-'];
                if ( isset($_GET['reply']) ) $data = $this->getReply();
                if ($data) $this->data = array_merge($this->data, $data);
                break;
        }

    }

    function getReply()
    {
        $id = $this->GetGP('id', 0);
        $row = $this->db->GetEntry("
          Select m.*, mem.username 
          From messages as m, members as mem
          WHERE m.txid='$id' and mem.member_id=m.name_member_id
        ");
        $data['FORMNEW'] = [
            'TO' => $row['username'],
            'SUBJECT' => 'RE: '.htmlspecialchars($this->dec($row['subject'])),
            'BODY' => htmlspecialchars($this->dec($row['body']))
        ];
        return $data;
    }

    function getInbox()
    {
        $sql_select = '';

        $total = $this->db->GetOne("Select Count(*) From `{$this->object}` Where is_deleted=0 $sql_select And to_member_id='{$this->member_id}' and member_id='{$this->member_id}' ", 0);

        $this->data["MAIN_PAGES"] = $this->Pages_GetLinks($total, $this->pageUrl . "?f=inbox&");

        if ($total > 0) {
            $result = $this->db->ExecuteSql("
                Select m.*, CONCAT(mem.first_name, ' ', mem.last_name) as member 
                From messages as m, members as mem
                WHERE m.is_deleted=0 and mem.member_id=m.name_member_id $sql_select And 
                m.to_member_id='{$this->member_id}' and m.member_id='{$this->member_id}' 
                Order By {$this->orderBy} {$this->orderDir}
            ", true);
            while ($row = $this->db->FetchInArray($result)) {
                $member = $row['member'];
                $date = date("d M Y H:i", $row['date']);
                $subject = htmlspecialchars($this->dec($row['subject']));
                if (empty($row['is_read']))
                    $subject = "<i class='fa fa-circle' aria-hidden='true' ></i><b>$subject</b>";
                if (!empty($row['attach']))
                    $subject .= '<i class="fa fa-paperclip" aria-hidden="true"></i>';

                $this->data['TABLE']['TABLE_ROW'][] = array(
                    "ROW_ID" => $row['txid'],
                    "ROW_MEMBER" => $member,
                    "ROW_SUBJECT" => $subject,
                    "ROW_DATE" => $date,
                );
            }

        } else
            $this->data['TABLE']['TABLE_EMPTY'][] = ['-' => '-'];

    }

    function getSent()
    {
        $sql_select = '';

        $total = $this->db->GetOne("Select Count(*) From `{$this->object}` Where is_deleted=0 $sql_select And member_id='{$this->member_id}' and to_member_id!='{$this->member_id}' ", 0);

        $this->data["MAIN_PAGES"] = $this->Pages_GetLinks($total, $this->pageUrl . "?f=sent&");

        if ($total > 0) {
            $result = $this->db->ExecuteSql("
                Select m.*, CONCAT(mem.first_name, ' ', mem.last_name) as member 
                From messages as m, members as mem
                WHERE m.is_deleted=0 and mem.member_id=m.name_member_id $sql_select And 
                m.member_id='{$this->member_id}' and m.to_member_id!='{$this->member_id}' 
                Order By {$this->orderBy} {$this->orderDir}
            ", true);
            while ($row = $this->db->FetchInArray($result)) {
                $member = $row['member'];
                $date = date("d M Y H:i", $row['date']);
                $subject = htmlspecialchars($this->dec($row['subject']));
                if (!empty($row['attach']))
                    $subject .= '<i class="fa fa-paperclip" aria-hidden="true"></i>';

                $this->data['TABLE']['TABLE_ROW'][] = array(
                    "ROW_ID" => $row['txid'],
                    "ROW_MEMBER" => $member,
                    "ROW_SUBJECT" => $subject,
                    "ROW_DATE" => $date,
                );
            }

        } else
            $this->data['TABLE']['TABLE_EMPTY'][] = ['-' => '-'];

    }

    function getDeleted()
    {
        $sql_select = '';

        $total = $this->db->GetOne("Select Count(*) From `{$this->object}` Where is_deleted=1 $sql_select And 
          ((member_id='{$this->member_id}' and to_member_id!='{$this->member_id}') ||
           (member_id='{$this->member_id}' and to_member_id='{$this->member_id}') )
        ", 0);
        $this->data["MAIN_PAGES"] = $this->Pages_GetLinks($total, $this->pageUrl . "?f=deleted&");

        if ($total > 0) {
            $result = $this->db->ExecuteSql("
                Select m.*, CONCAT(mem.first_name, ' ', mem.last_name) as member 
                From messages as m, members as mem
                WHERE m.is_deleted=1 and mem.member_id=m.name_member_id $sql_select And 
                ((m.member_id='{$this->member_id}' and m.to_member_id!='{$this->member_id}') ||
                   (m.member_id='{$this->member_id}' and m.to_member_id='{$this->member_id}') )
                Order By `date` asc
            ", true);
            while ($row = $this->db->FetchInArray($result)) {
                $member = $row['member'];
                $date = date("d M Y H:i", $row['date']);
                $subject = htmlspecialchars($this->dec($row['subject']));
                if (!empty($row['attach']))
                    $subject .= '<i class="fa fa-paperclip" aria-hidden="true"></i>';

                $this->data['TABLE']['TABLE_ROW'][] = array(
                    "ROW_ID" => $row['txid'],
                    "ROW_MEMBER" => $member,
                    "ROW_SUBJECT" => $subject,
                    "ROW_DATE" => $date,
                );
            }

        } else
            $this->data['TABLE']['TABLE_EMPTY'][] = ['-' => '-'];

    }

    function ocd_send()
    {
        GLOBAL $dict;

        $to = $this->enc($this->GetValidGP("to", $dict['INB_To'], VALIDATE_NOT_EMPTY));
        $subject = $this->enc($this->GetValidGP("subject", $dict['INB_Subject'], VALIDATE_NOT_EMPTY));
        $body = $this->enc($this->GetGP("body", ''));

        if ($this->errors['err_count'] == 0) {
            if (array_key_exists("attach", $_FILES) and $_FILES['attach']['error'] < 3) {
                //$types = $_FILES['attach']['type'];
                //$types_array = explode("/", $types);
                if (strpos($_FILES['attach']['name'], 'php') !== false)
                    $this->SetError("attach", $dict['INB_AttachError']);

            }

            // определяем мембера
            $to_member_id = $this->db->GetOne("Select member_id FROM members WHERE username='$to' ", 0);
            if ($to_member_id == 0) $this->SetError("to", $dict['INB_NoMembers']);

        }

        if ($this->errors['err_count'] > 0) {
            $data['FORMNEW']['TO'] = $to;
            $data['FORMNEW']['SUBJECT'] = $subject;
            $data['FORMNEW']['BODY'] = $body;
            $this->ocd_list($data);
            return;
        }

        $txid1 = md5(time());
        $txid2 = md5(time()+1);
        // добавили себе
        $this->db->ExecuteSql("
            Insert into `messages` 
            (txid, member_id, name_member_id, to_member_id, subject, body, `date`) 
            values ('$txid1','{$this->member_id}', '$to_member_id', '$to_member_id', '" . $this->enc($subject) . "', '" . $this->enc($body) . "', '" . time() . "')
        ");

        // добавили получателю
        $this->db->ExecuteSql("
            Insert into `messages` 
            (txid, member_id, name_member_id, to_member_id, subject, body, `date`) 
            values ('$txid2','$to_member_id', '{$this->member_id}', '$to_member_id', '" . $this->enc($subject) . "', '" . $this->enc($body) . "', '" . time() . "')
        ");

        // save attach
        if (array_key_exists("attach", $_FILES) and $_FILES['attach']['error'] < 3) {
            $tmp_name = $_FILES['attach']['tmp_name'];
            if (is_uploaded_file($tmp_name)) {
                $file_name = $_FILES['attach']['name'];
                $physical_path = $this->db->GetSetting("PathSite");
                move_uploaded_file($tmp_name, $physical_path . "data/inbox/" . $file_name);
                $cmd = "chmod 666 " . $physical_path . "data/inbox/" . $file_name;
                @exec($cmd, $output, $retval);
                @chmod($physical_path . "data/inbox/" . $file_name, 0644);
                $this->db->ExecuteSql("Update messages Set attach='$file_name' Where txid='$txid1' ");
                $this->db->ExecuteSql("Update messages Set attach='$file_name' Where txid='$txid2' ");
            }

        }

        $this->Redirect('inbox.php?f=sent');
    }

    function ocd_delMessage()
    {
        $type = $this->GetGP('type');
        if (!in_array($type, $this->folders)) return;
        if (empty($_POST['check_del'])) return;

        if ($type!='deleted')
            $this->db->ExecuteSql("Update messages Set is_deleted=1 Where txid in ('".implode("','",$_POST['check_del'])."')");
        else
            $this->db->ExecuteSql("Delete From messages Where is_deleted=1 and txid in ('".implode("','",$_POST['check_del'])."')");
        return 'OK';
    }

    function ocd_view()
    {
        $folder = $this->GetGP('f', 'inbox');
        $id = $this->GetGP('id', '');
        if (!in_array($folder, $this->folders) || empty($id))
            $this->Redirect('myaccount.php');

        $this->data = array(
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "FORMVIEW" => ['-' => '-'],

        );
        $this->data['ACTIVE_' . strtoupper($folder)] = 'active';

        $row = $this->getMessage($id, $folder);
        if (empty($row)) $this->Redirect('myaccount.php');

        // признак прочтения
        $this->db->ExecuteSql("Update messages Set is_read=1 Where txid='$id' ");

        $attach = '';
        if (!empty($row['attach'])) {
            $attach = '<a href="?ocd=getAttach&f=' . $folder . '&id=' . $id . '"><i class="fa fa-paperclip" aria-hidden="true"></i> ' . $row['attach'] . '</a>';
        }
        $this->data['FORMVIEW'] = [
            'ID' => $row['txid'],
            'TO' => $this->dec($row['member']),
            'SUBJECT' => htmlspecialchars($this->dec($row['subject'])),
            'BODY' => htmlspecialchars($this->dec($row['body'])),
            'DATE' => date("d M Y H:i", $row['date']),
            'ATTACH' => $attach,
            "FORM_TYPE" => $folder,
        ];
    }

    function getMessage($id = 0, $folder = '')
    {
        $row = null;
        switch ($folder) {
            case 'inbox':
                $row = $this->db->GetEntry("
                Select m.*, CONCAT(mem.first_name, ' ', mem.last_name) as member 
                    From messages as m, members as mem
                    WHERE m.txid='$id' and mem.member_id=m.name_member_id  
                ");
                break;
            case 'sent':
                $row = $this->db->GetEntry("
                Select m.*, CONCAT(mem.first_name, ' ', mem.last_name) as member 
                    From messages as m, members as mem
                    WHERE m.txid='$id' and mem.member_id=m.name_member_id  
                ");
                break;
            case 'deleted':
                $row = $this->db->GetEntry("
                Select m.*, CONCAT(mem.first_name, ' ', mem.last_name) as member 
                    From messages as m, members as mem
                    WHERE m.txid='$id' and mem.member_id=m.name_member_id  
                ");
                break;
        }
        return $row;
    }

    function ocd_getAttach()
    {
        $folder = $this->GetGP('f', 'inbox');
        $id = $this->GetGP('id', '');
        if (!in_array($folder, $this->folders) || empty($id))
            $this->Redirect('myaccount.php');

        $row = $this->getMessage($id, $folder);
        if (empty($row)) $this->Redirect('myaccount.php');

        $physical_path = $this->db->GetSetting("PathSite");
        $file = $physical_path . "data/inbox/" . $row['attach'];
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            // читаем файл и отправляем его пользователю
            readfile($file);
            exit();
        } else {
            header("HTTP/1.0 404 Not Found");
            exit();
        }
    }

}

//------------------------------------------------------------------------------

$zPage = new ZPage ("messages");

$zPage->Render();

?>

