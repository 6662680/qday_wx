//viewport
var userAgent = navigator.userAgent.toLowerCase();
if(userAgent.match(/android/i)){
	if(!userAgent.match(/galaxy/i)){
        var width = 640;
        var defaultDpi = 160;
        $('meta[name=viewport]').remove();
        var densityDpi = (defaultDpi * width * window.devicePixelRatio / screen.width) | 0;
        var meta = document.createElement('meta');
        meta.setAttribute('name', 'viewport');
        meta.setAttribute('content', 'width=device-width, target-densitydpi=' + densityDpi + ', initial-scale=1.0, maximum-scale=1.0, user-scalable=0');
        document.getElementsByTagName('head')[0].appendChild(meta);
    }
}

$(function(){
	if(!navigator.userAgent.match(/.*Mobile./i)){
		$(".page").css("width","640px");
	}
});