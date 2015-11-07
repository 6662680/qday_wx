var shflag = 0;
var fclick = 0;
var mapclick = 0;
/*function showcontent(){
	if(fclick==1)return;
	if(shflag == 0){
		show_content();
		shflag = 1;
	}else{
		hide_content();
		shflag = 0;
	}
	fclick = 1;
	setTimeout(function(){
		fclick = 0;
	}, 400);
};*/


function show_content(){
	$('#info_content').css('height','200px');
	$('#info_content').css('margin-top','-200px');
	$('#info_content img').attr('src','http://img2.soufun.com/wap/touch/img/rblu-more0.png');
	myScroll.refresh();
};
function hide_content(){
	$('#info_content').css('margin-top','-38px');
	$('#info_content img').attr('src','http://img2.soufun.com/wap/touch/img/rblu-more1.png');
	setTimeout(function(){
		$('#info_content').css('height','38px');
		myScroll.refresh();
	}, 400);
	myScroll.scrollTo(0,0,200,false);
};
var move_flag = 0;
/*document.addEventListener('click', function(ev){
	if(ev.target.getAttribute("class").indexOf('showcontent') < 0 && shflag == 1){
	//	hide_content();
		shflag = 0;
	}
});*/
function closebox(){
	$('#maptip').hide();
};
$(window).resize(function(){
	$("#container").css("height",window.innerHeight);
});
var mapx = $('#mapx').html();
var mapy = $('#mapy').html();
$('.wrap').css("height",window.innerHeight+100);  
window.scrollTo(0, 1);
$(".wrap").css("height",window.innerHeight);
var boxheight=50;
$("#container").css("height",window.innerHeight);
/*var map = new BMap.Map("container"); // 创建地图实例
var opts = {type: BMAP_NAVIGATION_CONTROL_ZOOM,offset: new BMap.Size(10, 35)}
var opts1 = {offset: new BMap.Size(50, 50)}
map.addControl(new BMap.NavigationControl(opts));
map.addControl(new BMap.ScaleControl(opts1));
map.addControl(new BMap.OverviewMapControl());
var gpsPoint = new BMap.Point(mapx, mapy); // 创建点坐标
var bdpoint;*/

var map = new BMap.Map("container");
var mainPoint = new BMap.Point(mapx, mapy);
map.centerAndZoom(mainPoint, 14);
map.addControl(new BMap.ZoomControl());
var gpsPoint = new BMap.Point(mapx, mapy); // 创建点坐标
var bdpoint;
var mersit = $("#merSit").val();
var city = $("#city").val();
var buildingName = $("#buildingName").val();
var address = $("#address").val();
var mobile = $("#mobile").val();


var myPoint;

if (window.navigator.geolocation) {
    window.navigator.geolocation.getCurrentPosition(function(p){
        myPoint = new BMap.Point(p.coords.longitude,p.coords.latitude);
    });
} else {
    alert("浏览器不支持html5来获取地理位置信息");
}

function myPosition(){
    var positionFlag = $("#positionFlag").val();
    if(positionFlag == 'true'){
        if (window.navigator.geolocation) {
            window.navigator.geolocation.getCurrentPosition(function(p){
         //       myPoint = new BMap.Point(p.coords.longitude,p.coords.latitude);
                var myMarker = new BMap.Marker(new BMap.Point(p.coords.longitude,p.coords.latitude));
                map.addOverlay(myMarker);
                var opts = {
                    width : 20,     // 信息窗口宽度
                    height: 20,     // 信息窗口高度
                    title : "我的位置" // 信息窗口标题
                }
                var infoWindow = new BMap.InfoWindow("我的位置", opts);  // 创建信息窗口对象
                map.openInfoWindow(infoWindow,myPoint);
                myMarker.addEventListener('click', function(){map.openInfoWindow(infoWindow,myPoint)});
             //   setTimeout(function(){map.click();}, 500);
                setTimeout(function(){infoWindow.close();}, 3000);
            });
        } else {
            alert("浏览器不支持html5来获取地理位置信息");
        }
        $("#positionFlag").val("flag");
        $('#JS_siteicon').removeClass('site');
        $('#JS_sitetxt').html('楼盘位置');
    }else{
        translateCallback();
        $("#positionFlag").val("true");
        $('#JS_siteicon').addClass('site');
        $('#JS_sitetxt').html('我的位置');
    }

}

