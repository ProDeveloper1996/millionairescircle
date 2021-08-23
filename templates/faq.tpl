<!-- BEGIN: MAIN -->

{FILE {HEADER_TEMPLATE}}

    <div class="container-fluid Title">
        <span class="fa-stack fa-1">
            <i class="fa fa-circle-thin fa-stack-2x"></i>
            <i class="fa fa-stack-1x">?</i>
        </span>
        {MAIN_HEADER}
    </div>
    
      
    <div class="container faq-content">
        <div class="row">

<!-- BEGIN: QUESTION_ROW -->
<!-- faq item-->       
            <div class="col-xs-12 col-sm-6 col-md-4">
                <a class="btn-faq collapsed" data-toggle="collapse" href="#faq-item-{ROW_ID}" aria-expanded="false">
                     {ROW_QUESTION}
                    <div class="faq-number">{ROW_ID}</div>
                </a>
                <div class="collapse" id="faq-item-{ROW_ID}">
                    <p>
                    {ROW_ANSWER}
                    </p>
                </div>
            </div>
<!-- faq item end-->   
    <!-- END: QUESTION_ROW -->

    <!-- BEGIN: FAQ_EMPTY -->
            <div class="col-xs-12 col-sm-6 col-md-4">
                {DICT.Empty_List}
            </div>
    <!-- END: FAQ_EMPTY -->   
   
        </div>
    </div>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->