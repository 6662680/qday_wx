/*
 * 微信分享内容设置
 * 
 * @param shareData <object> 传入分享的具体内容包括title：标题，content：简介，imgUrl：小图片的绝对地址，shareUrl：分享的绝对地址
 */
function wxShareFunc(shareData){
	/*var shareData = {
		"title": title,
		"content": content,
		"imgUrl": imgUrl,
		"shareUrl": shareUrl
	};*/

	document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
		WeixinJSBridge.call('showOptionMenu');

	// 发送给好友
	WeixinJSBridge.on('menu:share:appmessage', function (argv) {
		WeixinJSBridge.invoke('sendAppMessage', {
			"img_url": shareData.imgUrl,
			"img_width": "150",
			"img_height": "150",
			"link": shareData.shareUrl ? shareData.shareUrl : shareData.shareFriend,
			"desc": shareData.content,
			"title": shareData.title
		}, function (res) {
			//_report('send_msg', res.err_msg);
		})
	});

	// 分享到朋友圈
	WeixinJSBridge.on('menu:share:timeline', function (argv) {
		WeixinJSBridge.invoke('shareTimeline', {
			"img_url": shareData.imgUrl,
			"img_width": "150",
			"img_height": "150",
			"link": shareData.shareUrl ? shareData.shareUrl : shareData.shareCircle,
			"desc": shareData.content,
			"title": shareData.title
		}, function (res) {
			//_report('timeline', res.err_msg);
		});
	});
	
	}, false);
	
}