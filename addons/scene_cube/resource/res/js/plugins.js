/* 
 *  整合云+轻App项目插件js（组件级）
 * ----------------------------------
 *  作者：Charles
 *  时间：2014-04-06
 *  准则：jQuery 插件
 *  联系：16295853（qq）
 *****************************************************************************************/





//工具脚本
var util = {

	//使用的平台
	platform : {
		//是否为PC
		pc : navigator.platform.indexOf('Win') >= 0 ? true : false,

		//是否为Android
		android : navigator.userAgent.indexOf('Android') >=0,

		//是否为iPhone
		iphone : navigator.userAgent.indexOf('iPhone') >=0
	}
};

//为html元素添加平台探测脚本
for(var i in util.platform){
	if(util.platform[i]){
		$('html').addClass(i);
	}
}


//禁用不需要的浏览器默认行为
var $win = $(window);
//禁止ios的浏览器容器弹性
$win.on('scroll.elasticity', function (e) {
	e.preventDefault();
}).on('touchmove.elasticity', function (e) {
	e.preventDefault();
});

//禁止拖动图片
$win.delegate('img', 'mousemove', function (e) {
	e.preventDefault();
});


//ScrollLoading 插件
/**
 *  ScrollLoading 列表数据滚动加载插件
 *  -----------------------------
 *  作者：Charles
 *  时间：2014-04-6
 *  准则：jQuery 插件
 *  联系：16295853（qq）
 ******************************************************************************************
 *
 *	//使用示例
 *	$('#panel-dataList').ScrollLoading({
 *		url: 'xxx.php',						//ajax请求的url
 *		type: 'get',						//ajax请求的类型
 *		dataFactory: function () {			//ajax请求所需数据的数据工厂
 *			return {id: 123, name: 'xxx'};
 *		},
 *		templateID: 'template-dataList'		//使用百度模板引擎的模板ID
 *	});
 *
 *********************************************************************************************/
;(function ($) {
	$.fn.ScrollLoading = function (options) {
		//获取指令
		var command = 'init';
		if(arguments.length > 0){
			if (typeof arguments[0] == 'string'){
				command = arguments[0];
			}
		}

		//判断指令
		switch(command){
			//对象初始化
			case 'init':
				//默认设置
				var settings = {
					url: document.location.href,		//ajax请求的url
					type: 'get',						//ajax请求的类型
					dataFactory: function () {			//ajax请求所需数据的数据工厂
						return {};
					},
					templateID: 'template-list'			//模板ID
				}
				//合并数据
				$.extend(settings, options);

				//循环设定
				this.each(function (i, item) {
					//获取内容容器
					var $listBox = $(item),
						$list = $listBox.find('>ul');

					//标识是否正在加载
					var isLoading = false;
					var pageIndex = 1;
					//添加滚动事件
					$listBox.on(util.platform.pc ? 'scroll': 'touchend', function (e) {
						//没有加载中，并且滚动到了底部
						if(!isLoading && ($listBox.prop('scrollHeight') - ($listBox.scrollTop()+$listBox.height()) < 2)){
							//更新加载状态为正在加载
							isLoading = true;
							$listBox.addClass('z-loading').animate({scrollTop: $listBox.prop('scrollHeight')}, 300);

							//调用自定义数据工厂函数产生要提交的数据，并添加页码
							var postData = settings.dataFactory();
							//计算pageIndex的值
							if($listBox.data('reset-page-index')){
								pageIndex = 1;
								$listBox.data('reset-page-index', false)
							}
							if(typeof postData == 'string'){
								postData += '&pageIndex=' + (pageIndex++);
							}else{
								postData.pageIndex = pageIndex++;
							}

							//本地测试（正式环境需删除此代码）
							setTimeout(function () {
								//测试数据
								var data = [{},{},{},{},{},{},{},{},{},{}];

								//将json数据转换为html，再转成jQuery对象
								var templateID = $listBox.data('template-id');
								var $content = $( baidu.template( settings.templateID, {list: data}) );

								//追加到列表
								if($content.length>0){
									$list.append($content);
									//更新滚动条到新数据区域
									var $firstLi = $content.first();
									$listBox.animate({scrollTop: ($firstLi.index()-1) * $firstLi.height()}, 300);	
								}

								//更新加载状态为加载完毕
								$listBox.removeClass('z-loading');
								setTimeout(function(){ isLoading = false; }, 1000);
							}, 1000);


							//获取数据（此代码为正式环境代码）
							/*$.ajax({
								url: settings.url,
								type: settings.type,
								data: postData,
								dataType: 'json',
								success: function (data) {
									//将json数据转换为html，再转成jQuery对象
									var $content = $( baidu.template( settings.templateID, {list: data}) );

									//追加到列表
									if($content.length>0){
										$list.append($content);
										//更新滚动条到新数据区域
										var $firstLi = $content.first();
										$listBox.animate({scrollTop: ($firstLi.index()-1) * $firstLi.height()}, 300);	
									}
								},
								error: function () {
									alert('抱歉，数据加载错误！');
								}
							}).always(function () {
								//更新加载状态为加载完毕
								$listBox.removeClass('z-loading');
								setTimeout(function(){ isLoading = false; }, 1000);
							});*/
						}
					});

					//iphone添加滚动增量
					if(util.platform.iphone>=0){
						var startY = 0, stopY = 0;
						var scrollTopValue = 0;
						$listBox[0].addEventListener('touchstart', function (e) {
							startY = e.targetTouches[0].clientY;
						});
						$listBox[0].addEventListener('touchmove', function (e) {
							stopY = e.targetTouches[0].clientY;
							scrollTopValue = $listBox.scrollTop() + ((startY - stopY)*2);
							$listBox.animate({scrollTop: scrollTopValue}, 20);
						});
					}
				});
			break;

			//重置页码
			case 'resetPageIndex':
				this.data('reset-page-index', true);
			break;
		}

		//保持操作链
		return this;
	};
})(jQuery);


