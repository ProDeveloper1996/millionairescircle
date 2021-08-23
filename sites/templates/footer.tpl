<!-- BEGIN: FOOTER -->

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-8">
                    <p>{FOOTER_CONTENT}</p>
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
              autoplayTimeout:1500,
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