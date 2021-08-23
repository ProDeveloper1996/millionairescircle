<?php

require_once ("../includes/config.php");
require_once ("../includes/xtemplate.php");
require_once ("../includes/xpage_member.php");
require_once ("../includes/utilities.php");


class ZPage extends XPage
{

protected  $parget_arr = Array ('Messages', 'Max Deals', 'Classifieds', 'Pledge Dreams', 'Elevator Pitches');

    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        XPage::XPage ($object);
        $this->mainTemplate = "./templates/inbox.tpl";
        $this->pageTitle = "Inbox";
        $this->pageHeader = "Inbox";
        $this->rowsPerPage=10;

		$this->main_header_search=$this->get_header_search();
		$this->main_search_action='sn_myportal.php';
		$this->main_search_val_default='Search Maxalus';
    }

	// список категорий search
	function get_header_search()
	{
		$us=$this->db->GetEntry("Select `account_type`,`biz_type` From `members` Where member_id='".$this->member_id."' AND `count_login`='1' " , "");
	    $res = '<select name="section_sel" >'
			.($us['biz_type']>1?'<option value="1" >Source Products</option>':'' ).'
			<option value="2" >Retail Products</option>
			<option value="3" >Item Listings</option>
			<option value="4" >Max Deals</option>
	      <option value="5" >Social Clubs</option>
	    </select>';
	    return $res;

	}
	//--------------------------------------------------------------------------
	function ocd_search ()
	{
		switch($this->GetID('section_sel',0)){
			case 1:
				$sec='psource.php';
			break;
			case 2:
				$sec='pretail.php';
			break;
			case 3:
				$sec='itemlisting.php';
			break;
			case 4:
				$sec='deals.php';
			break;
			case 5:
				$sec='mysocial_clubs.php';
			break;
		}
		$this->Redirect('/member/'.$sec.'?search_val='.$this->GetGP('search_val','').'&section_sel=0&ocd=search&searchfrom=');
	}

    //--------------------------------------------------------------------------
    function ocd_list ()  {
      $uri = $this->pageUrl;
      $uri = parse_url ($uri);
//      $uri_par = '-1';
      $uri_mess = 'inbox';
      if (($uri['path']=='/member/inbox.php')&&isset($uri['query'])) {
        $uri = parseQueryString ($uri['query']);
//        $uri_par = $uri['par'];
      }
      $sn_messages = new sn_messages(Array('member_id' => $this->member_id));
      $count_mess = $sn_messages->count_inb_mess();
      unset ($sn_messages);
      if ($count_mess!=0) $count_mess = '('.$count_mess.')';
      else $count_mess ='';
      if (isset($uri['mess'])) $uri_mess = $uri['mess'];

        $html= '<br />
      <div id="inbox-mess">
        <a href="/member/inbox.php?mess=inbox" class="'.($uri_mess=='inbox'?'active_link':'').'">Inbox</a>
        <div class="inbox_palka">&nbsp;</div>
        <a href="/member/inbox.php?mess=sent" class="'.($uri_mess=='sent'?'active_link':'').'">Sent Messages</a>
        <div class="inbox_palka">&nbsp;</div>
        <a href="/member/inbox.php?mess=del" class="'.($uri_mess=='del'?'active_link':'').'">Deleted Messages</a>
        <div class="inbox_palka">&nbsp;</div>
        <a href="/member/inbox.php?mess=new" class="'.($uri_mess=='new'?'active_link':'').'">New Messages</a>

		  <div style="clear:both;"></div>
      </div>
        ';

		$this->inbox_topmenu = $html;

    // работаем с сообщениями
      $mess = $this->enc($this->GetGP('mess','inbox'));
      $mem_info['member_id'] = $this->member_id;

      switch ($mess) {
        case('inbox');
          $sn_messages = new sn_messages($mem_info);
          $get_mess['tofrom'] = 'From';
          $get_mess['delact'] = 'mess';
          $get_mess['mess'] = $sn_messages->get_mess_inbox();
				$get_events_mem = $this->sn_events->get_events_mem();
				$get_mess['mess']+=$get_events_mem;
				krsort($get_mess['mess']);
        break;
        case('sent');
          $sn_messages = new sn_messages($mem_info);
          $get_mess['tofrom'] = 'To';
          $get_mess['delact'] = 'mess';
          $get_mess['mess'] = $sn_messages->get_mess_sent();
        break;
        case('del');
          $sn_messages = new sn_messages($mem_info);
          $get_mess['tofrom'] = 'To/From';
          $get_mess['delact'] = 'messdel';
          $get_mess['mess'] = $sn_messages->get_mess_del();
        break;
        case('new');
          return $this->mess_send_form();
        break;
        default:

        break;

      }

//debug( $get_mess['mess']);

        $total =  count($get_mess['mess']);

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "HEAD_ID" => $this->Header_GetSortLink ("id", "ID"),
            "HEAD_TITLE" => $this->Header_GetSortLink ("short_descr", "short_descr"),
            "HEAD_TOFROM" => $get_mess['tofrom'],
            "HEAD_NAME" => 'Subject',
            "HEAD_DATE" => 'Date',
            "HEAD_DEL" => "<input type='checkbox' name='all_check_del' id='all_check_del' class='inp_checkbox all_check_del' >",
            "HEAD_DELLINK" => $get_mess['delact'],
            "MAIN_PAGES" => $this->Pages_GetLinksPN ($total, '?mess='.$mess),
        );

        $pg = $this->GetID("pg");

        $bgcolor = "";
        if ($total != 0) {
			 $cnt=$this->GetID("pg")*$this->rowsPerPage;
          $cnt_start=$cnt;
          $cnt_end=$cnt+$this->rowsPerPage;
			 $i=0;

			 foreach ($get_mess['mess'] as $key => $val) {
			 	if ($i < $cnt_start) {$i++;continue;}

				if ( $cnt >= $cnt_end ) break;
			 	$cnt++;

            $id = ( isset($val['id'])?$val['id']:$val['mess_id'] );
            $idmem = ( isset($val['event_id'])?$val['event_id']:$val['messmem_id'] );
            $read='';
            if ($val['read_mark']==0) {
              $read = 'font-weight: bold;';
            }

            // кнопка reply и мембер в поле tofrom
            $inbreply = '';
            if ($get_mess['tofrom']=='From') {
              $inbreply = '<a href="javascript:void(0)" class="inb_reply">Reply</a>';
              $col_tofrom = ( isset($val['event_mem_id'])?$val['event_mem_id']:$val['from_member_id'] );
              $col_tofrom_n = $val['from_member_name'];
            }
            else {
              $col_tofrom = $val['to_member_id'];
              $col_tofrom_n = $val['to_member_name'];
            }

             $this->data ['TABLE_ROW'][] = array (
                 "ROW_FNAME" => '<a href="/member/sn_myportal.php?id_v='.$col_tofrom.'" target="_blank">'.$col_tofrom_n.'</a>',
                 "ROW_TOMEM" => $col_tofrom,
                 "ROW_EVENT" => '<a href="javascript:void(0)" class="getmess" id="messid'.$idmem.'">'.$val['subj'].'</a>',
                 "ROW_SUBJ" => strip_tags($val['subj']),
                 "ROW_MESS" => $val['mess'],
                 "ROW_MESSMEM" => $idmem,
                 "ROW_INBREPLY" => $inbreply,
                 "ROW_DATE" => date ('d M Y H:i',$val['date']),
                 "ROW_READ" => $read,
                 "ROW_DEL" => "<input type='checkbox' name='check_del[]' value='".( isset($val['event_mem_id'])?'e':'m' ).$idmem."' class='inp_checkbox' >",
             );
          }
        }
        else {
            $this->data ['TABLE_EMPTY'][] = array (
                "ROW_BGCOLOR" => $bgcolor
            );
        }
    }












    // показываем события
    function get_events ($event_name) {
      $par = $this->enc($this->GetGP('par'));
      $mess = $this->enc($this->GetGP('mess'));
      $headfrom = 'From';
      $headname = 'Name';
      switch ($par) {
        case('0');
         // $this->sn_events->mem_ev(true);
         $headname = '';
        break;
        case('1');
          $headname = 'Deals '.$headname;
          $headfrom = 'To';
        break;
        case('2');
          $headname = 'Classified '.$headname;
        break;
        case('3');
          $headname = '';
        break;
        case('4');
          $headname = '';
        break;
        case('5');
          $headname = 'Travel Deals '.$headname;
          $headfrom = 'To';
        break;

      }

      switch ($mess) {
        case('inbox');
        break;
        case('sent');
          $this->sn_events->is_sent(true);
        break;
      }
/*
      switch ($mess) {
        case('inb');
         // $this->sn_events->mem_ev(true);
         $headfrom = 'From';
         $headname = 'Classified Name';
        break;
        case('sent');
          $headname = 'Classified Name';
          $headfrom = 'To';
          $this->sn_events->is_sent(true);
        break;

        case('inbox');
         $headfrom = 'From';
         $headname = 'Deals Name';
        break;

        default:
          $headfrom = 'From';
          $headname = 'Name';
        break;

      }
*/
        $total =  $this->sn_events->total_events_mem(false,$event_name);

        $total_arr = explode (',',$total);
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "HEAD_ID" => $this->Header_GetSortLink ("id", "ID"),
            "HEAD_TITLE" => $this->Header_GetSortLink ("short_descr", "short_descr"),
            "MAIN_PAGES" => $this->Pages_GetLinks (count($total_arr), $this->pageUrl."?"),
            "HEAD_TOFROM" => $headfrom,
            "HEAD_NAME" => $headname,
            "HEAD_DATE" => 'Date',
            "HEAD_DEL" => "<input type='checkbox' name='all_check_del' id='all_check_del' class='inp_checkbox all_check_del' >",
            "HEAD_DELLINK" => 'event',
        );

        $bgcolor = "";
        if ($total != 0) {
          $get_events_mem = $this->sn_events->get_events_mem($event_name);
          foreach ($get_events_mem as $key => $val) {
            $id = $val['event_id'];
            $read='';
            if ($val['read_mark']==0) {
              $read = 'font-weight: bold;';
            }

                $this->data ['TABLE_ROW'][] = array (
                    "ROW_FNAME" => '<a href="/member/sn_board.php?id_v='.$val['event_mem_id'].'" target="_blank">'.$val['first_name'].'</a>',
                    "ROW_MEMID" => $val['event_mem_id'],
                    "ROW_EVENT" => $val['event'],
                    "ROW_DATE" => date ('d M Y H:i',$val['event_date']),
                    "ROW_READ" => $read,
                    "ROW_MESSMEM" => $id,
                    "ROW_DEL" => "<input type='checkbox' name='check_del[]' value='".$id."' class='inp_checkbox' >",

                );
          }

        }
        else {

            $this->data ['TABLE_EMPTY'][] = array (
                "ROW_BGCOLOR" => $bgcolor
            );
        }

    }

    private function mess_send_form ($opCode = "insert", $source = FORM_EMPTY) {
//      $list_addfr ='';
      $sent = $this->enc($this->GetGP('sent',0));
      if ($sent==1) $sent = 'Your message was sent.';
      else $sent = '';

      $this->mainTemplate = "./templates/inbox_send_form.tpl";

        switch ($source)
        {
            case FORM_FROM_DB:

                break;

            case FORM_FROM_GP:
					$selfiles = $this->GetGP ("fiends",'');
			      $list_fr=explode(';',rtrim($selfiles,';'));

	            $fr_select='';
			      if (!empty($selfiles))
						foreach ($list_fr as $val) $fr_select.='<img class="user_avatar" src="'.check_avatar($val).'">';

	            $fr_select_id=$selfiles;
                $subj = '<input type="text" style="width: 450px;" name="subj" value="'.$this->enc($this->GetGP('subj')).'" >';
                $mess = "<textarea rows='14' style='width: 450px; height: 140px;' id='message' name='message'>".$this->enc($this->GetGP('message'))."</textarea>";
                break;

            case FORM_EMPTY:
	            $fr_select='';
	            $fr_select_id='';
			      if ($this->GetGP('send_to_id')!='') {
			      	$fr_select_id=$this->GetGP('send_to_id').';';
		      		$fr_select='<img class="user_avatar" src="'.check_avatar($this->GetGP('send_to_id')).'">';
					}

                $subj = '<input type="text" style="width: 450px;" name="subj" value="" >';
                $mess = "<textarea rows='14' style='width: 450px; height: 140px;' id='message' name='message'></textarea>";

            default:

                break;
        }

      $uri = $this->pageUri;
      $uri = parse_url ($uri);
      $uri_mess = 'inbox';
      if (($uri['path']=='/member/inbox.php')&&isset($uri['query'])) {
        $uri = parseQueryString ($uri['query']);
      }
      $sn_messages = new sn_messages(Array('member_id' => $this->member_id));
      $count_mess = $sn_messages->count_inb_mess();
      unset ($sn_messages);
      if ($count_mess!=0) $count_mess = '('.$count_mess.')';
      else $count_mess ='';
      if (isset($uri['mess'])) $uri_mess = $uri['mess'];
        $html= '<br />
      <div id="inbox-mess">
        <a href="/member/inbox.php?mess=inbox" class="'.($uri_mess=='inbox'?'active_link':'').'">Inbox</a>
        <div class="inbox_palka">&nbsp;</div>
        <a href="/member/inbox.php?mess=sent" class="'.($uri_mess=='sent'?'active_link':'').'">Sent Messages</a>
        <div class="inbox_palka">&nbsp;</div>
        <a href="/member/inbox.php?mess=del" class="'.($uri_mess=='del'?'active_link':'').'">Deleted Messages</a>
        <div class="inbox_palka">&nbsp;</div>
        <a href="/member/inbox.php?mess=new" class="'.($uri_mess=='new'?'active_link':'').'">New Messages</a>

		  <div style="clear:both;"></div>
      </div>
        ';
		$this->inbox_topmenu = $html;

        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_ACTION" => $this->pageUrl,
            "MAIN_OCD" => $opCode,

            "MAIN_SENT" => $sent,
//            "MAIN_FR" => $list_fr,
            "MAIN_FR_ERROR" => $this->GetError ("list_fr"),
//            "MAIN_ADDFR" => $list_addfr,
            "MAIN_SUBJ" => $subj,
            "MAIN_SUBJ_ERROR" => $this->GetError ("subj"),
            "MAIN_MESS" => $mess,
            "MAIN_MESS_ERROR" => $this->GetError ("mess"),

            "MAIN_FR_SELECT" => $fr_select,
            "MAIN_FR_SELECT_ID" => $fr_select_id,

        );


    }

    private function listfr_select () {

      $list_fr = $this-> sn_events->get_par('list_fr');
      $html ='<select id="inblistfr">';
      $html .='<option value="0">Select Friend</option>';
      foreach ($list_fr as $key => $val) {
        $html .='<option value="'.$val['member_id'].'">'.$val['first_name'].' '.$val['last_name'].'</option>';
      }
      $html .='</select>';

      return $html;
    }

    function ocd_insert() {
      $subj = $this->enc ($this->GetValidGP ("subj", "Subject", VALIDATE_NOT_EMPTY));
      $mess = $this->enc ($this->GetValidGP ("message", "Message", VALIDATE_NOT_EMPTY));
      if (strlen ($mess ) < 11) {
        $this->SetError ("mess", "The \"Message\" is too short.");
      }

      //$list_fr =  $this->GetGPArray('list_fr');
		$selfiles = $this->GetGP ("fiends");
      $list_fr=explode(';',rtrim($selfiles,';'));

      if (empty($selfiles)) {
        $this->SetError ("list_fr", "You need to select any friends.");
      }
      else $list_fr = array_unique($list_fr);

        if ($this->errors['err_count'] > 0) {
            $this->mess_send_form ("insert", FORM_FROM_GP);
        }
        else {

          // отправляем сообщение пользователям
          $mem_info['member_id'] = $this->member_id;
          $message['mess'] = $mess;
          $message['subj'] = $subj;
          $sn_messages = new sn_messages($mem_info);
          $sn_messages -> add_mess($list_fr, $message);


          $this->Redirect ('/member/inbox.php?mess=new&sent=1');
        }
    }
    // отправка сообщения через ajax
    function ocd_sendmess () {
      $tomem[] = $this->enc($this->GetGP("tomem"));
      $subj = $this->enc($this->GetGP("subj"));
      $mess = $this->enc($this->GetGP("mess"));
      // отправляем сообщение пользователям
      $mem_info['member_id'] = $this->member_id;
      $message['mess'] = $mess;
      $message['subj'] = $subj;
      $sn_messages = new sn_messages($mem_info);
      $sn_messages -> add_mess($tomem, $message);

      die('{"status":"0"}');
      //die('{"status":"0","fr_count":"'.$fr_count.'"}');
    }
    // помечаем сообщение прочитанным
    function ocd_readmark () {
      $idmess = $this->enc($this->GetGP("idmess"));
      $mem_info['member_id'] = $this->member_id;
      $sn_messages = new sn_messages($mem_info);
      $sn_messages -> read_mark($idmess);

      die('{"status":"0"}');
    }

    function ocd_deletemess() {
      $idmess = $this->enc($this->GetGP("idmess"));
      $delact = $this->enc($this->GetGP("delact"));
      if ($idmess != '') {
        $mem_info['member_id'] = $this->member_id;
        $idmess = explode(',',$idmess);
        if ($delact=='mess') {
          $sn_messages = new sn_messages($mem_info);
          foreach ($idmess as $key) {
            $sn_messages-> makedel_mess ($key);
          }
        }
        else if ($delact=='messdel') {
          $sn_messages = new sn_messages($mem_info);
          foreach ($idmess as $key) {
            $sn_messages-> del_mess ($key);
          }
        }
        else if ($delact=='event') {
          foreach ($idmess as $key) {
            $this-> sn_events-> del_events_mem ($key);
            //$this-> sn_events->add_par('lisasd','asd');
          }
        }


      }


      die('{"status":"0","msg":"'.$idmess.'"}');
    }

    function ocd_deletemess_new () {
      $idmess = $this->enc($this->GetGP("idmess"));
      $delact = $this->enc($this->GetGP("delact"));
      if ($idmess != '') {
        $mem_info['member_id'] = $this->member_id;
        $idmess = explode(',',$idmess);
        if ($delact=='mess') {
          $sn_messages = new sn_messages($mem_info);
          foreach ($idmess as $key) {
				if ($key[0]=='m'){
            	$key=str_replace('m','',$key);
					$sn_messages-> makedel_mess ($key);
   			}
				if ($key[0]=='e'){
            	$key=str_replace('e','',$key);
	            $this-> sn_events-> del_events_mem ($key);
   			}
          }
        }
        else if ($delact=='messdel') {
          $sn_messages = new sn_messages($mem_info);
          foreach ($idmess as $key) {
           	$key=str_replace('m','',$key);
            $sn_messages-> del_mess ($key);
          }
        }
        else if ($delact=='event') {
          foreach ($idmess as $key) {
            $this-> sn_events-> del_events_mem ($key);
            //$this-> sn_events->add_par('lisasd','asd');
          }
        }

      }
      die('{"status":"0","msg":"'.$idmess.'"}');
    }

}

//------------------------------------------------------------------------------

$zPage = new ZPage ("inbox");

$zPage->Render ();

?>
