define("http://c.h5.lietou-static.com/v1/js/base.js", [ "http://core.h5.lietou-static.com/v1/zepto/zepto.js", "http://core.h5.lietou-static.com/v1/dialogs/mask.js", "http://core.h5.lietou-static.com/v1/public/cookie.js" ], function(require, exports) {
    $ = require("http://core.h5.lietou-static.com/v1/zepto/zepto.js");
    var mask = require("http://core.h5.lietou-static.com/v1/dialogs/mask.js");
    var cookie = require("http://core.h5.lietou-static.com/v1/public/cookie.js");
    var $nav = $("#mod-nav");
    var $modBody = $(".mod-body");
    var $profileInfo = $(".mod-profile");
    var $body = $(document.body);
    var $main = $(".mod-body>.main");
    var $navProfile = $(".nav-profile", $nav[0]);
    var $html = $(document.documentElement);
    var $doc = $(document);
    var $businessBtn = $(".page-enterprise_new .btn");
    var isLogin = cookie.isLogin();
    var ele = $(".mask");
    var sure = $(".iknow");
    var username = "";
    var useravatar = "";
    var firstShowProfile = true;
    var maskLayer = new mask({
        el: $main,
        css: {
            background: "rgba(0,0,0,0)"
        },
        click: function() {
            profileHide();
        }
    });
    maskLayer.hide();
    function popup(ele, sure) {
        var height = $(window).height(), popup = ele.find(".popup").height(), top = (height - popup) / 2;
        ele.find(".popup").css({
            top: top
        });
        ele.find(sure).on("click", function() {
            ele.hide();
        });
    }
    popup(ele, sure);
    if (isLogin) {
        var enReg = /^([A-Za-z]+\s?)*[A-Za-z]$/;
        var getName = cookie.get("user_name");
        var len = getName.length;
        if (len > 1 && getName !== '""') {
            if (enReg.test(getName)) {
                $navProfile.removeClass("icon");
            }
            $navProfile.show().text(getName);
            if (len > 5) {
                len = 5;
                $navProfile.show().text(getName);
            }
            $navProfile.addClass("isLogin");
        } else {
            $navProfile.show().text("C");
        }
        $(".mod-profile .icon-group:last").click(function() {
            $.ajax({
                url: "http://m.liepin.com/user/logout/",
                success: function(res) {
                    res = JSON.parse(res);
                    var href = res.return_url;
                    if (location.hostname.indexOf("sns-m") != -1) {
                        location.href = "http://m.liepin.com";
                    } else if (res.return_url) {
                        location.href = href;
                    } else {
                        alert("退出失败,请稍后重试");
                    }
                }
            });
            return false;
        });
    } else {
        $navProfile.show().text("C");
    }
    var profileShowed = false;
    function profileHide() {
        maskLayer.hide();
        $modBody.css("right", "0");
        $profileInfo.css({
            right: "-211px"
        });
        $businessBtn.css({
            left: "0",
            "-webkit-transition": "left .5s ease"
        });
        setTimeout(function() {
            $profileInfo.css({
                display: "none"
            });
            $(".page-enterprise_new .btn").show();
            $("#jobPost .submitResume").show();
        }, 510);
        profileShowed = false;
    }
    function profileShow() {
        stat("profileshow");
        if (firstShowProfile && isLogin) {
            $(".mod-profile .icon:last").css({
                color: "#f00"
            });
            $(".mod-profile .text:last").css({
                color: "#dcdcdc"
            });
            $.ajax({
                url: "http://m.liepin.com/user/getuserinfo/",
                dataType: "json",
                success: function(data) {
                    if (data.flag == 1) {
                        username = data.username;
                        useravatar = data.photo;
                        var html = '<span class="icon-group">								<a href="http://m.liepin.com/profile/home/?sfrom=m-modprofile-0">									<img class="avatar" src="' + useravatar + '">								</a>								<div class="text">' + username + "</div>							</span>";
                        var $title = $(".title", $profileInfo[0]);
                        $title.html(html);
                    }
                }
            });
        }
        firstShowProfile = false;
        maskLayer.show();
        $nav.css({
            position: "static"
        });
        $profileInfo.css({
            display: "block"
        });
        if (!$profileInfo.length) {
            return;
        }
        $modBody.css("right", "211px");
        $businessBtn.css({
            left: "-211px",
            "-webkit-transition": "left .5s ease"
        });
        $profileInfo.css({
            right: "0"
        });
        profileShowed = true;
        $(".page-enterprise_new .btn").hide();
        $("#jobPost .submitResume").hide();
    }
    $(".mod-profile .icons").delegate(".icon-group", "click", function(e) {
        e.preventDefault();
        var link = $(this).attr("href");
        if (link == "javascript:;") {
            return;
        }
        link += link.indexOf("?") > 0 ? "&" : "?";
        link += "sfrom=modprofile";
        var index = $(".mod-profile .icon-group").indexOf(this);
        var slink = link + "-" + index;
        location.href = slink;
    });
    $navProfile.on("click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        if (profileShowed) {
            profileHide();
        } else {
            profileShow();
        }
    });
    var swipeNoList = {
        "/login.jsp": 1,
        "/register.jsp": 1
    };
    if (!(location.pathname in swipeNoList)) {
        $main.on("swipeRight", function() {
            profileHide();
        });
    }
    (function() {
        $navback = $(".nav-back");
        if ($navback.length) {
            var icon;
            if (typeof pageconfig !== "undefined") {
                icon = pageconfig.navleft;
            }
            if (icon == "noicon") {
                $navback.hide();
                return;
            }
            if (!document.referrer || icon == "home") {
                if (location.pathname != "/") {
                    $navback.html('<a href="http://m.liepin.com/"><span class="icon">9</span></a>').show();
                }
            } else {
                if (icon == "noback") {
                    $navback.hide();
                    return;
                }
                $navback.on("click", function() {
                    if (icon == "nogoback") {
                        return;
                    } else {
                        window.history.go(-1);
                    }
                }).show();
            }
        }
    })();
    var $foot = $("#mod-foot");
    if ($foot.length) {
        var goTopTimer;
        $(".gotop").click(function() {
            if (!document.body.scrollTop) return;
            goTopTimer = setInterval(function() {
                if (document.body.scrollTop > 0) {
                    document.body.scrollTop = document.body.scrollTop - 150;
                } else {
                    if (goTopTimer) {
                        clearInterval(goTopTimer);
                        goTopTimer = null;
                    }
                }
            }, 50);
        });
        $foot.css({
            visibility: "visible"
        });
    }
    var windowHeight = document.documentElement.clientHeight;
    if ($foot.length && $nav.length) {
        $main.css({
            "min-height": windowHeight - $nav[0].offsetHeight - $foot[0].offsetHeight - parseInt($foot.css("margin-top"), 10) + "px"
        });
    } else if ($nav.length) {
        $main.css({
            "min-height": windowHeight - $nav[0].offsetHeight + "px"
        });
    } else if ($main) {
        $main.css({
            "min-height": windowHeight - parseInt($main.css("padding-bottom"), 10) - parseInt($main.css("padding-top"), 10) + "px"
        });
    }
    $modBody.delegate(".util-select", "change", function() {
        var $this = $(this);
        var html = "";
        $("option", $this[0]).each(function(i, v) {
            if (v.selected) {
                html = $(v).html();
            }
        });
        $this.prev("span").children(".select-result").html(html);
    });
    $modBody.delegate(".util-date-selector", "click", function(e) {
        var $self = $(this);
        var val = $self.find('input[type="hidden"]').val();
        var notonow = $self.attr("notonow");
        function pad(num) {
            return (num + "").length < 2 ? "0" + num : num;
        }
        require.async("lib/multiselector/multiselector", function(selector) {
            if (document.activeElement) {
                try {
                    document.activeElement.blur();
                } catch (e) {}
            }
            new selector({
                val: val,
                notonow: notonow,
                complete: function(obj) {
                    var val = obj.year + "-" + pad(obj.month);
                    $self.find(".select-result").html(val);
                    $self.find('input[type="hidden"]').val(val).trigger("blur");
                },
                tonow: function(obj) {
                    var val = obj.year + "-" + pad(obj.month);
                    $self.find(".select-result").html("至今");
                    var now = $self.find(".text").text();
                    if (now == "End Date") {
                        $self.find(".select-result").html("Now");
                    }
                    $self.find('input[type="hidden"]').val(val).trigger("blur");
                }
            });
        });
    });
});