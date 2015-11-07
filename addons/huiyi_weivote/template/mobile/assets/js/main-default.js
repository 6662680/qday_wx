/*
 # =============================================================================
 #   Sparkline Linechart JS
 # =============================================================================
 */


(function() {


    $(document).ready(function() {

        /*
         # =============================================================================
         #   Navbar scroll animation
         # =============================================================================
         */

        $(".navbar.scroll-hide").mouseover(function() {
            $(".navbar.scroll-hide").removeClass("closed");
            return setTimeout((function() {
                return $(".navbar.scroll-hide").css({
                    overflow: "visible"
                });
            }), 150);
        });
        $(function() {
            var delta, lastScrollTop;
            lastScrollTop = 0;
            delta = 50;
            return $(window).scroll(function(event) {
                var st;
                st = $(this).scrollTop();
                if (Math.abs(lastScrollTop - st) <= delta) {
                    return;
                }
                if (st > lastScrollTop) {
                    $('.navbar.scroll-hide').addClass("closed");
                } else {
                    $('.navbar.scroll-hide').removeClass("closed");
                }
                return lastScrollTop = st;
            });
        });
        /*
         # =============================================================================
         #   Mobile Nav
         # =============================================================================
         */

        $('.navbar-toggle').click(function() {
            return $('body, html').toggleClass("nav-open");
        });


        /*
         # =============================================================================
         #   FitVids 让视频响应屏幕尺寸变化
         # =============================================================================
         */

        $(".timeline-content").fitVids();


    });

}).call(this);