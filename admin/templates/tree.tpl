<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}
<style type="text/css">
	.getmatrix{
		font-size: 12px;
		color: #000;
	}
</style>
<table width='100%' border='0' cellspacing='0' cellpadding='2' class='ptitle'>
    <tr><td >{MAIN_HEADER}</td>
        <td align='right'><div class="spoiler_style" onClick="open_close('spoiler1')">
         HELP GUIDE</div></td>
    </tr>
</table>
<table width='100%'>
    <tr>
      <td ><div id="spoiler1" style="display:none;">Here you can see the overall SPONSORSHIP of members in the system. This is NOT the matrix tree.</div></td>
    </tr>
</table> 
                        <div id="navcont_1" class="navbar-collapse collapse">
                            <ul class="nav nav-tab-type-1">
                                    {MAIN_LINKS}
                            </ul>  
                        </div> 
<div style="
    overflow: auto;
    min-width: 700px;
">
{MAIN_CONTENT}
</div>


<script type="text/javascript" language="JavaScript">
    $(document).ready(function() {
        $("a.getmatrix").on('click',function(e){
                click(this);
                return false;
        });
        //$('span').popover();	
    })
    function click($this){
		var el=$($this);
		var id = el.attr('id').replace('m','');
		var  reentry = el.data('reentryd');
		var  level = el.data('level');
		var  place = el.data('place');

		var  is_show = el.parent().find('table').length;
		//var  is_show = $('a#m'+id+'[data-reentry="'+reentry+'"]').parent().find('table').length;
		if ( is_show>0 ) {
			el.parent().find('table').remove();
			return false;
		}

		$.ajax({
			type: "post",
			url: "/admin/tree.php",
			data: "ocd=getmatrix&id="+id+"&reentry="+reentry+"&level="+level+"&place="+place,
			dataType: "html", //JSON
			success: function(msg){
				//console.log(msg);
				el.parent().append(msg);
			        $("a.getmatrix").unbind('click');
			        $("a.getmatrix").on('click',function(e){
			                click(this);
			                return false;
			        });
			        el.parent().find('span').popover();    	
			},
			error: function (msg) {
				console.error(msg);
			}
		});

    }
</script>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->