//基于IScroll的滚动加载插件
(function ($) {
	$.fn.IScrollLoading = function (options) {
		//获取指令
		var command = 'init';
		if(arguments.length > 0){
			if (typeof arguments[0] == 'string'){
				command = arguments[0];
			}
		}

		//判断指令
		switch(command){
			//对象初始化
			case 'init':
				//默认设置
				var settings = {
					//默认的渲染函数
					__renderItems: function (argument) {
						var items = '';
						for(var i=1; i<6; i++){
							items += '<li class="">newItem'+ i +'</li>';
						}
						return items;
					},
					//当向下推动结束时
					onPullDownEnd: function ($list, iScrollObj) {
						setTimeout(function () {
							$list.prepend(settings.__renderItems());
							iScrollObj.refresh();			// Remember to refresh when contents are loaded (ie: on ajax completion)
						}, 1200);
					},
					//当向上推动结束时
					onPullUpEnd: function ($list, iScrollObj) {
						setTimeout(function () {
							$list.append(settings.__renderItems());
							iScrollObj.refresh();			// Remember to refresh when contents are loaded (ie: on ajax completion)
						}, 1200);
					}
				};

				//合并设置
				$.extend(settings , options);

				//初始化
				this.each(function (i, item) {
					//定义相关对象
					var $iScroll = $(item), $list = $iScroll.find('ul'),
						iScrollObj,
						$pullDownEl, pullDownOffset,
						$pullUpEl, pullUpOffset,
						generatedCount = 0;

					//获取上，下元素
					$pullDownEl = $iScroll.find('.pullDown');
					pullDownOffset = $pullDownEl.length ? $pullDownEl.outerHeight() : 0;
					$pullUpEl = $iScroll.find('.pullUp');
					pullUpOffset = $pullUpEl.length ? $pullUpEl.outerHeight() : 0;

					//创建iScrollObj对象
					iScrollObj = new IScroll(item, {
						useTransition: true,
						topOffset: pullDownOffset,
						probeType: 2,
						mouseWheel: false,
						bindToWrapper:true,
						scrollY:true,
						deceleration : 0.0028,		//减速值
						preventDefaultException: { tagName: /^(INPUT|TEXTAREA|BUTTON|SELECT|IMG|A)$/ }
					});

					//当滚动超出时添加class标记
					iScrollObj.on('scroll', function () {
						if ($pullDownEl.length && this.y > 50 && !$pullDownEl.is('.flip')) {
							$pullDownEl.addClass('flip');
							$pullDownEl.find('span').text('松开加载');
							this.minScrollY = 0;
						} else if ($pullDownEl.length && this.y < 50 && $pullDownEl.is('.flip')) {
							$pullDownEl.removeClass('flip');
							$pullDownEl.find('span').text('下拉加载更多');
							this.minScrollY = -pullDownOffset;
						} else if ($pullUpEl.length && this.y < (this.maxScrollY - 50) && !$pullUpEl.is('.flip')) {
							$pullUpEl.addClass('flip');
							$pullUpEl.find('span').text('松开加载');
							this.maxScrollY = this.maxScrollY;
						} else if ($pullUpEl.length && this.y > (this.maxScrollY + 50) && $pullUpEl.is('.flip')) {
							$pullUpEl.removeClass('flip');
							$pullUpEl.find('span').text('上拉加载更多');
							this.maxScrollY = pullUpOffset;
						}
					});
					//当滚动结束时检测添加的标记，如果有则执行数据加载操作
					iScrollObj.on('scrollEnd', function () {
						if ($pullDownEl.length && $pullDownEl.is('.flip')) {
							$pullDownEl.removeClass('flip').addClass('loading');
							$pullDownEl.find('span').text('加载中...');
							settings.onPullDownEnd($list, iScrollObj);	// Execute custom function (ajax call?)
						} else if ($pullUpEl.length && $pullUpEl.is('.flip')) {
							$pullUpEl.removeClass('flip').addClass('loading');
							$pullUpEl.find('span').text('加载中...');
							settings.onPullUpEnd($list, iScrollObj);	// Execute custom function (ajax call?)
						}
					});
					//滚动内容更新后恢复指示样式
					iScrollObj.on('refresh', function () {
						if ($pullDownEl.length && $pullDownEl.is('.loading')) {
							$pullDownEl.removeClass('loading');
							$pullDownEl.find('span').text('下拉加载更多');
						} else if ($pullUpEl.length && $pullUpEl.is('.loading')) {
							$pullUpEl.removeClass('loading');
							$pullUpEl.find('span').text('上拉加载更多');
						}
					});

					$iScroll.data('iscroll', iScrollObj);
					$iScroll.data('loading-settings', settings);
				});
			break;

			case 'triggerPullDownEnd':
				this.each(function (i, item) {
					//获取相关对象
					var $iScroll = $(item),
						$pullDownEl = $iScroll.find('.pullDown');
					var iScrollObj = $iScroll.data('iscroll'),
						settings = $iScroll.data('loading-settings');
					//触发事件
					$pullDownEl.addClass('loading').find('span').text('加载中...');
					settings.onPullDownEnd($iScroll.find('ul'), iScrollObj);
				});
			break;

			case 'triggerPullUpEnd':
				this.each(function (i, item) {
					var $iScroll = $(item),
						$pullUpEl = $iScroll.find('.pullUp');
					var iScrollObj = $iScroll.data('iscroll'),
						settings = $iScroll.data('loading-settings');
					//触发事件
					$pullUpEl.addClass('loading').find('span').text('加载中...');
					settings.onPullUpEnd($iScroll.find('ul'), iScrollObj);
				});
			break;
		}

		//保持操作链
		return this;
	};
})(jQuery);


