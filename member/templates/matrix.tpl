<!-- BEGIN: MAIN -->
{FILE {HEADER_TEMPLATE}}

                        <h2>{MAIN_HEADER}
                            <button type="button" class="navbar-toggle collapsed nav-togle-type-2" data-toggle="collapse" data-target="#navcont_1" aria-expanded="false" aria-controls="navcont_1">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </h2>

                        <div id="navcont_1" class="navbar-collapse collapse">
                            <ul class="nav nav-tab-type-1">
                                    {MAIN_LINKS}
                            </ul>  
                        </div>    

<div style="width: 900px;overflow: auto;  border: 0px solid #1187FF;">{MAIN_CONTENT}</div>


{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->