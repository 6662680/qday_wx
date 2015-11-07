/*
# =============================================================================
#   Sparkline Linechart JS
# =============================================================================
*/


(function() {
  var linechartResize;

  linechartResize = function() {
    $("#linechart-1").sparkline([160, 240, 120, 200, 180, 350, 230, 200, 280, 380, 400, 360, 300, 220, 200, 150, 40, 70, 180, 110, 200, 160, 200, 220], {
      type: "line", 
      width: "100%",
      height: "226",
      lineColor: "#a5e1ff",
      fillColor: "rgba(241, 251, 255, 0.9)",
      lineWidth: 2,
      spotColor: "#a5e1ff",
      minSpotColor: "#bee3f6",
      maxSpotColor: "#a5e1ff",
      highlightSpotColor: "#80cff4",
      highlightLineColor: "#cccccc",
      spotRadius: 6,
      chartRangeMin: 0
    });
    $("#linechart-1").sparkline([100, 280, 150, 180, 220, 180, 130, 180, 180, 280, 260, 260, 200, 120, 200, 150, 100, 100, 180, 180, 200, 160, 180, 120], {
      type: "line",
      width: "100%",
      height: "226",
      lineColor: "#cfee74",
      fillColor: "rgba(244, 252, 225, 0.5)",
      lineWidth: 2,
      spotColor: "#b9e72a",
      minSpotColor: "#bfe646",
      maxSpotColor: "#b9e72a",
      highlightSpotColor: "#b9e72a",
      highlightLineColor: "#cccccc",
      spotRadius: 6,
      chartRangeMin: 0,
      composite: true
    });
    $("#linechart-2").sparkline([160, 240, 250, 280, 300, 250, 230, 200, 280, 380, 400, 360, 300, 220, 200, 150, 100, 100, 180, 180, 200, 160, 220, 140], {
      type: "line",
      width: "100%",
      height: "226",
      lineColor: "#a5e1ff",
      fillColor: "rgba(241, 251, 255, 0.9)",
      lineWidth: 2,
      spotColor: "#a5e1ff",
      minSpotColor: "#bee3f6",
      maxSpotColor: "#a5e1ff",
      highlightSpotColor: "#80cff4",
      highlightLineColor: "#cccccc",
      spotRadius: 6,
      chartRangeMin: 0
    });
    $("#linechart-3").sparkline([100, 280, 150, 180, 220, 180, 130, 180, 180, 280, 260, 260, 200, 120, 200, 150, 100, 100, 180, 180, 200, 160, 220, 140], {
      type: "line",
      width: "100%",
      height: "226",
      lineColor: "#cfee74",
      fillColor: "rgba(244, 252, 225, 0.5)",
      lineWidth: 2,
      spotColor: "#b9e72a",
      minSpotColor: "#bfe646",
      maxSpotColor: "#b9e72a",
      highlightSpotColor: "#b9e72a",
      highlightLineColor: "#cccccc",
      spotRadius: 6,
      chartRangeMin: 0
    });
    $("#linechart-4").sparkline([100, 220, 150, 140, 200, 180, 130, 180, 180, 210, 240, 200, 170, 120, 200, 150, 100, 100], {
      type: "line",
      width: "100",
      height: "30",
      lineColor: "#adadad",
      fillColor: "rgba(244, 252, 225, 0.0)",
      lineWidth: 2,
      spotColor: "#909090",
      minSpotColor: "#909090",
      maxSpotColor: "#909090",
      highlightSpotColor: "#666",
      highlightLineColor: "#666",
      spotRadius: 0,
      chartRangeMin: 0
    });
    $("#linechart-5").sparkline([100, 220, 150, 140, 200, 180, 130, 180, 180, 210, 240, 200, 170, 120, 200, 150, 100, 100], {
      type: "line",
      width: "100",
      height: "30",
      lineColor: "#adadad",
      fillColor: "rgba(244, 252, 225, 0.0)",
      lineWidth: 2,
      spotColor: "#909090",
      minSpotColor: "#909090",
      maxSpotColor: "#909090",
      highlightSpotColor: "#666",
      highlightLineColor: "#666",
      spotRadius: 0,
      chartRangeMin: 0
    });
    $("#barchart-2").sparkline([160, 220, 260, 120, 320, 260, 300, 160, 240, 100, 240, 120], {
      type: "bar",
      height: "226",
      barSpacing: 8,
      barWidth: 18,
      barColor: "#8fdbda"
    });
    $("#composite-chart-1").sparkline([160, 220, 260, 120, 320, 260, 300, 160, 240, 100, 240, 120], {
      type: "bar",
      height: "226",
      barSpacing: 8,
      barWidth: 18,
      barColor: "#8fdbda"
    });
    return $("#composite-chart-1").sparkline([100, 280, 150, 180, 220, 180, 130, 180, 180, 280, 260, 260], {
      type: "line",
      width: "100%",
      height: "226",
      lineColor: "#cfee74",
      fillColor: "rgba(244, 252, 225, 0.5)",
      lineWidth: 2,
      spotColor: "#b9e72a",
      minSpotColor: "#bfe646",
      maxSpotColor: "#b9e72a",
      highlightSpotColor: "#b9e72a",
      highlightLineColor: "#cccccc",
      spotRadius: 6,
      chartRangeMin: 0,
      composite: true
    });
  };

  $(document).ready(function() {
    

    
    /*
    # =============================================================================
    #   Sparkline Resize Script
    # =============================================================================
    */

    linechartResize();
    $(window).resize(function() {
      return linechartResize();
    });
    
    
  });

}).call(this);
