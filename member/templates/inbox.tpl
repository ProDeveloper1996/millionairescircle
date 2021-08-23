<!-- BEGIN: MAIN -->
{FILE {HEADER_TEMPLATE}}

<h2>{MAIN_HEADER}
    <button type="button" class="navbar-toggle collapsed nav-togle-type-2" data-toggle="collapse"
            data-target="#navcont_1" aria-expanded="false" aria-controls="navcont_1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
</h2>

<div class="navbar-collapse collapse">
    <ul class="nav nav-tab-type-1" style="margin-bottom: 0px;">
        <li class="{ACTIVE_INBOX}"><a href="./inbox.php?f=inbox">Inbox</a></li>
        <li class="{ACTIVE_SENT}"><a href="./inbox.php?f=sent">Sent</a></li>
        <li class="{ACTIVE_DELETED}"><a href="./inbox.php?f=deleted">Deleted</a></li>
        <li class="{ACTIVE_NEW}"><a href="./inbox.php?f=new">New message</a></li>
    </ul>
</div>


<!-- BEGIN: TABLE -->
<form method='POST' id="indoxForm" data-type="{FORM_TYPE}">
    <table width='100%' border='0' cellspacing='0' cellpadding='4' class="inbox simple-little-table"
           style='margin-top:10px;'>
        <tr bgcolor='#475567'>
            <th class='b_border' align='center' style="width: 200px;">{HEAD_MEMBER}</th>
            <th class='b_border' align='center'>{HEAD_SUBJECT}</th>
            <th class='b_border' align='center' style="width: 100px;">{HEAD_DATE}</th>
            <th class='b_border' align='center' style="width: 1px;">
                <input type="checkbox" name="all_check_del" id="all_check_del">
                <span class="inbox_delete">Delete</span>
            </th>
        </tr>

        <!-- BEGIN: TABLE_ROW -->
        <tr>
            <td class='b_border' style='text-align:left;'>{ROW_MEMBER}</td>
            <td class='b_border' align='left'><a href="?ocd=view&f={FORM_TYPE}&id={ROW_ID}">{ROW_SUBJECT}</a></td>
            <td class='b_border'>{ROW_DATE}</td>
            <td class='b_border'><input type="checkbox" name="check_del[]" value="{ROW_ID}" class="inp_checkbox"></td>
        </tr>
        <!-- END: TABLE_ROW -->

        <!-- BEGIN: TABLE_EMPTY -->
        <tr>
            <td class='b_border' colspan='4' align='center'>{DICT.INB_ListEmpty}</td>
        </tr>
        <!-- END: TABLE_EMPTY -->

    </table>
</form>
<!-- END: TABLE -->


<!-- BEGIN: FORMNEW -->
<div>
    <form action='?f=new' method='POST' enctype='multipart/form-data'>
        <div class="form-group">
            <div class="row">
                <span class='error'>{TO_ERROR}</span>
                <label class="col-sm-3 control-label">{DICT.INB_To} :</label>
                <div class="col-sm-9">
                    <input class="form-control" type="text" name="to" value="{TO}" style="width:200px;" maxlength="200">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <span class='error'>{SUBJECT_ERROR}</span>
                <label class="col-sm-3 control-label">{DICT.INB_Subject} :</label>
                <div class="col-sm-9">
                    <input class="form-control" type="text" name="subject" value="{SUBJECT}" maxlength="512">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <span class='error'>{BODY_ERROR}</span>
                <label class="col-sm-3 control-label">{DICT.INB_Body} :</label>
                <div class="col-sm-9">
                    <textarea class="form-control" name="body" rows="10">{BODY}</textarea>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <span class='error'>{ATTACH_ERROR}</span>
                <label class="col-sm-3 control-label">{DICT.INB_Files} :</label>
                <div class="col-sm-9">
                    <input type='file' class="form-control" name='attach'/>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label class="col-sm-3 control-label"></label>
                <div class="col-sm-9">
                    <button type="submit" class="btn btn-form-type-3" style="width: 200px;"><i
                                class="fa fa-check"></i> {DICT.INB_Send}</button>
                </div>
            </div>
        </div>

        <input type='hidden' name='ocd' value='send'/>
    </form>
</div>
<!-- END: FORMNEW -->


<!-- BEGIN: FORMVIEW -->
<div>
    <div class="form-group">
        <div class="row">
            <label class="col-sm-3 control-label">{DICT.INB_Member} :</label>
            <div class="col-sm-9">
                <b>{TO}</b> ({DATE})
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-sm-3 control-label">{DICT.INB_Subject} :</label>
            <div class="col-sm-9">
                {SUBJECT}
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-sm-3 control-label">{DICT.INB_Body} :</label>
            <div class="col-sm-9">
                {BODY}
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-sm-3 control-label">{DICT.INB_Files} :</label>
            <div class="col-sm-9">
                {ATTACH}
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <label class="col-sm-3 control-label"></label>
            <div class="col-sm-9">
                <a href="?f={FORM_TYPE}" class="btn btn-form-type-3" style="width: 150px; text-decoration: none"><i class="fa fa-chevron-left"></i> back</a>
                <a href="?f=new&id={ID}&reply" class="btn btn-form-type-3" style="width: 150px; text-decoration: none"><i class="fa fa-check"></i> {DICT.INB_Reply}</a>
            </div>
        </div>
    </div>
</div>
<!-- END: FORMVIEW -->


<div class="form-group" style="margin-top:20px">
    <div class="row">
        {MAIN_PAGES}
    </div>
</div>


<script>
    $(document).ready(function () {
        $('.inbox_delete').click(function () {
            if (!$('.inp_checkbox').is(':checked')) return;
            if (window.confirm('Are you sure?')) {
                var form = $('#indoxForm');
                $.ajax({
                    type: "POST",
                    url: '',
                    data: form.serialize() + "&type=" + form.data('type') + "&ocd=delMessage",
                    success: function (response) {
                        //console.log(response);
                        //$('.inp_checkbox:checked').parents('tr').remove()
                        document.location.reload(true);
                    }
                });

                //window.location.reload(true);
            }
        });
        $('#all_check_del').click(function () {
            if ($(this).is(':checked')) $('.inp_checkbox').prop('checked', true);
            else $('.inp_checkbox').prop('checked', false);
        });


    });
</script>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->