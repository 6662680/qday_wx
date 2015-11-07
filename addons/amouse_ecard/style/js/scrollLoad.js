/**
 * 数据滚动加载 v
 */
;(function($){
	$.fn.scrollLoad = function(options){
		var defaults = {
			"url": null, //请求地址
			"fromPage": 1, //从第几页开始
			"scrollWrap": $(document), //滚动的对象
			"pageSize": 10, //每页加载个数
			"params": null, //额外提交的参数,
			"htmlTemp": null, //function 数据的html结构 接受了返回的data
			"bsCallback": null, //function beforeSend callback
			"callback": null, //function 加载成功后的回调
			"dataType": "json"
		};
	
		var settings = $.extend({},defaults,options);
		
		return this.each(function(){
			var $this = $(this);
			var pageNum = settings.fromPage,
				totalPages = 0;
			
			var winHig = $(window).height();
			var loadedFlag = 1;
			function lazyLoad(runDis){
				if(loadedFlag && runDis < 70){
					var dataParams = {"pageNo":pageNum, "pageSize":settings.pageSize};
					if(settings.params) {
						dataParams = $.extend(dataParams,settings.params);
					}
					$.ajax({
						type: "POST",
						url: settings.url,
						data: dataParams,
						dataType: settings.dataType,
						beforeSend: function() {
							//hold住防止在本次加载完成之前加载下一页
							loadedFlag = 0;
							settings.bsCallback && settings.bsCallback();
						},
						success: function(data) {
							if(data.totalPages>0){
								totalPages = data.totalPages;
							}
							$this.append(settings.htmlTemp(data));
							
							pageNum += 1;
							loadedFlag = 1;
							
							settings.callback && settings.callback();
						}
					});
				}
			}
			
			//默认加载一页
			lazyLoad(0);
			
			//滚动后加载
			//settings.scrollWrap.unbind("scroll");
			settings.scrollWrap.scroll(function(e) {
				var $thisWrap = $(this);
				docSt = $thisWrap.scrollTop(),
				docHig = $thisWrap.height(),
				runDis = docHig - docSt - winHig;
				if(totalPages >= pageNum){
					lazyLoad(runDis);
				}
			});
		});
	
	};
})(jQuery);