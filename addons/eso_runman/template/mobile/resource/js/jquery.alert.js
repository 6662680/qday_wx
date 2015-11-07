/**
 *
 * @param e 提示内容，===0则关闭提示
 * @param t 自动关闭时间，默认2000，===0则不自动关闭
 * @param p 点击关闭，赋值则不关闭
 * @param but 赋值显示按钮名称
 */
jQuery.alert = function(e, t, p, but, bk) {
	$("div.jQuery-ui-alert").remove();
	$("div.jQuery-ui-alert-back").remove();
    if (e === 0) return;
	var m = Math.round(Math.random() * 10000);
    if (but) e+= but;
	var n = '<div class="jQuery-ui-alert" style="display:hidden;position:fixed;top:0;left:0;padding:15px 10px;min-width:100px;opacity:1;min-height:25px;text-align:center;color:#fff;display:block;z-index:2147483647;border-radius:3px;background-color: rgba(51,51,51,.9); opacity:1;font-size:14px; line-height:22px;" id="jQuery-ui-alert-' + m + '" >' + e + '</div>' +
        '<div class="jQuery-ui-alert-back" style="display:none;z-index:2147483646" id="jQuery-ui-alert-back-' + m + '"></div>';
	$("body").append(n);
	var nobjbg = $('#jQuery-ui-alert-back-' + m);
    nobjbg.css({
        "width":"100%",
        "height":$(document).height(),
        "position":"absolute",
        "top":"0px",
        "left":"0px",
        "background-color":"#cccccc",
        "opacity":"0.6"
    });
    if (!bk) nobjbg.show();
    var nobj = $('#jQuery-ui-alert-' + m);
    if (!p)	nobj.click(function(){ nobj.fadeOut(); nobjbg.hide(); });
    if (!p)	nobjbg.click(function(){ nobj.fadeOut(); nobjbg.hide(); });
	var i = $(window).width(),
	s = $(window).height(),
	o = nobj.width()+20,
	u = nobj.height(),
	l = (i - o) / 2;
	i > o && nobj.css("left", parseInt(l)),
	i > o && nobj.css("right", parseInt(l)),
	s > u && nobj.css("top", (s - u) / 2 - 20),
	l < 5 && nobj.css("margin", "0 5px"),
	nobj.show();
	if (t === 0) return;
	setTimeout(function() { nobj.fadeOut(); nobjbg.hide(); }, t || 2000)
};
jQuery.alertk = function(e, t, p, but) {
    $.alert(e, t, p, but, 1)
}
jQuery.alertb = function(e, but, diy) {
    if (!but) but = "确定";
    var _click =  (!diy)?'onclick="$.alert(0)" ':'';
    $.alert(e, 0, 1, '<div '+_click+'style="text-align:center;padding:5px;border-top:1px solid #ECECEC;margin:15px -10px -15px;">'+but+'</div>')
}
/**
 *
 * @param msg 提示文本
 * @param parame -1不为空、-2手机号码规则、-3电话号码、-4邮箱规则、-5不得包含非法字符、大于0字符长度要求
 * @param retu 是否执行
 * @param parame2 字符长度要求最大不超过parame
 * @param msgobj 可选提示在此对象之后
 * @returns {boolean}
 */
