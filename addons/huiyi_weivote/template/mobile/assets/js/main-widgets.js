/*
# =============================================================================
#   Sparkline Linechart JS
# =============================================================================
*/


(function() {


  $(document).ready(function() {
    
    /*
    # =============================================================================
    #   jQuery VMap
    # =============================================================================
    */

    if ($("#vmap").length) {
      $("#vmap").vectorMap({
        map: "world_en",
        backgroundColor: null,
        color: "#fff",
        hoverOpacity: 0.2,
        selectedColor: "#fff",
        enableZoom: true,
        showTooltip: true,
        values: sample_data,
        scaleColors: ["#59cdfe", "#0079fe"],
        normalizeFunction: "polynomial"
      });
    }
    /*
    # =============================================================================
    #   Full Calendar
    # =============================================================================
    */

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
    

    /*
    # =============================================================================
    #   Skycons
    # =============================================================================
    */

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
    
    
    
  });

}).call(this);