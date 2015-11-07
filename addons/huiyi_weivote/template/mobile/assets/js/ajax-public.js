/*
# =============================================================================
#   Skycons(首页&小工具页面都有用到)
# =============================================================================
*/
function SkyIcons() {
    $('.skycons-element').each(function() {
      var canvasId, skycons, weatherSetting;
      skycons = new Skycons({
        color: "white"
      });
      canvasId = $(this).attr('id');
      weatherSetting = $(this).data('skycons');
      skycons.add(canvasId, Skycons[weatherSetting]);
      return skycons.play();
    });
}


/*
# =============================================================================
#   Sparkline Linechart JS(首页&图表&筛选结果页面都有用到)
# =============================================================================
*/

function SparklineLinechart() {
    var $alpha, $container, $container2, addEvent, buildMorris, checkin, checkout, d, date, handleDropdown, initDrag, m, now, nowTemp, timelineAnimate, y;
    $("#barcharts").sparkline([190, 220, 210, 220, 220, 260, 300, 220, 240, 240, 220, 200, 240, 260, 210], {
      type: "bar",
      height: "100",
      barSpacing: 4,
      barWidth: 13,
      barColor: "#cbcbcb",
      highlightColor: "#89D1E6"
    });
    $("#pie-chart").sparkline([2, 8, 6, 10], {
      type: "pie",
      height: "220",
      width: "220",
      offset: "+90",
      sliceColors: ["#a0eeed", "#81e970", "#f5af50", "#f46f50"]
    });
    $(".sparkslim").sparkline('html', {
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
}

/*
# =============================================================================
#   Easy Pie Chart(首页&图表页面都有用到)
# =============================================================================
*/
function EasyPieChart() {
    $(".pie-chart1").easyPieChart({
      size: 200,
      lineWidth: 12,
      lineCap: "square",
      barColor: "#81e970",
      animate: 800,
      scaleColor: false
    });
    $(".pie-chart2").easyPieChart({
      size: 200,
      lineWidth: 12,
      lineCap: "square",
      barColor: "#f46f50",
      animate: 800,
      scaleColor: false
    });
    $(".pie-chart3").easyPieChart({
      size: 200,
      lineWidth: 12,
      lineCap: "square",
      barColor: "#fab43b",
      animate: 800,
      scaleColor: false
    });
}

/*
# =============================================================================
#   Morris Chart JS(首页&图表页面都有用到)
# =============================================================================
*/
function MorrisChart() {
    $(window).resize(function(e) {
      var morrisResize;
      clearTimeout(morrisResize);
      return morrisResize = setTimeout(function() {
        return buildMorris(true);
      }, 500);
    });
    $(function() {
      return buildMorris();
    });
    buildMorris = function($re) {
      var tax_data;
      if ($re) {
        $(".graph").html("");
      }
      tax_data = [
        {
          period: "2011 Q3",
          licensed: 3407,
          sorned: 660
        }, {
          period: "2011 Q2",
          licensed: 3351,
          sorned: 629
        }, {
          period: "2011 Q1",
          licensed: 3269,
          sorned: 618
        }, {
          period: "2010 Q4",
          licensed: 3246,
          sorned: 661
        }, {
          period: "2009 Q4",
          licensed: 3171,
          sorned: 676
        }, {
          period: "2008 Q4",
          licensed: 3155,
          sorned: 681
        }, {
          period: "2007 Q4",
          licensed: 3226,
          sorned: 620
        }, {
          period: "2006 Q4",
          licensed: 3245,
          sorned: null
        }, {
          period: "2005 Q4",
          licensed: 3289,
          sorned: null
        }
      ];
      if ($('#hero-graph').length) {
        Morris.Line({
          element: "hero-graph",
          data: tax_data,
          xkey: "period",
          ykeys: ["licensed", "sorned"],
          labels: ["Licensed", "Off the road"],
          lineColors: ["#5bc0de", "#60c560"]
        });
      }
      if ($('#hero-donut').length) {
        Morris.Donut({
          element: "hero-donut",
          data: [
            {
              label: "技术部门",
              value: 25
            }, {
              label: "市场销售",
              value: 40
            }, {
              label: "用户体验",
              value: 25
            }, {
              label: "人力资源",
              value: 10
            }
          ],
          colors: ["#f0ad4e"],
          formatter: function(y) {
            return y + "%";
          }
        });
      }
      if ($('#hero-area').length) {
        Morris.Area({
          element: "hero-area",
          data: [
            {
              period: "2010 Q1",
              iphone: 2666,
              ipad: null,
              itouch: 2647
            }, {
              period: "2010 Q2",
              iphone: 2778,
              ipad: 2294,
              itouch: 2441
            }, {
              period: "2010 Q3",
              iphone: 4912,
              ipad: 1969,
              itouch: 2501
            }, {
              period: "2010 Q4",
              iphone: 3767,
              ipad: 3597,
              itouch: 5689
            }, {
              period: "2011 Q1",
              iphone: 6810,
              ipad: 1914,
              itouch: 2293
            }, {
              period: "2011 Q2",
              iphone: 5670,
              ipad: 4293,
              itouch: 1881
            }, {
              period: "2011 Q3",
              iphone: 4820,
              ipad: 3795,
              itouch: 1588
            }, {
              period: "2011 Q4",
              iphone: 15073,
              ipad: 5967,
              itouch: 5175
            }, {
              period: "2012 Q1",
              iphone: 10687,
              ipad: 4460,
              itouch: 2028
            }, {
              period: "2012 Q2",
              iphone: 8432,
              ipad: 5713,
              itouch: 1791
            }
          ],
          xkey: "period",
          ykeys: ["iphone", "ipad", "itouch"],
          labels: ["iPhone", "iPad", "iPod Touch"],
          hideHover: "auto",
          lineWidth: 2,
          pointSize: 4,
          lineColors: ["#a0dcee", "#f1c88e", "#a0e2a0"],
          fillOpacity: 0.5,
          smooth: true
        });
      }
      if ($('#hero-bar').length) {
        return Morris.Bar({
          element: "hero-bar",
          data: [
            {
              device: "iPhone",
              geekbench: 136
            }, {
              device: "iPhone 3G",
              geekbench: 137
            }, {
              device: "iPhone 3GS",
              geekbench: 275
            }, {
              device: "iPhone 4",
              geekbench: 380
            }, {
              device: "iPhone 4S",
              geekbench: 655
            }, {
              device: "iPhone 5",
              geekbench: 1571
            }
          ],
          xkey: "device",
          ykeys: ["geekbench"],
          labels: ["Geekbench"],
          barRatio: 0.4,
          xLabelAngle: 35,
          hideHover: "auto",
          barColors: ["#5bc0de"]
        });
      }
    };
}

/*
# =============================================================================
#   Full Calendar(日历&小工具页面都有用到)
# =============================================================================
*/

function FullCalendar() {
    date = new Date();
    d = date.getDate();
    m = date.getMonth();
    y = date.getFullYear();
    initDrag = function(el) {
      /*
      # create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
      # it doesn't need to have a start or end
      */

      var eventObject;
      eventObject = {
        title: $.trim(el.text())
      };
      /*
      # store the Event Object in the DOM element so we can get to it later
      */

      el.data("eventObject", eventObject);
      /*
      # make the event draggable using jQuery UI
      */

      return el.draggable({
        zIndex: 999,
        revert: true,
        revertDuration: 0
      });
    };
    addEvent = function(title, priority) {
      var html;
      title = (title.length === 0 ? "未命名事件" : title);
      priority = (priority.length === 0 ? "default" : priority);
      html = $("<div data-class=\"label label-" + priority + "\" class=\"external-event label label-" + priority + "\">" + title + "</div>");
      jQuery("#event_box").append(html);
      return initDrag(html);
    };
    $("#external-events div.external-event").each(function() {
      return initDrag($(this));
    });
    $("#event_add").click(function() {
      var priority, title;
      title = $("#event_title").val();
      priority = $("#event_priority").val();
      return addEvent(title, priority);
    });
    /*
    # modify chosen options
    */

    handleDropdown = function() {
      $("#event_priority_chzn .chzn-search").hide();
      $("#event_priority_chzn_o_1").html("<span class=\"label label-default\">" + $("#event_priority_chzn_o_1").text() + "</span>");
      $("#event_priority_chzn_o_2").html("<span class=\"label label-success\">" + $("#event_priority_chzn_o_2").text() + "</span>");
      $("#event_priority_chzn_o_3").html("<span class=\"label label-info\">" + $("#event_priority_chzn_o_3").text() + "</span>");
      $("#event_priority_chzn_o_4").html("<span class=\"label label-warning\">" + $("#event_priority_chzn_o_4").text() + "</span>");
      return $("#event_priority_chzn_o_5").html("<span class=\"label label-important\">" + $("#event_priority_chzn_o_5").text() + "</span>");
    };
    $("#event_priority_chzn").click(handleDropdown);
    /*
    # predefined events
    */

    addEvent("我的事件1", "primary");
    addEvent("我的事件2", "success");
    addEvent("我的事件3", "info");
    addEvent("我的事件4", "warning");
    addEvent("我的事件5", "danger");
    addEvent("我的事件6", "default");
    $("#calendar").fullCalendar({
      header: {
        left: "prev,next today",
        center: "title",
        right: "month,agendaWeek,agendaDay"
      },
      editable: true,
      droppable: true,
      drop: function(date, allDay) {
        /*
        # retrieve the dropped element's stored Event Object
        */

        var copiedEventObject, originalEventObject;
        originalEventObject = $(this).data("eventObject");
        /*
        # we need to copy it, so that multiple events don't have a reference to the same object
        */

        copiedEventObject = $.extend({}, originalEventObject);
        /*
        # assign it the date that was reported
        */

        copiedEventObject.start = date;
        copiedEventObject.allDay = allDay;
        copiedEventObject.className = $(this).attr("data-class");
        /*
        # render the event on the calendar
        # the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
        */

        $("#calendar").fullCalendar("renderEvent", copiedEventObject, true);
        /*
        # is the "remove after drop" checkbox checked?
        # if so, remove the element from the "Draggable Events" list
        */

        if ($("#drop-remove").is(":checked")) {
          return $(this).remove();
        }
      },
      events: [
        {
          title: "全天事件",
          start: new Date(y, m, 1),
          className: "label label-default"
        }, {
          title: "长事件",
          start: new Date(y, m, d - 5),
          end: new Date(y, m, d - 2),
          className: "label label-success"
        }, {
          id: 999,
          title: "重复事件",
          start: new Date(y, m, d - 3, 16, 0),
          allDay: false,
          className: "label label-default"
        }, {
          id: 999,
          title: "重复事件",
          start: new Date(y, m, d + 4, 16, 0),
          allDay: false,
          className: "label label-important"
        }, {
          title: "会议",
          start: new Date(y, m, d, 10, 30),
          allDay: false,
          className: "label label-info"
        }, {
          title: "午餐",
          start: new Date(y, m, d, 12, 0),
          end: new Date(y, m, d, 14, 0),
          allDay: false,
          className: "label label-warning"
        }, {
          title: "生日",
          start: new Date(y, m, d + 1, 19, 0),
          end: new Date(y, m, d + 1, 22, 30),
          allDay: false,
          className: "label label-success"
        }, {
          title: "访问百度",
          start: new Date(y, m, 28),
          end: new Date(y, m, 29),
          url: "http://baidu.com/",
          className: "label label-warning"
        }
      ]
    });
}