/*
# =============================================================================
#   Sparkline Linechart JS
# =============================================================================
*/


(function() {
  

  $(document).ready(function() {
    
    /*
    # =============================================================================
    #   Isotope
    # =============================================================================
    */

    $container = $(".gallery-container");
    $container.isotope({});
    $(".gallery-filters a").click(function() {
      var selector;
      selector = $(this).attr("data-filter");
      $(".gallery-filters a.selected").removeClass("selected");
      $(this).addClass("selected");
      $container.isotope({
        filter: selector
      });
      return false;
    });
    

  });

}).call(this);
