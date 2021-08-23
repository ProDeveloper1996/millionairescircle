<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

{FILE {INBOXTOPMENU_TEMPLATE}}

<form action="/member/inbox.php?mess=new" method="POST" enctype="multipart/form-data">
<table width='100%' style="width:100%" border='0' cellspacing='0' cellpadding='0' class="inbox_new">
    <tr class="inbox-head" style="height: 26px;">
        <td colspan='2'></td>
    </tr>
  <tr class="" >
    <td width='20%' class="col_name"> </td>
    <td width='80%' class="col_inp"> <span class='error'><h3> {MAIN_SENT}</h3> </span> </td>  
  </tr>    
  <tr class="" >
    <td width='20%' class="col_name" style="vertical-align: top;"> <span class='signs_b'>To: </span> <sup><font color="#FF0000">*</font></sup></td>
    <td width='80%' class="col_inp"> 
		    <span class='error'>{MAIN_FR_ERROR}</span>
			 <div id="fiends_icons">{MAIN_FR_SELECT}</div>
			<input type="hidden" style="width: 230px;" name="fiends" id="fiends" value="{MAIN_FR_SELECT_ID}" > 
			<input type="button" id="addfriends" class='some_btn' value=" Add friends ">
     </td>  
  </tr>
  <tr class="" >
    <td width='20%' class="col_name"> <span class='signs_b'>Subject: </span> <sup><font color="#FF0000">*</font></sup></td>
    <td width='80%' class="col_inp"> <span class='error'>{MAIN_SUBJ_ERROR}</span> {MAIN_SUBJ} &nbsp; </td>  
  </tr>
  <tr class="" >
    <td width='20%' class="col_name" style="vertical-align: top;"> <span class='signs_b'>Message: </span> <sup><font color="#FF0000">*</font></sup></td>
    <td width='80%' class="col_inp"> <span class='error'>{MAIN_MESS_ERROR}</span> {MAIN_MESS} &nbsp; </td>  
  </tr>
    <tr style='height:5px;'>
        <td colspan='2'>
        </td>
    </tr>

    <tr>
        <td></td>
        <td>
            <input type='submit' value=" SEND " class='some_btn'> &nbsp;
            <input type='hidden' name='ocd' value='{MAIN_OCD}'>
        </td>
    </tr>  
  
</table>
</form>
<script>
$(document).ready(function(){
  $("#addfriends").live("click", function(e){
   	$( "#dialog-attach" ).dialog({
         title: 'Share With Friends',
         close: function(event, ui) {
         }
   	});
    $( "#dialog-attach" ).html('<center><img style="margin-top:70px;" src="images/wait1.gif" alt="loading.."/></center>');
    $( "#dialog-attach" ).dialog( "open" );
    load_select_friends($("#dialog-attach"),'','');
  });
});

function load_select_friends(id_dialog,numb,symb) {
    $f='';
    if ($('#fiends').length>0) $f=$('#fiends').val();
    if ($('#dlg_tmp_files_FRD').length>0) $f=$('#dlg_tmp_files_FRD').val();
    $.ajax({
      type: "POST",
      url: "/member/sn_friends.php",
      data: "ocd=getShareWithFriends&done=done_select_friends&numb="+numb+"&symb="+symb+"&selfriends="+$f,
      dataType: "html", //JSON
      success: function(msg){
			//alert(msg);
			id_dialog.html(msg);
      },
      error: function (msg) {
	      //alert (msg.status);
	      alert (msg);
      }
    });
}
function done_select_friends() {
    $("#loading_dlg").show();
	 $("#fiends").val($('#dlg_tmp_files_FRD').val());
//         $( "#dialog-attach" ).html(); 
//         $( "#dialog-attach" ).dialog( "close" );
    $.ajax({
      type: "POST",
      url: "/member/sn_friends.php",
      data: "ocd=getSelectFriends&selfriends="+$('#fiends').val(),
      dataType: "HTML",
      success: function(msg){
        //alert(msg.msg);
         $( "#dialog-attach" ).html(); 
         $( "#dialog-attach" ).dialog( "close" );
			$('#fiends_icons').html(msg);
      },
      error: function (msg) {
      //alert (msg.status);
         $("#loading_dlg").hide();
         alert ('Error send');
      }
    });

}

</script>
                 
{FILE {SOCNETRIGHT_TEMPLATE}}         

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->
