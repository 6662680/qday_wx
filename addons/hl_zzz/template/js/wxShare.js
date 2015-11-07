/*******************************
 * Author:Mr.Think
 * Description:微信分享通用代码
 * 使用方法：_WXShare('分享显示的LOGO','LOGO宽度','LOGO高度','分享标题','分享描述','分享链接','微信APPID(一般不用填)');
 *******************************/
function _WXShare(img, width, height, title, desc, url, appid){
    //初始化参数
    img = img || '';
    width = width || 100;
    height = height || 100;
    title = title || document.title;
    desc = desc || document.title;
    url = url || document.location.href;
    appid = appid || '';
    //微信内置方法
    function _ShareFriend(){
        WeixinJSBridge.invoke('sendAppMessage', {
            'appid': appid,
            'img_url': img,
            'img_width': width,
            'img_height': height,
            'link': url,
            'desc': desc,
            'title': title
        }, function(res){
            console.log('send_msg', res.err_msg);
        })
    }
    function _ShareTL(){
        WeixinJSBridge.invoke('shareTimeline', {
            'img_url': img,
            'img_width': width,
            'img_height': height,
            'link': url,
            'desc': desc,
            'title': desc//title + '\n\r' + desc
        }, function(res){
            console.log('timeline', res.err_msg);
        });
    }
    function _ShareWB(){
        WeixinJSBridge.invoke('shareWeibo', {
            'content': desc,
            'url': url,
        }, function(res){
            console.log('weibo', res.err_msg);
        });
    }
    // 当微信内置浏览器初始化后会触发WeixinJSBridgeReady事件。
    document.addEventListener('WeixinJSBridgeReady', function onBridgeReady(){
        // 发送给好友
        WeixinJSBridge.on('menu:share:appmessage', function(argv){
            _ShareFriend();
        });
        
        // 分享到朋友圈
        WeixinJSBridge.on('menu:share:timeline', function(argv){
            _ShareTL();
        });
        
        // 分享到微博
        WeixinJSBridge.on('menu:share:weibo', function(argv){
            _ShareWB();
        });
    }, false);
}
