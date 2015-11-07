function validateLogin(pageurl) {
    var account = max.$("popup_account").value.trim();
    var password_val = max.$("popup_password").value.trim();
    var code_val = max.$("popup_code").value.trim();
    var remember_val = 0;
    if (max.$("popup_remember").checked) {
        remember_val = 1;
    }
    var md5pass = "";
    var rw = "";
    if (password_val != "") {
        rw = password_val;
        md5pass = hex_md5(password_val);
    }
    ajaxRequest("/WxUser/loginAjax?type=validatelogin&username=" + encodeURIComponent(account) + "&password=" + encodeURIComponent(password_val) + "&rw=" + encodeURIComponent(rw) + "&verify=" + code_val + "&re=" + remember_val, function (content) {
        if (content == "") {
            if (pageurl == "/default.aspx") {
                window.location.href = "/";
            }
            else {
                window.location.href = pageurl;
            }
        }
        else {
            if (content == "账号出现异常|邮箱" || content == "账号出现异常|手机") {
                window.location.href = "/account-validate.aspx?account=" + encodeURIComponent(account);
            }
            else {
                var errArray = new Array();
                errArray = content.split('|');
                if (errArray.length == 2) {
                    max.$("div_code").style.display = errArray[1];
                    var rnd = Math.random();
                    max.$("validatecodeimg").src = "/WxUser/loginVerify?act=init&rnd=" + rnd;
                }
                max.$("err_popup_result").className = "msg-error-block";
                max.$("err_popup_result").innerHTML = errArray[0];
                max.$("popup_password").value = "";
            }
        }
    });
    return false;
}
function keydown(pageurl) {
    if (window.navigator.appName == "Microsoft Internet Explorer") {
        if (document.documentMode && document.documentMode == 8) {
            validateLogin(pageurl);
        }
    }
}
function textChangeColor(field) {
    if (field == "validatecode" || field=="code") {
        max.$(field).className = "text captcha text-input";
    }
    else {
        max.$(field).className = "text text-input";
    }
}
function validate(field) {
    validate(field,null, false,null);
}
function validatePhone() {
    var phone = $("#phone").val();
    ajaxRequest("/ajaxsync.aspx?type=validatephone&val=" + encodeURIComponent(phone), function (content) {
        if (content != "") {
            max.$("getvalidatecode").className = "disable";
            max.$("sendresult").innerHTML = " ";
            max.$("err_phone").innerHTML = content;
        }
        else {
            max.$("getvalidatecode").className = " ";
            max.$("sendresult").innerHTML = " ";
        }
    });
}

function validate(field, st, state, p) {
    if (field == "f_username" && max.$("username").value == "") {
        return;
    }
    else {
        if (field == "f_username") {
            field = "username";
        }
    }
   
    var value = max.$(field).value;
    ajaxRequest("/ajaxsync.aspx?type=validate" + field + "&val=" + encodeURIComponent(value) + "&s=" + state + "&t=" + st + "&p=" + p, function (content) {
        if (content && content != "") {
            if (field == "fb_content") {
                max.$("err_" + field).innerHTML = content;
            }
            else {
                if (field == "code" || field == "codePhone" || field == "verifyCode" || field == "checkCode") {
                    max.$(field).className = "text captcha text-error";
                }
                else if (field == "mobilephone") {
                    if (max.$("sendvalidatecode") != undefined) {
                        max.$("sendvalidatecode").className = "disable";
                        max.$("sendvalidatecode").href = "#";
                    }
                    if (max.$("sendresult") != undefined) {
                        max.$("sendresult").innerHTML = " ";
                    }
                    max.$("err_" + field).innerHTML = content;
                }
                else {
                    max.$(field).className = "text text-error gray";
                }
                max.$("err_" + field).innerHTML = content;
            }
        }
        else {
            if (field == "fb_content") {
                max.$("err_" + field).innerHTML = " ";
            }
            else {
                if (field == "code" || field == "codePhone" || field == "verifyCode" || field == "checkCode") {
                    max.$(field).className = "text captcha";
                }
                else if (field == "mobilephone") {
                    if (max.$("sendvalidatecode") != undefined) {
                        max.$("sendvalidatecode").className = " ";
                        max.$("sendvalidatecode").href = "javascript:sendValidateCode();";
                    }
                    if (max.$("sendresult") != undefined) {
                        max.$("sendresult").innerHTML = " ";
                    }
                    max.$("err_" + field).innerHTML = " ";
                }
                else {
                    max.$(field).className = "text";
                }
                max.$("err_" + field).innerHTML = " ";
            }
        }
    });
}

function validateConfirmPassword(filed) {
    var password = max.$("password").value;
    var cpassword = max.$(filed).value;
    if (password == cpassword) {
        max.$("err_" + filed).innerHTML = " ";
        max.$(filed).className = "text";
    }
    else {
        max.$("err_" + filed).innerHTML = "两次输入的密码不一致";
        max.$(filed).className = "text text-error";
    }
}

function validateMobileCode(filed) {
    var value = max.$(filed).value;
    if (value == "") {
        max.$(filed).className = "text captcha text-error";
        max.$("err_" + filed).innerHTML = "请输入收到6位验证码";
     }
    else {
        max.$(filed).className = "text captcha";
        max.$("err_" + filed).innerHTML = " ";
    }
}
$(function () {

    //清除表单错误提示，文本框获得焦点时
    $("form").find("input[type='text'] ").bind("focus", function () {
        $(this).siblings("span.msg-error").html("");
        $(this).siblings("div.red").html("");
        $(this).parent().siblings("span.msg-error").html("");
    });
    $("form").find("input[type = 'password'] ").bind("focus", function () {
        $(this).siblings("span.msg-error").html("");
        $(this).siblings("div.red").html("");
        $(this).parent().siblings("span.msg-error").html("");
    });
    ///表单，若有错误提示将阻止其提交表单
    $("form").bind("submit", function () {
        var errorcount = 0;
        var errorContainers = $(this).find("span.msg-error");
        for (var i = 0, count = errorContainers.length; i < count; i++) {
            if ($.trim(errorContainers.eq(i).html()) == "") {
                continue;
            }
            else {
                errorcount++;
                break;
            }
        }
        var errorDivContainers = $(this).find("div.red");
        for (var i = 0, count = errorContainers.length; i < count; i++) {
            if ($.trim(errorDivContainers.eq(i).html()) == "") {
                continue;
            }
            else {
                errorcount++;
                break;
            }
        }
        if (errorcount == 0) {
            return true;
        }
        else {
            return false;
        }
    });
});