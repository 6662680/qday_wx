
//smartbox class
var _searchListUrl="";
var classSmartBox = function(type)
{
	this.smartBox     = $("#smartBox");
	this.smartBoxUI   = $("#smartBox ul");
    this.smartBoxLI   = $("#smartBox li");
    this.search_input = $('#search_input');
    this.clear        = $('#clear');
    this.type = type;
}

classSmartBox.prototype = {
    initController:function(itemClickBack)
    {
    	var type = this.type;
    	var search_input = $("#search_input");
    	var smartBoxWidth = search_input.width();
    	if(smartBoxWidth=='100')
		{
    		this.smartBox.css('width','98%');
		}
    	else
		{
    		this.smartBox.css('width',smartBoxWidth+7+'px');
		}
    	
    	this.search_input.focus(function(){
    		var keyword = $(this).val();
    		if(keyword!='')
			{
    			classSmartBox.call(this);
    			this.clear.css('display','block');
			}
    	});
    	
    	this.search_input.trigger('focus');
    	
    	var smartBoxNode = $('#smartBox');
    	var smartBoxUINode = $('#smartBox ul');
    	var clearNode = $('#clear');
    	
    	//bind input event
    	this.search_input.bind("input propertychange",function(e){
    		var _this = $(this);
    		var keycode = e.which; 
        	var keyword = $.trim(_this.val());
	        if(keyword.length>0 && keycode!=13)
	        {
	            keyword = encodeURIComponent(keyword);
	            $.post('ajaxlist.jsp',{keyword:keyword,city:cityId,action:"getHouse"},function(res)
	            {
	                var htmlItem = "";
	                var length = getObjectLength(res.data);
	                if(res && res.error==0 && length>0)
	                {
	                    	if(type == "sale"){
	                    		for(var i=0;i<length;i++)
	    	                    {
	                    			htmlItem += '<li data-value="'+res.data[i].id+'"><a href="./salelist.jsp?cityId='+cityId+'&buildId='+res.data[i].id+'&keyword='+res.data[i].name+'">'+res.data[i].name+'</a></li>';
	                    		}
	                    	}else{
	                    		for(var i=0;i<length;i++)
	    	                    {
	                    			htmlItem += '<li data-value="'+res.data[i].id+'"><a href="./leaselist.jsp?cityId='+cityId+'&buildId='+res.data[i].id+'&keyword='+res.data[i].name+'">'+res.data[i].name+'</a></li>';
	                    		}
	                    	}	
	                }
	                else
                	{
	                	smartBoxNode.css("display","none");
                	}
	                if(htmlItem!="")
	                {
	                    htmlItem += '<li style="float: right;"><a href="javascript:void(0)" style="display: block;font-size: 16px;">关闭</a></li>';
	                    smartBoxNode.css("display","block");
	                }
	                smartBoxUINode.html(htmlItem);
	            },'json');
	            clearNode.css('display','block');
	        }
	        else
	        {
	        	clearNode.css('display','none');
	        	smartBoxNode.css("display","none");
	        	smartBoxUINode.html(''); 
	        	$("#search_input").val("");
	        	if(type== "sale"){
	        		window.location.href="./salelist.jsp?cityId="+cityId;
	        	}else{
	        		window.location.href="./leaselist.jsp?cityId="+cityId;
	        	}
	        	
	        }
        });
    	//bind click event for itemt
        this.smartBoxLI.live('click',function()
        {
        	var _this = $(this);
        	classSmartBox.call(this);
            var node = _this.find('a');
            var value = node.text();
            this.smartBox.css("display","none");
            if(value != "关闭")
            {
            	_houseId = _this.attr('data-value');
            	this.search_input.val(value);
                if(itemClickBack=='')
            	{
                	$.post('ajaxlist.jsp',{keyword:keyword,city:cityId,action:"getHouse"},function(res)
	            {
	                var htmlItem = "";
	                var length = getObjectLength(res.data);
	                if(res && res.error==0 && length>0)
	                {
	                    	if(type == "sale"){
	                    		for(var i=0;i<length;i++)
	    	                    {
	                    			htmlItem += '<li data-value="'+res.data[i].id+'"><a href="./salelist.jsp?cityId='+cityId+'&buildId='+res.data[i].id+'&keyword='+res.data[i].name+'">'+res.data[i].name+'</a></li>';
	                    		}
	                    	}else{
	                    		for(var i=0;i<length;i++)
	    	                    {
	                    			htmlItem += '<li data-value="'+res.data[i].id+'"><a href="./leaselist.jsp?cityId='+cityId+'&buildId='+res.data[i].id+'&keyword='+res.data[i].name+'">'+res.data[i].name+'</a></li>';
	                    		}
	                    	}	
	                }
	                else
                	{
	                	smartBoxNode.css("display","none");
                	}
	                if(htmlItem!="")
	                {
	                    htmlItem += '<li style="float: right;"><a href="javascript:void(0)" style="display: block;font-size: 16px;">关闭</a></li>';
	                    smartBoxNode.css("display","block");
	                }
	                smartBoxUINode.html(htmlItem);
	            },'json');
	            clearNode.css('display','block');
            	}
                else
            	{
                	eval(itemClickBack+'(false,false,false,true);');
            	}
            }
        });
        //bind close event
        $("#smartBox ul li:last-child a").live('click',function(){
        	classSmartBox.call(this);
        	this.smartBox.css("display","none");
        });
        
        this.clear.click(function(){
        	_houseId = 0;
        	$(this).hide();
        	classSmartBox.call(this);
        	this.search_input.val('');
        	this.smartBox.css("display","none");
        	classSmartBox.call(this);
        	this.smartBoxUI.html('');
        	$.post('ajaxlist.jsp',{keyword:keyword,city:cityId,action:"getHouse"},function(res)
	            {
	                var htmlItem = "";
	                var length = getObjectLength(res.data);
	                if(res && res.error==0 && length>0)
	                {
	                    	if(type == "sale"){
	                    		for(var i=0;i<length;i++)
	    	                    {
	                    			htmlItem += '<li data-value="'+res.data[i].id+'"><a href="./salelist.jsp?cityId='+cityId+'&buildId='+res.data[i].id+'&keyword='+res.data[i].name+'">'+res.data[i].name+'</a></li>';
	                    		}
	                    	}else{
	                    		for(var i=0;i<length;i++)
	    	                    {
	                    			htmlItem += '<li data-value="'+res.data[i].id+'"><a href="./leaselist.jsp?cityId='+cityId+'&buildId='+res.data[i].id+'&keyword='+res.data[i].name+'">'+res.data[i].name+'</a></li>';
	                    		}
	                    	}	
	                }
	                else
                	{
	                	smartBoxNode.css("display","none");
                	}
	                if(htmlItem!="")
	                {
	                    htmlItem += '<li style="float: right;"><a href="javascript:void(0)" style="display: block;font-size: 16px;">关闭</a></li>';
	                    smartBoxNode.css("display","block");
	                }
	                smartBoxUINode.html(htmlItem);
	            },'json');
	            clearNode.css('display','block');
        });
    },
    jumpSearchList:function()
    {
    	classSmartBox.call(this);
    	var value = this.search_input.val();
    	if(value!="")
		{
    		$.post('ajaxlist.jsp',{keyword:value,city:cityId,action:"getHouse"},function(res)
	            {
	                var htmlItem = "";
	                var length = getObjectLength(res.data);
	                if(res && res.error==0 && length>0)
	                {
	                    	if(type == "sale"){
	                    		for(var i=0;i<length;i++)
	    	                    {
	                    			htmlItem += '<li data-value="'+res.data[i].id+'"><a href="./salelist.jsp?cityId='+cityId+'&buildId='+res.data[i].id+'&keyword='+res.data[i].name+'">'+res.data[i].name+'</a></li>';
	                    		}
	                    	}else{
	                    		for(var i=0;i<length;i++)
	    	                    {
	                    			htmlItem += '<li data-value="'+res.data[i].id+'"><a href="./leaselist.jsp?cityId='+cityId+'&buildId='+res.data[i].id+'&keyword='+res.data[i].name+'">'+res.data[i].name+'</a></li>';
	                    		}
	                    	}	
	                }
	                else
                	{
	                	smartBoxNode.css("display","none");
                	}
	                if(htmlItem!="")
	                {
	                    htmlItem += '<li style="float: right;"><a href="javascript:void(0)" style="display: block;font-size: 16px;">关闭</a></li>';
	                    smartBoxNode.css("display","block");
	                }
	                smartBoxUINode.html(htmlItem);
	            },'json');
	            clearNode.css('display','block');
		}
    }
}