function buildingPosition(){
    /*var bmarker = new BMap.Marker(gpsPoint);
    var lng = mapx;
    var lat = mapy;
    map.addOverlay(bmarker);
    var sContent ="<div>"+
        "<h4 style='margin:0 0 5px 0;padding:0.2em 0'>"+buildingName+"</h4>" +
        "<img  onclick='trans("+lng+","+lat+")' style='float:right;margin:4px' id='imgDemo' src='"+mersit+"/common/images/map/go.png' width='77' height='28'/>" +
        "<p style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'> 地址:"+address+"</p>" +
        "</div>";
    var infoWindow = new BMap.InfoWindow(sContent);
    map.openInfoWindow(infoWindow,gpsPoint);
    bmarker.addEventListener('click', function(){this.openInfoWindow(infoWindow);});*/
    translateCallback();
 //   map.setCenter(gpsPoint);
}



/*setTimeout(function(){
    BMap.Convertor.translate(gpsPoint,2,translateCallback);     //真实经纬度转成百度坐标
}, 500);*/

setTimeout(translateCallback,500);
//setTimeout(function(){map.clearOverlays();}, 3000);
function trans(lng,lat){
    var start = {
        latlng:myPoint,
        name : '我的位置'
    }
    var end = {
        latlng:new BMap.Point(lng,lat),
        name : buildingName
    }
    var opts = {
        mode:BMAP_MODE_DRIVING,
        region:city
    }
    var ss = new BMap.RouteSearch();
    ss.routeCall(start,end,opts);
}

function translateCallback(){
    bdpoint = gpsPoint;
    var lng = gpsPoint.lng;
    var lat = gpsPoint.lat;
    var marker = new BMap.Marker(gpsPoint);
    map.addOverlay(marker);
    /*<div style='background:#000;width:200px;'>
        <h5 style='font-size:1em;color:#fff;font-weight:bold;line-height:1.5em'>时代景科名苑</h5>
        <div style='font-size:0.75em;color:#fff;line-height:1.5em;padding-top:10px;'>地址:高新区益州大道北段府城大道口1888号</div>
        <div style="padding-top:15px;">
            <a style="background:#0d6dd9;height:35px;line-height:35px;color:#fff;text-align:center;display:block">去这里</a>
        </div>
    </div>*/
    var sContent ="<div style='width:200px;'>"+
        "<h5 style='font-size:1em;color:#fff;font-weight:bold;line-height:1.5em'>"+buildingName+"</h5>" +
    //    "<img  onclick='trans("+lng+","+lat+")' style='float:right;margin:4px' id='imgDemo' src='"+mersit+"/common/images/map/go.png' width='77' height='28'/>" +
        "<div style='font-size:0.75em;color:#fff;line-height:1.5em;padding-top:10px;'>地址:"+address+"</div>"+
        "<div style='padding-top:15px;'>" +
        "<a href='javascript:trans("+lng+","+lat+")' style='background:#47ba08;height:35px;line-height:35px;color:#fff;text-align:center;display:block'>去这里</a>" +
        "</div>" +
        "</div>";
    var infoWindow = new BMap.InfoWindow(sContent);
    map.openInfoWindow(infoWindow,gpsPoint);
    marker.addEventListener('click', function(){this.openInfoWindow(infoWindow);});
  //  setTimeout(function(){infoWindow.close();}, 3000);
    var point1 = map.getCenter();
    Search.init();
    Search.gotosearchnear(null,"公交车站");
}


map.addEventListener('touchstart', function(ev){
	move_flag = 0;
});
map.addEventListener('touchmove', function(ev){
	move_flag = 1;
});
document.addEventListener('touchend', function(ev){
	if(move_flag == 0 && ev.target.getAttribute("class")=='BMap_mask'){
		if(fclick==1)return;
		if(mapclick == 0){
			$('#uri').hide();
			$('#info_content').css('margin-top','0px');
		//	$('#items').hide();
			setTimeout(function(){
				$('#info_content').css('height','0px');
			//	myScroll.refresh();
			}, 400);
			mapclick = 1;
		}else{
			$('#uri').show();
			$('#info_content').css('margin-top','-38px');
			$('#info_content').css('height','38px');
			$('#items').show();
			mapclick = 0;
		}
		setTimeout(function(){
			fclick = 0;
		}, 400);
	}
});