$.fn.inTips = function(msg, parame, retu, parame2, msgobj) {
    if (retu === false){
        return false;
    }
    if (!isNaN(parame)){
        if (parame === -1){
            if (!msg) msg = "不可留空";
            if (this.val() != "") return true;
        }else if (parame === -2){
            if (!msg) msg = "格式错误"; //手机号码
            if (/^1\d{10}$/g.test(this.val())) return true;
        }else if (parame === -3){
            if (!msg) msg = "格式错误"; //电话号码
            if (/^0\d{2,3}-?\d{7,8}$/g.test(this.val())) return true;
        }else if (parame === -4){
            if (!msg) msg = "格式错误"; //电子邮箱
            if (/^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/g.test(this.val())) return true;
        }else if (parame === -5){
            if (!msg) msg = "格式错误"; // 用户名
            if (/^[a-zA-z]\w{1,50}$/g.test(this.val())) return true;
        }else if (parame > 0){
            if (parame2){
                if (!msg) msg = "最大" + parame + "个字符";
                if (this.val().length <= parame) return true;
            }else{
                if (!msg) msg = "最少" + parame + "个字符";
                if (this.val().length >= parame) return true;
            }
        }else if (parame < 0){
            if (!msg) msg = "最大" + (parame*-1) + "个字符";
            if (this.val().length <= (parame*-1)) return true;
        }
    }else if (parame instanceof jQuery){
        if (!msg) msg = "两次输入不一致";
        if (parame.val() == this.val()) return true;
    }
    //
	var m = Math.round(Math.random() * 10000);
	var $imithis = this;
    if (msgobj instanceof jQuery) $imithis = msgobj;
	if ($imithis.attr("data-jQueryuitips-id") > 0){
		m = $imithis.attr("data-jQueryuitips-id");
	}else{	
		$imithis.attr("data-jQueryuitips-id", m)
	}
	$("span#jQuery-ui-tips-"+m).remove();
	var $imitate = $('<span id="jQuery-ui-tips-' + m + '" style="position:absolute; display:none; overflow:hidden; color:#cccccc; height:20px;"><i>！' + msg + '</i></span>');
	$(document.body).append($imitate.click(function () {
		$imithis.trigger('focus');
		$imithis.removeClass('jQuery-ui-tips');
		$imitate.remove();
	}));
	if ($imithis.attr("data-uitips") == "jQ"){
		$imithis.unbind("click",jQUiTips);
	}
	$imithis.addClass('jQuery-ui-tips');
	$imithis.attr("data-uitips", "jQ");
	$imithis.bind("click", jQUiTips = function(){
		$imithis.removeClass('jQuery-ui-tips');
		$imitate.remove();
	});
	//定位
    var ttop  = $imithis.offset().top;     		//控件的定位点高
    var thei  = $imithis.outerHeight();  		//控件本身的高
    var twid  = $imithis.outerWidth();  		//控件本身的宽
    var tleft = $imithis.offset().left;    		//控件的定位点宽
	$("#jQuery-ui-tips-" + m).css({
        width:$("#jQuery-ui-tips-" + m).width() + 12,
        height:thei,
        top:ttop,
        left:tleft + twid + 10,
        'line-height':thei+'px'
    }).show();
	$("#jQuery-ui-tips-" + m + " i").css({
		'color':'#ff0000',
		'font-style': 'normal',
		'font-weight': '600',
		'font-size': '12px'
    }).show();
    //
    $body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
    $body.animate({scrollTop: $imithis.offset().top - 10}, 100);
    return false;
};

/**
 *
 * @param msg
 */
jQuery.inModal = function(msg) {
    $.showModal(msg, '', '', 1);
};
/**
 *
 * @param msg
 * @param url
 * @param goonurl
 * @param isautohide
 */