//search object
var searchHeaderHtml = '<div class="resultWarp"><h5 id="search_result_num">共搜索到0个房源</h5><span></span></div>';
var _searchPage=1;
var _flagLoadingData = false;
var classSearch = function(type)
{
    this.criteriaListLi = $('#criteriaList li');
    this.criteriaListSpan = $('#criteriaList li p span');
    this.loadingpic = $('#loadingpic');
    this.searchHeader = $('#searchHeader');
    this.orderNone = $('#orderNone');
    this.search_condition_order = $('#search_condition_order');
    this.searchcondition = $('#searchcondition');
    this.search_input = $('#search_input');
    this.keyword='';
    
    $.post('ajaxlist.jsp',{keyword:keyword,city:cityId,action:"getHouse"},function(res)
	            {
	                var htmlItem = "";
	                var length = getObjectLength(res.data);
	                if(res && res.error==0 && length>0)
	                {
	                    	if(type == "sale"){
	                    		for(var i=0;i<length;i++)
	    	                    {
	                    			htmlItem += '<li data-value="'+res.data[i].id+'"><a href="./salelist.jsp?cityId='+cityId+'&buildId='+res.data[i].id+'&keyword='+res.data[i].name+'">'+res.data[i].name+'</a></li>';
	                    		}
	                    	}else{
	                    		for(var i=0;i<length;i++)
	    	                    {
	                    			htmlItem += '<li data-value="'+res.data[i].id+'"><a href="./leaselist.jsp?cityId='+cityId+'&buildId='+res.data[i].id+'&keyword='+res.data[i].name+'">'+res.data[i].name+'</a></li>';
	                    		}
	                    	}	
	                }
	                else
                	{
	                	smartBoxNode.css("display","none");
                	}
	                if(htmlItem!="")
	                {
	                    htmlItem += '<li style="float: right;"><a href="javascript:void(0)" style="display: block;font-size: 16px;">关闭</a></li>';
	                    smartBoxNode.css("display","block");
	                }
	                smartBoxUINode.html(htmlItem);
	            },'json');
	            clearNode.css('display','block');
}
classSearch.prototype = {
	//初始化
	init:function()
	{
	   	classSearch.call();
	   	this.initSelectItem();
	   	_searchPage=1;
	   	this.loadData(false,true);
	},
	
	//重置搜索界面
	resetSearch:function()
	{
		classSearch.call(this);
		this.orderNone.hide();
		_searchPage=1;
		this.searchHeader.html('');
		$('#newHouseList .linked').remove();
		$('#newHouseList .container').remove();
	}
};
//写cookies 
function setCookie(name,value,expires) 
{ 	
	var path = arguments[3]?arguments[3]:'/';
    var Days = 30; 
    var exp = new Date(); 
    exp.setTime(expires*1000); 
    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString() + ";path=" + path;
} 
//读取cookies 
function getCookie(name) 
{ 
    var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
 
    if(arr=document.cookie.match(reg))
 
        return unescape(arr[2]); 
    else 
        return null; 
} 

