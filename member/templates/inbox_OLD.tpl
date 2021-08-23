<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

{FILE {INBOXTOPMENU_TEMPLATE}}

<table width='100%' style="width:100%" border='0' cellspacing='0' cellpadding='0' class="inbox_list">

       
    <tr class="inbox-head">
        <td width="20%">{HEAD_TOFROM}</td>
        <td width="">{HEAD_NAME}</td>
        <td width="20%">{HEAD_DATE}</td>
        <td width="10%">{HEAD_DEL} <a href="javascript:void(0);" class="inbox_delete" onclick="delete_mess('{HEAD_DELLINK}');" >Delete</a></td>
    </tr>
<!-- BEGIN: TABLE_ROW -->
<tr style="{ROW_READ}" class="inbox_list_tr">
 <td>{ROW_FNAME}</td> 
 <td>{ROW_EVENT} </td>
 <td> {ROW_DATE}</td>
 <td> {ROW_DEL} </td>
</tr>
<tr class="inbox_list_tr_mess" id="blmessm{ROW_MESSMEM}">
 <td colspan="4">
 {ROW_MESS}
 

 <div class="block_reply_mess">
  <form action="/member/inbox.php" method="POST" enctype="multipart/form-data" name="from_inbreply" class="from_inbreply">
    <input type='hidden' name='tomem' value='{ROW_TOMEM}'>
    <input type='hidden' name='ocd' value='sendmess'>
    <input type="text" style="width: 450px;" name="subj" value="Re: {ROW_SUBJ}" > <br>
    <textarea rows='14' style='width: 450px; height: 140px;' id='mess' name='mess' class='mess' ></textarea> <br>
    <input type='submit' value=" SEND " class='some_btn'> <img class="loading" id="load{ROW_MESSMEM}"  src="images/45.gif" alt="loading.."/>    
  </form>
 </div>

 <div class="block_reply"> <a href="javascript:void(0)" class="inb_close">X</a> {ROW_INBREPLY} <div style="clear:both"></div></div>

 </td> 
</tr>
<!-- END: TABLE_ROW -->
    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td colspan='4' style='text-align: center;'>The Inbox is empty</td>
    </tr>
    <!-- END: TABLE_EMPTY -->
</table>


<table width="80%"><tr><td><div class="fr_pagination"> {MAIN_PAGES} &nbsp; </div></td></tr></table>


<script type="text/javascript">
$(document).ready(function(){
  //alert('asd');
  send_mess();
  getmess();
  

});

// удаление сообщений или событий
function delete_mess (delact) {

  check_mess = $("input[name='check_del[]']:checked");
    ids = [];
        check_mess.each(function() {
          id_mess = $(this).val();
          ids.push(id_mess);
          mess = $('#blmess'+id_mess);
          mess.prev('.inbox_list_tr:first').remove();
          mess.remove();
          
        });

     if (ids!='') {
      ids = ids.join(',');
      $.ajax({
        type: "POST",
        url: "/member/inbox.php",
        data: "delact="+delact+"&idmess="+ids+"&ocd=deletemess_new",
        dataType: "JSON",
        success: function(msg){
          //alert(msg.msg);
				location.href=location.href;
        },
        error: function (msg) {
        //alert (msg.status); 
        }
      });      
     }
  
  
  return false;
}

function getmess () {

  $(".inb_reply").live("click", function(e){
    par = $(this).parent();
//    reply_bl = par.next();
    reply_bl = par.prev();
    hideshow_reply (reply_bl);
    //alert(reply_bl.attr('class'));
  });
  // помечаем сообщение как прочитанное
  $(".getmess").live("click", function(e){
    par = $(this).parents().parent('.inbox_list_tr');
    par_next = par.next();
    hideshow_mess (par, par_next);
    
    par_weight = par.css('font-weight');
    if (par_weight == 'bold' || par_weight == '700') {
      idmess = get_number2($(this).attr('id'));
      //отправляем отметку что сообщение прочитано
      $.ajax({
        type: "POST",
        url: "/member/inbox.php",
        data: "idmess="+idmess['numb']+"&ocd=readmark",
        dataType: "JSON",
        success: function(msg){

          par.css('font-weight','400');
          
          // уменьшаем счётчик сообщений
          h_inbox_count = $('#h_inbox_count');
          m_mess_count = $('#m_mess_count');
          count_down (h_inbox_count);
          count_down (m_mess_count); 
          
        },
        error: function (msg) {
        //alert (msg.status); 
        }
      });    
    }
    
    //alert();
  });
  
  $(".inb_close").live("click", function(e){
    par = $(this).parents().parent('.inbox_list_tr_mess');
    par_prev =par.prev();
    hideshow_mess (par_prev, par);
  });
}

function count_down (el) {
  el_text = el.text();
  if (el_text!='') {
    el_text = el_text.replace(/\((\d)*\)/gi,'$1');
    el_text = parseInt(el_text);
    if (el_text==1) el.text('');
    else {
      el_text = el_text -1;
      el.text('('+el_text+')');
    }
  }
}

function send_mess () {


  $('.from_inbreply').submit(function(e) {
    thisform = $(this);
    loading = $(this).find('.loading').attr('id');
    idmess = get_number2 (loading);
    par = $('#blmess'+idmess['numb']);
    hideshow(loading,1);
    
    $.ajax({
      type: "POST",
      url: "/member/inbox.php",
      data: thisform.serialize(),
      dataType: "JSON",
      success: function(msg){
        hideshow_reply(par.find('.block_reply_mess'));
        par_prev =par.prev();
        hideshow_mess (par_prev, par);
        hideshow(loading,0);
      },
      error: function (msg) {
      //alert (msg.status); 
      }
    }); 
      
    e.preventDefault();
  });


}

function hideshow_mess(el, el_next) {
  el_css = el_next.css('display');
  //alert(el_css);
  if (el_css=='none') {
    el.css('border-left','1px solid #d4d4d4');
    el.css('border-right','1px solid #d4d4d4');
    el_next.css('display','table-row');
  }
  else {
    el.css('border-left','none');
    el.css('border-right','none');  
    el_next.css('display','none');
  }
}

function hideshow_reply(el) {
  el_css = el.css('display');
  //alert(el_css);
  if (el_css=='none') {
    el.css('display','block');
  }
  else {
    el.css('display','none');
  }
}
</script>
                 
{FILE {SOCNETRIGHT_TEMPLATE}}         

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
