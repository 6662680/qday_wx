function getCanvasCover() {
    function t() {
        i.save(), i.fillStyle = "#b4b3b2", i.fillRect(0, 0, a, s), i.restore()
    }
    function e(t, e, n) {
        t.save(), t.lineWidth = 3, t.strokeStyle = "#d5d5d5", t.save(), t.translate(e, n), t.transform(1, -.1, -.1, 1, 0, 0), t.fillStyle = "#d5d5d5", t.font = '14px "Microsoft YaHei"', t.fillText("刮", -7, 5), t.restore(), t.beginPath(), t.arc(e, n, 16, 0, 2 * Math.PI), t.stroke(), t.restore()
    }
    var n = document.createElement("canvas"),
        i = n.getContext("2d"),
        a = n.width,
        s = n.height;
    return t(), function () {
        var t, n;
        for (t = -1; 8 > t; t++) for (n = -1; 5 > n; n++) e(i, 45 * t + 16 - 4 * n, 45 * n + 16 + 3 * t)
    }(), n.toDataURL()
}!
function () {
    function t(t, e) {
        return this.sp = null, this.settings = t, this.$elem = e, this.enabled = !0, this.scratch = !1, this.canvas = null, this.ctx = null, this
    }
    $.fn.wScratchPad = function (e, n) {
        if ("object" == typeof e) n = e;
        else if ("string" == typeof e) {
            var i = [],
                a = this.each(function () {
                    var t = $(this).data("_wScratchPad");
                    t && ("reset" === e ? t.reset() : "clear" === e ? t.clear() : "enabled" === e ? t.enabled = n === !0 : void 0 !== $.fn.wScratchPad.defaultSettings[e] && (void 0 !== n ? t.settings[e] = n : i.push(t.settings[e])))
                });
            return 1 === i.length ? i[0] : i.length > 0 ? i : a
        }
        return n = $.extend({}, $.fn.wScratchPad.defaultSettings, n || {}), this.each(function () {
            var e = $(this),
                i = $.extend(!0, {}, n),
                a = document.createElement("canvas");
            if (!a.getContext) return e.html("Browser does not support HTML5 canvas, please upgrade to a more modern browser."), !1;
            var s = new t(i, e);
            e.append(s.generate()), s.pixels = s.canvas.width * s.canvas.height, e.data("_wScratchPad", s), s.init()
        })
    }, $.fn.wScratchPad.defaultSettings = {
        width: 210,
        height: 100,
        backImage: null,
        frontImage: null,
        color: "#336699",
        overlay: "none",
        size: 10,
        scratchDown: null,
        scratchUp: null,
        scratchMove: null,
        cursor: null
    }, t.prototype = {
        backingScale: function (t) {
            return "devicePixelRatio" in window && window.devicePixelRatio > 1 && t.webkitBackingStorePixelRatio < 2 ? window.devicePixelRatio : 1
        },
        generate: function () {
            var t = this;
            return this.canvas = document.createElement("canvas"), this.ctx = this.canvas.getContext("2d"), this.sp = $("<div></div>").css({
                position: "relative"
            }).append($(this.canvas).attr("width", this.settings.width + "px").attr("height", this.settings.height + "px")), $(this.canvas).mousedown(function (e) {
                return t.enabled ? (e.preventDefault(), e.stopPropagation(), t.canvas_offset = $(t.canvas).offset(), t.scratch = !0, void t.scratchFunc(e, t, "Down")) : !0
            }).mousemove(function (e) {
                e.preventDefault(), e.stopPropagation(), t.scratch && t.scratchFunc(e, t, "Move")
            }).mouseup(function (e) {
                e.preventDefault(), e.stopPropagation(), t.scratch && (t.scratch = !1, t.scratchFunc(e, t, "Up"))
            }), this.bindMobile(this.sp), this.sp
        },
        bindMobile: function (t) {
            t.bind("touchstart touchmove touchend touchcancel", function () {
                var t = event.changedTouches,
                    e = t[0],
                    n = "";
                switch (event.type) {
                case "touchstart":
                    n = "mousedown";
                    break;
                case "touchmove":
                    n = "mousemove";
                    break;
                case "touchend":
                    n = "mouseup";
                    break;
                default:
                    return
                }
                var i = document.createEvent("MouseEvent");
                i.initMouseEvent(n, !0, !0, window, 1, e.screenX, e.screenY, e.clientX, e.clientY, !1, !1, !1, !1, 0, null), e.target.dispatchEvent(i), event.preventDefault()
            })
        },
        init: function () {
            this.sp.css("width", this.settings.width), this.sp.css("height", this.settings.height), this.sp.css("cursor", this.settings.cursor ? 'url("' + this.settings.cursor + '"), default' : "default"), $(this.canvas).css({
                cursor: this.settings.cursor ? 'url("' + this.settings.cursor + '"), default' : "default"
            }), this.canvas.width = this.settings.width, this.canvas.height = this.settings.height, this.pixels = this.canvas.width * this.canvas.height, this.settings.frontImage ? this.drawImage(this.settings.frontImage) : ("none" != this.settings.overlay ? (this.settings.backImage && this.drawImage(this.settings.backImage), this.ctx.globalCompositeOperation = this.settings.overlay) : this.setBgImage(), this.ctx.fillStyle = this.settings.color, this.ctx.beginPath(), this.ctx.rect(0, 0, this.settings.width, this.settings.height), this.ctx.fill()), this._fixBug()
        },
        _fixBug: function () {
            $(this.canvas).css("margin-right", "0px" == $(this.canvas).css("margin-right") ? "1px" : "0px")
        },
        reset: function () {
            this.ctx.globalCompositeOperation = "source-over", this.init()
        },
        clear: function () {
            this.ctx.clearRect(0, 0, this.settings.width, this.settings.height)
        },
        setBgImage: function () {
            this.settings.backImage && this.sp.css({
                backgroundImage: "url(" + this.settings.backImage + ")"
            })
        },
        drawImage: function (t) {
            var e = this,
                n = new Image;
            n.src = t, $(n).bind("load", function () {
                e.ctx.drawImage(n, 0, 0, e.settings.width, e.settings.height), e.setBgImage()
            })
        },
        scratchFunc: function (t, e, n) {
            t.pageX = Math.floor(t.pageX - e.canvas_offset.left), t.pageY = Math.floor(t.pageY - e.canvas_offset.top), e["scratch" + n](t, e), e.settings["scratch" + n] && e.settings["scratch" + n].apply(e, [t, e.scratchPercentage(e)])
        },
        scratchPercentage: function (t) {
            for (var e = 0, n = t.ctx.getImageData(0, 0, t.canvas.width, t.canvas.height), i = 0, a = n.data.length; a > i; i += 4) 0 == n.data[i] && 0 == n.data[i + 1] && 0 == n.data[i + 2] && 0 == n.data[i + 3] && e++;
            return e / t.pixels * 100
        },
        scratchDown: function (t, e) {
            e.ctx.globalCompositeOperation = "destination-out", e.ctx.lineJoin = "round", e.ctx.lineCap = "round", e.ctx.strokeStyle = e.settings.color, e.ctx.lineWidth = e.settings.size, e.ctx.beginPath(), e.ctx.arc(t.pageX, t.pageY, e.settings.size / 2, 0, 2 * Math.PI, !0), e.ctx.closePath(), e.ctx.fill(), e.ctx.beginPath(), e.ctx.moveTo(t.pageX, t.pageY), this._fixBug()
        },
        scratchMove: function (t, e) {
            e.ctx.lineTo(t.pageX, t.pageY), e.ctx.stroke(), this._fixBug()
        },
        scratchUp: function (t, e) {
            e.ctx.closePath(), this._fixBug()
        }
    }
}();
var appUtils = {};
appUtils.preset = {
    errorModal: {
        content: {
            html: "糟糕，网络不给力"
        },
        confirm: {
            html: "重新进入",
            click: function () {
                window.location.reload()
            }
        },
        cancel: "remove"
    }
}, appUtils.modal = function () {
    function t() {
        i && i.remove(), i = $($("#apps-modal-tpl").html()), a = {};
        for (var t in c) a[t] = i.find(".js-apps-modal-" + t)
    }
    function e(e) {
        t();
        var n, i, s, c;
        for (n in e) if ("string" != typeof e[n]) for (i in e[n]) s = a[n][i], $.isFunction(s) && s.call(a[n], e[n][i]);
        else c = e[n], "remove" == c && a[n].remove()
    }
    function n(t) {
        return $.extend(!0, {}, c, t)
    }
    var i, a, s = {
        open: function (t) {
            e(n(t)), $(document.body).append(i)
        },
        close: function (t) {
            t === !0 ? i.find(".apps-modal").remove() : i.remove()
        }
    },
        c = {
            content: {
                html: ""
            },
            confirm: {
                html: "确定",
                click: $.noop
            },
            cancel: {
                html: "取消",
                click: s.close
            }
        };
    return s
}(), appUtils.process = function () {
    function t(t) {
        c.cancel.html = t, o.cancel.html = t
    }
    function e() {
        return 0 !== a ? 10999 == a ? (s.open(c), !1) : 10998 == a ? (s.open(o), !1) : (s.open(l), !1) : 0 !== i.costPoint && void 0 != i.costPoint ? (s.open(r), !1) : !0
    }
    var n, i = _apps_global,
        a = i.errorCode,
        s = appUtils.modal,
        c = {
            content: {
                html: i.errorMsg
            },
            confirm: {
                html: "关注平台",
                click: function () {
                    location.href = i.subscribe
                }
            },
            cancel: {
                html: "取消抽奖",
                click: function () {
                    s.close()
                }
            }
        },
        o = {
            content: {
                html: i.errorMsg
            },
            confirm: {
                html: "关注",
                click: function () {
                    window.showGuide && window.showGuide("follow")
                }
            },
            cancel: {
                html: "取消抽奖",
                click: function () {
                    s.close()
                }
            }
        },
        r = {
            content: {
                html: '每次抽奖将消耗<span class="important"> ' + i.costName + ':' + i.costPoint + "</span>"
            },
            confirm: {
                html: "赌一把",
                click: function () {
                    s.close(), n.onconfirm && n.onconfirm()
                }
            },
            cancel: {
                html: "舍不得",
                click: function () {
                    s.close()
                }
            }
        },
        l = {
            content: {
                html: i.errorMsg
            },
            confirm: {
                html: "知道了",
                click: function () {
                    s.close()
                }
            },
            cancel: "remove"
        };
    return n = {
        check: e,
        setCancelText: t,
        onconfirm: $.noop
    }
}(), appUtils.atLeast = function (t, e) {
    function n() {
        s.resolve.apply(null, arguments)
    }
    var i, a = !1,
        s = {};
    return setTimeout(function () {
        a ? e.apply(null, i) : s.resolve = function () {
            e.apply(null, arguments)
        }
    }, t), s.resolve = function () {
        i = arguments, a = !0
    }, {
        resolve: n
    }
}, appUtils.randInt = function (t, e) {
    var n = t + Math.random() * (e - t);
    return parseInt(n)
}, appUtils.format = function (t) {
    var e = Array.prototype.slice.call(arguments, 1);
    return t.replace(/{(\d+)}/g, function (t, n) {
        return "undefined" != typeof e[n] ? e[n] : t
    })
}, appUtils.getUrlParam = function (t, e) {
    var n = new RegExp("(^|&)" + t + "=([^&]*)(&|$)"),
        i = "router" === e ? window.location.href : window.location.search,
        a = i.substr(1).match(n);
    return null !== a ? window.unescape(a[2]) : null
}, function (t, e, n) {
    var i = _apps_global,
        a = {
            content: {
                html: ""
            },
            confirm: {
                html: "我知道了",
                click: appUtils.modal.close
            },
            cancel: "remove"
        },
        s = function () {
            function t(t) {
                var n = e.extend({}, f, {
                    scratchDown: p,
                    scratchMove: d,
                    scratchUp: s,
                    frontImage: getCanvasCover()
                });
                h = t.wScratchPad(n)
            }
            function s(t, e) {
                u.load && e > 10 && g()
            }
            function c() {
                var t = e(".result-area"),
                    n = t.find(".result-title"),
                    i = t.find(".result-content");
                return {
                    set: function (t, e) {
                        n.html(t), i.html(e)
                    },
                    change: function (e) {
                        t.addClass(e)
                    }
                }
            }
            function o() {
                var t = i.alias;
                e.ajax({
                    url: i.logout,
                    data: {
                         id: i.id
                    },
                    cache: !1,
                    type: "post",
                    dataType: "json",
                    timeout: 5e3
                }).done(function (t) {
                    var i, a, s = "";
                    u.load = !0, 0 === t.code ? (i = t.data, i.point != n && (s = i.point_name +":" +i.point), i.title != n && (s = i.type + ":" + i.value + i.remsg), a = ["一等奖", "二等奖", "三等奖", "普通奖"][i.level - 1], l.change("suc"), l.set("恭喜您，" + a, s), u.suc = !0, u.givePoint = i.give_point,u.giveName = i.give_name, i.detail_url && i.detail_url.length > 0 && e(".js-view-prize").attr("href", i.detail_url)) : (l.change("fail"), l.set("真遗憾，未中奖", f.failedInfo), u.suc = !1)
                }).fail(function () {
                    l.set("", "正在努力刮"), appUtils.modal.open(appUtils.preset.errorModal)
                })
            }
            var r, l, h, u = {
                load: !1,
                suc: null,
                givePoint: i.givePoint,
                giveName: i.giveName
            },
                f = {
                    successInfo: "恭喜您，中奖啦！",
                    failedInfo: "" === i.failedInfo ? "哎呀，肯定姿势不对！" : i.failedInfo,
                    width: 260,
                    height: 132,
                    size: 20
                },
                p = function () {
                    var t = !0;
                    return function () {
                        t && (t = !1, o())
                    }
                }(),
                d = function (t, e) {
                    var n = !0;
                    return function () {
                        u.load && n && e > 70 && (h.wScratchPad("clear"), n = !1)
                    }
                }(),
                g = function () {
                    var t = !1;
                    return function () {
                        if (!t) {
                            t = !0;
                            var i = u.givePoint;
                            if (i != n && i > 0) {
                                var s = "";
                                s += u.suc ? "手气真棒，再送您" : "哎呀，大奖和您擦身而过！<br>送您", s += '<span class="give-point">' + u.giveName + ':' + i + "</span>", a.content.html = s, appUtils.modal.open(a)
                            }
                            e(".opt-area").slideDown()
                        }
                    }
                }(),
                v = !1;
            return r = {
                init: function () {
                    v || (l = c(), t(e(".scratch-area")), l.set("", "正在努力刮"))
                }
            }
        }();
    t.apps_card = s
}(this, jQuery);