/**
 *  轮播图插件
 *  -----------------------------
 *  作者：叼怎么写！- -||
 *  时间：2014-03-12
 *  准则：Jquery插件
 *  联系：wechat--shoe11414255
 *  一张网页，要经历怎样的过程，才能抵达用户面前
 *  一个特效，要经历这样的修改，才能让用户点个赞
 *  一个产品，创意源于生活，源于内心，需要慢慢品味
 ******************************************************************************************
 * 
 *	这是个半成品--技术不到家--努力努力 ^-^||
 *	
 * -----------保持队形------------------
 *  <div class="targetBox">
		<div class="imgSlider">
			<input type="hidden" value="src" />
		</div>
	</div>
 *********************************************************************************************/
;(function($){
	$.fn.ylSlider = function(options){
		// 默认参数
		$.fn.ylSlider.defaults = {
			auto 		: 'true',		// 开启图片自动轮播
			autotime 	: 5000, 		// 自动轮播时间
			width 		: '',			// 轮播图的宽度
			btn_auto	: true,			// 是不是生成btn标识标志
			btn_col 	: '#7b7775',	// 按钮不提示的颜色
			btn_col_on	: '#3282c5',	// 按钮提示的颜色
			pos_img		: 'left', 		// 轮播图方向（左右）
			lazy 		: 'false'		// 是否启动图片延迟加载
		};
		
		/* 初始值继承 */
		var opts = $.extend({},$.fn.ylSlider.defaults, options);

		/* 适配css属性到浏览器中 */
		_elementStyle = document.createElement('div').style;	
		function _vendor() {
			var vendors = ['t', 'webkitT', 'MozT', 'msT', 'OT'],
				transform,
				i = 0,
				l = vendors.length;
	
			for ( ; i < l; i++ ) {
				transform = vendors[i] + 'ransform';
				if ( transform in _elementStyle ) return vendors[i].substr(0, vendors[i].length-1);
			}
			return false;
		}
		function _prefixStyle(style) {
			if ( _vendor() === false ) return false;
			if ( _vendor() === '' ) return style;
			return _vendor() + style.charAt(0).toUpperCase() + style.substr(1);
		}

		return this.each(function(){
			var targetBox = $(this);										//获取到插件外层对象（div-imgSlider）
			/*
			**自动根据input传入的src值生成li-img标签
			*/
			var srcVal = targetBox.find('input[name="img_url"]').val(),		//轮播图片的地址值
            	urls = srcVal.split(","),
				linkVal = targetBox.find('input[name="link_url"]').val(),
				links = linkVal.split(","),
				idVal = targetBox.find('input[name="id"]').val(),
				ids = idVal.split(","),
				typesVal = targetBox.find('input[name="type"]').val(),
				types = typesVal.split(","),
				//将整个地址值存放一个数组中
            	imageNum = urls.length;										//计算要加载的个数
           		targetBox.find('input[type="hidden"]').remove();

            var width = opts.width ? opts.width : targetBox.width() ? targetBox.width() : 640,
            	lengths	= imageNum <=1 ? imageNum : imageNum + 2,
            	timeVal	= null,
            	imgNow	= 2;

            /*
			**生成轮播插件容器imgSlider 
			**生成2个ul容器，imgSlider-img 和 imgSlider-btn
            */
            var img 	=null ,
            	imgBox  =null ,
           		btnBox  =null ;

            if(targetBox.find(".imgSlider").length<=0)
            	img = $('<div></div>').addClass("imgSlider");			
            if(targetBox.find(".imgSlider-img").length<=0)
           		imgBox = $('<ul></ul>').addClass("imgSlider-img");		
           	if(targetBox.find(".imgSlider-btn").length<=0&&opts.btn_auto)
            	btnBox = $('<ul></ul>').addClass("imgSlider-btn");

            var imgSlider = img||targetBox.find(".imgSlider");
            img ? imgSlider.append(imgBox).append(btnBox).appendTo(targetBox) : imgSlider.append(imgBox).append(btnBox);	 //将生成的容器追加到targetBox里

            // 生成图片，并对应的格式
            function init_img(){
	            for(var i=0; i<imageNum; i++){
	            	if(opts.lazy!='false'){
  						$('<li><span></span><img class="lazy-img" /></li>').appendTo(imgBox).find('img').attr({'data-src':urls[i]});
	            	}else{
						if(types[i]!='c'){
	            		$('<li><a href="'+links[i]+'"><img class="lazy-img" onclick="yunlaiWebCallMobileTerminal('+types[i]+','+ids[i]+');" /></a></li>').appendTo(imgBox).find('img').attr({'src':urls[i]});
						}else{
						$('<li><a href="'+links[i]+'"><img class="lazy-img" /></a></li>').appendTo(imgBox).find('img').attr({'src':urls[i]});
						}
	            	}
                  
                    $('<li></li>').appendTo(btnBox);
                    if(imageNum == 1) btnBox.hide();
                }

                if(lengths>1){
                	imgBox.children().first().clone().appendTo(imgBox);
					imgBox.children().last().prev().clone().prependTo(imgBox);
					imgBox[0].style[_prefixStyle('transform')] = 'translate(-'+width+'px,0) translateZ(0)';
					imgBox.attr('data-translate',-width);

					btnBox.children().eq(imgNow-2).addClass('on');
                }
                
				imgBox.width(width*lengths);

				if(lengths>1) start();
            }

            var firstPoint 		= null,

            	movePosition_c  = true,
            	movePosition 	= null,
            	moveInit		= false,

            	_touchStart 	= true,
            	touchDelat		= 0,

            	mouseDown 		= null;

            // 轮播图开始
            function start(){
            	imgBox.on('touchstart mousedown',touch_start);
		 		imgBox.on('touchmove mousemove',touch_move);
		 		imgBox.on('touchend mouseup',touch_end);
            }

            // 轮播图停止
            function stop(){
            	imgBox.off('touchstart mousedown');
		 		imgBox.off('touchmove mousemove');
		 		imgBox.off('touchend mouseup');
            }

            // touch_start
            function touch_start(e){
            	// 自动停止
		        auto_slider_stop();
            	if(!_touchStart) return

            	if(e.type == "touchstart"){
		        	firstPoint = window.event.touches[0].pageX;
		        }else{
		        	firstPoint = e.pageX||e.x;
		        	mouseDown = true;
		        }

		        moveInit = true;

		        
            }

            // touch_move
            function touch_move(e){
            	e.stopPropagation();
            	e.preventDefault();

            	// 自动停止
		        auto_slider_stop();
            	if(!_touchStart || !moveInit) return;

            	var moveP,x,move=false;
            	if(e.type == "touchmove"){
		        	moveP = window.event.touches[0].pageX;
		        	move = true;
		        }else{
		        	if(mouseDown){
		        		moveP = e.pageX||e.x;
		        		move = true;
		        	}
		        }

		        if(!move) return;
		        
	        	if(movePosition_c){
		        	movePosition = moveP - firstPoint >0 ? 'right' : 'left';
		        	movePosition_c = false;
		        }

		        if(movePosition == 'right'){
		        	if(imgNow==1){
		        		imgNow = lengths-1;
		        		imgBox[0].style[_prefixStyle('transform')] = 'translate(-'+(lengths-2)*width+'px,0) translateZ(0)';
		        		imgBox.attr('data-translate',-(lengths-2)*width);
		        	}
		        }else{
		        	if(imgNow==lengths){
		        		imgNow = 2;
		        		imgBox[0].style[_prefixStyle('transform')] = 'translate(-'+width+'px,0) translateZ(0)';
		        		imgBox.attr('data-translate',-width);
		        	}
		        }

		        touchDelat = moveP - firstPoint;

		 		if(imgBox.attr('data-translate')) x = touchDelat + parseInt(imgBox.attr('data-translate'));
				imgBox[0].style[_prefixStyle('transform')] = 'translate('+x+'px,0) translateZ(0)';
            }

            // touch_end
            function touch_end(){
            	// 自动停止
		        auto_slider_stop();
            	if(!_touchStart) return;
            	_touchStart = false;
            	moveInit = false;

            	if(Math.abs(touchDelat)>=100){
            		success();
            	}else if(Math.abs(touchDelat)>0&&Math.abs(touchDelat)<100){
            		fail();
            	}else{
            		_touchStart = true;
            		// 自动开始
            		auto_slider_start();
            	}

            	movePosition = null;
            	movePosition_c = true;
            	mouseDown = false;
		 		firstPoint = 0;
		 		touchDelat = 0;
            }
				
			// success
			function success(val){
				var x;
				if(typeof(val)==='undefined'){
					imgBox.addClass('move');
					if(touchDelat>0){
						right();
					}else{
						left();
					}
				}else{
					if(val=='right'){
						if(imgNow==1){
			        		imgNow = lengths-1;
			        		imgBox[0].style[_prefixStyle('transform')] = 'translate(-'+(lengths-2)*width+'px,0) translateZ(0)';
			        		imgBox.attr('data-translate',-(lengths-2)*width);
			        	}
						setTimeout(function(){
							imgBox.addClass('move');
							right(val);
			        	},100)
					}else{
						if(imgNow==lengths){
			        		imgNow = 2;
			        		imgBox[0].style[_prefixStyle('transform')] = 'translate(-'+width+'px,0) translateZ(0)';
			        		imgBox.attr('data-translate',-width);
			        	}
			        	setTimeout(function(){
							imgBox.addClass('move');
							left(val);
			        	},100)
					}
				}

				setTimeout(function(){
					// 按钮变化
					btnBox.children().removeClass('on');
					var index = imgNow;
					if(index==1){
		        		index = lengths-1;
		        	}else if(index==lengths){
		        		index = 2;
		        	}
		        	btnBox.children().eq(index-2).addClass('on');

					imgBox.attr('data-translate',x);
					imgBox.removeClass('move');
					_touchStart = true;

					// 自动开始
					if(typeof(val)==='undefined') auto_slider_start();
				},600)

				// 移动
				function right(val){
					x = parseInt(imgBox.attr('data-translate'));
					x = x + width;
					imgBox[0].style[_prefixStyle('transform')] = 'translate('+x+'px,0) translateZ(0)';
					imgNow --;
				}

				function left(val){
					x = parseInt(imgBox.attr('data-translate'));
					x = x - width;
					imgBox[0].style[_prefixStyle('transform')] = 'translate('+x+'px,0) translateZ(0)';
					imgNow ++;
				}
			}

			// fail
			function fail(){
				imgBox.addClass('move');
				var x = parseInt(imgBox.attr('data-translate'));
				imgBox[0].style[_prefixStyle('transform')] = 'translate('+x+'px,0) translateZ(0)';

				setTimeout(function(){
					imgBox.removeClass('move');
					_touchStart = true;

					// 自动开始
					auto_slider_start();
				},600)
			}

			// 自动播放轮播图-开始
			function auto_slider_start(){
				timeVal = setInterval(function(){
					_touchStart = false;
					success(opts.pos_img);
				},opts.autotime)
			}

			// 自动播放轮播图-结束
			function auto_slider_stop(){
				clearInterval(timeVal);
				timeVal =null;
			}

			function loadfunction() {  
				/*加载轮播图样式--插件样式*/
				var Style = document.createElement("style");  
				Style.type = "text/css";
				
				var style_map = 
					".imgSlider { position:relative; width:100%; height:100%; overflow:hidden;}"+
					".imgSlider .imgSlider-img { position:absolute; top:0; left:0; overflow:hidden; height:100%; padding:0px; list-style-type: none; }"+
					".imgSlider .imgSlider-img li { float:left; width:640px; height:100%; text-align:center; }"+
					".imgSlider .imgSlider-img li img { display:inline-block; vertical-align: middle; }"+
					".imgSlider .imgSlider-img li span { display:inline-block; height:100%; width:0; vertical-align: middle; }"+
					".imgSlider .imgSlider-btn { position:absolute; z-index:20; bottom:15px; left:0; text-align:center; width:100%;padding: 0px }"+
					".imgSlider .imgSlider-btn li { display:inline-block; margin-right:15px; width:20px; height:20px; border-radius:50%; background:"+opts.btn_col+"; cursor:pointer; opacity:0.5; }"+
					".imgSlider .imgSlider-btn li:last-child { margin-right:0; }"+
					".imgSlider .imgSlider-btn li.on { background:"+opts.btn_col_on+"; opacity:1; }"+
					".imgSlider .imgSlider-img.move {-webkit-transition:all 0.5s;-moz-transition:all 0.5s;-ms-transition:all 0.5s;-o-transition:all 0.5s;transition:all 0.5s;}";
				Style.innerHTML = style_map ;
				document.head.appendChild(Style);
			}

			function ylSliderinit(){
				loadfunction();
				if(lengths>1&&opts.auto){
					auto_slider_start();
				}
				init_img();
			}

			return ylSliderinit()
		});
	}
})(jQuery);