function myTrans(lng,lat,title){
    var start = {
        latlng:mainPoint,
        name:buildingName
    }
    var end = {
        latlng:new BMap.Point(lng,lat),
        name:title
    }
    var opts = {
        mode:BMAP_MODE_DRIVING,
        region:city
    }
    var ss = new BMap.RouteSearch();
    ss.routeCall(start,end,opts);
}

var Search = {
	map:map,
	//驾车查询的参数
	searchdrive:{start:'', x1:'', y1:'', sstatus:'', end:'', x2:'', y2:'', estatus:'', type:''},
	//包含所有点的矩形边界
	markerBounds:[],
	//当前标准点的信息
	markerNow:null,
	//右侧菜单的驾车搜索
	rfirst:0,
	//当前搜索的类别
	searchtype:null,
	markerList:{},
	ua:navigator.userAgent.toLowerCase(),
	gotosearchnear:function(obj,key) {
		var me = this;
		var nearName = key;
		searchtype = key;
		if(nearName == '楼盘'){
			if(obj.getAttribute('flag')==0){
				me.showResult();
				obj.setAttribute('flag','1');
				obj.style.background = obj.style.background.replace('-0.png','-1.png');
				obj.style.color = '#0B6CD8';
			}else if(obj.getAttribute('flag')==1){
				me.clearMarkers();
				obj.setAttribute('flag','0');
				obj.style.background = obj.style.background.replace('-1.png','-0.png');
				obj.style.color = '#565D68';
			}
			return;
		}
		if(obj == null){
			rfirst=1;
			me.searchNear(nearName);
		}else if(obj != null && obj.getAttribute('flag')==0){
			rfirst=0;
			me.searchNear(nearName);
			obj.setAttribute('flag','1');
			obj.style.background = obj.style.background.replace('-0.png','-1.png');
			obj.style.color = '#0B6CD8';
		}else if(obj != null && obj.getAttribute('flag')==1){
			rfirst=0;
			me.clearTypeMarkers(nearName);
			obj.setAttribute('flag','0');
			obj.style.background = obj.style.background.replace('-1.png','-0.png');
			obj.style.color = '#565D68';
		}
	},
	searchNear:function(nearName) {
		var me = this;
		var point = bdpoint;
		me.localSearch.searchNearby(nearName, point ,3000);
	},
	showFailure:function() {
		alert("系统忙，请重试");
	},
	clearTypeMarkers:function(nearName) {
		var me=this,map = this.map;
		if('undefined' != typeof me.nearResults[nearName]) {
			while(me.nearResults[nearName].length > 0) map.removeOverlay(me.nearResults[nearName].shift());
		}
		//me.nearResults[nearName] = [];
	},
	clearAllTypeMarkers:function() {
		var me=this,map = this.map;
		for(var nearName in me.nearResults) {
			while(me.nearResults[nearName].length > 0) map.removeOverlay(me.nearResults[nearName].shift());
		}
	},
	getTypeImg:function(nearName) {
		var i = 0;
		switch(nearName) {
			case '公交车站':
				i = -20;
				break;
			case '餐饮':
				i = -40;
				break;
			case '银行':
				i = -60;
				break;
			case '超市':
				i = -80;
				break;
			case '购物':
				i = -80;
				break;
			case '商场':
				i = -100;
				break;
			case '学校':
				i = -120;
				break;
			case '医院':
				i = -140;
				break;
			case '加油站':
				i = -160;
				break;
			case '地铁站':
				i = -268;
				break;
		}
		return {url:mersit+'/icon003.gif', size:new BMap.Size(16,16), imageOffset:new BMap.Size(0, i), offset:new BMap.Size(8, 16)};
	},
	init:function() {
		var me = this;
		for(var si in me.searchdrive) me.searchdrive[si] = '';
		me.markerBounds = [];
	//	me.driving = new BMap.DrivingRoute(map, {renderOptions: {map: map, panel: 'jcresult', autoViewport: true}});
		me.markerNow = null;
		me.nearResults = {};
		me._markerManager = {};
		var myLocalSearch = function(results) {
			var map = this.map;
			// 判断状态是否正确
			if (me.localSearch.getStatus() != BMAP_STATUS_SUCCESS) return;
			var nearName = results.keyword;
			me.clearTypeMarkers(nearName);
			var markerInfo = me.getTypeImg(nearName);
			var s={}, result, marker, markerOptions={icon:new BMap.Icon(markerInfo.url,markerInfo.size,{offset:markerInfo.offset,imageOffset:markerInfo.imageOffset})};
			var z = [];
			for (var i=0; i<results.getCurrentNumPois(); i++) {
				result = results.getPoi(i);
                var lng = result.point.lng;
                var lat = result.point.lat;
                var title = result.title;
                /*<div style="background:#000;width:200px;">
                 <h5 style="font-size:1em;color:#fff;font-weight:bold;line-height:1.5em">时代景科名苑</h5>
                 <div style='font-size:0.75em;color:#fff;line-height:1.5em;padding-top:10px;'>地址:高新区益州大道北段府城大道口1888号</div>
                 <div style="padding-top:15px;">
                 <a style="background:#0d6dd9;height:35px;line-height:35px;color:#fff;text-align:center;display:block">去这里</a>
                 </div>
                 </div>*/
                result.title.replace(/&#39;/g, '&acute;')
				marker = new BMap.Marker(result.point,markerOptions);
				var sContent = '<div style="width:200px;">';
				sContent += '<h5 style="font-size:1em;color:#fff;font-weight:bold;line-height:1.5em">'+result.title.replace(/&#39;/g, '&acute;')+'</h5>';
				if(searchtype == '公交车站'){
					sContent += '<span class="f14"><b>车次：</b>'+result.address.replace(/&#39;/g, '&acute;')+'</span><br/>';
				}else{
					sContent += '<span class="f14"><b>地址：</b>'+result.address.replace(/&#39;/g, '&acute;')+'</span><br/>';
				}
				if(result.phoneNumber) {
					sContent += '<span class="f14"><b>电话：</b>'+result.phoneNumber+'</span><br/>';
				}
             //   sContent += '<img onclick="myTrans('+lng+','+lat+',\'' + title + '\')" style="float:right;margin:4px" id="imgDemo" src="'+mersit+'/common/images/map/go.png" width="77" height="28"/>';
                sContent += '<a onclick="myTrans('+lng+','+lat+',\'' + title + '\')" style="background:#47ba08;height:35px;line-height:35px;color:#fff;text-align:center;display:block">去这里</a>';
				sContent += '</div>';
				marker.provalue = sContent;
				if(rfirst==0){
					map.addOverlay(marker); // 将标注添加到地图中
				}
				marker.addEventListener('click', function(){this.openInfoWindow(new BMap.InfoWindow(this.provalue));});
				if('undefined' == typeof(me.nearResults[nearName]))
				{
					me.nearResults[nearName] = [];
				}
				me.nearResults[nearName].push(marker);
				z.push(results.getPoi(i).title + ", " + results.getPoi(i).address);
			}
		/*	document.getElementById("key").innerHTML = searchtype.replace('车站','').replace('超市','购物');
			document.getElementById("r-result").innerHTML = z.join("<br/>");
			myScroll.refresh();
			myScroll.scrollTo(0,0,200,false);*/
		};
		me.localSearch = new BMap.LocalSearch(map, {onSearchComplete:myLocalSearch});
	},
	shownearproj:function() {
		var hiddenMarker=false, keyPoint=null;
		showResult();
	},
	showResult:function() {
		var me = this;
		var z = [];
		var map = this.map;
		var metaMarkers = [];
		var result = $('#result').html();
		var result_array = result.split(';');
		for(var i=0; i<result_array.length; i++) {
			var info_proj = result_array[i].split(',');
			var info = {title:''+info_proj[0]+'',newcode:''+info_proj[1]+'',price:''+info_proj[2]+'',mapx:''+info_proj[3]+'',mapy:''+info_proj[4]+'',city:''+info_proj[5]+''};
			metaMarkers.push(info);
			z.push(info_proj[0] + ", " + info_proj[2]);
		}
		document.getElementById("key").innerHTML = searchtype.replace('车站','');
		document.getElementById("r-result").innerHTML = z.join("<br/>");
		myScroll.refresh();
		myScroll.scrollTo(0,0,200,false);
		me.drawMarkers(metaMarkers);
	},
	drawMarkers:function(metaMarkers) {
		var me = this;
		var bounds = new BMap.Bounds();
		for(var i=0; i<metaMarkers.length; i++) {
			var info = metaMarkers[i];
			if(!info.mapx || !info.mapy) continue;
			point = new BMap.Point(info.mapx,info.mapy);
			bounds.extend(point);
			var marker = me.createMarker(info);
			me.addMarker(marker);
		}
	},
	createMarker:function(info) {
		var me = this;
		var map = this.map;
		var flag_marker = true;
		var mm = me._markerManager;
		var lclass = 'mapFinddingCanvasLabelStyle3';
		var sTc = {'0':'9', '1':'3', '2':'10', '3':'4', '4':'3'};
		var ltext = '<table cellpadding=0 cellspacing=0 border=0><tr><td class="s1">&nbsp;</td><td class="s2">'+info.title+'<span id="tip_price_'+info.newCode+'" style="display:none;"></span>'+'</td><td class="s3">&nbsp;</td></tr><tr><td colspan="3" class="s5"></td></tr></table>';
		var offset_new = new BMap.Size(0,-40);
		var latLng = new BMap.Point(info.mapx,info.mapy);
		var mdiv = document.createElement("div");
		mdiv.setAttribute("class",lclass);
		mdiv.setAttribute("id","tip"+info.newcode+"");
		mdiv.innerHTML = ltext;
		var marker = new BMapLib.RichMarker(mdiv, latLng,{anchor:offset_new});
		marker.provalue = info;
		mdiv.addEventListener("touchstart", function(e){ 
			flag_marker = true;
		});
		mdiv.addEventListener("touchmove", function(e){
			flag_marker = false;
		});
		mdiv.addEventListener("touchend", function(e){
			if(flag_marker){
				setTimeout(function(){
					marker.dispatchEvent("onclick");
				}, 300);
			}
		});
		marker.addEventListener('click',function(){me.openTip(marker);});
		return marker;
	},
	addMarker:function(marker) {
		var me = this;
		var map = this.map;
		map.addOverlay(marker);
		if('undefined' == typeof(me._markerManager['楼盘']))
		{
			me._markerManager['楼盘'] = [];
		}
		me._markerManager['楼盘'].push(marker);
	},
	openTip:function(marker) {
		var me = this;
		var map = this.map;
		var mm = me._markerManager['楼盘'];
		var node = document.getElementById('maptip');
		var content = '<div class="rbox6" style="padding:10px;-webkit-box-shadow:0 0 8px rgba(0,0,0,0.8);">'+
					'<div style="position:relative">'+
					'<div style="position:absolute; right:-10px; top:-10px; background:url(http://img2.soufun.com/wap/touch/img/tc-close.png) no-repeat center; background-size:20px 20px; width:30px; height:30px; cursor:pointer;" onclick="closebox()">'+
					'</div></div>'+
					'<a class="ablack" href="/xf/'+marker.provalue.city+'/'+marker.provalue.newcode+'.htm" title=\''+marker.provalue.title+'\'><div>'+marker.provalue.title+'</div><div class="f16">'+marker.provalue.price+'</div><div class="fblu f14">点击查看楼盘详情</div></a></div>';
		node.innerHTML = content;
		node.style.display = 'block';
		var mapNode = document.getElementById('container');
		var mapHeight = mapNode.offsetHeight;
		var mapwidth = mapNode.offsetWidth;
		var topPx = 0;
		var leftPx = 0;
		var nodeWidth = node.offsetWidth;
		var nodeHeight = node.offsetHeight;
		var atLeft = true;
		var point = new BMap.Point(marker.provalue.mapx, marker.provalue.mapy);
		var pixel = map.pointToPixel(point);
		if(pixel.y > nodeHeight - 27) {
			topPx = pixel.y - nodeHeight + 27;
			if(topPx+nodeHeight > mapHeight) topPx = mapHeight - nodeHeight;
		} else {
			topPx = 3;
		}
		if(pixel.x > nodeWidth) {
			leftPx = pixel.x - nodeWidth;
		} else {
			if((pixel.x + nodeWidth) > (mapwidth-10)){
				leftPx = pixel.x - (pixel.x + nodeWidth - mapwidth + 10);
			}else{
				leftPx = pixel.x;
				atLeft = false;
			}
		}
		node.style.top = topPx+'px';
		node.style.left = leftPx+'px';
	},
	clearMarkers:function() {
		var me=this,map = this.map;
		if('undefined' != typeof me._markerManager['楼盘']) {
			while(me._markerManager['楼盘'].length > 0) map.removeOverlay(me._markerManager['楼盘'].shift());
		}
	}
};