<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}
<style type="text/css">
    .getmatrix {
        font-size: 12px;
        color: #000;
    }
</style>
<h2>{MAIN_HEADER}</h2>

<div id="navcont_1" class="navbar-collapse collapse">
    <ul class="nav nav-tab-type-1">
        {MAIN_LINKS}
    </ul>
</div>
<div style="
    overflow: auto;
    min-width: 700px;
">


    <link href="/css/circle.css" rel="stylesheet">

    {MAIN_CONTENT}

</div>




<script type="text/javascript" language="JavaScript">
    var block = false;
    $(document).ready(function () {
        //$(".canvas span:empty").remove();

        $("a.getmatrix").on('click', function (e) {
            if (!block) click(this);
            return false;
        });
        $('span').popover();
    });

    function click($this) {
        var el = $($this);
        var id = el.attr('id').replace('m', '');
        var reentry = el.data('reentryd');
        var level = el.data('level');
        var place = el.data('place');

        block = true;
        var is_show = el.parent().find('table').length;
        //var  is_show = $('a#m'+id+'[data-reentry="'+reentry+'"]').parent().find('table').length;
        if (is_show > 0) {
            el.parent().find('table').remove();
            block = false;
            return false;
        }

        $.ajax({
            type: "post",
            url: "/member/tree_matrix.php",
            data: "ocd=getmatrix&id=" + id + "&reentry=" + reentry + "&level=" + level + "&place=" + place,
            dataType: "html", //JSON
            success: function (msg) {
                //console.log(msg);
                el.parent().append(msg);
                $("a.getmatrix").unbind('click');
                $("a.getmatrix").on('click', function (e) {
                    if (!block) click(this);
                    return false;
                });
                el.parent().find('span').popover();
                block = false;
            },
            error: function (msg) {
                console.error(msg);
                block = false;
            }
        });

    }
</script>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->