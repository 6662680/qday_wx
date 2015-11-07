var __rptk_login_private = new
(function () {
    var triggered = false;

    this.redir = function (url) {
	    window.location.href = url;
    }

    this.trigger = function () {
	    if (triggered) return false;
	    triggered = true;
	    setTimeout(function() {
    	    if (out_ripple()) {
    		    rptk_apis.go_away();
    	    }
	    }, 5000);
	    return true;
    }

    this.in_ripple = function () {
	    return (typeof rptk_apis == "object" && rptk_apis.ripple_inside());
    }

    function out_ripple() {
	    if (typeof rptk_apis != "object") {
	        return true;
	    }
	    return (! rptk_apis.ripple_inside());
    }

    var self = this;
    this.fun_probation = function (ptype) {
	    return function () {
	        var checkUrl = "http://service.rippletek.com/Portal/Api/checkBySN";
	        if (self.in_ripple()) {
		        self.redir(checkUrl + "?sn=" + rptk_apis.key() + "&" + ptype + "=true");
	        } else {
		        self.trigger();
	        }
	    };
    }
});

function rptk_login(type) {
    var oauthUrl = "http://service.rippletek.com/Portal/Api/oAuthLogin";
    if (__rptk_login_private.in_ripple()) {
	    __rptk_login_private.redir(
	        oauthUrl + "?type=" + type.toLowerCase() + "&sn=" + rptk_apis.key());
    } else {
	    __rptk_login_private.trigger();
    }
}

function rptk_probation() {
    __rptk_login_private.fun_probation("probation")();
}

function rptk_oneclick() {
    __rptk_login_private.fun_probation("oneclick")();
}

function rptk_weixin_submit(inputId) {
    var token=document.getElementById(inputId).value;
    var to_pattern = /^\d{4}$/;
    if (token == "") {
        return false;
    }else{
        if(! to_pattern.test(token)){
            alert("请正确输入您的4位动态口令后点击\"立即上网\"按钮");
            return false;
        }
    }
    var checkUrl = "http://service.rippletek.com/Portal/Api/checkBySN";
    if (__rptk_login_private.in_ripple()) {
	    __rptk_login_private.redir(
	        checkUrl + "?sn=" + rptk_apis.key() + "&token="+token);
	    return true;
    }
	__rptk_login_private.trigger();
    return false;
}

function rptk_phone_submit(inputId) {
    var phone_no = document.getElementById(inputId).value;
    var ph_pattern = /^1\d{10}$/;
    if (! ph_pattern.test(phone_no)) {
        alert("请正确输入您的11位手机后点击\"立即上网\"按钮");
        return false;
    }
    var checkUrl = "http://service.rippletek.com/Portal/Api/phoneLogin";
    if (__rptk_login_private.in_ripple()) {
	    __rptk_login_private.redir(
	        checkUrl + "?sn=" + rptk_apis.key() + "&phoneNum=" + phone_no);
	    return true;
    }
	__rptk_login_private.trigger();
    return false;
}

function rptk_ios_goto_weixin() {
    function is_ios() {
        var u = window.navigator.userAgent;
        var device_types = ["iPhone", "iPod", "iPad"];
        for (var i in device_types) {
            var dt = device_types[i];
            if (u.indexOf(dt) >= 0) {
                return true;
            }
        }
        return false;
    }
    if (! is_ios()) {
        return false;
    }
    var dev_url = "http://rippletek.lan:3000/rippletek/hide_ios_cp";
    var script = document.createElement("script");
    var wx_url = "http://service.rippletek.com/ext/goto_wx.html";
    script.type = "text/javascript";
    script.src = dev_url + "?rand=" + Math.random();
    document.body.appendChild(script);
    var cnt = 0;
    var f = function () {
        if (typeof rptk_cp_done == "boolean" && rptk_cp_done) {
	        window.location.href = wx_url;
        } else {
            if (cnt < 30) {
                setTimeout(f, 100);
            } else {
                rptk_probation();
            }
        }
        cnt ++;
    };
    f();
}
