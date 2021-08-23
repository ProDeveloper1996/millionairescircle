<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

<table width='100%' border='0' cellspacing='0' cellpadding='0' class='ptitle'>
    <tr><td>{MAIN_HEADER} <span title="You can administer (add/edit/delete) Banners created by you for its usage by members for promoting site purposes" class="vtip"><img src='./images/question.png'></span></td></tr>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>    
    <tr><td align='left'><H3>Add New Banner</H3></td></tr>
      <tr><td height='20'><H4>
      <form action='{MAIN_ACTION}' method='POST' enctype='multipart/form-data'>
          <table width='100%' border='0' cellspacing='0' cellpadding='2'>
              <tr>
                  <td>Title:</td>
                  <td>
                      <input type='text' name='title' value='' maxlength='250' style='width: 500px;' />
                  </td>
              </tr>
              <tr>
                  <td>File:</td>
                  <td>
                      <input type='file' name='photo' value='' style='width: 320px;' />
                  </td>
              </tr>
              <tr>
                  <td>
                  </td>
                  <td>
                      <input class='some_btn' type='submit' value=" Add " /> {MAIN_ERROR}
                  </td>
              </tr>
          
          
          
          <input type='hidden' name='ocd' value='insert' />
      </form> 
    
    </td></tr>
</table>
<hr>
<table width='100%' border='0' cellspacing='0' cellpadding='2' class="simple-little-table">
    <tr>
        <td>{HEAD_ID}</td>
        <td width='30%'>{HEAD_PHOTO} </td>
        <td>{HEAD_TITLE} </td>
        <td colspan='2'>Actions</td>
    </tr>

    <!-- BEGIN: TABLE_ROW -->
    <tr>
        <td width='25'>{ROW_ID}</td>
        <td>{ROW_PHOTO}</td>
        <td>{ROW_TITLE}</td>
        <td width='25'>{ROW_ACTIVELINK} </td>
        <td width='25'>{ROW_DELLINK}</td>
    </tr>
    <!-- END: TABLE_ROW -->

    <!-- BEGIN: TABLE_EMPTY -->
    <tr>
        <td colspan='5' align='center'>No banners were created by you yet</td>
    </tr>
    <!-- END: TABLE_EMPTY -->

</table>
{MAIN_PAGES}

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->