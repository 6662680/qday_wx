$(function (){

    var shareUrl=$("#shareUrl").val();
    var word="长按链接全选复制";
    if(PC){
        word="请选择文本框连接进行复制";
    }
    $(".bdsharebuttonbox a").click(function(e){
        e.preventDefault();
        var $this = $(this);
        if($this.hasClass("bds_copy")){
            $.vAlert({
                "tit": "复制链接",
                "msg": "<textarea id='copyArea'>"+shareUrl+"</textarea><p>"+word+"</p>",
                "okBtnText": "关闭"
            });
        }
    });
    if(shareUrl){
        loadShare();
    }

});

function loadShare(){
    window._bd_share_config = {
        common : shareContent,
        share : [ {
            "bdSize" : 24,
            "bdCustomStyle": $("#shareCss").val()
        } ]
    };
    with (document)
        0[(getElementsByTagName('head')[0] || body)
            .appendChild(createElement('script')).src = 'http://bdimg.share.baidu.com/static/api/js/share.js?cdnversion='
            + ~(-new Date() / 36e5)];

}