//大图页面图片样式初始化
function initSwiperImgList(type)
{
//	return;
	var shadeBlock   = $('#shadeBlock');
	var height       = 0;
	var imglistblock = $('#imglistblock');
    var width        = imglistblock.width();
    var tempMarginTop = 0;
    var tempNewWidth  = 0;
    var tempNewHeight = 0;
    
    switch(type)
    {
    	case 'detail':
    		height = 213;
    		break;
		case 'big':
			height = imglistblock.height()-44;
    }
    var swiperImg=$('#imglistblock .swiper-wrapper img');
    var swiperImgLength=swiperImg.length;
    swiperImg.each(function(index,domEle)
	{
    	var _this     = $(domEle);
    	var img       = new Image();
    	img.src       = _this.attr('src');
    	
    	var imgwidth;
    	var imgheight;
    	var handler = function()
    	{
    		imgwidth  = img.width;
        	imgheight = img.height;
        	var flagType  = -1;//标记调整大小模式
    		if(imgheight>height && imgwidth>width)
    		{
        		flagType = 2;
        		if(width/height<imgwidth/imgheight)
    			{
        			flagType = 1;
    			}
    		}
        	else if(imgwidth>width)
    		{
        		flagType = 1;
    		}
        	else if(imgheight>height)
    		{
        		flagType = 2;
    		}
        	else
    		{
        		flagType = 0;
    		}
        	switch(flagType)
        	{
        		case 0://原图
        			tempMarginTop = Math.floor((height-imgheight)/2);
            		style = 'margin-top:' + tempMarginTop+'px'+';height:'+imgheight+'px';
        			break;
        		case 1:
        			tempNewWidth = width;
        			tempNewHeight = Math.floor(imgheight/(imgwidth/width));
        			tempMarginTop = Math.floor((height-tempNewHeight)/2);
            		style = "width:"+tempNewWidth+"px;height:"+tempNewHeight+"px;margin-top:"+tempMarginTop+"px";
        			break;
        		case 2://图片高大于屏幕
        			tempNewWidth  = Math.floor(imgwidth/(imgheight/height));
        			tempNewHeight = height;
            		style = "width:"+tempNewWidth+"px;height:"+tempNewHeight+"px";
        		default:
        			break;
        	}
        	_this.attr('style',style);
        	if(index==swiperImgLength-1)
    		{
        		shadeBlock.hide();
    		}
    	}
    	img.onerror=function()
    	{
    		_this.attr('src','http://www.haofang.net/images/default/default_small.jpg');
    		img.width = 600;
    		img.height = 337;
    		handler();
    	}
    	img.onload=function()
    	{
        	handler();
    	}
    });
    
    var loadingShade = $("#loadingShade");
    if(loadingShade.length==1)
	{
    	var nowId = window.location.hash;
        if(nowId!="")
        {
        	var index = parseInt(nowId.substring(1));
        	if(index>=0)
    		{
        		mySwiper.swipeTo(index,0);
    		}
        }
        loadingShade.hide();
	}
}

