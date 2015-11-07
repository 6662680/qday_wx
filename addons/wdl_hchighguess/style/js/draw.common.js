!
function(t) {
    function e(t) {
        var e = t instanceof $ ? t: $(t),
        n = e.is("form") ? e.parent() : e;
        this.$wrapper = n,
        this.events = {},
        this.useAjax = !1,
        this.ajaxFunc = null,
        this.init()
    }
    e.prototype = {
        INPUT_SELECTOR: "input[type=text], textarea[name]",
        EDIT_CLS: "edit",
        init: function() {
            this.onCustomerEvents().onFormEvents()
        },
        getVal: function(t) {
            return /radio|checkbox/i.test(t.attr("type")) ? t[0].checked: t.val()
        },
        serialize: function(t) {
            var e = this,
            n = {};
            return t.find("[name]").each(function() {
                var t = $(this),
                i = t.attr("name"),
                o = e.getVal(t);
                n[i] = o
            }),
            n
        },
        setAjax: function() {
            this.useAjax = !0,
            this.successCallbackFunc = arguments[0],
            this.failCallbackFunc = arguments[1]
        },
        submit: function(t) {
            var e = this;
            if (e.fire("submitBefore", t), e.useAjax) {
                var n = t.attr("action"),
                i = t.attr("method"),
                o = e.serialize(t),
                a = e.successCallbackFunc,
                r = e.failCallbackFunc;
                $.ajax({
                    url: n,
                    type: i,
                    data: o
                }).done(function(e) {
                    a && a(e, t)
                }).fail(function(e) {
                    r && r(e, t)
                }).always(function() {
                    e.fire("submitComplete", t)
                })
            } else t[0].submit()
        },
        onCustomerEvents: function() {
            return this.onReset().onValid().onSubmitBtn(),
            this
        },
        onFormEvents: function() {
            return this.onSubmit().onInput(),
            this
        },
        onReset: function() {
            var t = this,
            e = this.EDIT_CLS,
            n = this.INPUT_SELECTOR;
            return t.on("reset",
            function(t) {
                var i = $.Deferred();
                return t[0].reset(),
                t.removeClass(e),
                t.find(n).trigger("blur"),
                setTimeout(function() {
                    i.resolve()
                },
                500),
                i
            }),
            this
        },
        onValid: function() {
            var t = ".form-field",
            e = "form-field-error",
            n = "invalid";
            return this.on("valid",
            function(i) {
                for (var o = 0,
                a = i.length; a > o; o++) i[o].removeClass(n).closest(t).removeClass(e)
            }),
            this.on("invalid",
            function(i) {
                for (var o = 0,
                a = i.length; a > o; o++) i[o].addClass(n).closest(t).addClass(e)
            }),
            this
        },
        onSubmitBtn: function() {
            return this.on("submitBefore",
            function(t) {
                var e = t.find("input[type=submit]"),
                n = e.val();
                e.attr("default-value", n),
                e.addClass("ing").prop("disabled", !0),
                function() {
                    var t = 0,
                    i = setInterval(function() {
                        e.prop("disabled") ? (t = 3 == t ? 0 : ++t, e.val(n + "...".slice(0, t))) : clearInterval(i)
                    },
                    1e3)
                } ()
            }),
            this.on("submitComplete",
            function(t) {
                var e = t.find("input[type=submit]");
                e.val(e.attr("default-value")),
                e.removeClass("ing").prop("disabled", !1)
            }),
            this
        },
        onSubmit: function() {
            var t = this;
            return t.$wrapper.on("submit", "form",
            function(e) {
                e.preventDefault();
                var n = $(e.target),
                i = t.checkInValid(n);
                return i ? (console.log("表单填写错误"), !1) : void t.submit(n)
            }),
            this
        },
        onInput: function() {
            var t = this.INPUT_SELECTOR,
            e = this.EDIT_CLS,
            n = /MSIE|Trident/i.test(navigator.userAgent) ? "keyup": "input",
            i = this;
            return this.$wrapper.on(n, t,
            function() {
                var t = $(this),
                e = (t.attr("name"), i.getVal(t));
                e.length && i.fire("valid", [t])
            }).on("focus", t,
            function() {
                {
                    var t = $(this),
                    n = t.closest("form");
                    t.attr("name")
                }
                n.addClass(e)
            }).on("blur", t,
            function() {
                var t = $(this),
                n = i.getVal(t);
                t[0].blur(),
                0 === n.length && t.closest("form").removeClass(e)
            }),
            this
        },
        checkInValid: function(t) {
            var e = t.find("[empty=no]"),
            n = [],
            i = [],
            o = this;
            return e.each(function() {
                var t = $(this),
                e = o.getVal(t),
                a = /.+/g;
                a.test(e) ? i.push(t) : n.push(t)
            }),
            o.fire("valid", i),
            o.fire("invalid", n),
            n.length
        },
        on: function(t, e) {
            this.events[t] = e
        },
        fire: function(t) {
            var e = this.events[t];
            return "function" == typeof e ? e.apply(this, Array.prototype.slice.call(arguments, 1)) : void 0
        }
    },
    t.Form = e
} (window, document),
function(t, e) {
    return e.browser = {
        mobile: !1,
        ios: !1,
        ipad: !1,
        type: "default",
        app: "无秘",
        supportSchemaLevel: 1
    },
    t ? (t = t.toLowerCase(), e.browser.mobile = /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(t) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(t.substr(0, 4)), t.indexOf("os x") > -1 && (e.browser.ios = !0, e.browser.app = "友秘", e.browser.supportSchemaLevel = 2, t.indexOf("ipad") > -1 && (e.browser.ipad = !0)), t.indexOf("micromessenger") > -1 ? (e.browser.type = "wechat", e.browser.supportSchemaLevel = 0) : t.indexOf("weibo") > -1 ? (e.browser.type = "weibo", e.browser.supportSchemaLevel = 0) : t.indexOf("qq") > -1 ? (e.browser.type = "qq", e.browser.supportSchemaLevel = 1) : t.indexOf("qzone") > -1 && (e.browser.type = "qzone", e.browser.supportSchemaLevel = 2), void(t.indexOf("android") > -1 && e.browser.supportSchemaLevel > 1 && (e.browser.supportSchemaLevel = 1))) : !1
} (navigator.userAgent || navigator.vendor || window.opera, window),
function(t, e, n) {
    if (!t.mobile || window.noBanner) return ! 1;
    var i = $("<div></div>").addClass("app-download").html(["", "<a onclick=\"ga('send', 'event', 'link', 'click', '" + e + ' banner\')" href="https://www.wumii.org/download?f=wechat&utm_source=weixin&utm_medium=wxapp&utm_campaign=' + e + '&utm_content=banner">', '   <div class="app-download-banner"><img src="' + n + '/images/banner_v2.png"></div>', '   <div class="app-download-btn"><img src="' + n + '/images/banner_btn.png"></div>', "</a>"].join(""));
    $("body").css({
        "padding-bottom": 65
    }).append(i).on("focus", "textarea, input[type=text]",
    function() {
        $(".app-download").hide()
    }).on("blur", "textarea, input[type=text]",
    function() {
        $(".app-download").show()
    })
} (window.browser, window.productName, window.cdn),
function(t) {
    function e(t) {
        var e = Date.now();
        t = $.extend({
            id: "confirm-" + e,
            "class": "",
            html: "",
            btn: "确认"
        },
        t),
        this.idKey = e,
        this.options = t,
        this.run()
    }
    e.prototype = {
        run: function() {
            var t = this.getView();
            this.$el = t
        },
        get: function() {
            return this.$el
        },
        getView: function() {
            var t = this.options,
            e = this,
            n = $("#" + t.id);
            return n.length || (n = $("<div></div>").attr({
                id: t.id,
                "class": "confirm " + t.class
            }).html(['<div class="confirm-inner">', '   <div class="confirm-container">', '       <div class="confirm-content">', '           <div class="confirm-content-html j-confirm-content-html">', "           " + t.html, "           </div>", "       </div>", '       <div class="confirm-buttons j-confirm-buttons">', '           <div class="confirm-button-cnt">', '               <a href="javascript:;" class="j-confirm-btn j-confirm-btn-main">' + t.btn + "</a>", "           </div>", "       </div>", "   </div>", "</div>"].join("")), t.btnExt && n.find(".j-confirm-buttons").prepend(['<div class="confirm-button-cnt">', '   <a href="javascript:;" class="j-confirm-btn j-confirm-btn-ext">' + t.btnExt + "</a>", "</div>"].join("")), $("body").append(n)),
            n.show(),
            n.off().on("click", ".j-confirm-btn",
            function() {
                e.hide(),
                setTimeout(function() {
                    e.destory()
                },
                1e3)
            }),
            n
        },
        hide: function() {
            this.$el.hide()
        },
        destory: function() {
            this.$el.off().remove()
        }
    },
    t.Confirm = e
} (window),
function(t) { !
    function(t, e, n, i, o, a, r) {
        t.GoogleAnalyticsObject = o,
        t[o] = t[o] ||
        function() { (t[o].q = t[o].q || []).push(arguments)
        },
        t[o].l = 1 * new Date,
        a = e.createElement(n),
        r = e.getElementsByTagName(n)[0],
        a.async = 1,
        a.src = i,
        r.parentNode.insertBefore(a, r)
    } (window, document, "script", "//www.google-analytics.com/analytics.js", "ga"),
    t = t.slice(1, -1);
    for (var e = t.split(","), n = 0, i = e.length; i > n; n++) if (location.hostname.indexOf(e[n]) > -1) {
        e.splice(n, 1);
        break
    }
    ga("create", "UA-51909929-1", "auto", {
        allowLinker: !0
    }),
    ga("require", "linker"),
    ga("linker:autoLink", e),
    ga("send", "pageview")
} (hostList),
function(t, e) {
    function n(t, e) {
        return t.replace(/\{\{(.*?)\}\}/g,
        function(t, n) {
            return n in e ? e[n] : ""
        })
    }
    e.pushTips = {
        run: function() {
            if ("wechat" !== t.type) return ! 1;
            var e = this;
            this.getData().done(function(t) {
                var n = +t.unreadCount || 0,
                i = [];
                if (n ? i = ["unread", n] : t.aboutToExpire && (i = ["expire"]), i.length) {
                    var o = e.getDom.apply(e, i);
                    $("body").append(o),
                    e.listenClose(o)
                }
            })
        },
        getData: function() {
            return $.ajax({
                url: "/wxapp/tip",
                type: "GET",
                dataType: "json",
                cache: !1
            })
        },
        postKnown: function() {
            $.post("/wxapp/tip/close")
        },
        getDom: function(t) {
            var e = $("<div></div>").attr({
                id: "pushTips",
                "class": "push-tips"
            }),
            i = ['<div class="tips-header">', '   <h3 class="tips-title">{{title}}</h3>', "</div>", '<div class="tips-content">{{content}}</div>', '<div class="tips-buttons">', '   <button class="btn btn-known">知道了</button>', "</div>"].join(""),
            o = "";
            switch (t) {
            case "unread":
                o = n(i, {
                    title: "未读消息",
                    content: "你有" + arguments[1] + "条未读消息，请在无秘公众号回复“未读消息”查看。"
                });
                break;
            case "expire":
                o = n(i, {
                    title: "温馨提示",
                    content: "<p>由于微信政策限制，即将不能给你推送提醒消息。若想继续收到，请在无秘公众号回复字母'N'</p>"
                })
            }
            return e.html(o),
            e
        },
        listenClose: function(t) {
            var e = this;
            t.on("click", ".btn-known",
            function() {
                e.postKnown(),
                t.off().remove()
            })
        }
    }
} (window.browser, window),
function(t, e) {
    e.wxJsBridge = function(e, n, i) {
        switch (arguments.length) {
        case 1:
            n = e;
            break;
        case 2:
            "function" == typeof n && (i = n)
        }
        "wechat" === t.type && document.addEventListener("WeixinJSBridgeReady",
        function() {
            WeixinJSBridge.call("showOptionMenu"),
            WeixinJSBridge.on("menu:share:appmessage",
            function() {
                WeixinJSBridge.invoke("sendAppMessage", e,
                function(t) {
                    i && i(t)
                })
            }),
            WeixinJSBridge.on("menu:share:timeline",
            function() {
                WeixinJSBridge.invoke("shareTimeline", n,
                function(t) {
                    i && i(t)
                })
            })
        },
        !1)
    }
} (window.browser, window),
function() {
    var t = window.shareData || {};
    t.img_url = t.img_url || "../images/app.jpg",
    t.desc = t.desc || " ",
    window.wxJsBridge({
        img_url: t.img_url,
        img_width: "200",
        img_height: "200",
        link: location.href,
        desc: t.desc,
        title: "据说我有独特的画风，快来猜猜我画的是神马！"
    },
    {
        img_url: t.img_url,
        img_width: "200",
        img_height: "200",
        link: location.href,
        desc: " ",
        title: "据说我有独特的画风，快来猜猜我画的是神马！"
    })
} ();