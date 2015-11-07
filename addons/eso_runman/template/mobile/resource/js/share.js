if (typeof(shareData) != "undefined") {
	if (typeof(wx) != "undefined") wx = null;
	$.getScript("http://res.wx.qq.com/open/js/jweixin-1.0.0.js", function(){
		if (shareData.imgUrl && (shareData.imgUrl.substring(0,1) == '.' || shareData.imgUrl.substring(0,1) == '#')) {
			if ($(shareData.imgUrl).find("img:eq(0)").attr("src")) {
				shareData.imgUrl = $(shareData.imgUrl).find("img:eq(0)").attr("src");
			}
		}
		if (!shareData.link) {
			shareData.link = document.URL;
		}
		if (!shareData.title) {
			shareData.title = $("title:eq(0)").text();
		}
		if (!shareData.desc) {
			shareData.desc = $("body").text();
		}
		if (shareData.desc && (shareData.desc.substring(0,1) == '.' || shareData.desc.substring(0,1) == '#')) {
			if ($(shareData.desc).text()) {
				shareData.desc = $(shareData.desc).text().replace(/^\s+|\s+$/g,"");
			}
		}
		// 是否启用调试
		jssdkconfig.debug = false;
		//
		jssdkconfig.jsApiList = [
			'checkJsApi',
			'onMenuShareTimeline',
			'onMenuShareAppMessage',
			'onMenuShareQQ',
			'onMenuShareWeibo'
		];
		wx.config(jssdkconfig);
		wx.ready(function () {
			wx.onMenuShareAppMessage(shareData);
			wx.onMenuShareTimeline(shareData);
			wx.onMenuShareQQ(shareData);
			wx.onMenuShareWeibo(shareData);
		});
	});
}