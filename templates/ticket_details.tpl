<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

    <div class="container-fluid Title">
        <span class="fa-stack fa-1">
            <i class="fa fa-circle-thin fa-stack-2x"></i>
            <i class="fa fa-comments fa-stack-1x"></i>
        </span>
        {MAIN_HEADER}
    </div>
<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
     border-top: 0
}
</style>    
      
    <div class="container faq-content">
        <div class="_row">

                <div class="form-group">
                    <div class="row">
                        <label class="col-sm-4 control-label">Subject:</label>
                        <label class="col-sm-4 control-label">{MAIN_SUBJECT}</label>
                    </div>    
                </div>
                <div class="form-group">
                    <div class="row">
                        <label class="col-sm-4 control-label">Subjects of all your tickets:</label>
                        <label class="col-sm-4 control-label">
                            <form action='{MAIN_ACTION}' style="padding:0px;margin:0px;">
                                {MAIN_SELECT}
                                <input type='hidden' name='ocd' value='view'>
                            </form>
                        </label>
                    </div>    
                </div>
                <div class="form-group">
                    <div class="row">
                        <label class="col-sm-4 control-label">Ticket ID:</label>
                        <label class="col-sm-4 control-label">{MAIN_TICKET_ID}</label>
                    </div>    
                </div>
                <div class="form-group">
                    <div class="row">
                        <label class="col-sm-4 control-label">Created:</label>
                        <label class="col-sm-4 control-label">{MAIN_DATE_CREATE}</label>
                    </div>    
                </div>
                <div class="form-group">
                    <div class="row">
                        <label class="col-sm-4 control-label">Last Update:</label>
                        <label class="col-sm-4 control-label">{MAIN_LAST_UPDATE}</label>
                    </div>    
                </div>
                <div class="form-group">
                    <div class="row">
                        <label class="col-sm-4 control-label">Status:</label>
                        <label class="col-sm-4 control-label">{MAIN_STATUS}</label>
                    </div>    
                </div>
                <div class="form-group">
                    <div class="row">
                        <label class="col-sm-4 control-label"></label>
                        <label class="col-sm-4 control-label">{MAIN_MES}</label>
                    </div>    
                </div>
                <div class="form-group">
                    <div class="row">

                        <div class="table-bordered table-responsive" style="border-radius: 5px;">
                          <table class="table">
                            <tbody>
                            <!-- BEGIN: TABLE_ROW -->
                            <tr style="background: {ROW_BGCOLOR}">
                                <td width="20%" rowspan="2" class='w_border'>{ROW_FROM}</td>
                                <td class='w_border'>Posted on {ROW_DATE_POST}</td>
                            </tr>
                            <tr style="background: {ROW_BGCOLOR}">
                                <td class='w_border'>{ROW_MESSAGE}</td>
                            </tr>
                            <!-- END: TABLE_ROW -->
                            </tbody>
                          </table>
                        </div>

                    </div>    
                </div>

{MAIN_FORM}   


        </div>
    </div>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->