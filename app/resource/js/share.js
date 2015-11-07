/**
 *//todo: 
 */
function share(sharedata, callback){
	
	WeixinApi.ready(function(Api) {

		// 微信分享的数据
		var wxData = {
			"appId"  : sharedata['appId']+'', // 服务号可以填写appId
			"imgUrl" : sharedata['imgUrl']+'',
			"link"   : sharedata['link']+'&wxref=mp.weixin.qq.com',
			"desc"   : sharedata['desc']+'',
			"title"  : sharedata['title']+''
		};

		// 分享的回调
		var wxCallbacks = {
			// 分享成功
			confirm : function(resp) {
				// 分享成功了，我们是不是可以做一些分享统计呢？
				if(callback && typeof(callback) == 'function'){
					callback(resp);
				}
			}
		};

		// 用户点开右上角popup菜单后，点击分享给好友，会执行下面这个代码
		Api.shareToFriend(wxData, wxCallbacks);

		// 点击分享到朋友圈，会执行下面这个代码
		Api.shareToTimeline(wxData, wxCallbacks);

		// 点击分享到腾讯微博，会执行下面这个代码
		Api.shareToWeibo(wxData, wxCallbacks);
	});
}