// JQuery扩展 diaoshuang 创建于 2012/12/06
// 获取cookie值，设置cookie值
$.cookie = function(key, value, options){
    if(typeof value=="undefined"){
        value=null;
        if(document.cookie && document.cookie!=''){
            var arr = document.cookie.split(";");
            for(var i=0;i<arr.length;i++){

                var c = $.trim(arr[i]);
                if (c.substr(0, key.length) == key) {
                    var l = c.indexOf("=",key.length);
                    if(l && l != c.length){
                        value = decodeURIComponent(c.substr(l+1));
                    }
                    break;
                }
            }
        }
        return value;
    }
    options = options || {};
    if(value===null){
        value="";
        options.expires=-1;
    }
    var expires="";
    var date;
    if(options.expires && (typeof options.expires=="number" || options.expires.toUTCString)){
        if(typeof options.expires=="number"){
            date = new Date();
            date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
        }else{
            date = options.expires;
        }
        expires = "; expires=" + date.toUTCString();
    }
    var path = options.path ? '; path=' + (options.path) : '';
    var domain = options.domain ? '; domain=' + (options.domain) : '';
    var secure = options.secure ? '; secure' : '';
    var ccd = [key, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    document.cookie = ccd;
    path = domain = secure = ccd = expires = date = null;
    delete path,domain,secure,ccd,expires,date;
};
// 删除cookie值
$.deleteCookie = function(name){
    var now_v = $.cookie(name);
    if(!now_v){
        return;
    }
    // 获取当前时间
    var ndate = new Date();
    // 将date设置为过去的时间
    ndate.setTime(ndate.getTime()-10000);
    // 将对应的cookie删除
    document.cookie = [name,"=",$.cookie(name),"; expires=",ndate.toGMTString()].join("");
    now_v = ndate = null;
    delete now_v,ndate;
};
// 获取地址
$.getHost = function(){
    var url = window.location.host;
    var protocol = window.location.protocol;
    return protocol + "//" + url;
};
var chars = ['0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
function generateMixed(n) {
    var res = "";
    for(var i = 0; i < n ; i ++) {
        var id = Math.ceil(Math.random()*35);
        res += chars[id];
    }
    return res;
}
$.getRadomUserId = function(){
    if(!$.cookie("radomUserId")){
        var id = (new Date()).getTime()+"_";
        var l = id.length;
        id += generateMixed(30-l);
        id.replace(/,/g,"");
        $.cookie("radomUserId",id,{"path":"/","expires":180});
        // 来源地址
        var referrer = document.referrer;
        if (!referrer) {
            try {
                if (window.opener) {
                    // IE下如果跨域则抛出权限异常
                    // Safari和Chrome下window.opener.location没有任何属性
                    referrer = window.opener.location.href;
                }
            }
            catch (e) {}
        }
        if(!referrer){
            referrer = "N";
        }
        var m = "youyuan.com";
        if(referrer.indexOf(m) > -1){
            referrer = "N";
        }
        $.cookie("out_url",referrer,{"path":"/","expires":180});
        return id;
    }
    a = b = id = m = referrer = null;
    return $.cookie("radomUserId");
};
$.yyLog = function(name,type,value){
    name = name?name:"N";
    type = type?type:"V";
    value = value?value:"N";
    // 上一页地址
    var referrer = document.referrer;
    if(!referrer){
        referrer = "N";
    }
    var rId = $.getRadomUserId();
    var t1 = rId.split("_")[0];
    var t = new Date(parseInt(t1));
    var month = t.getMonth()+1;
    var day = t.getDate();
    var hour = t.getHours();
    var minutes = t.getMinutes();
    var seconds = t.getSeconds();
    month = month<10?"0"+month:""+month;
    day = day<10?"0"+day:""+day;
    hour = hour<10?"0"+hour:""+hour;
    minutes = minutes<10?"0"+minutes:""+minutes;
    seconds = seconds<10?"0"+seconds:""+seconds;

    t = t.getFullYear() + "-" + month + "-" + day + " " + hour + ":" + minutes + ":" + seconds;
    var a = $.getHost();
    var l = window.location.href;
    if(referrer.indexOf(a) == 0){
        referrer = referrer.substr(a.length);
    }
    if(l.indexOf(a) == 0){
        l = l.substr(a.length);
    }
    var data ={
        "t_user_id":rId,
        "t_user_time":t,
        "t_referrer":$.cookie("out_url")?$.cookie("out_url"):"N",
        "referrer":referrer,
        "location":l,
        "log_type":type,
        "log_name":name,
        "log_value":value
   };
    var path="/log.html";
    Util.ajax(path,function(){},data);
    l = referrer = rId = t = data = path = null;
};
$.YYLOGKEY = {
    // 每日PV，UV
    "DAY_V" : "N",
    // 注册数
    "REGISTER" : "REGISTER",
    // 引导页
    "SPREAD" : "SPREAD",
    // 登录数
    "LOGIN" : "LOGIN",
    // 上传头像数
    "UPLOAD_LOGO" : "UPLOAD_LOGO",
    // 完善资料数
    "UPLOAD_INFO" : "UPLOAD_INFO",
    // 更改征友条件
    "UPLOAD_CONDISION" : "UPLOAD_CONDISION",
    // 手机验证
    "UPLOAD_APPROVETEL" : "UPLOAD_APPROVETEL",
    // 身份验证
    "UPLOAD_IDENTITY" : "UPLOAD_IDENTITY",
    // 设置邮箱
    "SAVEMAIL" : "SAVEMAIL",
    // 设置免打扰
    "SETDND" : "SETDND",
    // 上传照片
    "UPLOAD_IMG" : "UPLOAD_IMG",
    // 浏览空间数
    "VIEW_USER_SPACE" : "VIEW_USER_SPACE",
    // 打招呼数
    "SAYHI" : "SAYHI",
    // 下单数
    "MAKE_BILL" : "MAKE_BILL",
    // 支付数
    "CHARGE_BILL" : "CHARGE_BILL",
    // 购买产品类型
    "PRODUCT_TYPE" : "PRODUCT_TYPE",
    // 支付方式数
    // 电话卡
    "CHARGE_TYPE_TEL" : "CHARGE_TYPE_TEL",
    // 易宝
    "CHARGE_TYPE_YEEBAO" : "CHARGE_TYPE_YEEBAO",
    // 银行转帐
    "CHARGE_TYPE_BANK" : "CHARGE_TYPE_BANK",
    // 推广
    "SPREAD" : "SPREAD",
    // 支付宝
    "CHARGE_TYPE_ALI" : "CHARGE_TYPE_ALI"
};
// 格式化数字
$.numberFormat = function(val,key,flg){
    key = key?key:"";
    if(flg == "number"){
        val = parseInt(val.replace(/[^0-9]/g,""));
        return val;
    }
    if(typeof val == "number"){
        val = val + "";
        var arrN = val.split("").reverse();
        var k="";
        for(var i=1;i<=arrN.length;i++){
            k +=arrN[i-1];
            if(i%3 == 0){
                k +=key;
            }
        }
        return k.split("").reverse().join("");
    }
};
$.MD5 = function(){
    var arr = [""];
}
// 设置主页
// 保存到书签
// 扩展按钮信息提示
// 扩展按钮上标