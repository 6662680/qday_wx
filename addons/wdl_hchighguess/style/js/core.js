!
function(a, g, f) {
    function p(a, c) {
        for (var d = 0,
        e = a.length,
        k = [], m; d < e; d++)"undefined" !== typeof(m = c(a[d])) && k.push(m);
        return k
    }
    function m() {
        var a = arguments.length - 1,
        c = arguments[a],
        d = arguments[0] || {},
        e = typeof d,
        k = 1,
        f,
        l;
        "boolean" !== typeof c && (c = !1, a++);
        for ("object" != e && "function" != e && (d = {}); k < a; k++) if (null != (e = arguments[k])) for (f in e) l = d[f],
        copy = e[f],
        d !== copy && (d[f] = c && "object" == typeof copy && null != copy ? m("object" == typeof l && null != l ? l: "[object Array]" == {}.toString.call(copy) ? [] : {},
        copy, c) : copy);
        return d
    }
    function e(a, c) {
        var d = "function" == typeof a;
        return d == ("function" == typeof c) ? 0 : d ? -1 : 1
    }
    function q() {}
    var l = a[g] = function() {
        function a(c) {
            d.apply(this, c)
        }
        function c() {
            return new a(arguments)
        }
        var d = arguments[0],
        s = [].slice.call(arguments, "function" == typeof d && !
        function(c) {
            for (c in d.prototype) return ! 0
        } () ? 1 : (d = q, 0)),
        k = [],
        f = {
            _self: d,
            _super: function() {
                k[0] && (k[0].prototype._self || k[0]).apply(this, arguments)
            },
            extend: function() {
                var a = this;
                return m.apply(null, (this === f ? [] : [this]).concat(p([{
                    constructor: c
                }].concat([].slice.call(arguments, 0), f).sort(e),
                function(c) {
                    "function" == typeof c && !a.isInstanceof(c) && k.push(c);
                    "function" == typeof c && (q.prototype = c.prototype, c = new q);
                    return c
                })))
            },
            isInstanceof: function(c) {
                var a;
                if (! (a = this instanceof c)) a: {
                    for (var d = 0,
                    e = k.length; d < e; d++) if (a = k[d].prototype.isInstanceof, k[d] === c || a && a.call(this, c)) {
                        a = !0;
                        break a
                    }
                    a = !1
                }
                return a
            }
        };
        a.prototype = c.fn = c.prototype = f.extend.apply(f, s);
        c.extend = function() {
            return l.apply(null, [].slice.call(arguments, 0).concat(this))
        };
        return c
    }
} (window, "createClass"); (function(a, g, f, p) {
    var m = a.document,
    e = a.localStorage || a.globalStorage && a.globalStorage[location.hostname];
    g.prototype = g;
    var q = function(a, c) {
        return (e ? {
            length: 0,
            init: function() {
                this.name = a ? a + "/": "";
                this.getStorages();
                return this
            },
            getStorages: function() {
                for (var c = this.storages = {},
                a = this.keys = [], k = 0, f = e.length, m, l, g = RegExp("^" + this.name.replace(/([\.\?\+\*\[\]\(\)\^\$\/\|\\])/g, "\\$1"), "i"); k < f; k++) m = e.key(k),
                g.test(m) && (l = m.replace(g, ""), a.push(l), c[l] = e.getItem(m));
                this.length = a.length;
                return this
            },
            key: function(c) {
                return this.keys[c]
            },
            getItem: function(c) {
                return this.storages[c]
            },
            setItem: function(c, a) {
                e.setItem(this.name + c, a);
                return this.getStorages()
            },
            removeItem: function(c) {
                e.removeItem(this.name + c);
                return this.getStorages()
            },
            clear: function() {
                if (this.name) for (var c = this.length,
                a = 0; a++<c;) this.removeItem(this.key(0));
                else e.clear();
                return this.getStorages()
            }
        }: {
            length: 0,
            userData: c || m.getElementsByTagName("head")[0],
            init: function() {
                this.name = a ? "_" + a: "";
                try {
                    this.userData.addBehavior("#default#userdata"),
                    this.refresh()
                } catch(c) {}
                return this
            },
            load: function() {
                try {
                    this.userData.load("oXMLBranch" + this.name),
                    this.storeNode = this.userData.xmlDocument.firstChild
                } catch(c) {}
            },
            refresh: function() {
                this.load();
                this.length = this.storeNode.attributes.length;
                return this
            },
            key: function(c) {
                this.load();
                return this.storeNode.attributes[c].nodeName.replace(/^_/, "")
            },
            getItem: function(c) {
                this.load();
                return this.userData.getAttribute("_" + c)
            },
            setItem: function(c, a) {
                this.load();
                this.userData.setAttribute("_" + c, a);
                return this.save()
            },
            removeItem: function(c) {
                this.load();
                this.userData.removeAttribute("_" + c);
                return this.save()
            },
            clear: function() {
                this.load();
                this.userData.xmlDocument.removeChild(this.storeNode);
                return this.save()
            },
            save: function() {
                this.userData.save("oXMLBranch" + this.name);
                return this.refresh()
            }
        }).init()
    },
    l;
    p = {
        version: "2.4",
        constructor: g,
        init: function(a, c) {
            "string" != typeof a && (c = a, a = "");
            this.storage = q(a, c);
            return this.test()
        },
        test: function() {
            try {
                this.support = this.set("__storage__", 1) && this.remove("__storage__")
            } catch(a) {
                this.support = !1
            }
            return this
        },
        refresh: function() {
            this.storages = this.getStorages();
            return this
        },
        has: function(a) {
            return null != this.get(a)
        },
        get: function(a) {
            return this.storages[a]
        },
        set: function(a, c) {
            this.storage.setItem(a, c);
            return this.refresh().has(a)
        },
        remove: function(a) {
            this.storage.removeItem(a);
            return ! this.refresh().has(a)
        },
        clear: function() {
            this.storage.clear();
            return ! this.refresh().size()
        },
        size: function() {
            return this.storage.length
        },
        getStorages: function() {
            for (var a = {},
            c = this.storage,
            d = 0,
            e = c && c.length || 0,
            k; d < e; d++) k = c.key(d),
            a[k] = c.getItem(k);
            return a
        },
        key: function(a) {
            return this.storage.key(a)
        }
    };
    for (l in p) g[l] = p[l];
    return a[f] = g.init()
})(window,
function(a, g) {
    if (! (this instanceof arguments.callee)) return new arguments.callee(a, g);
    this.init(a, g)
},
"storage"); (function(a, g, f, p) {
    var m = a.document,
    e = function(c) {
        var a = 0,
        e = 0;
        if ("getBoundingClientRect" in c) c = c.getBoundingClientRect(),
        a = c.top + l,
        e = c.left + A;
        else {
            a += c.scrollTop || 0;
            for (e += c.scrollLeft || 0; c;) e += c.offsetLeft || 0,
            a += c.offsetTop || 0,
            c = c.offsetParent
        }
        return {
            top: a,
            left: e
        }
    },
    q = function(c, d, s) {
        return null == d ? c.lazyData || (c.lazyData = {
            ret: [],
            bind: null,
            timer: null,
            tick: function() {
                var d = 0,
                s = q(c),
                f = m.documentElement,
                g = m.body,
                p = null != c && c == c.window;
                s.WST = (p ? c.pageYOffset || f && f.scrollTop || g.scrollTop: e(c).top) || 0;
                s.WSL = (p ? c.pageXOffset || f && f.scrollLeft || g.scrollLeft: e(c).left) || 0;
                s.WH = (p ? c.innerHeight || f && f.clientHeight || g.clientHeight: c.clientHeight) || 0;
                s.WW = (p ? c.innerWidth || f && f.clientWidth || g.clientWidth: c.clientWidth) || 0;
                p && (l = s.WST, A = s.WSL);
                for (; d < s.ret.length;) s.ret[d].length ? s.ret[d++].check() : delete s.ret.splice(d, 1)[0].checking;
                if (!s.ret.length) {
                    a: {
                        try {
                            var n = q(c, "resize");
                            a.removeEventListener ? (a.removeEventListener("resize", n, !1), c.removeEventListener("scroll", n, !1)) : (a.detachEvent("onresize", n), c.detachEvent("onscroll", n))
                        } catch(h) {
                            d = !1;
                            break a
                        }
                        d = !0
                    }
                    s.bind = !d
                }
            },
            resize: function() {
                var a = q(c);
                clearTimeout(a.timer);
                a.timer = setTimeout(a.tick, 100)
            }
        }) : null == s ? q(c)[d] : q(c)[d] = s
    },
    l = 0,
    A = 0;
    f.fn = f.prototype = {
        constructor: f,
        length: 0,
        splice: [].splice,
        dcb: function() {
            var a = this.getAttribute("data-original");
            a && (this.src = a)
        },
        init: function(c, d) {
            var e, k, f, l = typeof d;
            if ("function" == l) e = d;
            else if ("object" == l && (e = d.callback, k = d.container, f = parseFloat(d.range), this.isArrayLike(k) && (k = k[0]), "string" == typeof k && (k = m.getElementById(k)), null == k || 1 != k.nodeType || "body" == k.nodeName.toLowerCase() || "html" == k.nodeName.toLowerCase())) k = a;
            this.cb = e || this.dcb;
            this.range = f || 0;
            this.container = k || a;
            return this.push(c)
        },
        push: function(c) {
            "string" == typeof c && (c = m.getElementById(c));
            this.merge(c);
            if (this.length && (c = q(this.container), this.checking || (c.ret.push(this), this.checking = !0), c.resize(), !c.bind)) {
                var d;
                a: {
                    var e = this.container;
                    try {
                        d = q(e, "resize"),
                        a.addEventListener ? (a.addEventListener("resize", d, !1), e.addEventListener("scroll", d, !1)) : (a.attachEvent("onresize", d), e.attachEvent("onscroll", d))
                    } catch(k) {
                        d = !1;
                        break a
                    }
                    d = !0
                }
                c.bind = d
            }
            return this
        },
        isArrayLike: function(a) {
            var d = typeof a;
            return !! a && "function" != d && "string" != d && (0 === a.length || a.length && a.length - 1 in a)
        },
        merge: function(a) {
            var d = this.length,
            e = 0;
            for (a = this.isArrayLike(a) ? a: [a]; e < a.length;) a[e] && 1 == a[e].nodeType && (this[d++] = a[e]),
            e++;
            this.length = d;
            return this
        },
        check: function() {
            for (var a = 0,
            d = this.range,
            f = q(this.container), k, l; a < this.length;) {
                k = this[a];
                l = e(k);
                var g = k,
                p = m.documentElement; ! ("none" == (g.currentStyle || getComputedStyle(g, null) || g.style).display || p !== g && !(p.contains ? p.contains(g) : p.compareDocumentPosition && p.compareDocumentPosition(b) & 16)) && l.top + k.offsetHeight + d >= f.WST && l.top - d <= f.WST + f.WH && l.left + k.offsetWidth + d >= f.WSL && l.left - d <= f.WSL + f.WW ? this.cb.call(this.splice(a, 1)[0]) : a++
            }
            return this
        },
        empty: function() {
            this.length = 0;
            return this
        }
    };
    f.fn.init.prototype = f.fn;
    return a[g] = f
})(window, "LazyLoad",
function(a, g) {
    return new arguments.callee.fn.init(a, g)
}); (function(a, g, f) {
    "function" != typeof Function.prototype.bind && (Function.prototype.bind = function(a) {
        var e = this;
        return function() {
            return e.apply(a, arguments)
        }
    });
    var p = "PointerEvent" in a ? "pointerdown pointermove pointerup pointercancel": "createTouch" in a.document || "ontouchstart" in a ? "touchstart touchmove touchend touchcancel": "mousedown mousemove mouseup";
    g.prototype = {
        constructor: g,
        lineWidth: 5,
        color: "#000",
        erase: !1,
        bgcolor: "transparent",
        init: function(a) {
            this.events = {};
            this.actions = [];
            this.ctx = this.canvas.getContext("2d");
            "object" == typeof a && ["width", "height", "lineWidth", "color", "bgcolor"].forEach(function(e) {
                this[e] = "undefined" == typeof a[e] ? this[e] : a[e]
            }.bind(this));
            p.split(" ").forEach(function(a) {
                this.canvas.addEventListener(a, this, !1)
            }.bind(this));
            this.on({
                start: function() {
                    this.actions.push({
                        width: this.lineWidth,
                        color: this.color,
                        erase: this.erase,
                        pens: [arguments]
                    })
                },
                move: function() {
                    var a = this.actions[this.steps - 1];
                    a.pens.push(arguments);
                    this.draw({
                        width: a.width,
                        color: a.color,
                        erase: a.erase,
                        pens: a.pens.slice( - 2)
                    })
                },
                end: function() {
                    2 > this.actions[this.steps - 1].pens.length && this.actions.pop()
                },
                redraw: function() {
                    var a = this.ctx;
                    "transparent" != this.bgcolor && (a.fillStyle = this.bgcolor, a.fillRect(0, 0, this.width, this.height))
                }
            }).clear()
        },
        handleEvent: function(a) {
            var e = a.clientX || 0,
            f = a.clientY || 0,
            l = this.canvas.getBoundingClientRect();
            if (a.touches && a.touches.length) {
                if (1 < a.touches.length) return;
                e = a.touches.item(0).clientX;
                f = a.touches.item(0).clientY
            }
            e -= l.left;
            f -= l.top;
            e *= this.width / l.width;
            f *= this.height / l.height;
            switch (a.type.toLowerCase()) {
            case "mousedown":
            case "touchstart":
            case "pointerdown":
                this.moving = !0;
                this.fire("start", e, f);
                break;
            case "mousemove":
            case "touchmove":
            case "pointermove":
                this.moving && (a.preventDefault(), this.fire("move", e, f));
                break;
            case "mouseup":
            case "touchend":
            case "touchcancel":
            case "pointerup":
            case "pointercancel":
                this.moving && (delete this.moving, this.fire("end"))
            }
        },
        on: function(a, e) {
            "object" == typeof a ? Object.keys(a).forEach(function(e) {
                this.on(e, a[e])
            }.bind(this)) : (this.events[a] || (this.events[a] = []), this.events[a].push(e));
            return this
        },
        fire: function(a) {
            var e = [].slice.call(arguments, 1); (this.events[a] || []).forEach(function(a) {
                "function" == typeof a && a.apply(this, e)
            }.bind(this));
            return this
        },
        draw: function(a) {
            var e = this.ctx;
            e.lineWidth = a.width;
            e.lineJoin = e.lineCap = "round";
            a.erase ? (e.strokeStyle = "#000", e.globalCompositeOperation = "destination-out") : (e.globalCompositeOperation = "source-over", e.strokeStyle = a.color);
            e.beginPath();
            a.pens.forEach(function(a, f) {
                e[f ? "lineTo": "moveTo"].apply(e, a)
            });
            e.stroke();
            return this
        },
        reDraw: function() {
            this.ctx.clearRect(0, 0, this.width, this.height);
            this.fire("redraw");
            this.actions.forEach(this.draw.bind(this));
            return this
        },
        cancel: function(a) {
            this.actions.splice( - (a || 1));
            return this.reDraw()
        },
        clear: function() {
            this.actions.length = 0;
            return this.reDraw()
        },
        toDataUrl: function(a) {
            return this.canvas.toDataURL(a)
        },
        toBlob: function() {
            return (this.canvas.toBlob ||
            function(f, e) {
                if ("mozGetAsFile" in this) return f(this.mozGetAsFile("blob", e));
                var g = this.toDataURL(e).split(","),
                l = a.atob(g[1]),
                p = new Uint8Array(l.length),
                c,
                d;
                c = 0;
                for (d = p.length; c < d; c++) p[c] = l.charCodeAt(c);
                f(new Blob([p.buffer], {
                    type: e || g[0].split(/[:;]/)[1]
                }))
            }).apply(this.canvas, arguments)
        }
    };
    "function" == typeof Object.defineProperties && (["width", "height"].forEach(function(a) {
        Object.defineProperty(g.prototype, a, {
            get: function() {
                return this.canvas[a]
            },
            set: function(e) {
                this.canvas[a] = e
            },
            enumerable: !0
        })
    }), Object.defineProperty(g.prototype, "steps", {
        get: function() {
            return this.actions.length
        },
        enumerable: !0
    }));
    a.Sketch = g
})(window,
function(a, g) {
    if (! (this instanceof arguments.callee)) return new arguments.callee(a, g);
    this.canvas = "string" == typeof a ? document.getElementById(a) : a;
    this.init(g)
}); (function(a, g) {})(window);
var Zepto = function() {
    function a(a) {
        return null == a ? String(a) : N[T.call(a)] || "object"
    }
    function g(M) {
        return "function" == a(M)
    }
    function f(a) {
        return null != a && a == a.window
    }
    function p(a) {
        return null != a && a.nodeType == a.DOCUMENT_NODE
    }
    function m(M) {
        return "object" == a(M)
    }
    function e(a) {
        return m(a) && !f(a) && Object.getPrototypeOf(a) == Object.prototype
    }
    function q(a) {
        return D.call(a,
        function(a) {
            return null != a
        })
    }
    function l(a) {
        return a.replace(/::/g, "/").replace(/([A-Z]+)([A-Z][a-z])/g, "$1_$2").replace(/([a-z\d])([A-Z])/g, "$1_$2").replace(/_/g, "-").toLowerCase()
    }
    function A(a) {
        return a in t ? t[a] : t[a] = RegExp("(^|\\s)" + a + "(\\s|$)")
    }
    function c(a) {
        return "children" in a ? B.call(a.children) : h.map(a.childNodes,
        function(a) {
            if (1 == a.nodeType) return a
        })
    }
    function d(a, c, r) {
        for (n in c) r && (e(c[n]) || I(c[n])) ? (e(c[n]) && !e(a[n]) && (a[n] = {}), I(c[n]) && !I(a[n]) && (a[n] = []), d(a[n], c[n], r)) : c[n] !== u && (a[n] = c[n])
    }
    function s(a, c) {
        return null == c ? h(a) : h(a).filter(c)
    }
    function k(a, c, d, e) {
        return g(c) ? c.call(a, d, e) : c
    }
    function z(a, c) {
        var d = a.className || "",
        e = d && d.baseVal !== u;
        if (c === u) return e ? d.baseVal: d;
        e ? d.baseVal = c: a.className = c
    }
    function C(a) {
        var c;
        try {
            return a ? "true" == a || ("false" == a ? !1 : "null" == a ? null: !/^0/.test(a) && !isNaN(c = Number(a)) ? c: /^[\[\{]/.test(a) ? h.parseJSON(a) : a) : a
        } catch(d) {
            return a
        }
    }
    function w(a, c) {
        c(a);
        for (var d = 0,
        e = a.childNodes.length; d < e; d++) w(a.childNodes[d], c)
    }
    var u, n, h, F, E = [],
    B = E.slice,
    D = E.filter,
    x = window.document,
    r = {},
    t = {},
    G = {
        "column-count": 1,
        columns: 1,
        "font-weight": 1,
        "line-height": 1,
        opacity: 1,
        "z-index": 1,
        zoom: 1
    },
    H = /^\s*<(\w+|!)[^>]*>/,
    J = /^<(\w+)\s*\/?>(?:<\/\1>|)$/,
    U = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/ig,
    P = /^(?:body|html)$/i,
    v = /([A-Z])/g,
    V = "val css html text data width height offset".split(" "),
    O = x.createElement("table"),
    Q = x.createElement("tr"),
    R = {
        tr: x.createElement("tbody"),
        tbody: O,
        thead: O,
        tfoot: O,
        td: Q,
        th: Q,
        "*": x.createElement("div")
    },
    W = /complete|loaded|interactive/,
    X = /^[\w-]*$/,
    N = {},
    T = N.toString,
    y = {},
    K,
    L,
    S = x.createElement("div"),
    Y = {
        tabindex: "tabIndex",
        readonly: "readOnly",
        "for": "htmlFor",
        "class": "className",
        maxlength: "maxLength",
        cellspacing: "cellSpacing",
        cellpadding: "cellPadding",
        rowspan: "rowSpan",
        colspan: "colSpan",
        usemap: "useMap",
        frameborder: "frameBorder",
        contenteditable: "contentEditable"
    },
    I = Array.isArray ||
    function(a) {
        return a instanceof Array
    };
    y.matches = function(a, c) {
        if (!c || !a || 1 !== a.nodeType) return ! 1;
        var d = a.webkitMatchesSelector || a.mozMatchesSelector || a.oMatchesSelector || a.matchesSelector;
        if (d) return d.call(a, c);
        var e;
        e = a.parentNode; (d = !e) && (e = S).appendChild(a);
        e = ~y.qsa(e, c).indexOf(a);
        d && S.removeChild(a);
        return e
    };
    K = function(a) {
        return a.replace(/-+(.)?/g,
        function(a, c) {
            return c ? c.toUpperCase() : ""
        })
    };
    L = function(a) {
        return D.call(a,
        function(c, d) {
            return a.indexOf(c) == d
        })
    };
    y.fragment = function(a, c, d) {
        var r, f, t;
        J.test(a) && (r = h(x.createElement(RegExp.$1)));
        r || (a.replace && (a = a.replace(U, "<$1></$2>")), c === u && (c = H.test(a) && RegExp.$1), c in R || (c = "*"), t = R[c], t.innerHTML = "" + a, r = h.each(B.call(t.childNodes),
        function() {
            t.removeChild(this)
        }));
        e(d) && (f = h(r), h.each(d,
        function(a, c) {
            if ( - 1 < V.indexOf(a)) f[a](c);
            else f.attr(a, c)
        }));
        return r
    };
    y.Z = function(a, c) {
        a = a || [];
        a.__proto__ = h.fn;
        a.selector = c || "";
        return a
    };
    y.isZ = function(a) {
        return a instanceof y.Z
    };
    y.init = function(a, c) {
        var d;
        if (a) if ("string" == typeof a) if (a = a.trim(), "<" == a[0] && H.test(a)) d = y.fragment(a, RegExp.$1, c),
        a = null;
        else {
            if (c !== u) return h(c).find(a);
            d = y.qsa(x, a)
        } else {
            if (g(a)) return h(x).ready(a);
            if (y.isZ(a)) return a;
            if (I(a)) d = q(a);
            else if (m(a)) d = [a],
            a = null;
            else if (H.test(a)) d = y.fragment(a.trim(), RegExp.$1, c),
            a = null;
            else {
                if (c !== u) return h(c).find(a);
                d = y.qsa(x, a)
            }
        } else return y.Z();
        return y.Z(d, a)
    };
    h = function(a, c) {
        return y.init(a, c)
    };
    h.extend = function(a) {
        var c, e = B.call(arguments, 1);
        "boolean" == typeof a && (c = a, a = e.shift());
        e.forEach(function(e) {
            d(a, e, c)
        });
        return a
    };
    y.qsa = function(a, c) {
        var d, e = "#" == c[0],
        r = !e && "." == c[0],
        f = e || r ? c.slice(1) : c,
        t = X.test(f);
        return p(a) && t && e ? (d = a.getElementById(f)) ? [d] : [] : 1 !== a.nodeType && 9 !== a.nodeType ? [] : B.call(t && !e ? r ? a.getElementsByClassName(f) : a.getElementsByTagName(c) : a.querySelectorAll(c))
    };
    h.contains = x.documentElement.contains ?
    function(a, c) {
        return a !== c && a.contains(c)
    }: function(a, c) {
        for (; c && (c = c.parentNode);) if (c === a) return ! 0;
        return ! 1
    };
    h.type = a;
    h.isFunction = g;
    h.isWindow = f;
    h.isArray = I;
    h.isPlainObject = e;
    h.isEmptyObject = function(a) {
        for (var c in a) return ! 1;
        return ! 0
    };
    h.inArray = function(a, c, d) {
        return E.indexOf.call(c, a, d)
    };
    h.camelCase = K;
    h.trim = function(a) {
        return null == a ? "": String.prototype.trim.call(a)
    };
    h.uuid = 0;
    h.support = {};
    h.expr = {};
    h.map = function(a, c) {
        var d, e = [],
        r;
        if ("number" == typeof a.length) for (r = 0; r < a.length; r++) d = c(a[r], r),
        null != d && e.push(d);
        else for (r in a) d = c(a[r], r),
        null != d && e.push(d);
        return 0 < e.length ? h.fn.concat.apply([], e) : e
    };
    h.each = function(a, c) {
        var d;
        if ("number" == typeof a.length) for (d = 0; d < a.length && !1 !== c.call(a[d], d, a[d]); d++);
        else for (d in a) if (!1 === c.call(a[d], d, a[d])) break;
        return a
    };
    h.grep = function(a, c) {
        return D.call(a, c)
    };
    window.JSON && (h.parseJSON = JSON.parse);
    h.each("Boolean Number String Function Array Date RegExp Object Error".split(" "),
    function(a, c) {
        N["[object " + c + "]"] = c.toLowerCase()
    });
    h.fn = {
        forEach: E.forEach,
        reduce: E.reduce,
        push: E.push,
        sort: E.sort,
        indexOf: E.indexOf,
        concat: E.concat,
        map: function(a) {
            return h(h.map(this,
            function(c, d) {
                return a.call(c, d, c)
            }))
        },
        slice: function() {
            return h(B.apply(this, arguments))
        },
        ready: function(a) {
            W.test(x.readyState) && x.body ? a(h) : x.addEventListener("DOMContentLoaded",
            function() {
                a(h)
            },
            !1);
            return this
        },
        get: function(a) {
            return a === u ? B.call(this) : this[0 <= a ? a: a + this.length]
        },
        toArray: function() {
            return this.get()
        },
        size: function() {
            return this.length
        },
        remove: function() {
            return this.each(function() {
                null != this.parentNode && this.parentNode.removeChild(this)
            })
        },
        each: function(a) {
            E.every.call(this,
            function(c, d) {
                return ! 1 !== a.call(c, d, c)
            });
            return this
        },
        filter: function(a) {
            return g(a) ? this.not(this.not(a)) : h(D.call(this,
            function(c) {
                return y.matches(c, a)
            }))
        },
        add: function(a, c) {
            return h(L(this.concat(h(a, c))))
        },
        is: function(a) {
            return 0 < this.length && y.matches(this[0], a)
        },
        not: function(a) {
            var c = [];
            if (g(a) && a.call !== u) this.each(function(d) {
                a.call(this, d) || c.push(this)
            });
            else {
                var d = "string" == typeof a ? this.filter(a) : "number" == typeof a.length && g(a.item) ? B.call(a) : h(a);
                this.forEach(function(a) {
                    0 > d.indexOf(a) && c.push(a)
                })
            }
            return h(c)
        },
        has: function(a) {
            return this.filter(function() {
                return m(a) ? h.contains(this, a) : h(this).find(a).size()
            })
        },
        eq: function(a) {
            return - 1 === a ? this.slice(a) : this.slice(a, +a + 1)
        },
        first: function() {
            var a = this[0];
            return a && !m(a) ? a: h(a)
        },
        last: function() {
            var a = this[this.length - 1];
            return a && !m(a) ? a: h(a)
        },
        find: function(a) {
            var c = this;
            return a ? "object" == typeof a ? h(a).filter(function() {
                var a = this;
                return E.some.call(c,
                function(c) {
                    return h.contains(c, a)
                })
            }) : 1 == this.length ? h(y.qsa(this[0], a)) : this.map(function() {
                return y.qsa(this, a)
            }) : []
        },
        closest: function(a, c) {
            var d = this[0],
            e = !1;
            for ("object" == typeof a && (e = h(a)); d && !(e ? 0 <= e.indexOf(d) : y.matches(d, a));) d = d !== c && !p(d) && d.parentNode;
            return h(d)
        },
        parents: function(a) {
            for (var c = [], d = this; 0 < d.length;) d = h.map(d,
            function(a) {
                if ((a = a.parentNode) && !p(a) && 0 > c.indexOf(a)) return c.push(a),
                a
            });
            return s(c, a)
        },
        parent: function(a) {
            return s(L(this.pluck("parentNode")), a)
        },
        children: function(a) {
            return s(this.map(function() {
                return c(this)
            }), a)
        },
        contents: function() {
            return this.map(function() {
                return B.call(this.childNodes)
            })
        },
        siblings: function(a) {
            return s(this.map(function(a, d) {
                return D.call(c(d.parentNode),
                function(a) {
                    return a !== d
                })
            }), a)
        },
        empty: function() {
            return this.each(function() {
                this.innerHTML = ""
            })
        },
        pluck: function(a) {
            return h.map(this,
            function(c) {
                return c[a]
            })
        },
        show: function() {
            return this.each(function() {
                "none" == this.style.display && (this.style.display = "");
                if ("none" == getComputedStyle(this, "").getPropertyValue("display")) {
                    var a = this.style,
                    c = this.nodeName,
                    d, e;
                    r[c] || (d = x.createElement(c), x.body.appendChild(d), e = getComputedStyle(d, "").getPropertyValue("display"), d.parentNode.removeChild(d), "none" == e && (e = "block"), r[c] = e);
                    a.display = r[c]
                }
            })
        },
        replaceWith: function(a) {
            return this.before(a).remove()
        },
        wrap: function(a) {
            var c = g(a);
            if (this[0] && !c) var d = h(a).get(0),
            e = d.parentNode || 1 < this.length;
            return this.each(function(r) {
                h(this).wrapAll(c ? a.call(this, r) : e ? d.cloneNode(!0) : d)
            })
        },
        wrapAll: function(a) {
            if (this[0]) {
                h(this[0]).before(a = h(a));
                for (var c; (c = a.children()).length;) a = c.first();
                h(a).append(this)
            }
            return this
        },
        wrapInner: function(a) {
            var c = g(a);
            return this.each(function(d) {
                var e = h(this),
                r = e.contents();
                d = c ? a.call(this, d) : a;
                r.length ? r.wrapAll(d) : e.append(d)
            })
        },
        unwrap: function() {
            this.parent().each(function() {
                h(this).replaceWith(h(this).children())
            });
            return this
        },
        clone: function() {
            return this.map(function() {
                return this.cloneNode(!0)
            })
        },
        hide: function() {
            return this.css("display", "none")
        },
        toggle: function(a) {
            return this.each(function() {
                var c = h(this); (a === u ? "none" == c.css("display") : a) ? c.show() : c.hide()
            })
        },
        prev: function(a) {
            return h(this.pluck("previousElementSibling")).filter(a || "*")
        },
        next: function(a) {
            return h(this.pluck("nextElementSibling")).filter(a || "*")
        },
        html: function(a) {
            return 0 in arguments ? this.each(function(c) {
                var d = this.innerHTML;
                h(this).empty().append(k(this, a, c, d))
            }) : 0 in this ? this[0].innerHTML: null
        },
        text: function(a) {
            return 0 in arguments ? this.each(function(c) {
                c = k(this, a, c, this.textContent);
                this.textContent = null == c ? "": "" + c
            }) : 0 in this ? this[0].textContent: null
        },
        attr: function(a, c) {
            var d;
            return "string" == typeof a && !(1 in arguments) ? !this.length || 1 !== this[0].nodeType ? u: !(d = this[0].getAttribute(a)) && a in this[0] ? this[0][a] : d: this.each(function(d) {
                if (1 === this.nodeType) if (m(a)) for (n in a) {
                    var e = n;
                    d = a[n];
                    null == d ? this.removeAttribute(e) : this.setAttribute(e, d)
                } else e = a,
                d = k(this, c, d, this.getAttribute(a)),
                null == d ? this.removeAttribute(e) : this.setAttribute(e, d)
            })
        },
        removeAttr: function(a) {
            return this.each(function() {
                1 === this.nodeType && this.removeAttribute(a)
            })
        },
        prop: function(a, c) {
            a = Y[a] || a;
            return 1 in arguments ? this.each(function(d) {
                this[a] = k(this, c, d, this[a])
            }) : this[0] && this[0][a]
        },
        data: function(a, c) {
            var d = "data-" + a.replace(v, "-$1").toLowerCase(),
            d = 1 in arguments ? this.attr(d, c) : this.attr(d);
            return null !== d ? C(d) : u
        },
        val: function(a) {
            return 0 in arguments ? this.each(function(c) {
                this.value = k(this, a, c, this.value)
            }) : this[0] && (this[0].multiple ? h(this[0]).find("option").filter(function() {
                return this.selected
            }).pluck("value") : this[0].value)
        },
        offset: function(a) {
            if (a) return this.each(function(c) {
                var d = h(this);
                c = k(this, a, c, d.offset());
                var e = d.offsetParent().offset();
                c = {
                    top: c.top - e.top,
                    left: c.left - e.left
                };
                "static" == d.css("position") && (c.position = "relative");
                d.css(c)
            });
            if (!this.length) return null;
            var c = this[0].getBoundingClientRect();
            return {
                left: c.left + window.pageXOffset,
                top: c.top + window.pageYOffset,
                width: Math.round(c.width),
                height: Math.round(c.height)
            }
        },
        css: function(c, d) {
            if (2 > arguments.length) {
                var e = this[0],
                r = getComputedStyle(e, "");
                if (!e) return;
                if ("string" == typeof c) return e.style[K(c)] || r.getPropertyValue(c);
                if (I(c)) {
                    var f = {};
                    h.each(c,
                    function(a, c) {
                        f[c] = e.style[K(c)] || r.getPropertyValue(c)
                    });
                    return f
                }
            }
            var t = "";
            if ("string" == a(c)) ! d && 0 !== d ? this.each(function() {
                this.style.removeProperty(l(c))
            }) : t = l(c) + ":" + ("number" == typeof d && !G[l(c)] ? d + "px": d);
            else for (n in c) ! c[n] && 0 !== c[n] ? this.each(function() {
                this.style.removeProperty(l(n))
            }) : t += l(n) + ":" + ("number" == typeof c[n] && !G[l(n)] ? c[n] + "px": c[n]) + ";";
            return this.each(function() {
                this.style.cssText += ";" + t
            })
        },
        index: function(a) {
            return a ? this.indexOf(h(a)[0]) : this.parent().children().indexOf(this[0])
        },
        hasClass: function(a) {
            return ! a ? !1 : E.some.call(this,
            function(a) {
                return this.test(z(a))
            },
            A(a))
        },
        addClass: function(a) {
            return ! a ? this: this.each(function(c) {
                if ("className" in this) {
                    F = [];
                    var d = z(this);
                    k(this, a, c, d).split(/\s+/g).forEach(function(a) {
                        h(this).hasClass(a) || F.push(a)
                    },
                    this);
                    F.length && z(this, d + (d ? " ": "") + F.join(" "))
                }
            })
        },
        removeClass: function(a) {
            return this.each(function(c) {
                if ("className" in this) {
                    if (a === u) return z(this, "");
                    F = z(this);
                    k(this, a, c, F).split(/\s+/g).forEach(function(a) {
                        F = F.replace(A(a), " ")
                    });
                    z(this, F.trim())
                }
            })
        },
        toggleClass: function(a, c) {
            return ! a ? this: this.each(function(d) {
                var e = h(this);
                k(this, a, d, z(this)).split(/\s+/g).forEach(function(a) { (c === u ? !e.hasClass(a) : c) ? e.addClass(a) : e.removeClass(a)
                })
            })
        },
        scrollTop: function(a) {
            if (this.length) {
                var c = "scrollTop" in this[0];
                return a === u ? c ? this[0].scrollTop: this[0].pageYOffset: this.each(c ?
                function() {
                    this.scrollTop = a
                }: function() {
                    this.scrollTo(this.scrollX, a)
                })
            }
        },
        scrollLeft: function(a) {
            if (this.length) {
                var c = "scrollLeft" in this[0];
                return a === u ? c ? this[0].scrollLeft: this[0].pageXOffset: this.each(c ?
                function() {
                    this.scrollLeft = a
                }: function() {
                    this.scrollTo(a, this.scrollY)
                })
            }
        },
        position: function() {
            if (this.length) {
                var a = this[0],
                c = this.offsetParent(),
                d = this.offset(),
                e = P.test(c[0].nodeName) ? {
                    top: 0,
                    left: 0
                }: c.offset();
                d.top -= parseFloat(h(a).css("margin-top")) || 0;
                d.left -= parseFloat(h(a).css("margin-left")) || 0;
                e.top += parseFloat(h(c[0]).css("border-top-width")) || 0;
                e.left += parseFloat(h(c[0]).css("border-left-width")) || 0;
                return {
                    top: d.top - e.top,
                    left: d.left - e.left
                }
            }
        },
        offsetParent: function() {
            return this.map(function() {
                for (var a = this.offsetParent || x.body; a && !P.test(a.nodeName) && "static" == h(a).css("position");) a = a.offsetParent;
                return a
            })
        }
    };
    h.fn.detach = h.fn.remove; ["width", "height"].forEach(function(a) {
        var c = a.replace(/./,
        function(a) {
            return a[0].toUpperCase()
        });
        h.fn[a] = function(d) {
            var e, r = this[0];
            return d === u ? f(r) ? r["inner" + c] : p(r) ? r.documentElement["scroll" + c] : (e = this.offset()) && e[a] : this.each(function(c) {
                r = h(this);
                r.css(a, k(this, d, c, r[a]()))
            })
        }
    }); ["after", "prepend", "before", "append"].forEach(function(c, d) {
        var e = d % 2;
        h.fn[c] = function() {
            var c, r = h.map(arguments,
            function(d) {
                c = a(d);
                return "object" == c || "array" == c || null == d ? d: y.fragment(d)
            }),
            f,
            t = 1 < this.length;
            return 1 > r.length ? this: this.each(function(a, c) {
                f = e ? c: c.parentNode;
                c = 0 == d ? c.nextSibling: 1 == d ? c.firstChild: 2 == d ? c: null;
                var k = h.contains(x.documentElement, f);
                r.forEach(function(a) {
                    if (t) a = a.cloneNode(!0);
                    else if (!f) return h(a).remove();
                    f.insertBefore(a, c);
                    k && w(a,
                    function(a) {
                        null != a.nodeName && "SCRIPT" === a.nodeName.toUpperCase() && ((!a.type || "text/javascript" === a.type) && !a.src) && window.eval.call(window, a.innerHTML)
                    })
                })
            })
        };
        h.fn[e ? c + "To": "insert" + (d ? "Before": "After")] = function(a) {
            h(a)[c](this);
            return this
        }
    });
    y.Z.prototype = h.fn;
    y.uniq = L;
    y.deserializeValue = C;
    h.zepto = y;
    return h
} ();
window.Zepto = Zepto;
void 0 === window.$ && (window.$ = Zepto); (function(a) {
    "__proto__" in {} || a.extend(a.zepto, {
        Z: function(f, g) {
            f = f || [];
            a.extend(f, a.fn);
            f.selector = g || "";
            f.__Z = !0;
            return f
        },
        isZ: function(f) {
            return "array" === a.type(f) && "__Z" in f
        }
    });
    try {
        getComputedStyle(void 0)
    } catch(g) {
        var f = getComputedStyle;
        window.getComputedStyle = function(a) {
            try {
                return f(a)
            } catch(g) {
                return null
            }
        }
    }
})(Zepto); (function(a) {
    function g(c) {
        c = a(c);
        return ! (!c.width() && !c.height()) && "none" !== c.css("display")
    }
    function f(a, c) {
        a = a.replace(/=#\]/g, '="#"]');
        var e, f, g = l.exec(a);
        g && g[2] in q && (e = q[g[2]], f = g[3], a = g[1], f && (g = Number(f), f = isNaN(g) ? f.replace(/^["']|["']$/g, "") : g));
        return c(a, e, f)
    }
    var p = a.zepto,
    m = p.qsa,
    e = p.matches,
    q = a.expr[":"] = {
        visible: function() {
            if (g(this)) return this
        },
        hidden: function() {
            if (!g(this)) return this
        },
        selected: function() {
            if (this.selected) return this
        },
        checked: function() {
            if (this.checked) return this
        },
        parent: function() {
            return this.parentNode
        },
        first: function(a) {
            if (0 === a) return this
        },
        last: function(a, c) {
            if (a === c.length - 1) return this
        },
        eq: function(a, c, e) {
            if (a === e) return this
        },
        contains: function(c, e, f) {
            if ( - 1 < a(this).text().indexOf(f)) return this
        },
        has: function(a, c, e) {
            if (p.qsa(this, e).length) return this
        }
    },
    l = /(.*):(\w+)(?:\(([^)]+)\))?$\s*/,
    A = /^\s*>/,
    c = "Zepto" + +new Date;
    p.qsa = function(d, e) {
        return f(e,
        function(f, g, l) {
            try {
                var q; ! f && g ? f = "*": A.test(f) && (q = a(d).addClass(c), f = "." + c + " " + f);
                var u = m(d, f)
            } catch(n) {
                throw console.error("error performing selector: %o", e),
                n;
            } finally {
                q && q.removeClass(c)
            }
            return ! g ? u: p.uniq(a.map(u,
            function(a, c) {
                return g.call(a, c, u, l)
            }))
        })
    };
    p.matches = function(a, c) {
        return f(c,
        function(c, f, g) {
            return (!c || e(a, c)) && (!f || f.call(a, null, g) === a)
        })
    }
})(Zepto); (function(a) {
    a.fn.end = function() {
        return this.prevObject || a()
    };
    a.fn.andSelf = function() {
        return this.add(this.prevObject || a())
    };
    "filter add not eq first last find closest parents parent children siblings".split(" ").forEach(function(g) {
        var f = a.fn[g];
        a.fn[g] = function() {
            var a = f.apply(this, arguments);
            a.prevObject = this;
            return a
        }
    })
})(Zepto); (function(a) {
    function g(a) {
        return a._zid || (a._zid = A++)
    }
    function f(a, c, d, e) {
        c = p(c);
        if (c.ns) var f = RegExp("(?:^| )" + c.ns.replace(" ", " .* ?") + "(?: |$)");
        return (z[g(a)] || []).filter(function(a) {
            return a && (!c.e || a.e == c.e) && (!c.ns || f.test(a.ns)) && (!d || g(a.fn) === g(d)) && (!e || a.sel == e)
        })
    }
    function p(a) {
        a = ("" + a).split(".");
        return {
            e: a[0],
            ns: a.slice(1).sort().join(" ")
        }
    }
    function m(d, e, f, t, h, k, l) {
        var s = g(d),
        m = z[s] || (z[s] = []);
        e.split(/\s/).forEach(function(e) {
            if ("ready" == e) return a(document).ready(f);
            var g = p(e);
            g.fn = f;
            g.sel = h;
            g.e in n && (f = function(c) {
                var d = c.relatedTarget;
                if (!d || d !== this && !a.contains(this, d)) return g.fn.apply(this, arguments)
            });
            var x = (g.del = k) || f;
            g.proxy = function(a) {
                a = q(a);
                if (!a.isImmediatePropagationStopped()) {
                    a.data = t;
                    var e = x.apply(d, a._args == c ? [a] : [a].concat(a._args)); ! 1 === e && (a.preventDefault(), a.stopPropagation());
                    return e
                }
            };
            g.i = m.length;
            m.push(g);
            "addEventListener" in d && d.addEventListener(n[g.e] || w && u[g.e] || g.e, g.proxy, g.del && !w && g.e in u || !!l)
        })
    }
    function e(a, c, d, e, h) {
        var k = g(a); (c || "").split(/\s/).forEach(function(c) {
            f(a, c, d, e).forEach(function(c) {
                delete z[k][c.i];
                "removeEventListener" in a && a.removeEventListener(n[c.e] || w && u[c.e] || c.e, c.proxy, c.del && !w && c.e in u || !!h)
            })
        })
    }
    function q(d, e) {
        if (e || !d.isDefaultPrevented) if (e || (e = d), a.each(B,
        function(a, c) {
            var f = e[a];
            d[a] = function() {
                this[c] = h;
                return f && f.apply(e, arguments)
            };
            d[c] = F
        }), e.defaultPrevented !== c ? e.defaultPrevented: "returnValue" in e ? !1 === e.returnValue: e.getPreventDefault && e.getPreventDefault()) d.isDefaultPrevented = h;
        return d
    }
    function l(a) {
        var d, e = {
            originalEvent: a
        };
        for (d in a) ! E.test(d) && a[d] !== c && (e[d] = a[d]);
        return q(e, a)
    }
    var A = 1,
    c, d = Array.prototype.slice,
    s = a.isFunction,
    k = function(a) {
        return "string" == typeof a
    },
    z = {},
    C = {},
    w = "onfocusin" in window,
    u = {
        focus: "focusin",
        blur: "focusout"
    },
    n = {
        mouseenter: "mouseover",
        mouseleave: "mouseout"
    };
    C.click = C.mousedown = C.mouseup = C.mousemove = "MouseEvents";
    a.event = {
        add: m,
        remove: e
    };
    a.proxy = function(c, e) {
        var f = 2 in arguments && d.call(arguments, 2);
        if (s(c)) {
            var t = function() {
                return c.apply(e, f ? f.concat(d.call(arguments)) : arguments)
            };
            t._zid = g(c);
            return t
        }
        if (k(e)) return f ? (f.unshift(c[e], c), a.proxy.apply(null, f)) : a.proxy(c[e], c);
        throw new TypeError("expected function");
    };
    a.fn.bind = function(a, c, d) {
        return this.on(a, c, d)
    };
    a.fn.unbind = function(a, c) {
        return this.off(a, c)
    };
    a.fn.one = function(a, c, d, e) {
        return this.on(a, c, d, e, 1)
    };
    var h = function() {
        return ! 0
    },
    F = function() {
        return ! 1
    },
    E = /^([A-Z]|returnValue$|layer[XY]$)/,
    B = {
        preventDefault: "isDefaultPrevented",
        stopImmediatePropagation: "isImmediatePropagationStopped",
        stopPropagation: "isPropagationStopped"
    };
    a.fn.delegate = function(a, c, d) {
        return this.on(c, a, d)
    };
    a.fn.undelegate = function(a, c, d) {
        return this.off(c, a, d)
    };
    a.fn.live = function(c, d) {
        a(document.body).delegate(this.selector, c, d);
        return this
    };
    a.fn.die = function(c, d) {
        a(document.body).undelegate(this.selector, c, d);
        return this
    };
    a.fn.on = function(f, g, r, t, h) {
        var n, p, q = this;
        if (f && !k(f)) return a.each(f,
        function(a, c) {
            q.on(a, g, r, c, h)
        }),
        q; ! k(g) && !s(t) && !1 !== t && (t = r, r = g, g = c);
        if (s(r) || !1 === r) t = r,
        r = c; ! 1 === t && (t = F);
        return q.each(function(c, k) {
            h && (n = function(a) {
                e(k, a.type, t);
                return t.apply(this, arguments)
            });
            g && (p = function(c) {
                var e, f = a(c.target).closest(g, k).get(0);
                if (f && f !== k) return e = a.extend(l(c), {
                    currentTarget: f,
                    liveFired: k
                }),
                (n || t).apply(f, [e].concat(d.call(arguments, 1)))
            });
            m(k, f, t, r, g, p || n)
        })
    };
    a.fn.off = function(d, f, r) {
        var g = this;
        if (d && !k(d)) return a.each(d,
        function(a, c) {
            g.off(a, f, c)
        }),
        g; ! k(f) && !s(r) && !1 !== r && (r = f, f = c); ! 1 === r && (r = F);
        return g.each(function() {
            e(this, d, r, f)
        })
    };
    a.fn.trigger = function(c, d) {
        c = k(c) || a.isPlainObject(c) ? a.Event(c) : q(c);
        c._args = d;
        return this.each(function() {
            "dispatchEvent" in this ? this.dispatchEvent(c) : a(this).triggerHandler(c, d)
        })
    };
    a.fn.triggerHandler = function(c, d) {
        var e, g;
        this.each(function(h, n) {
            e = l(k(c) ? a.Event(c) : c);
            e._args = d;
            e.target = n;
            a.each(f(n, c.type || c),
            function(a, c) {
                g = c.proxy(e);
                if (e.isImmediatePropagationStopped()) return ! 1
            })
        });
        return g
    };
    "focusin focusout load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select keydown keypress keyup error".split(" ").forEach(function(c) {
        a.fn[c] = function(a) {
            return a ? this.bind(c, a) : this.trigger(c)
        }
    }); ["focus", "blur"].forEach(function(c) {
        a.fn[c] = function(a) {
            a ? this.bind(c, a) : this.each(function() {
                try {
                    this[c]()
                } catch(a) {}
            });
            return this
        }
    });
    a.Event = function(a, c) {
        k(a) || (c = a, a = c.type);
        var d = document.createEvent(C[a] || "Events"),
        e = !0;
        if (c) for (var f in c)"bubbles" == f ? e = !!c[f] : d[f] = c[f];
        d.initEvent(a, e, !0);
        return q(d)
    }
})(Zepto); (function(a) {
    function g(c, d) {
        var g = c[l],
        g = g && m[g];
        if (void 0 === d) return g || f(c);
        if (g) {
            if (d in g) return g[d];
            var k = q(d);
            if (k in g) return g[k]
        }
        return e.call(a(c), d)
    }
    function f(c, d, e) {
        var f = c[l] || (c[l] = ++a.uuid);
        c = m[f] || (m[f] = p(c));
        void 0 !== d && (c[q(d)] = e);
        return c
    }
    function p(c) {
        var d = {};
        a.each(c.attributes || A,
        function(c, e) {
            0 == e.name.indexOf("data-") && (d[q(e.name.replace("data-", ""))] = a.zepto.deserializeValue(e.value))
        });
        return d
    }
    var m = {},
    e = a.fn.data,
    q = a.camelCase,
    l = a.expando = "Zepto" + +new Date,
    A = [];
    a.fn.data = function(c, d) {
        return void 0 === d ? a.isPlainObject(c) ? this.each(function(d, e) {
            a.each(c,
            function(a, c) {
                f(e, a, c)
            })
        }) : 0 in this ? g(this[0], c) : void 0 : this.each(function() {
            f(this, c, d)
        })
    };
    a.fn.removeData = function(c) {
        "string" == typeof c && (c = c.split(/\s+/));
        return this.each(function() {
            var d = this[l],
            e = d && m[d];
            e && a.each(c || e,
            function(a) {
                delete e[c ? q(this) : a]
            })
        })
    }; ["remove", "empty"].forEach(function(c) {
        var d = a.fn[c];
        a.fn[c] = function() {
            var a = this.find("*");
            "remove" === c && (a = a.add(this));
            a.removeData();
            return d.call(this)
        }
    })
})(Zepto); (function(a) {
    function g(c, d, e, f) {
        if (c.global) return c = d || C,
        e = a.Event(e),
        a(c).trigger(e, f),
        !e.isDefaultPrevented()
    }
    function f(c) {
        c.global && 0 === a.active++&&g(c, null, "ajaxStart")
    }
    function p(a, c) {
        var d = c.context;
        if (!1 === c.beforeSend.call(d, a, c) || !1 === g(c, d, "ajaxBeforeSend", [a, c])) return ! 1;
        g(c, d, "ajaxSend", [a, c])
    }
    function m(a, c, d, e) {
        var f = d.context;
        d.success.call(f, a, "success", c);
        e && e.resolveWith(f, [a, "success", c]);
        g(d, f, "ajaxSuccess", [c, d, a]);
        q("success", c, d)
    }
    function e(a, c, d, e, f) {
        var h = e.context;
        e.error.call(h, d, c, a);
        f && f.rejectWith(h, [d, c, a]);
        g(e, h, "ajaxError", [d, e, a || c]);
        q(c, d, e)
    }
    function q(c, d, e) {
        var f = e.context;
        e.complete.call(f, d, c);
        g(e, f, "ajaxComplete", [d, e]);
        e.global && !--a.active && g(e, null, "ajaxStop")
    }
    function l() {}
    function A(a) {
        a && (a = a.split(";", 2)[0]);
        return a && (a == B ? "html": a == E ? "json": h.test(a) ? "script": F.test(a) && "xml") || "text"
    }
    function c(a, c) {
        return "" == c ? a: (a + "&" + c).replace(/[&?]{1,2}/, "?")
    }
    function d(d) {
        d.processData && d.data && "string" != a.type(d.data) && (d.data = a.param(d.data, d.traditional));
        if (d.data && (!d.type || "GET" == d.type.toUpperCase())) d.url = c(d.url, d.data),
        d.data = void 0
    }
    function s(c, d, e, f) {
        a.isFunction(d) && (f = e, e = d, d = void 0);
        a.isFunction(e) || (f = e, e = void 0);
        return {
            url: c,
            data: d,
            success: e,
            dataType: f
        }
    }
    function k(c, d, e, f) {
        var g, h = a.isArray(d),
        l = a.isPlainObject(d);
        a.each(d,
        function(d, t) {
            g = a.type(t);
            f && (d = e ? f: f + "[" + (l || "object" == g || "array" == g ? d: "") + "]"); ! f && h ? c.add(t.name, t.value) : "array" == g || !e && "object" == g ? k(c, t, e, d) : c.add(d, t)
        })
    }
    var z = 0,
    C = window.document,
    w, u, n = /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi,
    h = /^(?:text|application)\/javascript/i,
    F = /^(?:text|application)\/xml/i,
    E = "application/json",
    B = "text/html",
    D = /^\s*$/;
    a.active = 0;
    a.ajaxJSONP = function(c, d) {
        if (! ("type" in c)) return a.ajax(c);
        var f = c.jsonpCallback,
        g = (a.isFunction(f) ? f() : f) || "jsonp" + ++z,
        h = C.createElement("script"),
        k = window[g],
        l,
        n = function(c) {
            a(h).triggerHandler("error", c || "abort")
        },
        s = {
            abort: n
        },
        q;
        d && d.promise(s);
        a(h).on("load error",
        function(f, n) {
            clearTimeout(q);
            a(h).off().remove();
            "error" == f.type || !l ? e(null, n || "error", s, c, d) : m(l[0], s, c, d);
            window[g] = k;
            l && a.isFunction(k) && k(l[0]);
            k = l = void 0
        });
        if (!1 === p(s, c)) return n("abort"),
        s;
        window[g] = function() {
            l = arguments
        };
        h.src = c.url.replace(/\?(.+)=\?/, "?$1=" + g);
        C.head.appendChild(h);
        0 < c.timeout && (q = setTimeout(function() {
            n("timeout")
        },
        c.timeout));
        return s
    };
    a.ajaxSettings = {
        type: "GET",
        beforeSend: l,
        success: l,
        error: l,
        complete: l,
        context: null,
        global: !0,
        xhr: function() {
            return new window.XMLHttpRequest
        },
        accepts: {
            script: "text/javascript, application/javascript, application/x-javascript",
            json: E,
            xml: "application/xml, text/xml",
            html: B,
            text: "text/plain"
        },
        crossDomain: !1,
        timeout: 0,
        processData: !0,
        cache: !0
    };
    a.ajax = function(g) {
        var h = a.extend({},
        g || {}),
        k = a.Deferred && a.Deferred();
        for (w in a.ajaxSettings) void 0 === h[w] && (h[w] = a.ajaxSettings[w]);
        f(h);
        h.crossDomain || (h.crossDomain = /^([\w-]+:)?\/\/([^\/]+)/.test(h.url) && RegExp.$2 != window.location.host);
        h.url || (h.url = window.location.toString());
        d(h);
        var n = h.dataType,
        s = /\?.+=\?/.test(h.url);
        s && (n = "jsonp");
        if (!1 === h.cache || (!g || !0 !== g.cache) && ("script" == n || "jsonp" == n)) h.url = c(h.url, "_=" + Date.now());
        if ("jsonp" == n) return s || (h.url = c(h.url, h.jsonp ? h.jsonp + "=?": !1 === h.jsonp ? "": "callback=?")),
        a.ajaxJSONP(h, k);
        g = h.accepts[n];
        var q = {},
        s = function(a, c) {
            q[a.toLowerCase()] = [a, c]
        },
        z = /^([\w-]+:)\/\//.test(h.url) ? RegExp.$1: window.location.protocol,
        v = h.xhr(),
        E = v.setRequestHeader,
        x;
        k && k.promise(v);
        h.crossDomain || s("X-Requested-With", "XMLHttpRequest");
        s("Accept", g || "*/*");
        if (g = h.mimeType || g) - 1 < g.indexOf(",") && (g = g.split(",", 2)[0]),
        v.overrideMimeType && v.overrideMimeType(g);
        if (h.contentType || !1 !== h.contentType && h.data && "GET" != h.type.toUpperCase()) s("Content-Type", h.contentType || "application/x-www-form-urlencoded");
        if (h.headers) for (u in h.headers) s(u, h.headers[u]);
        v.setRequestHeader = s;
        v.onreadystatechange = function() {
            if (4 == v.readyState) {
                v.onreadystatechange = l;
                clearTimeout(x);
                var c, d = !1;
                if (200 <= v.status && 300 > v.status || 304 == v.status || 0 == v.status && "file:" == z) {
                    n = n || A(h.mimeType || v.getResponseHeader("content-type"));
                    c = v.responseText;
                    try {
                        "script" == n ? (0, eval)(c) : "xml" == n ? c = v.responseXML: "json" == n && (c = D.test(c) ? null: a.parseJSON(c))
                    } catch(f) {
                        d = f
                    }
                    d ? e(d, "parsererror", v, h, k) : m(c, v, h, k)
                } else e(v.statusText || null, v.status ? "error": "abort", v, h, k)
            }
        };
        if (!1 === p(v, h)) return v.abort(),
        e(null, "abort", v, h, k),
        v;
        if (h.xhrFields) for (u in h.xhrFields) v[u] = h.xhrFields[u];
        v.open(h.type, h.url, "async" in h ? h.async: !0, h.username, h.password);
        for (u in q) E.apply(v, q[u]);
        0 < h.timeout && (x = setTimeout(function() {
            v.onreadystatechange = l;
            v.abort();
            e(null, "timeout", v, h, k)
        },
        h.timeout));
        v.send(h.data ? h.data: null);
        return v
    };
    a.get = function() {
        return a.ajax(s.apply(null, arguments))
    };
    a.post = function() {
        var c = s.apply(null, arguments);
        c.type = "POST";
        return a.ajax(c)
    };
    a.getJSON = function() {
        var c = s.apply(null, arguments);
        c.dataType = "json";
        return a.ajax(c)
    };
    a.fn.load = function(c, d, e) {
        if (!this.length) return this;
        var f = this,
        g = c.split(/\s/),
        h;
        c = s(c, d, e);
        var k = c.success;
        1 < g.length && (c.url = g[0], h = g[1]);
        c.success = function(c) {
            f.html(h ? a("<div>").html(c.replace(n, "")).find(h) : c);
            k && k.apply(f, arguments)
        };
        a.ajax(c);
        return this
    };
    var x = encodeURIComponent;
    a.param = function(a, c) {
        var d = [];
        d.add = function(a, c) {
            this.push(x(a) + "=" + x(c))
        };
        k(d, a, c);
        return d.join("&").replace(/%20/g, "+")
    }
})(Zepto); (function(a) {
    a.fn.serializeArray = function() {
        var g, f, p = [];
        a([].slice.call(this.get(0).elements)).each(function() {
            g = a(this);
            f = g.attr("type");
            this.name && "fieldset" != this.nodeName.toLowerCase() && (!this.disabled && "submit" != f && "reset" != f && "button" != f && ("radio" != f && "checkbox" != f || this.checked)) && p.push({
                name: g.attr("name"),
                value: g.val()
            })
        });
        return p
    };
    a.fn.serialize = function() {
        var a = [];
        this.serializeArray().forEach(function(f) {
            a.push(encodeURIComponent(f.name) + "=" + encodeURIComponent(f.value))
        });
        return a.join("&")
    };
    a.fn.submit = function(g) {
        g ? this.bind("submit", g) : this.length && (g = a.Event("submit"), this.eq(0).trigger(g), g.isDefaultPrevented() || this.get(0).submit());
        return this
    }
})(Zepto); (function(a, g) {
    var f = "",
    p, m = window.document.createElement("div"),
    e = /^((translate|rotate|scale)(X|Y|Z|3d)?|matrix(3d)?|perspective|skew(X|Y)?)$/i,
    q,
    l,
    A,
    c,
    d,
    s,
    k,
    z,
    C,
    w = {};
    a.each({
        Webkit: "webkit",
        Moz: "",
        O: "o"
    },
    function(a, c) {
        if (m.style[a + "TransitionProperty"] !== g) return f = "-" + a.toLowerCase() + "-",
        p = c,
        !1
    });
    q = f + "transform";
    w[l = f + "transition-property"] = w[A = f + "transition-duration"] = w[d = f + "transition-delay"] = w[c = f + "transition-timing-function"] = w[s = f + "animation-name"] = w[k = f + "animation-duration"] = w[C = f + "animation-delay"] = w[z = f + "animation-timing-function"] = "";
    a.fx = {
        off: p === g && m.style.transitionProperty === g,
        speeds: {
            _default: 400,
            fast: 200,
            slow: 600
        },
        cssPrefix: f,
        transitionEnd: p ? p + "TransitionEnd": "transitionend",
        animationEnd: p ? p + "AnimationEnd": "animationend"
    };
    a.fn.animate = function(c, d, e, f, k) {
        a.isFunction(d) && (f = d, d = e = g);
        a.isFunction(e) && (f = e, e = g);
        a.isPlainObject(d) && (e = d.easing, f = d.complete, k = d.delay, d = d.duration);
        d && (d = ("number" == typeof d ? d: a.fx.speeds[d] || a.fx.speeds._default) / 1E3);
        k && (k = parseFloat(k) / 1E3);
        return this.anim(c, d, e, f, k)
    };
    a.fn.anim = function(f, n, h, p, m) {
        var B, D = {},
        x, r = "",
        t = this,
        G, H = a.fx.transitionEnd,
        J = !1;
        n === g && (n = a.fx.speeds._default / 1E3);
        m === g && (m = 0);
        a.fx.off && (n = 0);
        if ("string" == typeof f) D[s] = f,
        D[k] = n + "s",
        D[C] = m + "s",
        D[z] = h || "linear",
        H = a.fx.animationEnd;
        else {
            x = [];
            for (B in f) e.test(B) ? r += B + "(" + f[B] + ") ": (D[B] = f[B], x.push(B.replace(/([a-z])([A-Z])/, "$1-$2").toLowerCase()));
            r && (D[q] = r, x.push(q));
            0 < n && "object" === typeof f && (D[l] = x.join(", "), D[A] = n + "s", D[d] = m + "s", D[c] = h || "linear")
        }
        G = function(c) {
            if ("undefined" !== typeof c) {
                if (c.target !== c.currentTarget) return;
                a(c.target).unbind(H, G)
            } else a(this).unbind(H, G);
            J = !0;
            a(this).css(w);
            p && p.call(this)
        };
        0 < n && (this.bind(H, G), setTimeout(function() {
            J || G.call(t)
        },
        1E3 * n + 25));
        this.size() && this.get(0).clientLeft;
        this.css(D);
        0 >= n && setTimeout(function() {
            t.each(function() {
                G.call(this)
            })
        },
        0);
        return this
    };
    m = null
})(Zepto); (function(a, g) {
    function f(e, f, c, d, s) {
        "function" == typeof f && !s && (s = f, f = g);
        c = {
            opacity: c
        };
        d && (c.scale = d, e.css(a.fx.cssPrefix + "transform-origin", "0 0"));
        return e.animate(c, f, null, s)
    }
    function p(g, m, c, d) {
        return f(g, m, 0, c,
        function() {
            e.call(a(this));
            d && d.call(this)
        })
    }
    var m = a.fn.show,
    e = a.fn.hide,
    q = a.fn.toggle;
    a.fn.show = function(a, e) {
        m.call(this);
        a === g ? a = 0 : this.css("opacity", 0);
        return f(this, a, 1, "1,1", e)
    };
    a.fn.hide = function(a, f) {
        return a === g ? e.call(this) : p(this, a, "0,0", f)
    };
    a.fn.toggle = function(e, f) {
        return e === g || "boolean" == typeof e ? q.call(this, e) : this.each(function() {
            var c = a(this);
            c["none" == c.css("display") ? "show": "hide"](e, f)
        })
    };
    a.fn.fadeTo = function(a, e, c) {
        return f(this, a, e, null, c)
    };
    a.fn.fadeIn = function(a, e) {
        var c = this.css("opacity");
        0 < c ? this.css("opacity", 0) : c = 1;
        return m.call(this).fadeTo(a, c, e)
    };
    a.fn.fadeOut = function(a, e) {
        return p(this, a, null, e)
    };
    a.fn.fadeToggle = function(e, f) {
        return this.each(function() {
            var c = a(this);
            c[0 == c.css("opacity") || "none" == c.css("display") ? "fadeIn": "fadeOut"](e, f)
        })
    }
})(Zepto); (function(a) {
    function g() {
        c = null;
        e.last && (e.el.trigger("longTap"), e = {})
    }
    function f() {
        q && clearTimeout(q);
        l && clearTimeout(l);
        A && clearTimeout(A);
        c && clearTimeout(c);
        q = l = A = c = null;
        e = {}
    }
    function p(a) {
        return ("touch" == a.pointerType || a.pointerType == a.MSPOINTER_TYPE_TOUCH) && a.isPrimary
    }
    function m(a, c) {
        return a.type == "pointer" + c || a.type.toLowerCase() == "mspointer" + c
    }
    var e = {},
    q, l, A, c, d;
    a(document).ready(function() {
        var s, k, z = 0,
        C = 0,
        w, u;
        "MSGesture" in window && (d = new MSGesture, d.target = document.body);
        a(document).bind("MSGestureEnd",
        function(a) {
            if (a = 1 < a.velocityX ? "Right": -1 > a.velocityX ? "Left": 1 < a.velocityY ? "Down": -1 > a.velocityY ? "Up": null) e.el.trigger("swipe"),
            e.el.trigger("swipe" + a)
        }).on("touchstart MSPointerDown pointerdown",
        function(f) {
            if (! (u = m(f, "down")) || p(f)) w = u ? f: f.touches[0],
            f.touches && 1 === f.touches.length && e.x2 && (e.x2 = void 0, e.y2 = void 0),
            s = Date.now(),
            k = s - (e.last || s),
            e.el = a("tagName" in w.target ? w.target: w.target.parentNode),
            q && clearTimeout(q),
            e.x1 = w.pageX,
            e.y1 = w.pageY,
            0 < k && 250 >= k && (e.isDoubleTap = !0),
            e.last = s,
            c = setTimeout(g, 750),
            d && u && d.addPointer(f.pointerId)
        }).on("touchmove MSPointerMove pointermove",
        function(a) {
            if (! (u = m(a, "move")) || p(a)) w = u ? a: a.touches[0],
            c && clearTimeout(c),
            c = null,
            e.x2 = w.pageX,
            e.y2 = w.pageY,
            z += Math.abs(e.x1 - e.x2),
            C += Math.abs(e.y1 - e.y2)
        }).on("touchend MSPointerUp pointerup",
        function(d) {
            if (! (u = m(d, "up")) || p(d)) c && clearTimeout(c),
            c = null,
            e.x2 && 30 < Math.abs(e.x1 - e.x2) || e.y2 && 30 < Math.abs(e.y1 - e.y2) ? A = setTimeout(function() {
                e.el.trigger("swipe");
                e.el.trigger("swipe" + (Math.abs(e.x1 - e.x2) >= Math.abs(e.y1 - e.y2) ? 0 < e.x1 - e.x2 ? "Left": "Right": 0 < e.y1 - e.y2 ? "Up": "Down"));
                e = {}
            },
            0) : "last" in e && (30 > z && 30 > C ? l = setTimeout(function() {
                var c = a.Event("tap");
                c.cancelTouch = f;
                e.el.trigger(c);
                e.isDoubleTap ? (e.el && e.el.trigger("doubleTap"), e = {}) : q = setTimeout(function() {
                    q = null;
                    e.el && e.el.trigger("singleTap");
                    e = {}
                },
                250)
            },
            0) : e = {}),
            z = C = 0
        }).on("touchcancel MSPointerCancel pointercancel", f);
        a(window).on("scroll", f)
    });
    "swipe swipeLeft swipeRight swipeUp swipeDown doubleTap tap singleTap longTap".split(" ").forEach(function(c) {
        a.fn[c] = function(a) {
            return this.on(c, a)
        }
    })
})(Zepto); (function(a, g) {
    var f = window.KingSoft = window.K || {},
    p = document.createElement("div").style,
    m = {};
    window.K = f;
    f.supports = f.supports || m;
    var e = f.camelCase = function(a) {
        return a.replace(/^-ms-/, "ms-").replace(/-([a-z]|[0-9])/ig,
        function(a, c) {
            return (c + "").toUpperCase()
        })
    };
    f.uncamelCase = function(a) {
        return a.replace(/([A-Z]|^ms)/g, "-$1").toLowerCase()
    };
    var q = f.cssVendor = function() {
        var a = ["-webkit-", "-moz-", "-ms-", ""],
        d = 0;
        do
        if (e(a[d] + "transform") in p) return a[d];
        while (++d < a.length);
        return ""
    } ();
    f.transitionend = {
        "-webkit-": "webkitTransitionEnd",
        "-moz-": "transitionend",
        "-ms-": "MSTransitionEnd",
        "": "transitionend"
    } [q];
    var l = f.isCSS = function(a) {
        var d = e(a);
        a = e(q + a);
        return d in p && d || a in p && a || ""
    };
    f.isArrayLike = function(a) {
        var d = f.type(a);
        return !! a && "function" != d && "string" != d && (0 === a.length || a.length && a.length - 1 in a)
    };
    var A = f.type = function() {
        for (var a = "Boolean Number String Function Array Date RegExp Object Error Undefined Null".split(" "), d = 0, e = {}; d < a.length;) f["is" + a[d]] = function() {
            var e = a[d].toLowerCase();
            return function(a) {
                return A(a) === e
            }
        } (),
        e["[object " + a[d] + "]"] = a[d++].toLowerCase();
        return function(a) {
            return null == a ? String(a) : "object" == typeof a ? e[e.toString.call(a)] || "object": typeof a
        }
    } ();
    f.getQueryValue = function(a, d) {
        var e = decodeURIComponent(d || location.search).match(RegExp(a + "=([^&]*)"));
        return e && e[1] || ""
    };
    f.getAbsUrl = function(a) {
        var d = document.createElement("a");
        d.href = a;
        return /^http/i.test(d.href) ? d.href: d.getAttribute("href", 4)
    };
    f.htmlencode = function(a) {
        return String(a).replace(/&(?![\w#]+;)|[<>"']/g,
        function(a) {
            return {
                "<": "&#60;",
                ">": "&#62;",
                '"': "&#34;",
                "'": "&#39;",
                "&": "&#38;"
            } [a] || ""
        })
    };
    a.each("transform transition animation perspective border-image border-radius box-shadow background-size background-clip text-shadow min-height opacity".split(" "),
    function(a, d) {
        m[e(d)] = !!l(d)
    });
    m.touch = "createTouch" in document || "ontouchstart" in window;
    m.canvas = "function" == typeof document.createElement("canvas").getContext;
    m.svg = !!document.createElementNS && !!document.createElementNS("http://www.w3.org/2000/svg", "svg").createSVGRect;
    f.transform = l("transform");
    f.transition = l("transition");
    f.animation = l("animation");
    f.EVENTS = "PointerEvent" in window ? "pointerdown pointermove pointerup pointercancel": m.touch ? "touchstart touchmove touchend touchcancel": "mousedown mousemove mouseup";
    m.touch || a(document).click(function(c) {
        var d = a.Event("tap");
        a(c.target || c.srcElement).trigger(d)
    });
    f.Panel = createClass(function() {
        this.zIndex = this.constructor.prototype.zIndex++;
        this.mask = a('<div class="Mask" style="z-index:' + this.zIndex + ';"></div>');
        this.frame = a('<div class="Panel" style="z-index:' + this.zIndex + ';"></div>')
    },
    {
        zIndex: 100,
        setContent: function(a) {
            this.frame.html(a);
            return this
        },
        show: function(c) {
            a.contains(document.body, this.frame[0]) || (this.mask.appendTo("body"), this.frame.appendTo("body"));
            this.frame.fadeIn(400, c);
            this.mask.fadeIn(400);
            return this
        },
        hide: function(a) {
            this.frame.fadeOut(400, a);
            this.mask.fadeOut(400);
            return this
        },
        destroy: function(a) {
            this.hide(function() {
                this.frame.remove();
                this.mask.remove();
                a && a()
            }.bind(this))
        }
    });
    a.ajaxSettings.cache = !1;
    a.ajaxSettings.dataType = "json";
    a.ajaxSettings.type = "POST";
    f.alert = function(a) {
        var d = f.Panel().setContent('<a class="close"></a><div class="text">' + a + "</div>");
        d.frame.addClass("syswin").find(".close").tap(function() {
            d.destroy()
        });
        d.mask.tap(function() {
            d.destroy()
        });
        d.show()
    };
    a(document).on("click", "[trace]",
    function(c) {
        var d = this.nodeName.toLowerCase(),
        e = a(this),
        f = e.attr("trace").split(/,\s*/g),
        g = this.href || "";
        _hmt.push(["_trackEvent"].concat(f, 1));
        "a" == d && ("_blank" != e.attr("target") && g && !/^javascript/i.test(g)) && setTimeout(function() {
            location.href = g
        },
        150);
        c.preventDefault()
    })
})(Zepto);
