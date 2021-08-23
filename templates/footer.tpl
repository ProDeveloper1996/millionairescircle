<!-- BEGIN: FOOTER -->
<style>
.primer {
  display: inline-block;
  text-align: center;
  width: 30%;
  border: 2px solid grey;
  border-radius: 4px;
  margin: 10px;
}

.primer:hover {
  box-shadow: 0px 0px 5px 2px grey;
}
</style>

    <footer>
        <div class="container customcont" >
                         <div>
               <div class="primer">
                  <h2>${TOTAL_PAID}</h2>
                  <p>Total Deposits</p>
               </div>
               <div class="primer">
                  <h2>{TOTAL_MEMBERS}</h2>
                  <p>Total Members</p>
               </div>
               <div class="primer">
                  <h2>${TOTAL_IN_CASH_OUT}</h2>
                  <p>Total Withdrawals</p>
               </div>
            </div> 
            <div class="row">
                <div class="col-xs-12 col-sm-8">
                    <p class="ptext" id="1">{FOOTER_CONTENT}</p>
                </div>
                <div class="col-xs-12 col-sm-8 text-right-not-xs footerr" id="1">
		<a href="mailto:{SITE_EMAIL}">{DICT.ContactUs}</a>   
                </div>
            </div>
        </div> 
        
    </footer>  
 
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/js/jquery_1.11.2.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/owl.carousel.min.js"></script>
      
    <script>
        $(document).ready(function(){
// Carousel            
          $(".owl-main").owlCarousel({
              loop:true,
              dots:true,
              autoplay:true,
              autoplayHoverPause:true,
              autoplayTimeout:{CAROUSEL_AUTOPLAYTIMEOUT},
              items:1
          });
// Tooltip   
          $(function () {
              $('[data-toggle="tooltip"]').tooltip()
          });
            
        });
    </script>  
  </body>
</html>
<!-- END: FOOTER -->