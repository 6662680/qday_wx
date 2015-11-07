/*
# =============================================================================
#   Sparkline Linechart JS
# =============================================================================
*/


(function() {


  $(document).ready(function() {

    /*
    # =============================================================================
    #   Form wizard
    # =============================================================================
    */

    $("#wizard").bootstrapWizard({
      nextSelector: ".btn-next",
      previousSelector: ".btn-previous",
      onNext: function(tab, navigation, index) {
        var $current, $percent, $total;
        if (index === 1) {
          if (!$("#name").val()) {
            $("#name").focus();
            $("#name").addClass("has-error");
            return false;
          }
        }
        $total = navigation.find("li").length;
        $current = index + 1;
        $percent = ($current / $total) * 100;
        return $("#wizard").find(".progress-bar").css("width", $percent + "%");
      },
      onPrevious: function(tab, navigation, index) {
        var $current, $percent, $total;
        $total = navigation.find("li").length;
        $current = index + 1;
        $percent = ($current / $total) * 100;
        return $("#wizard").find(".progress-bar").css("width", $percent + "%");
      },
      onTabShow: function(tab, navigation, index) {
        var $current, $percent, $total;
        $total = navigation.find("li").length;
        $current = index + 1;
        $percent = ($current / $total) * 100;
        return $("#wizard").find(".progress-bar").css("width", $percent + "%");
      }
    });
    
   
    /*
    # =============================================================================
    #   WYSIWYG Editor
    # =============================================================================
    */

    if ($('#summernote').length) {
      $('#summernote').summernote({
        height: 300,
        focus: true,
        toolbar: [['style', ['style']], ['style', ['bold', 'italic', 'underline', 'clear']], ['fontsize', ['fontsize']], ['color', ['color']], ['para', ['ul', 'ol', 'paragraph']], ['height', ['height']], ['insert', ['picture', 'link']], ['table', ['table']], ['fullscreen', ['fullscreen']]]
      });
    }
    
  });

}).call(this);
