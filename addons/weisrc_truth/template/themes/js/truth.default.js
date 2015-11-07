!function(e) {
    function t(e) {
        var t = e instanceof $ ? e: $(e), i = t.is("form") ? t.parent(): t;
        this.$wrapper = i, this.events = {}, this.useAjax=!1, this.ajaxFunc = null, this.init()
    }
    t.prototype = {
        INPUT_SELECTOR: "input[type=text], textarea[name]",
        EDIT_CLS: "edit",
        init: function() {
            this.onCustomerEvents().onFormEvents()
        },
        getVal: function(e) {
            return /radio|checkbox/i.test(e.attr("type")) ? e[0].checked : e.val()
        },
        serialize: function(e) {
            var t = this, i = {};
            return e.find("[name]").each(function() {
                var e = $(this), n = e.attr("name"), o = t.getVal(e);
                i[n] = o
            }), i
        },
        setAjax: function() {
            this.useAjax=!0, this.successCallbackFunc = arguments[0], this.failCallbackFunc = arguments[1]
        },
        submit: function(e) {
            var t = this;
            if (t.fire("submitBefore", e), t.useAjax) {
                var i = e.attr("action"), n = e.attr("method"), o = t.serialize(e), r = t.successCallbackFunc, a = t.failCallbackFunc;
                $.ajax({
                    url: i,
                    type: n,
                    data: o
                }).done(function(t) {
                    r && r(t, e)
                }).fail(function(t) {
                    a && a(t, e)
                }).always(function() {
                    t.fire("submitComplete", e)
                })
            } else 
                e[0].submit()
        },
        onCustomerEvents: function() {
            return this.onReset().onValid().onSubmitBtn(), this
        },
        onFormEvents: function() {
            return this.onSubmit().onInput(), this
        },
        onReset: function() {
            var e = this, t = this.EDIT_CLS, i = this.INPUT_SELECTOR;
            return e.on("reset", function(e) {
                var n = $.Deferred();
                return e[0].reset(), e.removeClass(t), e.find(i).trigger("blur"), setTimeout(function() {
                    n.resolve()
                }, 500), n
            }), this
        },
        onValid: function() {
            var e = ".form-field", t = "form-field-error", i = "invalid";
            return this.on("valid", function(n) {
                for (var o = 0, r = n.length; r > o; o++)
                    n[o].removeClass(i).closest(e).removeClass(t)
            }), this.on("invalid", function(n) {
                for (var o = 0, r = n.length; r > o; o++)
                    n[o].addClass(i).closest(e).addClass(t)
            }), this
        },
        onSubmitBtn: function() {
            return this.on("submitBefore", function(e) {
                var t = e.find("input[type=submit]"), i = t.val();
                t.attr("default-value", i), t.addClass("ing").prop("disabled", !0), function() {
                    var e = 0, n = setInterval(function() {
                        t.prop("disabled") ? (e = 3 == e ? 0 : ++e, t.val(i + "...".slice(0, e))) : clearInterval(n)
                    }, 1e3)
                }()
            }), this.on("submitComplete", function(e) {
                var t = e.find("input[type=submit]");
                t.val(t.attr("default-value")), t.removeClass("ing").prop("disabled", !1)
            }), this
        },
        onSubmit: function() {
            var e = this;
            return e.$wrapper.on("submit", "form", function(t) {
                t.preventDefault();
                var i = $(t.target), n = e.checkInValid(i);
                return n ? (console.log("表单填写错误"), !1) : void e.submit(i)
            }), this
        },
        onInput: function() {
            var e = this.INPUT_SELECTOR, t = this.EDIT_CLS, i = /MSIE|Trident/i.test(navigator.userAgent) ? "keyup": "input", n = this;
            return this.$wrapper.on(i, e, function() {
                var e = $(this), t = (e.attr("name"), n.getVal(e));
                t.length && n.fire("valid", [e])
            }).on("focus", e, function() {
                {
                    var e = $(this), i = e.closest("form");
                    e.attr("name")
                }
                i.addClass(t)
            }).on("blur", e, function() {
                var e = $(this), i = n.getVal(e);
                e[0].blur(), 0 === i.length && e.closest("form").removeClass(t)
            }), this
        },
        checkInValid: function(e) {
            var t = e.find("[empty=no]"), i = [], n = [], o = this;
            return t.each(function() {
                var e = $(this), t = o.getVal(e), r = /.+/g;
                r.test(t) ? n.push(e) : i.push(e)
            }), o.fire("valid", n), o.fire("invalid", i), i.length
        },
        on: function(e, t) {
            this.events[e] = t
        },
        fire: function(e) {
            var t = this.events[e];
            return "function" == typeof t ? t.apply(this, Array.prototype.slice.call(arguments, 1)) : void 0
        }
    }, e.Form = t
}(window, document), function(e, t) {
    return t.browser = {
        mobile: !1,
        ios: !1,
        ipad: !1,
        type: "default",
        app: "无秘",
        supportSchemaLevel: 1
    }, e ? (e = e.toLowerCase(), t.browser.mobile = /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(e) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(e.substr(0, 4)), e.indexOf("os x")>-1 && (t.browser.ios=!0, t.browser.app = "友秘", t.browser.supportSchemaLevel = 2, e.indexOf("ipad")>-1 && (t.browser.ipad=!0)), e.indexOf("micromessenger")>-1 ? (t.browser.type = "wechat", t.browser.supportSchemaLevel = 0) : e.indexOf("weibo")>-1 ? (t.browser.type = "weibo", t.browser.supportSchemaLevel = 0) : e.indexOf("qq")>-1&&-1 === e.indexOf("qqbrowser") ? (t.browser.type = "qq", t.browser.supportSchemaLevel = 1) : e.indexOf("qzone")>-1 && (t.browser.type = "qzone", t.browser.supportSchemaLevel = 2), void(e.indexOf("android")>-1 && t.browser.supportSchemaLevel > 1 && (t.browser.supportSchemaLevel = 1))) : !1
}(navigator.userAgent || navigator.vendor || window.opera, window), function(e, t, i) {
    if (!e.mobile || window.noBanner)
        return !1;
}(window.browser, window.productName, window.cdn), function(e) {
    function t(e) {
        var t = Date.now();
        e = $.extend({
            id: "confirm-" + t,
            "class": "",
            html: "",
            btn: "确认"
        }, e), this.idKey = t, this.options = e, this.run()
    }
    t.prototype = {
        run: function() {
            var e = this.getView();
            this.$el = e
        },
        get: function() {
            return this.$el
        },
        getView: function() {
            var e = this.options, t = this, i = $("#" + e.id);
            return i.length || (i = $("<div></div>").attr({
                id: e.id,
                "class": "confirm " + e.class
            }).html(['<div class="confirm-inner">', '   <div class="confirm-container">', '       <div class="confirm-content">', '           <div class="confirm-content-html j-confirm-content-html">', "           " + e.html, "           </div>", "       </div>", '       <div class="confirm-buttons j-confirm-buttons">', '           <div class="confirm-button-cnt">', '               <a href="javascript:;" class="j-confirm-btn j-confirm-btn-main">' + e.btn + "</a>", "           </div>", "       </div>", "   </div>", "</div>"].join("")), e.btnExt && i.find(".j-confirm-buttons").prepend(['<div class="confirm-button-cnt">', '   <a href="javascript:;" class="j-confirm-btn j-confirm-btn-ext">' + e.btnExt + "</a>", "</div>"].join("")), $("body").append(i)), i.show(), i.off().on("click", ".j-confirm-btn", function() {
                t.hide(), setTimeout(function() {
                    t.destory()
                }, 1e3)
            }), i
        },
        hide: function() {
            this.$el.hide()
        },
        destory: function() {
            this.$el.off().remove()
        }
    }, e.Confirm = t
}(window), function(e, t) {
    function i(e, t) {
        return e.replace(/\{\{(.*?)\}\}/g, function(e, i) {
            return i in t ? t[i] : ""
        })
    }
    t.pushTips = {
        run: function() {
            if ("wechat" !== e.type)
                return !1;
            var t = this;
            this.getData().done(function(e) {
                var i =+ e.unreadCount || 0, n = [];
                if (i ? n = ["unread", i] : e.aboutToExpire && (n = ["expire"]), n.length) {
                    var o = t.getDom.apply(t, n);
                    $("body").append(o), t.listenClose(o)
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
            $.get("/wxapp/tip/close")
        },
        getDom: function(e) {
            var t = $("<div></div>").attr({
                id: "pushTips",
                "class": "push-tips"
            }), n = ['<div class="tips-header">', '   <h3 class="tips-title">{{title}}</h3>', "</div>", '<div class="tips-content">{{content}}</div>', '<div class="tips-buttons">', '   <button class="btn btn-known">知道了</button>', "</div>"].join(""), o = "";
            switch (e) {
            case"unread":
                o = i(n, {
                    title: "未读消息",
                    content: "你有" + arguments[1] + "条未读消息，请在无秘公众号回复“未读消息”查看。"
                });
                break;
            case"expire":
                o = i(n, {
                    title: "温馨提示",
                    content: "<p>由于微信政策限制，即将不能给你推送提醒消息。若想继续收到，请在无秘公众号回复字母'N'</p>"
                })
            }
            return t.html(o), t
        },
        listenClose: function(e) {
            var t = this;
            e.on("click", ".btn-known", function() {
                t.postKnown(), e.off().remove()
            })
        }
    }
}(window.browser, window), function(e) {
    function t(e) {
        var t = document.querySelector('meta[name="wumi-weixin-share-' + e + '"]');
        return t && t.content
    }
    function i(e) {
        var t = document.createElement("link");
        t.rel = "prefetch", t.href = e, document.querySelector("head").appendChild(t)
    }
    function n(e) {
        var t = new Image;
        t.src = e, t.id = "weixin_share_img", document.querySelector("#weixin_share_img") ? document.querySelector("head").replaceChild(t, document.querySelector("#weixin_share_img")) : document.querySelector("head").insertBefore(t, document.querySelector("head").firstChild)
    }
    var o = t("title") || document.title, r = t("img-url") || "/wxapp/images/logo-512.png", a = t("desc") || document.URL, s = t("link") || document.URL, c = {
        title: o,
        img_url: r,
        img_width: "320",
        img_height: "320",
        desc: a,
        link: s
    }, u = function(e) {
        e = e || {}, e.title = e.title || c.title, e.img_url = e.img_url || c.img_url, e.img_width = e.img_width || c.img_width, e.img_height = e.img_height || c.img_height, e.desc = e.desc || c.desc, e.link = e.link || c.link, i(e.img_url), n(e.img_url), document.addEventListener("WeixinJSBridgeReady", function() {
            WeixinJSBridge.call("showOptionMenu"), document.addEventListener("WeixinJSBridgeReady", function() {
                WeixinJSBridge.on("menu:share:appmessage", function() {
                    WeixinJSBridge.invoke("sendAppMessage", e, function() {})
                }), WeixinJSBridge.on("menu:share:timeline", function() {
                    WeixinJSBridge.invoke("shareTimeline", e, function() {})
                })
            }, !1)
        }, !1), "wx"in window && (wx.ready(function() {
            wx.showOptionMenu()
        }), wx.onMenuShareTimeline({
            title : e.title, link : e.link, imgUrl : e.img_url, success : function() {}, cancel: function() {}
        }), wx.onMenuShareAppMessage({
            title: e.title,
            desc: e.desc,
            link: e.link,
            imgUrl: e.img_url,
            type: "link",
            dataUrl: "",
            success: function() {},
            cancel: function() {}
        }))
    };
    u(c), e.setShare = u
}(window);