/**
 * baiduTemplate简单好用的Javascript模板引擎 1.0.6 版本
 * http://baidufe.github.com/BaiduTemplate
 * 开源协议：BSD License
 * 浏览器环境占用命名空间 baidu.template ，nodejs环境直接安装 npm install baidutemplate
 * @param str{String} dom结点ID，或者模板string
 * @param data{Object} 需要渲染的json对象，可以为空。当data为{}时，仍然返回html。
 * @return 如果无data，直接返回编译后的函数；如果有data，返回html。
 * @author wangxiao 
 * @email 1988wangxiao@gmail.com
*/
;(function(window){

    //取得浏览器环境的baidu命名空间，非浏览器环境符合commonjs规范exports出去
    //修正在nodejs环境下，采用baidu.template变量名
    var baidu = typeof module === 'undefined' ? (window.baidu = window.baidu || {}) : module.exports;

    //模板函数（放置于baidu.template命名空间下）
    baidu.template = function(str, data){

        //检查是否有该id的元素存在，如果有元素则获取元素的innerHTML/value，否则认为字符串为模板
        var fn = (function(){

            //判断如果没有document，则为非浏览器环境
            if(!window.document){
                return bt._compile(str);
            };

            //HTML5规定ID可以由任何不包含空格字符的字符串组成
            var element = document.getElementById(str);
            if (element) {
                    
                //取到对应id的dom，缓存其编译后的HTML模板函数
                if (bt.cache[str]) {
                    return bt.cache[str];
                };

                //textarea或input则取value，其它情况取innerHTML
                var html = /^(textarea|input)$/i.test(element.nodeName) ? element.value : element.innerHTML;
                return bt._compile(html);

            }else{

                //是模板字符串，则生成一个函数
                //如果直接传入字符串作为模板，则可能变化过多，因此不考虑缓存
                return bt._compile(str);
            };

        })();

        //有数据则返回HTML字符串，没有数据则返回函数 支持data={}的情况
        var result = bt._isObject(data) ? fn( data ) : fn;
        fn = null;

        return result;
    };

    //取得命名空间 baidu.template
    var bt = baidu.template;

    //标记当前版本
    bt.versions = bt.versions || [];
    bt.versions.push('1.0.6');

    //缓存  将对应id模板生成的函数缓存下来。
    bt.cache = {};
    
    //自定义分隔符，可以含有正则中的字符，可以是HTML注释开头 <! !>
    bt.LEFT_DELIMITER = bt.LEFT_DELIMITER||'<%';
    bt.RIGHT_DELIMITER = bt.RIGHT_DELIMITER||'%>';

    //自定义默认是否转义，默认为默认自动转义
    bt.ESCAPE = true;

    //HTML转义
    bt._encodeHTML = function (source) {
        return String(source)
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/\\/g,'&#92;')
            .replace(/"/g,'&quot;')
            .replace(/'/g,'&#39;');
    };

    //转义影响正则的字符
    bt._encodeReg = function (source) {
        return String(source).replace(/([.*+?^=!:${}()|[\]/\\])/g,'\\$1');
    };

    //转义UI UI变量使用在HTML页面标签onclick等事件函数参数中
    bt._encodeEventHTML = function (source) {
        return String(source)
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;')
            .replace(/'/g,'&#39;')
            .replace(/\\\\/g,'\\')
            .replace(/\\\//g,'\/')
            .replace(/\\n/g,'\n')
            .replace(/\\r/g,'\r');
    };

    //将字符串拼接生成函数，即编译过程(compile)
    bt._compile = function(str){
        var funBody = "var _template_fun_array=[];\nvar fn=(function(__data__){\nvar _template_varName='';\nfor(name in __data__){\n_template_varName+=('var '+name+'=__data__[\"'+name+'\"];');\n};\neval(_template_varName);\n_template_fun_array.push('"+bt._analysisStr(str)+"');\n_template_varName=null;\n})(_template_object);\nfn = null;\nreturn _template_fun_array.join('');\n";
        return new Function("_template_object",funBody);
    };

    //判断是否是Object类型
    bt._isObject = function (source) {
        return 'function' === typeof source || !!(source && 'object' === typeof source);
    };

    //解析模板字符串
    bt._analysisStr = function(str){

        //取得分隔符
        var _left_ = bt.LEFT_DELIMITER;
        var _right_ = bt.RIGHT_DELIMITER;

        //对分隔符进行转义，支持正则中的元字符，可以是HTML注释 <!  !>
        var _left = bt._encodeReg(_left_);
        var _right = bt._encodeReg(_right_);

        str = String(str)
            
            //去掉分隔符中js注释
            .replace(new RegExp("("+_left+"[^"+_right+"]*)//.*\n","g"), "$1")

            //去掉注释内容  <%* 这里可以任意的注释 *%>
            //默认支持HTML注释，将HTML注释匹配掉的原因是用户有可能用 <! !>来做分割符
            .replace(new RegExp("<!--.*?-->", "g"),"")
            .replace(new RegExp(_left+"\\*.*?\\*"+_right, "g"),"")

            //把所有换行去掉  \r回车符 \t制表符 \n换行符
            .replace(new RegExp("[\\r\\t\\n]","g"), "")

            //用来处理非分隔符内部的内容中含有 斜杠 \ 单引号 ‘ ，处理办法为HTML转义
            .replace(new RegExp(_left+"(?:(?!"+_right+")[\\s\\S])*"+_right+"|((?:(?!"+_left+")[\\s\\S])+)","g"),function (item, $1) {
                var str = '';
                if($1){

                    //将 斜杠 单引 HTML转义
                    str = $1.replace(/\\/g,"&#92;").replace(/'/g,'&#39;');
                    while(/<[^<]*?&#39;[^<]*?>/g.test(str)){

                        //将标签内的单引号转义为\r  结合最后一步，替换为\'
                        str = str.replace(/(<[^<]*?)&#39;([^<]*?>)/g,'$1\r$2')
                    };
                }else{
                    str = item;
                }
                return str ;
            });


        str = str 
            //定义变量，如果没有分号，需要容错  <%var val='test'%>
            .replace(new RegExp("("+_left+"[\\s]*?var[\\s]*?.*?[\\s]*?[^;])[\\s]*?"+_right,"g"),"$1;"+_right_)

            //对变量后面的分号做容错(包括转义模式 如<%:h=value%>)  <%=value;%> 排除掉函数的情况 <%fun1();%> 排除定义变量情况  <%var val='test';%>
            .replace(new RegExp("("+_left+":?[hvu]?[\\s]*?=[\\s]*?[^;|"+_right+"]*?);[\\s]*?"+_right,"g"),"$1"+_right_)

            //按照 <% 分割为一个个数组，再用 \t 和在一起，相当于将 <% 替换为 \t
            //将模板按照<%分为一段一段的，再在每段的结尾加入 \t,即用 \t 将每个模板片段前面分隔开
            .split(_left_).join("\t");

        //支持用户配置默认是否自动转义
        if(bt.ESCAPE){
            str = str

                //找到 \t=任意一个字符%> 替换为 ‘，任意字符,'
                //即替换简单变量  \t=data%> 替换为 ',data,'
                //默认HTML转义  也支持HTML转义写法<%:h=value%>  
                .replace(new RegExp("\\t=(.*?)"+_right,"g"),"',typeof($1) === 'undefined'?'':baidu.template._encodeHTML($1),'");
        }else{
            str = str
                
                //默认不转义HTML转义
                .replace(new RegExp("\\t=(.*?)"+_right,"g"),"',typeof($1) === 'undefined'?'':$1,'");
        };

        str = str

            //支持HTML转义写法<%:h=value%>  
            .replace(new RegExp("\\t:h=(.*?)"+_right,"g"),"',typeof($1) === 'undefined'?'':baidu.template._encodeHTML($1),'")

            //支持不转义写法 <%:=value%>和<%-value%>
            .replace(new RegExp("\\t(?::=|-)(.*?)"+_right,"g"),"',typeof($1)==='undefined'?'':$1,'")

            //支持url转义 <%:u=value%>
            .replace(new RegExp("\\t:u=(.*?)"+_right,"g"),"',typeof($1)==='undefined'?'':encodeURIComponent($1),'")

            //支持UI 变量使用在HTML页面标签onclick等事件函数参数中  <%:v=value%>
            .replace(new RegExp("\\t:v=(.*?)"+_right,"g"),"',typeof($1)==='undefined'?'':baidu.template._encodeEventHTML($1),'")

            //将字符串按照 \t 分成为数组，在用'); 将其合并，即替换掉结尾的 \t 为 ');
            //在if，for等语句前面加上 '); ，形成 ');if  ');for  的形式
            .split("\t").join("');")

            //将 %> 替换为_template_fun_array.push('
            //即去掉结尾符，生成函数中的push方法
            //如：if(list.length=5){%><h2>',list[4],'</h2>');}
            //会被替换为 if(list.length=5){_template_fun_array.push('<h2>',list[4],'</h2>');}
            .split(_right_).join("_template_fun_array.push('")

            //将 \r 替换为 \
            .split("\r").join("\\'");

        return str;
    };
})(window);