                    </div>
                </div>
            </div>
        </div>  
    </div>  

<!-- BEGIN: FOOTER -->
<!-- BEGIN: NEWS -->
        <div class="row">
            <div class="container-fluid news-head text-center">
                <span class="fa-stack fa-1">
                  <i class="fa fa-circle-thin fa-stack-2x"></i>
                  <i class="fa fa-align-justify fa-stack-1x"></i>
                </span>
                Latest News
            </div>
            <div class="container-fluid news-content">
                <div class="container">
                    <div class="row">
{NEWS}
                    </div>
                </div>
            </div>
        </div>  
    </div>
<!-- END: NEWS -->
      
    <div class="clearfix"></div>  

    <footer>
        <div class="container customcont">
            <div class="row">
                <div class="col-xs-12 col-sm-8">
                    <p>{FOOTER_CONTENT}</p>
                </div>
                <div class="col-xs-12 col-sm-8 text-right-not-xs">
                </div>
            </div>
        </div>  
    </footer>  

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/js/owl.carousel.min.js"></script>
      
    <script>
            $(document).ready(function(){

                $('#processor').change(function(){
                    if ( $(this).val() == -1 ) 
                        $(this).parents('form').find('button[type=submit]').html('<i class="fa fa-check"></i> Buy Now');
                    else 
                        $(this).parents('form').find('button[type=submit]').html('<i class="fa fa-check"></i> Preview');
                })
                
// Carousel            
          $(".owl-main").owlCarousel({
              loop:true,
              dots:true,
              autoplay:true,
              autoplayHoverPause:true,
              autoplayTimeout:1500,
              items:1
          });
// Tooltip   
          $(function () {
              $('[data-toggle="tooltip"]').tooltip()
          });
            
// Show Hide popover-pass         
/*
            $('#popover-pass-btn').on("click",function(e){
                $('#popover-pass').toggleClass("visible");
                e.stopPropagation();
                return false;
            });
            $('#popover-pass #cancel_btn').on("click",function(){
                $("#popover-pass").removeClass("visible");
                e.stopPropagation();
                return false;
            });
            $(document).click(function(event) {
                if (
                    $(event.target).closest("#popover-pass").length ||
                    $(event.target).closest("#popover-pass-btn").length
                ) return;
                $("#popover-pass").removeClass("visible");
                event.stopPropagation();
            });
*/
        });
    </script>  
  </body>
</html>
<!-- END: FOOTER -->