function jumpCityMap()
{
	var redirect = window.location.href;
	redirect=encodeURIComponent(redirect);
	window.location.href = urlCityMap+'?redirect='+redirect;
}

//滚动才显示拨打电话悬浮框
var flagScrollPhoneBlock = false;
function showCallBox(boxId)
{
	$(window).scroll( function() {
		if(flagScrollPhoneBlock)
		{
			return ;
		}
		var node = $('#'+boxId);
		if(node.length == 1)
		{
			$('#'+boxId).show();			
		}
		flagScrollPhoneBlock = true;
	});
}
//获取设备系统
function getDeviceSystem()
{
	if(/iphone/i.test(navigator.userAgent.toLowerCase())){  
		return 'iphone';
	}
    else if(/ipad/i.test(navigator.userAgent.toLowerCase())){  
    	return 'ipad';
    }
    else if(/samsung|HTC|android/i.test(navigator.userAgent.toLowerCase())){
    	return 'android';
    }
	return 'none';
}
//下载或打开APP
function downloadApp(appurl)
{
	if(appurl == null || appurl == "" ){
		window.location.href = "http://qq.haofang.net";
	}else{
		window.location.href = appurl;
	}
}


//get object length
function getObjectLength(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
}



// 自定义字典对象,构建查询条件字符串
function Dictionary(){
	this.data = new Array();
	 
	this.put = function(key,value){
		 this.data[key] = value;
	};
	
	this.get = function(key){
		return this.data[key];
	};
	
	this.remove = function(key){
		this.data[key] = null;
	};
	 
	this.isEmpty = function(){
		return this.data.length == 0;
	};
	
	this.size = function(){
		return this.data.length;
	};
}

function toMap(longt,lat){
	window.location.href = "map.jsp?longt="+longt+"&lat="+lat+"&t="+new Date().getTime();
}

