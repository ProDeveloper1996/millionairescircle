<?php

//installation process

if (is_dir ('install')) {
    header ("Location: ./install/install.php");
    exit ();
}

require_once ("./includes/config.php");
require_once ("./includes/xtemplate.php");
require_once ("./includes/xpage_public.php");
require_once ("./includes/utilities.php");

//------------------------------------------------------------------------------

class ZPage extends XPage
{
    //--------------------------------------------------------------------------
    function ZPage ($object)
    {
        XPage::XPage ($object);
        $this->mainTemplate = "./templates/index.tpl";
    }

    //--------------------------------------------------------------------------
    function ocd_list ()
    {
        $title = $this->dec ($this->db->GetOne ("Select title From `pages` Where page_id=1", ""));
        $content = $this->dec ($this->db->GetOne ("Select content From `pages` Where page_id=1", ""));
        //$this->mainTemplate = "./templates/content.tpl";
        $this->mainTemplate = "./templates/home.tpl";
        $this->pageTitle = $title;
        $this->pageHeader = $title;

            $slider='';
/*
			if ($_SERVER['REQUEST_URI']=='/' || $_SERVER['REQUEST_URI']=='/index.php'){
				$slider='
	          <div class="slides">
	              <ul> <!-- Слайды -->
	                  <li><img src="/images/pic1.jpg" alt="Start your Own Business" />
	                      <div>Start your Own Business</div>
	                  </li>
	                  <li><img src="/images/pic2.jpg" alt="Start Earning Right Now" />
	                      <div>Start Earning Right Now</div>
	                  </li>
	                  <li><img src="/images/pic3.jpg" alt="Create Your Network" />
	                      <div>Create Your Network</div>
	                  </li>
	                  <li><img src="/images/pic4.jpg" alt="To Your Success" />
	                      <div>It is Your Way To Success</div>
	                  </li>
	              </ul>
	          </div>
				';
			}
*/
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_CONTENT" => $content,
            "MAIN_SLIDER" => $slider,
        );

    }

    function ocd_register()
    {
        $title = $this->dec ($this->db->GetOne ("Select title From `pages` Where page_id=1", ""));
        $content = $this->dec ($this->db->GetOne ("Select content From `pages` Where page_id=1", ""));
        $this->mainTemplate = "./templates/home.tpl";
        $this->pageTitle = $title;
        $this->pageHeader = $title;
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_CONTENT" => $content,
        );
    }

    function ocd_login()
    {
        $title = $this->dec ($this->db->GetOne ("Select title From `pages` Where page_id=1", ""));
        $content = $this->dec ($this->db->GetOne ("Select content From `pages` Where page_id=1", ""));
        $this->mainTemplate = "./templates/home.tpl";
        $this->pageTitle = $title;
        $this->pageHeader = $title;
        $this->data = array (
            "MAIN_HEADER" => $this->pageHeader,
            "MAIN_CONTENT" => $content,
        );
    }




}

//------------------------------------------------------------------------------

$zPage = new ZPage ("index");

$zPage->Render ();

?>