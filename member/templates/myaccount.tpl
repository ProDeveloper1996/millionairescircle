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
                        
                        <div style='text-align:center;'>
                            <span class='message'>{MAIN_CONFIRM}</span>  
                        </div>
                        <div id="navcont_1" class="navbar-collapse collapse">
                            <ul class="nav nav-tab-type-1">
                                    {ACCOUNT_TABS}
                            </ul>  
                        </div>    
                        <div class="tab-content">
                            
<!-- tab body Overview -->                             
                            <div role="tabpanel" class="tab-pane {OVERVIEW}" id="Overview">
                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-sm-4 control-label">{DICT.MyAcc_RegistrationDate}:</label>
                                        <label class="col-sm-4 control-label">{ACCOUNT_REGISTRATION}</label>
                                    </div>    
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-sm-4 control-label">{DICT.MyAcc_LastAccess}:</label>
                                        <label class="col-sm-4 control-label">{ACCOUNT_LAST_ACCESS}</label>
                                    </div>    
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-sm-4 control-label">{DICT.MyAcc_YourAccountID}:</label>
                                        <label class="col-sm-4 control-label">{ACCOUNT_ID}</label>
                                    </div>    
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-sm-4 control-label">{DICT.MyAcc_YourReferralLink}:   </label>
                                        <div class="col-sm-8">
                                        {ACCOUNT_LINK}

                                        </div>
                                    </div>    
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-sm-4 control-label">{ACCOUNT_LANDS_TITLE}</label>
                                        <div class="col-sm-8">{ACCOUNT_LANDS}</div>
                                    </div>    
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-sm-4 control-label">{DICT.MyAcc_YourEnrollerID}:</label>
                                        <label class="col-sm-4 control-label">{ACCOUNT_ENROLLER}</label>
                                    </div>    
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-sm-4 control-label">{DICT.MyAcc_SponsoredMembers}</label>
                                        <label class="col-sm-4 control-label">{ACCOUNT_SPONSORS}</span> [ <a href='sponsors.php'>{DICT.MyAcc_Details}</a> ]</label>
                                    </div>    
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-sm-4 control-label">{DICT.MyAcc_YourLevel}:</label>
                                        <label class="col-sm-4 control-label"><span class='answer'>{ACCOUNT_LEVEL}</span> {ACCOUNT_UPGRADE}</label>
                                    </div>    
                                </div>
                                {ACCOUNT_STATUS}
                                <!-- BEGIN: ACCOUNT_EARNED-->
                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-sm-4 control-label">{DICT.MyAcc_Amountearned}:</label>
                                        <label class="col-sm-4 control-label"><a href='./cash.php' >{CURRENCY}{ACCOUNT_CASH}</a></label>
                                    </div>
                                </div>
                                <!-- END: ACCOUNT_EARNED-->
                            </div>
<!-- tab body Overview end-->                             
                            
<!-- tab body Access -->                            
                            <div role="tabpanel" class="tab-pane {ACCESSSETTINGS}" id="Access">                                
                               <form action='{MAIN_ACTION}?accesssettings' method='POST' enctype='multipart/form-data'>
                                        <div class="form-group">
                                            <div class="row">
                                                <span class='error'>{OVERVIEW_FIRSTNAME_ERROR}</span>
                                                <label class="col-sm-4 control-label">{DICT.MyAcc_FirstName} :</label>
                                                <div class="col-sm-8">
                                                    {OVERVIEW_FIRSTNAME}
                                                </div>
                                            </div>    
                                        </div>                              
                                        <div class="form-group">
                                            <div class="row">
                                                <span class='error'>{OVERVIEW_LASTNAME_ERROR}</span>
                                                <label class="col-sm-4 control-label">{DICT.MyAcc_LastName} :</label>
                                                <div class="col-sm-8">
                                                    {OVERVIEW_LASTNAME}
                                                </div>
                                            </div>    
                                        </div>                              
                                        <div class="form-group">
                                            <div class="row">
                                                <span class='error'>{OVERVIEW_EMAIL_ERROR}</span>
                                                <label class="col-sm-4 control-label">{DICT.MyAcc_EmailAddress} :</label>
                                                <div class="col-sm-8">
                                                    {OVERVIEW_EMAIL}
                                                </div>
                                            </div>    
                                        </div>                              
                                        <div class="form-group">
                                            <div class="row">
                                                <span class='error'>{OVERVIEW_USERNAME_ERROR}</span>
                                                <label class="col-sm-4 control-label">{DICT.MyAcc_Username} :</label>
                                                <div class="col-sm-8">
                                                    {OVERVIEW_USERNAME}
                                                </div>
                                            </div>    
                                        </div>                           
                                        <div class="form-group">
                                            <div class="row">
                                                <label class="col-sm-4 control-label">{DICT.MyAcc_Password} :</label>
                                                <div class="col-sm-8">
                                                    {OVERVIEW_PASSWD}
                                                    <a id="popover-pass-btn" href="security.php">
                                                        <i class="fa fa-key"></i>
                                                    </a>
