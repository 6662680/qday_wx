/*
# =============================================================================
#   Sparkline Linechart JS
# =============================================================================
*/


(function() {
  

  $(document).ready(function() {
    
    /*
    # =============================================================================
    #   DataTables
    # =============================================================================
    */

    $("#dataTable1").dataTable({
      "sPaginationType": "full_numbers",
      aoColumnDefs: [
        {
          bSortable: false,
          aTargets: [0, -1]
        }
      ]
    });
    $('.table').each(function() {
      return $(".table #checkAll").click(function() {
        if ($(".table #checkAll").is(":checked")) {
          return $(".table input[type=checkbox]").each(function() {
            return $(this).prop("checked", true);
          });
        } else {
          return $(".table input[type=checkbox]").each(function() {
            return $(this).prop("checked", false);
          });
        }
      });
    });
    
  });

}).call(this);
