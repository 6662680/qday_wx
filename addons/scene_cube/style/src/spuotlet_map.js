function baidu_map(data) {
    var self = this;
    var opt = this.option;
    var map = new BMap.Map("l-map");
    var myGeo = new BMap.Geocoder(); 
    var currentPoint; 
    var point = new BMap.Point(121.525772, 31.308563);
    var marker1 = new BMap.Marker(point);        // 创建标注; 
    var infoWindow;
    map.addOverlay(marker1);
    var opts = {
        width: 220,     // 信息窗口宽度 220-730
        height: 80,     // 信息窗口高度 60-650
        title: "原本位置"  // 信息窗口标题
    };
    marker1.enableDragging();
    marker1.addEventListener("dragend", function (e) {
        document.getElementById('lat').value = e.point.lat;
        document.getElementById('lng').value = e.point.lng;
        dragend(new BMap.Point(e.point.lng, e.point.lat));
        marker1.closeInfoWindow();
    });
    marker1.addEventListener("click", function (e) {
        marker1.openInfoWindow(infoWindow);    
    });
    map.enableScrollWheelZoom();
    if (data) {
        point = new BMap.Point(data.lng, data.lat); 
        infoWindow = new BMap.InfoWindow(" " + data.adr + " ,拖拽地图或红点修改位置!你也可以直接修改上方位置系统自动定位!", opts);  // 创建信息窗口对象
        marker1.setPosition(point);
        marker1.openInfoWindow(infoWindow);      // 打开信息窗口  
        doit(point);
    } else { 
        doit(point); 
        window.setTimeout(function () {
            auto();
        }, 100);
    }
    map.enableDragging();
    map.enableContinuousZoom();
    map.addControl(new BMap.NavigationControl());
    map.addControl(new BMap.ScaleControl());
    map.addControl(new BMap.OverviewMapControl());
    
    
    function auto() {
        var geolocation = new BMap.Geolocation();

        geolocation.getCurrentPosition(function (r) {
            if (this.getStatus() == BMAP_STATUS_SUCCESS) { 
                point = new BMap.Point(r.point.lng, r.point.lat);
                marker1.setPosition(point);
                var opts = {
                    width: 220,     // 信息窗口宽度 220-730
                    height: 60,     // 信息窗口高度 60-650
                    title: "定位成功"  // 信息窗口标题
                }

                infoWindow = new BMap.InfoWindow("这是你当前的位置!,移动红点标注目标位置，你也可以直接修改上方位置,系统自动定位!", opts);  // 创建信息窗口对象
                marker1.openInfoWindow(infoWindow);      // 打开信息窗口
                doit(point);

            } else {
                console.log("无法获取定位");
            }
        })
    };
    function dragend(p) {
        myGeo.getLocation(p, function (result) {
            if (result) {
                document.getElementById('suggestId').value = result.address;
                marker1.setPosition(p);
                map.panTo(p);
                var opts = {
                    width: 220,     // 信息窗口宽度 220-730
                    height: 80,     // 信息窗口高度 60-650
                    title: "标注位置"  // 信息窗口标题
                }; 
                infoWindow = new BMap.InfoWindow(" " + result.address + " ,拖拽地图或红点修改位置!你也可以直接修改上方位置系统自动定位!", opts);  // 创建信息窗口对象
            }
        });
      
    }
    function doit(point) { 
        if (point) {  
            document.getElementById('lat').value = point.lat;
            document.getElementById('lng').value = point.lng;
            map.setCenter(point);
            map.centerAndZoom(point, 15);
            map.panTo(point);

            var cp = map.getCenter();
            myGeo.getLocation(point, function (result) {
                if (result) {
                    document.getElementById('suggestId').value = result.address;
                }
            }); 
            map.addEventListener("dragend", function showInfo() {
                var cp = map.getCenter();
                document.getElementById('lat').value = cp.lat;
                document.getElementById('lng').value = cp.lng; 
                dragend(new BMap.Point(cp.lng, cp.lat));
                marker1.closeInfoWindow(); 
            });

            map.addEventListener("dragging", function showInfo() {
                var cp = map.getCenter(); 
                marker1.setPosition(new BMap.Point(cp.lng, cp.lat));
                map.panTo(new BMap.Point(cp.lng, cp.lat)); 
                map.centerAndZoom(marker1.getPosition(), map.getZoom());
            });


        }


         


    } 
    function loadmap() { 
        var city = document.getElementById('suggestId').value; 
        var myGeo = new BMap.Geocoder();
        myGeo.getPoint(city, function (point) {
            if (point) {
                marker1.setPosition(new BMap.Point(point.lng, point.lat));
                document.getElementById('lat').value = point.lat;
                document.getElementById('lng').value = point.lng;
                map.panTo(new BMap.Point(marker1.getPosition().lng, marker1.getPosition().lat));
                var opts = {
                    width: 220,     // 信息窗口宽度 220-730
                    height: 60,     // 信息窗口高度 60-650
                    title: "搜索位置"  // 信息窗口标题
                }
                infoWindow = new BMap.InfoWindow(""+city+",移动红点标注目标位置，你也可以直接修改上方位置,系统自动定位!", opts);  // 创建信息窗口对象
                marker1.openInfoWindow(infoWindow);      // 打开信息窗口
                map.centerAndZoom(marker1.getPosition(), map.getZoom());
            }
        }, "全国");
     
    } 
    $("#suggestId").change(function () { loadmap(); })
    $("#positioning").click(function () { loadmap(); });

}