jQuery.showModal = function(msg, url, goonurl, isautohide) {
    $("div.jQuery-ui-myModal").remove();
    $("div.jQuery-ui-myModal-Bg").remove();
	var m = Math.round(Math.random() * 10000);
	var n = '<div class="jQuery-ui-myModal" id="jQuery-ui-myModal-' + m + '" style="display:none;">'
		+'	<div class="jqmodal-header">'
		+'		<button type="button" class="close" onclick="$(\'.jQuery-ui-myModal\').hide();$(\'.jQuery-ui-myModal-Bg\').hide();">\u00d7</button>'
		+'		<h3 id="myModalLabel">\u6d88\u606f</h3>'
		+'	</div>'
		+'	<div class="jqmodal-body">'
		+'		<p class="error-text"><i class="jqmodal-icon fa fa-exclamation-triangle"></i><span id="myModalContent">Are you sure you?</span></p>'
		+'	</div>'
		+'	<div class="jqmodal-footer">'
		+'		<a id="myModalLink" href="javascript:void(0);" class="jqmodal-btn-danger"><i class="fa fa-check">\u221a\u0020</i>\u786e\u5b9a</a>'
		+'		<a id="myModalGoon" href="javascript:void(0);" class="jqmodal-btn-info">\u7ee7\u7eed<i class="fa fa-chevron-right">\u0020\u003e</i></a>'
		+'	</div>'
		+'</div>'
		+'<div class="jQuery-ui-myModal-Bg" id="jQuery-ui-myModal-Bg-' + m + '" style="display:none;"></div>';
	$("body").append(n);
    var nobjbg = $('#jQuery-ui-myModal-Bg-' + m);
    nobjbg.css({
        "width":"100%",
        "height":$(document).height(),
        "position":"absolute",
        "top":"0px",
        "left":"0px",
        "background-color":"#cccccc",
        "opacity":"0.5",
        "z-index":"10000"
    });
    var nobj = $('#jQuery-ui-myModal-' + m);
	nobj.css({
        "background-color":"#ffffff",
        "border-radius":"6px",
        "box-shadow":"0 3px 7px rgba(0, 0, 0, 0.3)",
        "border":"1px solid rgba(0, 0, 0, 0.5)",
        "position":"fixed",
        "margin":"0px auto",
        "width":"420px",
        "z-index":"10001"
    });
    nobj.find(".jqmodal-header").css({
        "background-color":"#ffffff",
        "border-radius":"6px 6px 0 0",
        "padding": "9px 15px",
        "border-bottom": "1px solid #eee"
    });
	nobj.find(".jqmodal-header h3").css({
        "line-height": "30px"
    });
	nobj.find("button.close").css({
        "margin-top": "5px",
        "padding": "0",
        "background": "transparent",
        "border": "0",
        "-webkit-appearance": "0",
        "float": "right",
        "font-size": "12px",
        "line-height": "20px",
        "font-weight": "bold",
        "color": "#000000",
        "text-shadow": "0 1px 0 #ffffff",
        "opacity": "0.2"
    });
	nobj.find(".jqmodal-body").css({
        "font-size": "14px",
        "padding": "2em",
        "overflow-y": "auto",
        "max-height": "400px"
    });
    nobj.find(".jqmodal-body p").css({
        "line-height": "1.5em"
    });
    nobj.find(".jqmodal-body p i").css({
        "vertical-align": "middle",
        "font-size": "56px",
        "float": "left",
        "line-height": "28px",
        "margin-right": ".25em"
    });
    nobj.find(".jqmodal-footer").css({
        "background-color":"#f5f5f5",
        "border-radius":"0 0 6px 6px",
        "padding":"12px",
        "text-align":"right",
        "border-top":"1px solid #ddd",
        "box-shadow":"inset 0 1px 0 #ffffff"
    });
    nobj.find(".jqmodal-footer a i").css({"font-style": "normal"});
    if (nobj.find(".jqmodal-footer a i").css("display") == 'inline-block'){
        nobj.find(".jqmodal-footer a i").each(function(){$(this).attr("data-text", $(this).text());});
        nobj.find(".jqmodal-footer a i").text("");
        nobj.find(".jqmodal-footer a i.fa-check").css({"padding-right": "3px"});
        nobj.find(".jqmodal-footer a i.fa-chevron-right").css({"padding-left": "3px"});
    }
    nobj.find(".jqmodal-btn-danger").css({
        "background-color":"#553333",
        "border-radius":"5px",
        "border":"1px solid #452929",
        "color":"#ffffff",
        "text-shadow":"0 -1px 0 rgba(0, 0, 0, 0.25)",
        "background-image":"linear-gradient(to bottom, #955959, #553333)",
        "font-size": "14px",
        "line-height": "20px",
        "padding":"7px 14px"
    });
    nobj.find(".jqmodal-btn-info").css({
        "margin-left": "5px",
        "display": "none",
        "background-color":"#49afcd",
        "border-radius":"5px",
        "border":"1px solid #2f96b4",
        "color":"#ffffff",
        "text-shadow":"0 -1px 0 rgba(0, 0, 0, 0.25)",
        "background-image":"linear-gradient(to bottom, #5bc0de, #2f96b4)",
        "font-size": "14px",
        "line-height": "20px",
        "padding":"7px 14px"
    });
    var i = $(window).width(),
        s = $(window).height(),
        o = nobj.width(),
        u = nobj.height(),
        l = (i - o) / 2;
    i > o && nobj.css("left", l),
        s > u && nobj.css("top", (s - u) / 2),
        l < 5 && nobj.css("margin", "0 5px");
	nobj.show();

    var isIe6 = false;
    if (/msie/.test(navigator.userAgent.toLowerCase())) {
        if (jQuery.browser && jQuery.browser.version && jQuery.browser.version == '6.0') {
            isIe6 = true
        } else if (!$.support.leadingWhitespace) {
            isIe6 = true;
        }
    }
    if(isIe6){
        nobj.find(".jqmodal-body p i").hide();
        nobj.find(".jqmodal-footer a i").each(function(){$(this).text($(this).attr("data-text"));});
        nobj.css({
            "position":"absolute"
        });
        $(window).scroll(function (){
            var offsetTop = ($(window).scrollTop() + ($(window).height() - nobj.height()) / 2) +"px";
            nobj.animate({top : offsetTop },{ duration:500 , queue:false });
        });
    }else{
        nobjbg.show();
    }

    $(window).resize(function(){
        var i = $(window).width(),
            s = $(window).height(),
            o = nobj.width(),
            u = nobj.height(),
            l = (i - o) / 2;
        i > o && nobj.css("left", l),
            s > u && nobj.css("top", (s - u) / 2),
            l < 5 && nobj.css("margin", "0 5px");
        if(isIe6){
            var offsetTop = ($(window).scrollTop() + ($(window).height() - nobj.height()) / 2) +"px";
            nobj.css("top", offsetTop);
        }
    });

	if (goonurl) {
        nobj.find("#myModalGoon").show();
        nobj.find("#myModalGoon").attr("href", goonurl);
	}
    nobj.find("#myModalContent").html(msg);
	if (url) {
        nobj.find("#myModalLink").attr("href", url);
        if (!isautohide) {
			setTimeout(function(){
				nobj.fadeOut(1000, function(){window.location.href = url;});
			},1000);			
		}
	} else {
        nobj.find("#myModalLink").attr("href", 'javascript:void(0);');
        nobj.find("#myModalLink").click(function(){nobj.hide();nobjbg.hide();});
		if (!isautohide) {
			setTimeout(function(){
				nobj.fadeOut(1000, function(){nobjbg.hide();});
			},1000);
		}		
	}
};