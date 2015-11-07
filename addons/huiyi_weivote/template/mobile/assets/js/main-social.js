(function() {
  
  $(document).ready(function() {

    /*
    # =============================================================================
    #   Isotope with Masonry
    # =============================================================================
    */

    $alpha = $('#hidden-items');
    $container2 = $('#social-container');
    $(window).load(function() {
      
      /*
      # init isotope, then insert all items from hidden alpha
      */
      $container2.isotope({
        itemSelector: '.item'
      }).isotope('insert', $alpha.find('.item'));
      return $("#load-more").html("加载更多").find("i").hide();

    });
    $('#load-more').click(function() {
      var item1, item2, item3, items, tmp;
      items = $container2.find('.social-entry');
      item1 = $(items[Math.floor(Math.random() * items.length)]).clone();
      item2 = $(items[Math.floor(Math.random() * items.length)]).clone();
      item3 = $(items[Math.floor(Math.random() * items.length)]).clone();
      tmp = $().add(item1).add(item2).add(item3);
      return $container2.isotope('insert', tmp);
    });
    
  });

}).call(this);