<!--
                                                    <div class="popover bottom" id="popover-pass">
                                                        <div class="arrow"></div>
                                                        <div class="popover-content">
                                                            <h3>Security Settings</h3>
                                                            <input type="text" class="form-control" placeholder="Your username">
                                                            <input type="password" class="form-control" placeholder="Your new password">
                                                            <input type="password" class="form-control" placeholder="Your new password (confirm)">
                                                            <input type="password" class="form-control" placeholder="Current Password">
                                                            <div class="form-group col-xs-6 text-left">
                                                                <div class="row">
                                                                    <button class="btn btn-form-type-1">UPDATE</button>
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-xs-6 text-right">
                                                                <div class="row">
                                                                    <button id="cancel_btn" class="btn btn-form-type-1">CANCEL</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
-->                                                    
                                                </div>
                                            </div>    
                                        </div>                            

                                        <div class="form-group">
                                            <div class="row">
                                                <span class='error'>{OVERVIEW_AVATAR_ERROR}</span>
                                                <label class="col-sm-4 control-label">Avatar :</label>
                                                <div class="col-sm-8">
                                                    {OVERVIEW_AVATAR}
                                                </div>
                                            </div>    
                                        </div>                           

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-form-type-3"><i class="fa fa-check"></i> {DICT.MyAcc_Update}</button>
                                        </div>
                                    <input type='hidden' name='ocd_type' value='ACCESSSETTINGS' />
                                    <input type='hidden' name='ocd' value='update' />
                                </form>
                             </div>
<!-- tab body Access end -->
                            
<!-- tab body Address -->                            
                            <div role="tabpanel" class="tab-pane {ADDRESSSETTINGS}" id="Address">
                               <form action='{MAIN_ACTION}?addresssettings' method='POST'>
                                <div class="form-group">
                                    <div class="row">
                                        <span class='error'>{ACCOUNT_STREET_ERROR}</span>
                                        <label class="col-sm-4 control-label">{DICT.MyAcc_Address} :</label>
                                        <div class="col-sm-8">
                                            {ACCOUNT_STREET}
                                        </div>
                                    </div>    
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <span class='error'>{ACCOUNT_CITY_ERROR}</span>
                                        <label class="col-sm-4 control-label">{DICT.MyAcc_City} :</label>
                                        <div class="col-sm-8">
                                            {ACCOUNT_CITY}
                                        </div>
                                    </div>    
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <span class='error'>{ACCOUNT_STATE_ERROR}</span>
                                        <label class="col-sm-4 control-label">{DICT.MyAcc_State} :</label>
                                        <div class="col-sm-8">
                                            {ACCOUNT_STATE}
                                        </div>
                                    </div>    
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <span class='error'>{ACCOUNT_COUNTRY_ERROR}</span>
                                        <label class="col-sm-4 control-label">{DICT.MyAcc_Country} :</label>
                                        <div class="col-sm-8">
                                            {ACCOUNT_COUNTRY}
                                        </div>
                                    </div>    
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <span class='error'>{ACCOUNT_POSTAL_ERROR}</span>
                                        <label class="col-sm-4 control-label">{DICT.MyAcc_PostalCode} :</label>
                                        <div class="col-sm-8">
                                            {ACCOUNT_POSTAL}
                                        </div>
                                    </div>    
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <span class='error'>{ACCOUNT_PHONE_ERROR}</span>
                                        <label class="col-sm-4 control-label">{DICT.MyAcc_Phone} :</label>
                                        <div class="col-sm-8">
                                            {ACCOUNT_PHONE}
                                        </div>
                                    </div>    
                                </div>
                                
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-form-type-3"><i class="fa fa-check"></i> {DICT.MyAcc_Update}</button>
                                        </div>
                                    <input type='hidden' name='ocd_type' value='ADDRESSSETTINGS' />
                                    <input type='hidden' name='ocd' value='update' />
                                </form>
                            </div>
<!-- tab body Address end -->                             

<!-- tab body Payment -->                             
                            <div role="tabpanel" class="tab-pane {PAYMENTSETTONGS}" id="Payment">                                
                               <form action='{MAIN_ACTION}?paymentsettongs' method='POST'>
                                <div class="form-group">
                                    <div class="row">
                                        <span class='error'>{ACCOUNT_PROCESSOR_ERROR}</span>
                                        <label class="col-sm-4 control-label">{DICT.MyAcc_PaymentProcessor} :</label>
                                        <div class="col-sm-8">
                                            {ACCOUNT_PROCESSOR}
                                        </div>
                                    </div>    
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <span class='error'>{ACCOUNT_ACCOUNT_ID_ERROR}</span>
                                        <label class="col-sm-4 control-label">{DICT.MyAcc_AccountID} :</label>
                                        <div class="col-sm-8">
                                            {ACCOUNT_ACCOUNT_ID}
                                        </div>
                                    </div>    
                                </div>
                                
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-form-type-3"><i class="fa fa-check"></i> {DICT.MyAcc_Update}</button>
                                        </div>
                                    <input type='hidden' name='ocd_type' value='PAYMENTSETTONGS' />
                                    <input type='hidden' name='ocd' value='update' />
                                </form>
                            </div>
<!-- tab body Payment end-->                             
                            
                        </div>

{FILE {FOOTER_TEMPLATE}}

<!-- END: MAIN -->