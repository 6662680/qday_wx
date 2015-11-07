var Util = (function() {
    var b;
    var a = function() {
        this.ajaxCount = 0;
        b = this
    };
    a.prototype = {
        getQueryString: function(c) {
            var e = new RegExp("(^|&)" + c + "=([^&]*)(&|$)", "i");
            var d = window.location.search.substr(1).match(e);
            if (d != null) {
                return unescape(d[2])
            }
            return ""
        },
        info: function(e) {
            if (top != window) {
                top.Util.info(e);
                return
            }
            $(".pop_info").remove();
            var c = document.createElement("div");
            c.className = "pop_info";
            c.style.cssText = "position:fixed;top:20px;left:0;width:100%;text-align:center;z-index:10000;";
            var d = document.createElement("div");
            d.style.cssText = "display:inline-block;background-color:rgba(0,0,0,0.8);border-radius:3px;padding:5px 8px;color:white;";
            d.innerHTML = e;
            c.appendChild(d);
            top.document.body.appendChild(c);
            setTimeout(function() {
                top.document.body.removeChild(c)
            },
            3000)
        },
        ajax: function(d, e, c) {
            this.showLoading();
            $.ajax({
                type: "POST",
                url: e.url,
                data: e.data,
                dataType: "text",
                success: function(f) {
                    if (f) {
                        f = JSON.parse(f)
                    }
                    b.hideLoading();
                    if (c) {
                        if (d.$timeout) {
                            d.$timeout(function() {
                                c(f)
                            })
                        } else {
                            c(f)
                        }
                    }
                },
                error: function(f, g) {
                    console.log(g);
                    b.hideLoading();
                    b.info("请重试")
                }
            })
        },
        ajaxNomu: function(d, c) {
            this.showLoading();
            $.ajax({
                type: "POST",
                url: d.url,
                data: d.data,
                dataType: "text",
                success: function(e) {
                    if (e) {
                        e = JSON.parse(e)
                    }
                    b.hideLoading();
                    if (c) {
                        c(e)
                    }
                },
                error: function(f, g) {
                    console.log(g);
                    b.hideLoading();
                    b.info("请重试")
                }
            })
        },
        showLoading: function() {
            this.ajaxCount++;
            var c = top.document.getElementById("loading");
            if (!c) {
                c = document.createElement("div");
                c.id = "loading";
                c.innerHTML = '<img src="/addons/hx_read/template/style/img/loading.gif" />';
                top.document.body.appendChild(c)
            }
        },
        hideLoading: function() {
            this.ajaxCount--;
            if (this.ajaxCount <= 0) {
                var c = top.document.getElementById("loading");
                if (c) {
                    top.document.body.removeChild(c)
                }
                this.ajaxCount = 0
            }
        },
        alert: function(c) {
            alert(c)
        },
        isMobile: function(d) {
            var c = /^(1\d{10})$/;
            return c.test(d)
        },
        isInteger: function(d) {
            var c = /^(\-?\d+)$/;
            return c.test(d)
        },
        isEmpty: function(c) {
            return ! c || /^\s*$/.test(c)
        },
        zoomImg: function(e) {
            var c = document.createElement("div");
            c.style.cssText = "position:fixed;top:0;bottom:0;right:0;left:0;background-color:rgba(0,0,0,0.9);padding:20px 0;";
            c.onclick = function() {
                $(this).remove()
            };
            var d = document.createElement("div");
            d.style.cssText = "width:100%;height:100%;background:url(" + e + ") center center no-repeat;background-size:contain;";
            c.appendChild(d);
            document.body.appendChild(c)
        },
        getDateString: function(e) {
            var d = /(\d+)/.exec(e);
            if (d) {
                var c = new Date(parseFloat(d[1]));
                return c.getFullYear() + "-" + (c.getMonth() + 1) + "-" + c.getDate()
            }
            return ""
        },
        getDateString2: function(e) {
            var d = /(\d+)/.exec(e);
            if (d) {
                var c = new Date(parseFloat(d[1]));
                return c.getFullYear() + "," + (c.getMonth() + 1) + "," + c.getDate() + "," + c.getHours() + "," + c.getMinutes() + "," + c.getSeconds()
            }
            return ""
        },
        callPhone: function(c) {
            location.href = "tel:" + c
        },

    };
    return new a()
})();
var LocalStorage = (function() {
    var a = function() {
        this.ajaxCount = 0
    };
    a.prototype = {
        set: function(b, c) {
            localStorage[b] = c
        },
        get: function(b) {
            return localStorage[b]
        }
    };
    return new a()
})();
var DataCache = {};
DataCache.userInfo = null;
DataCache.currentProject = null;
DataCache.projectList = [];
DataCache.search = {};
DataCache.area = {}; (function(dt, cf, cp) {
    function U(a) {
        return function() {
            var p = arguments[0],
            s,
            p = "[" + (a ? a + ":": "") + p + "] http://errors.angularjs.org/undefined/" + (a ? a + "/": "") + p;
            for (s = 1; s < arguments.length; s++) {
                p = p + (1 == s ? "?": "&") + "p" + (s - 1) + "=" + encodeURIComponent("function" == typeof arguments[s] ? arguments[s].toString().replace(/ \{[\s\S]*$/, "") : "undefined" == typeof arguments[s] ? "undefined": "string" != typeof arguments[s] ? JSON.stringify(arguments[s]) : arguments[s])
            }
            return Error(p)
        }
    }
    function bF(s) {
        if (null == s || du(s)) {
            return ! 1
        }
        var p = s.length;
        return 1 === s.nodeType && p ? !0: aq(s) || aL(s) || 0 === p || "number" === typeof p && 0 < p && p - 1 in s
    }
    function bV(s, p, v) {
        var w;
        if (s) {
            if (ag(s)) {
                for (w in s) {
                    "prototype" != w && ("length" != w && "name" != w && s.hasOwnProperty(w)) && p.call(v, s[w], w)
                }
            } else {
                if (s.forEach && s.forEach !== bV) {
                    s.forEach(p, v)
                } else {
                    if (bF(s)) {
                        for (w = 0; w < s.length; w++) {
                            p.call(v, s[w], w)
                        }
                    } else {
                        for (w in s) {
                            s.hasOwnProperty(w) && p.call(v, s[w], w)
                        }
                    }
                }
            }
        }
        return s
    }
    function bp(s) {
        var p = [],
        v;
        for (v in s) {
            s.hasOwnProperty(v) && p.push(v)
        }
        return p.sort()
    }
    function aH(s, p, v) {
        for (var w = bp(s), x = 0; x < w.length; x++) {
            p.call(v, s[w[x]], w[x])
        }
        return w
    }
    function bx(a) {
        return function(p, s) {
            a(s, p)
        }
    }
    function cV() {
        for (var s = aV.length, p; s;) {
            s--;
            p = aV[s].charCodeAt(0);
            if (57 == p) {
                return aV[s] = "A",
                aV.join("")
            }
            if (90 == p) {
                aV[s] = "0"
            } else {
                return aV[s] = String.fromCharCode(p + 1),
                aV.join("")
            }
        }
        aV.unshift("0");
        return aV.join("")
    }
    function bG(s, p) {
        p ? s.$$hashKey = p: delete s.$$hashKey
    }
    function aB(s) {
        var p = s.$$hashKey;
        bV(arguments, 
        function(v) {
            v !== s && bV(v, 
            function(w, x) {
                s[x] = w
            })
        });
        bG(s, p);
        return s
    }
    function c4(a) {
        return parseInt(a, 10)
    }
    function aR(s, p) {
        return aB(new(aB(function() {},
        {
            prototype: s
        })), p)
    }
    function cS() {}
    function dE(a) {
        return a
    }
    function g(a) {
        return function() {
            return a
        }
    }
    function dD(a) {
        return "undefined" == typeof a
    }
    function c3(a) {
        return "undefined" != typeof a
    }
    function cq(a) {
        return null != a && "object" == typeof a
    }
    function aq(a) {
        return "string" == typeof a
    }
    function bO(a) {
        return "number" == typeof a
    }
    function aN(a) {
        return "[object Date]" == c6.apply(a)
    }
    function aL(a) {
        return "[object Array]" == c6.apply(a)
    }
    function ag(a) {
        return "function" == typeof a
    }
    function dk(a) {
        return "[object RegExp]" == c6.apply(a)
    }
    function du(a) {
        return a && a.document && a.location && a.alert && a.setInterval
    }
    function a0(a) {
        return a && (a.nodeName || a.on && a.find)
    }
    function ba(s, p, v) {
        var w = [];
        bV(s, 
        function(a, x, z) {
            w.push(p.call(v, a, x, z))
        });
        return w
    }
    function dv(s, p) {
        if (s.indexOf) {
            return s.indexOf(p)
        }
        for (var v = 0; v < s.length; v++) {
            if (p === s[v]) {
                return v
            }
        }
        return - 1
    }
    function aW(s, p) {
        var v = dv(s, p);
        0 <= v && s.splice(v, 1);
        return p
    }
    function ar(s, p) {
        if (du(s) || s && s.$evalAsync && s.$watch) {
            throw a6("cpws")
        }
        if (p) {
            if (s === p) {
                throw a6("cpi")
            }
            if (aL(s)) {
                for (var v = p.length = 0; v < s.length; v++) {
                    p.push(ar(s[v]))
                }
            } else {
                v = p.$$hashKey;
                bV(p, 
                function(a, x) {
                    delete p[x]
                });
                for (var w in s) {
                    p[w] = ar(s[w])
                }
                bG(p, v)
            }
        } else { (p = s) && (aL(s) ? p = ar(s, []) : aN(s) ? p = new Date(s.getTime()) : dk(s) ? p = RegExp(s.source) : cq(s) && (p = ar(s, {})))
        }
        return p
    }
    function bi(s, p) {
        p = p || {};
        for (var v in s) {
            s.hasOwnProperty(v) && "$$" !== v.substr(0, 2) && (p[v] = s[v])
        }
        return p
    }
    function h(s, p) {
        if (s === p) {
            return ! 0
        }
        if (null === s || null === p) {
            return ! 1
        }
        if (s !== s && p !== p) {
            return ! 0
        }
        var v = typeof s,
        w;
        if (v == typeof p && "object" == v) {
            if (aL(s)) {
                if (!aL(p)) {
                    return ! 1
                }
                if ((v = s.length) == p.length) {
                    for (w = 0; w < v; w++) {
                        if (!h(s[w], p[w])) {
                            return ! 1
                        }
                    }
                    return ! 0
                }
            } else {
                if (aN(s)) {
                    return aN(p) && s.getTime() == p.getTime()
                }
                if (dk(s) && dk(p)) {
                    return s.toString() == p.toString()
                }
                if (s && s.$evalAsync && s.$watch || p && p.$evalAsync && p.$watch || du(s) || du(p) || aL(p)) {
                    return ! 1
                }
                v = {};
                for (w in s) {
                    if ("$" !== w.charAt(0) && !ag(s[w])) {
                        if (!h(s[w], p[w])) {
                            return ! 1
                        }
                        v[w] = !0
                    }
                }
                for (w in p) {
                    if (!v.hasOwnProperty(w) && "$" !== w.charAt(0) && p[w] !== cp && !ag(p[w])) {
                        return ! 1
                    }
                }
                return ! 0
            }
        }
        return ! 1
    }
    function bY(s, p) {
        var v = 2 < arguments.length ? cJ.call(arguments, 2) : [];
        return ! ag(p) || p instanceof RegExp ? p: v.length ? 
        function() {
            return arguments.length ? p.apply(s, v.concat(cJ.call(arguments, 0))) : p.apply(s, v)
        }: function() {
            return arguments.length ? p.apply(s, arguments) : p.call(s)
        }
    }
    function br(s, p) {
        var v = p;
        "string" === typeof s && "$" === s.charAt(0) ? v = cp: du(p) ? v = "$WINDOW": p && cf === p ? v = "$DOCUMENT": p && (p.$evalAsync && p.$watch) && (v = "$SCOPE");
        return v
    }
    function bM(s, p) {
        return "undefined" === typeof s ? cp: JSON.stringify(s, br, p ? "  ": null)
    }
    function bP(a) {
        return aq(a) ? JSON.parse(a) : a
    }
    function be(a) {
        a && 0 !== a.length ? (a = o("" + a), a = !("f" == a || "0" == a || "false" == a || "no" == a || "n" == a || "[]" == a)) : a = !1;
        return a
    }
    function aC(s) {
        s = dh(s).clone();
        try {
            s.html("")
        } catch(p) {}
        var v = dh("<div>").append(s).html();
        try {
            return 3 === s[0].nodeType ? o(v) : v.match(/^(<[^>]+>)/)[1].replace(/^<([\w\-]+)/, 
            function(x, z) {
                return "<" + o(z)
            })
        } catch(w) {
            return o(v)
        }
    }
    function bZ(s) {
        try {
            return decodeURIComponent(s)
        } catch(p) {}
    }
    function b9(s) {
        var p = {},
        v,
        w;
        bV((s || "").split("&"), 
        function(a) {
            a && (v = a.split("="), w = bZ(v[0]), c3(w) && (a = c3(v[1]) ? bZ(v[1]) : !0, p[w] ? aL(p[w]) ? p[w].push(a) : p[w] = [p[w], a] : p[w] = a))
        });
        return p
    }
    function cj(s) {
        var p = [];
        bV(s, 
        function(a, v) {
            aL(a) ? bV(a, 
            function(w) {
                p.push(cU(v, !0) + (!0 === w ? "": "=" + cU(w, !0)))
            }) : p.push(cU(v, !0) + (!0 === a ? "": "=" + cU(a, !0)))
        });
        return p.length ? p.join("&") : ""
    }
    function b8(a) {
        return cU(a, !0).replace(/%26/gi, "&").replace(/%3D/gi, "=").replace(/%2B/gi, "+")
    }
    function cU(s, p) {
        return encodeURIComponent(s).replace(/%40/gi, "@").replace(/%3A/gi, ":").replace(/%24/g, "$").replace(/%2C/gi, ",").replace(/%20/g, p ? "%20": "+")
    }
    function bz(s, p) {
        function v(E) {
            E && w.push(E)
        }
        var w = [s],
        x,
        z,
        B = ["ng:app", "ng-app", "x-ng-app", "data-ng-app"],
        D = /\sng[:\-]app(:\s*([\w\d_]+);?)?\s/;
        bV(B, 
        function(E) {
            B[E] = !0;
            v(cf.getElementById(E));
            E = E.replace(":", "\\:");
            s.querySelectorAll && (bV(s.querySelectorAll("." + E), v), bV(s.querySelectorAll("." + E + "\\:"), v), bV(s.querySelectorAll("[" + E + "]"), v))
        });
        bV(w, 
        function(E) {
            if (!x) {
                var F = D.exec(" " + E.className + " ");
                F ? (x = E, z = (F[2] || "").replace(/\s+/g, ",")) : bV(E.attributes, 
                function(a) { ! x && B[a.name] && (x = E, z = a.value)
                })
            }
        });
        x && p(x, z ? [z] : [])
    }
    function cu(s, p) {
        var v = function() {
            s = dh(s);
            if (s.injector()) {
                var a = s[0] === cf ? "document": aC(s);
                throw a6("btstrpd", a)
            }
            p = p || [];
            p.unshift(["$provide", 
            function(x) {
                x.value("$rootElement", s)
            }]);
            p.unshift("ng");
            a = cD(p);
            a.invoke(["$rootScope", "$rootElement", "$compile", "$injector", "$animate", 
            function(x, z, B, D, E) {
                x.$apply(function() {
                    z.data("$injector", D);
                    B(z)(x)
                });
                E.enabled(!0)
            }]);
            return a
        },
        w = /^NG_DEFER_BOOTSTRAP!/;
        if (dt && !w.test(dt.name)) {
            return v()
        }
        dt.name = dt.name.replace(w, "");
        dF.resumeBootstrap = function(a) {
            bV(a, 
            function(x) {
                p.push(x)
            });
            v()
        }
    }
    function b(s, p) {
        p = p || "_";
        return s.replace(bI, 
        function(a, v) {
            return (v ? p: "") + a.toLowerCase()
        })
    }
    function ci(s, p, v) {
        if (!s) {
            throw a6("areq", p || "?", v || "required")
        }
        return s
    }
    function bn(s, p, v) {
        v && aL(s) && (s = s[s.length - 1]);
        ci(ag(s), p, "not a function, got " + (s && "object" == typeof s ? s.constructor.name || "Object": typeof s));
        return s
    }
    function bW(s, p) {
        if ("hasOwnProperty" === s) {
            throw a6("badname", p)
        }
    }
    function ct(s, p, v) {
        if (!p) {
            return s
        }
        p = p.split(".");
        for (var w, x = s, z = p.length, B = 0; B < z; B++) {
            w = p[B],
            s && (s = (x = s)[w])
        }
        return ! v && ag(s) ? bY(x, s) : s
    }
    function bR(s) {
        function p(w, x, z) {
            return w[x] || (w[x] = z())
        }
        var v = U("$injector");
        return p(p(s, "angular", Object), "module", 
        function() {
            var a = {};
            return function(w, x, z) {
                bW(w, "module");
                x && a.hasOwnProperty(w) && (a[w] = null);
                return p(a, w, 
                function() {
                    function B(H, J, Q) {
                        return function() {
                            D[Q || "push"]([H, J, arguments]);
                            return G
                        }
                    }
                    if (!x) {
                        throw v("nomod", w)
                    }
                    var D = [],
                    E = [],
                    F = B("$injector", "invoke"),
                    G = {
                        _invokeQueue: D,
                        _runBlocks: E,
                        requires: x,
                        name: w,
                        provider: B("$provide", "provider"),
                        factory: B("$provide", "factory"),
                        service: B("$provide", "service"),
                        value: B("$provide", "value"),
                        constant: B("$provide", "constant", "unshift"),
                        animation: B("$animateProvider", "register"),
                        filter: B("$filterProvider", "register"),
                        controller: B("$controllerProvider", "register"),
                        directive: B("$compileProvider", "directive"),
                        config: F,
                        run: function(H) {
                            E.push(H);
                            return this
                        }
                    };
                    z && F(z);
                    return G
                })
            }
        })
    }
    function bv(a) {
        return a.replace(b1, 
        function(p, s, v, w) {
            return w ? v.toUpperCase() : v
        }).replace(cb, "Moz$1")
    }
    function cC(s, p, v, w) {
        function x(a) {
            var B = v && a ? [this.filter(a)] : [this],
            F = p,
            D,
            E,
            J,
            H,
            G,
            Q;
            if (!w || null != a) {
                for (; B.length;) {
                    for (D = B.shift(), E = 0, J = D.length; E < J; E++) {
                        for (H = dh(D[E]), F ? H.triggerHandler("$destroy") : F = !F, G = 0, H = (Q = H.children()).length; G < H; G++) {
                            B.push(r(Q[G]))
                        }
                    }
                }
            }
            return z.apply(this, arguments)
        }
        var z = r.fn[s],
        z = z.$original || z;
        x.$original = z;
        r.fn[s] = x
    }
    function a4(s) {
        if (s instanceof a4) {
            return s
        }
        if (! (this instanceof a4)) {
            if (aq(s) && "<" != s.charAt(0)) {
                throw cL("nosel")
            }
            return new a4(s)
        }
        if (aq(s)) {
            var p = cf.createElement("div");
            p.innerHTML = "<div>&#160;</div>" + s;
            p.removeChild(p.firstChild);
            cW(this, p.childNodes);
            dh(cf.createDocumentFragment()).append(this)
        } else {
            cW(this, s)
        }
    }
    function c7(a) {
        return a.cloneNode(!0)
    }
    function bE(s) {
        cM(s);
        var p = 0;
        for (s = s.childNodes || []; p < s.length; p++) {
            bE(s[p])
        }
    }
    function cX(s, p, v, w) {
        if (c3(w)) {
            throw cL("offargs")
        }
        var x = a5(s, "events");
        a5(s, "handle") && (dD(p) ? bV(x, 
        function(z, B) {
            dl(s, B, z);
            delete x[B]
        }) : bV(p.split(" "), 
        function(z) {
            dD(v) ? (dl(s, z, x[z]), delete x[z]) : aW(x[z] || [], v)
        }))
    }
    function cM(s, p) {
        var v = s[i],
        w = bN[v];
        w && (p ? delete bN[v].data[p] : (w.handle && (w.events.$destroy && w.handle({},
        "$destroy"), cX(s)), delete bN[v], s[i] = cp))
    }
    function a5(s, p, v) {
        var w = s[i],
        w = bN[w || -1];
        if (c3(v)) {
            w || (s[i] = w = ++cl, w = bN[w] = {}),
            w[p] = v
        } else {
            return w && w[p]
        }
    }
    function c8(s, p, v) {
        var w = a5(s, "data"),
        x = c3(v),
        z = !x && c3(p),
        B = z && !cq(p);
        w || B || a5(s, "data", w = {});
        if (x) {
            w[p] = v
        } else {
            if (z) {
                if (B) {
                    return w && w[p]
                }
                aB(w, p)
            } else {
                return w
            }
        }
    }
    function dw(s, p) {
        return s.getAttribute ? -1 < (" " + (s.getAttribute("class") || "") + " ").replace(/[\n\t]/g, " ").indexOf(" " + p + " ") : !1
    }
    function dG(s, p) {
        p && s.setAttribute && bV(p.split(" "), 
        function(v) {
            s.setAttribute("class", q((" " + (s.getAttribute("class") || "") + " ").replace(/[\n\t]/g, " ").replace(" " + q(v) + " ", " ")))
        })
    }
    function j(s, p) {
        if (p && s.setAttribute) {
            var v = (" " + (s.getAttribute("class") || "") + " ").replace(/[\n\t]/g, " ");
            bV(p.split(" "), 
            function(w) {
                w = q(w); - 1 === v.indexOf(" " + w + " ") && (v += w + " ")
            });
            s.setAttribute("class", q(v))
        }
    }
    function cW(s, p) {
        if (p) {
            p = p.nodeName || !c3(p.length) || du(p) ? [p] : p;
            for (var v = 0; v < p.length; v++) {
                s.push(p[v])
            }
        }
    }
    function dm(s, p) {
        return t(s, "$" + (p || "ngController") + "Controller")
    }
    function t(s, p, v) {
        s = dh(s);
        for (9 == s[0].nodeType && (s = s.find("html")); s.length;) {
            if ((v = s.data(p)) !== cp) {
                return v
            }
            s = s.parent()
        }
    }
    function dx(s, p) {
        var v = L[p.toLowerCase()];
        return v && dH[s.nodeName] && v
    }
    function cw(s, p) {
        var v = function(a, w) {
            a.preventDefault || (a.preventDefault = function() {
                a.returnValue = !1
            });
            a.stopPropagation || (a.stopPropagation = function() {
                a.cancelBubble = !0
            });
            a.target || (a.target = a.srcElement || cf);
            if (dD(a.defaultPrevented)) {
                var x = a.preventDefault;
                a.preventDefault = function() {
                    a.defaultPrevented = !0;
                    x.call(a)
                };
                a.defaultPrevented = !1
            }
            a.isDefaultPrevented = function() {
                return a.defaultPrevented || !1 == a.returnValue
            };
            bV(p[w || a.type], 
            function(z) {
                z.call(s, a)
            });
            8 >= b5 ? (a.preventDefault = null, a.stopPropagation = null, a.isDefaultPrevented = null) : (delete a.preventDefault, delete a.stopPropagation, delete a.isDefaultPrevented)
        };
        v.elem = s;
        return v
    }
    function K(s) {
        var p = typeof s,
        v;
        "object" == p && null !== s ? "function" == typeof(v = s.$$hashKey) ? v = s.$$hashKey() : v === cp && (v = s.$$hashKey = cV()) : v = s;
        return p + ":" + v
    }
    function bX(a) {
        bV(a, this.put, this)
    }
    function c(s) {
        var p,
        v;
        "function" == typeof s ? (p = s.$inject) || (p = [], s.length && (v = s.toString().replace(cF, ""), v = v.match(cO), bV(v[1].split(cZ), 
        function(a) {
            a.replace(da, 
            function(w, x, z) {
                p.push(z)
            })
        })), s.$inject = p) : aL(s) ? (v = s.length - 1, bn(s[v], "fn"), p = s.slice(0, v)) : bn(s, "fn", !0);
        return p
    }
    function cD(s) {
        function p(R) {
            return function(a, S) {
                if (cq(a)) {
                    bV(a, bx(R))
                } else {
                    return R(a, S)
                }
            }
        }
        function v(R, S) {
            bW(R, "service");
            if (ag(S) || aL(S)) {
                S = Q.instantiate(S)
            }
            if (!S.$get) {
                throw b7("pget", R)
            }
            return F[R + D] = S
        }
        function w(R, S) {
            return v(R, {
                $get: S
            })
        }
        function x(R) {
            var S = [];
            bV(R, 
            function(V) {
                if (!E.get(V)) {
                    E.put(V, !0);
                    try {
                        if (aq(V)) {
                            var W = ch(V);
                            S = S.concat(x(W.requires)).concat(W._runBlocks);
                            for (var X = W._invokeQueue, W = 0, Y = X.length; W < Y; W++) {
                                var dd = X[W],
                                dc = Q.get(dd[0]);
                                dc[dd[1]].apply(dc, dd[2])
                            }
                        } else {
                            ag(V) ? S.push(Q.invoke(V)) : aL(V) ? S.push(Q.invoke(V)) : bn(V, "module")
                        }
                    } catch(de) {
                        throw aL(V) && (V = V[V.length - 1]),
                        de.message && (de.stack && -1 == de.stack.indexOf(de.message)) && (de = de.message + "\n" + de.stack),
                        b7("modulerr", V, de.stack || de.message || de)
                    }
                }
            });
            return S
        }
        function z(R, S) {
            function V(a) {
                if (R.hasOwnProperty(a)) {
                    if (R[a] === B) {
                        throw b7("cdep", G.join(" <- "))
                    }
                    return R[a]
                }
                try {
                    return G.unshift(a),
                    R[a] = B,
                    R[a] = S(a)
                } finally {
                    G.shift()
                }
            }
            function W(X, Y, dc) {
                var dd = [],
                dO = c(X),
                dN,
                de,
                dP;
                de = 0;
                for (dN = dO.length; de < dN; de++) {
                    dP = dO[de];
                    if ("string" !== typeof dP) {
                        throw b7("itkn", dP)
                    }
                    dd.push(dc && dc.hasOwnProperty(dP) ? dc[dP] : V(dP))
                }
                X.$inject || (X = X[dN]);
                switch (Y ? -1: dd.length) {
                case 0:
                    return X();
                case 1:
                    return X(dd[0]);
                case 2:
                    return X(dd[0], dd[1]);
                case 3:
                    return X(dd[0], dd[1], dd[2]);
                case 4:
                    return X(dd[0], dd[1], dd[2], dd[3]);
                case 5:
                    return X(dd[0], dd[1], dd[2], dd[3], dd[4]);
                case 6:
                    return X(dd[0], dd[1], dd[2], dd[3], dd[4], dd[5]);
                case 7:
                    return X(dd[0], dd[1], dd[2], dd[3], dd[4], dd[5], dd[6]);
                case 8:
                    return X(dd[0], dd[1], dd[2], dd[3], dd[4], dd[5], dd[6], dd[7]);
                case 9:
                    return X(dd[0], dd[1], dd[2], dd[3], dd[4], dd[5], dd[6], dd[7], dd[8]);
                case 10:
                    return X(dd[0], dd[1], dd[2], dd[3], dd[4], dd[5], dd[6], dd[7], dd[8], dd[9]);
                default:
                    return X.apply(Y, dd)
                }
            }
            return {
                invoke: W,
                instantiate: function(X, Y) {
                    var dc = function() {},
                    dd;
                    dc.prototype = (aL(X) ? X[X.length - 1] : X).prototype;
                    dc = new dc;
                    dd = W(X, dc, Y);
                    return cq(dd) ? dd: dc
                },
                get: V,
                annotate: c,
                has: function(a) {
                    return F.hasOwnProperty(a + D) || R.hasOwnProperty(a)
                }
            }
        }
        var B = {},
        D = "Provider",
        G = [],
        E = new bX,
        F = {
            $provide: {
                provider: p(v),
                factory: p(w),
                service: p(function(R, S) {
                    return w(R, ["$injector", 
                    function(V) {
                        return V.instantiate(S)
                    }])
                }),
                value: p(function(R, S) {
                    return w(R, g(S))
                }),
                constant: p(function(R, S) {
                    bW(R, "constant");
                    F[R] = S;
                    J[R] = S
                }),
                decorator: function(R, S) {
                    var V = Q.get(R + D),
                    W = V.$get;
                    V.$get = function() {
                        var X = H.invoke(W, V);
                        return H.invoke(S, null, {
                            $delegate: X
                        })
                    }
                }
            }
        },
        Q = F.$injector = z(F, 
        function() {
            throw b7("unpr", G.join(" <- "))
        }),
        J = {},
        H = J.$injector = z(J, 
        function(R) {
            R = Q.get(R + D);
            return H.invoke(R.$get, R)
        });
        bV(x(s), 
        function(R) {
            H.invoke(R || cS)
        });
        return H
    }
    function dp() {
        var a = !0;
        this.disableAutoScrolling = function() {
            a = !1
        };
        this.$get = ["$window", "$location", "$rootScope", 
        function(p, s, v) {
            function w(B) {
                var D = null;
                bV(B, 
                function(E) {
                    D || "a" !== o(E.nodeName) || (D = E)
                });
                return D
            }
            function x() {
                var B = s.hash(),
                D;
                B ? (D = z.getElementById(B)) ? D.scrollIntoView() : (D = w(z.getElementsByName(B))) ? D.scrollIntoView() : "top" === B && p.scrollTo(0, 0) : p.scrollTo(0, 0)
            }
            var z = p.document;
            a && v.$watch(function() {
                return s.hash()
            },
            function() {
                v.$evalAsync(x)
            });
            return x
        }]
    }
    function dz(v, p, w, B) {
        function E(dQ) {
            try {
                dQ.apply(null, cJ.call(arguments, 1))
            } finally {
                if (dP--, 0 === dP) {
                    for (; s.length;) {
                        try {
                            s.pop()()
                        } catch(dR) {
                            w.error(dR)
                        }
                    }
                }
            }
        }
        function F(dQ, dR) { (function dS() {
                bV(x, 
                function(dT) {
                    dT()
                });
                dN = dR(dS, dQ)
            })()
        }
        function G() {
            dd = null;
            dO != H.url() && (dO = H.url(), bV(de, 
            function(dQ) {
                dQ(H.url())
            }))
        }
        var H = this,
        S = p[0],
        J = v.location,
        R = v.history,
        dc = v.setTimeout,
        Y = v.clearTimeout,
        W = {};
        H.isMock = !1;
        var dP = 0,
        s = [];
        H.$$completeOutstandingRequest = E;
        H.$$incOutstandingRequestCount = function() {
            dP++
        };
        H.notifyWhenNoOutstandingRequests = function(dQ) {
            bV(x, 
            function(dR) {
                dR()
            });
            0 === dP ? dQ() : s.push(dQ)
        };
        var x = [],
        dN;
        H.addPollFn = function(dQ) {
            dD(dN) && F(100, dc);
            x.push(dQ);
            return dQ
        };
        var dO = J.href,
        V = p.find("base"),
        dd = null;
        H.url = function(dQ, dR) {
            J !== v.location && (J = v.location);
            if (dQ) {
                if (dO != dQ) {
                    return dO = dQ,
                    B.history ? dR ? R.replaceState(null, "", dQ) : (R.pushState(null, "", dQ), V.attr("href", V.attr("href"))) : (dd = dQ, dR ? J.replace(dQ) : J.href = dQ),
                    H
                }
            } else {
                return dd || J.href.replace(/%27/g, "'")
            }
        };
        var de = [],
        z = !1;
        H.onUrlChange = function(dQ) {
            if (!z) {
                if (B.history) {
                    dh(v).on("popstate", G)
                }
                if (B.hashchange) {
                    dh(v).on("hashchange", G)
                } else {
                    H.addPollFn(G)
                }
                z = !0
            }
            de.push(dQ);
            return dQ
        };
        H.baseHref = function() {
            var dQ = V.attr("href");
            return dQ ? dQ.replace(/^https?\:\/\/[^\/]*/, "") : ""
        };
        var X = {},
        D = "",
        Q = H.baseHref();
        H.cookies = function(dQ, dR) {
            var dS,
            dT,
            dU,
            dV;
            if (dQ) {
                dR === cp ? S.cookie = escape(dQ) + "=;path=" + Q + ";expires=Thu, 01 Jan 1970 00:00:00 GMT": aq(dR) && (dS = (S.cookie = escape(dQ) + "=" + escape(dR) + ";path=" + Q).length + 1, 4096 < dS && w.warn("Cookie '" + dQ + "' possibly not set or overflowed because it was too large (" + dS + " > 4096 bytes)!"))
            } else {
                if (S.cookie !== D) {
                    for (D = S.cookie, dS = D.split("; "), X = {},
                    dU = 0; dU < dS.length; dU++) {
                        dT = dS[dU],
                        dV = dT.indexOf("="),
                        0 < dV && (dQ = unescape(dT.substring(0, dV)), X[dQ] === cp && (X[dQ] = unescape(dT.substring(dV + 1))))
                    }
                }
                return X
            }
        };
        H.defer = function(dQ, dR) {
            var dS;
            dP++;
            dS = dc(function() {
                delete W[dS];
                E(dQ)
            },
            dR || 0);
            W[dS] = !0;
            return dS
        };
        H.defer.cancel = function(dQ) {
            return W[dQ] ? (delete W[dQ], Y(dQ), E(cS), !0) : !1
        }
    }
    function dJ() {
        this.$get = ["$window", "$log", "$sniffer", "$document", 
        function(s, p, v, w) {
            return new dz(s, w, p, v)
        }]
    }
    function d() {
        this.$get = function() {
            function s(a, v) {
                function w(J) {
                    J != H && (G ? G == J && (G = J.n) : G = J, x(J.n, J.p), x(J, H), H = J, H.n = null)
                }
                function x(J, Q) {
                    J != Q && (J && (J.p = Q), Q && (Q.n = J))
                }
                if (a in p) {
                    throw U("$cacheFactory")("iid", a)
                }
                var z = 0,
                B = aB({},
                v, {
                    id: a
                }),
                F = {},
                D = v && v.capacity || Number.MAX_VALUE,
                E = {},
                H = null,
                G = null;
                return p[a] = {
                    put: function(J, Q) {
                        var R = E[J] || (E[J] = {
                            key: J
                        });
                        w(R);
                        if (!dD(Q)) {
                            return J in F || z++,
                            F[J] = Q,
                            z > D && this.remove(G.key),
                            Q
                        }
                    },
                    get: function(J) {
                        var Q = E[J];
                        if (Q) {
                            return w(Q),
                            F[J]
                        }
                    },
                    remove: function(J) {
                        var Q = E[J];
                        Q && (Q == H && (H = Q.p), Q == G && (G = Q.n), x(Q.n, Q.p), delete E[J], delete F[J], z--)
                    },
                    removeAll: function() {
                        F = {};
                        z = 0;
                        E = {};
                        H = G = null
                    },
                    destroy: function() {
                        E = B = F = null;
                        delete p[a]
                    },
                    info: function() {
                        return aB({},
                        B, {
                            size: z
                        })
                    }
                }
            }
            var p = {};
            s.info = function() {
                var a = {};
                bV(p, 
                function(v, w) {
                    a[w] = v.info()
                });
                return a
            };
            s.get = function(a) {
                return p[a]
            };
            return s
        }
    }
    function l() {
        this.$get = ["$cacheFactory", 
        function(a) {
            return a("templates")
        }]
    }
    function u(s) {
        var p = {},
        v = "Directive",
        w = /^\s*directive\:\s*([\d\w\-_]+)\s+(.*)$/,
        x = /(([\d\w\-_]+)(?:\:([^;]+))?;?)/,
        z = /^\s*(https?|ftp|mailto|tel|file):/,
        B = /^\s*(https?|ftp|file):|data:image\//,
        D = /^(on[a-z]+|formaction)$/;
        this.directive = function E(a, F) {
            bW(a, "directive");
            aq(a) ? (ci(F, "directiveFactory"), p.hasOwnProperty(a) || (p[a] = [], s.factory(a + v, ["$injector", "$exceptionHandler", 
            function(G, H) {
                var J = [];
                bV(p[a], 
                function(Q, R) {
                    try {
                        var V = G.invoke(Q);
                        ag(V) ? V = {
                            compile: g(V)
                        }: !V.compile && V.link && (V.compile = g(V.link));
                        V.priority = V.priority || 0;
                        V.index = R;
                        V.name = V.name || a;
                        V.require = V.require || V.controller && V.name;
                        V.restrict = V.restrict || "A";
                        J.push(V)
                    } catch(S) {
                        H(S)
                    }
                });
                return J
            }])), p[a].push(F)) : bV(a, bx(E));
            return this
        };
        this.aHrefSanitizationWhitelist = function(F) {
            return c3(F) ? (z = F, this) : z
        };
        this.imgSrcSanitizationWhitelist = function(F) {
            return c3(F) ? (B = F, this) : B
        };
        this.$get = ["$injector", "$interpolate", "$exceptionHandler", "$http", "$templateCache", "$parse", "$controller", "$rootScope", "$document", "$sce", "$animate", 
        function(G, de, dV, dU, dQ, d2, a, R, dY, dZ, dO) {
            function dW(J, d5, d6, d7, d8) {
                J instanceof dh || (J = dh(J));
                bV(J, 
                function(ea, eb) {
                    3 == ea.nodeType && ea.nodeValue.match(/\S+/) && (J[eb] = dh(ea).wrap("<span></span>").parent()[0])
                });
                var d9 = S(J, d5, J, d6, d7, d8);
                return function(ea, eb) {
                    ci(ea, "scope");
                    for (var ec = eb ? cs.clone.call(J) : J, ed = 0, ef = ec.length; ed < ef; ed++) {
                        var ee = ec[ed];
                        1 != ee.nodeType && 9 != ee.nodeType || ec.eq(ed).data("$scope", ea)
                    }
                    dX(ec, "ng-scope");
                    eb && eb(ec, ea);
                    d9 && d9(ea, ec, ec);
                    return ec
                }
            }
            function dX(J, d5) {
                try {
                    J.addClass(d5)
                } catch(d6) {}
            }
            function S(J, d5, d6, d7, d8, d9) {
                function ec(eh, ei, ej, ek) {
                    var el,
                    em,
                    en,
                    eq,
                    eo,
                    ep,
                    er,
                    eg = [];
                    eo = 0;
                    for (ep = ei.length; eo < ep; eo++) {
                        eg.push(ei[eo])
                    }
                    er = eo = 0;
                    for (ep = ea.length; eo < ep; er++) {
                        em = eg[er],
                        ei = ea[eo++],
                        el = ea[eo++],
                        ei ? (ei.scope ? (en = eh.$new(cq(ei.scope)), dh(em).data("$scope", en)) : en = eh, (eq = ei.transclude) || !ek && d5 ? ei(el, en, em, ej, 
                        function(es) {
                            return function(et) {
                                var eu = eh.$new();
                                eu.$$transcluded = !0;
                                return es(eu, et).on("$destroy", bY(eu, eu.$destroy))
                            }
                        } (eq || d5)) : ei(el, en, em, cp, ek)) : el && el(eh, em.childNodes, cp, ek)
                    }
                }
                for (var ea = [], eb, ed, ef, ee = 0; ee < J.length; ee++) {
                    ed = new d3,
                    eb = dR(J[ee], [], ed, 0 == ee ? d7: cp, d8),
                    eb = (d9 = eb.length ? dN(eb, J[ee], ed, d5, d6, null, [], [], d9) : null) && d9.terminal || !J[ee].childNodes || !J[ee].childNodes.length ? null: S(J[ee].childNodes, d9 ? d9.transclude: d5),
                    ea.push(d9),
                    ea.push(eb),
                    ef = ef || d9 || eb,
                    d9 = null
                }
                return ef ? ec: null
            }
            function dR(J, d6, d7, d8, eb) {
                var d9 = d7.$attr,
                ea;
                switch (J.nodeType) {
                case 1:
                    d4(d6, bm(Z(J).toLowerCase()), "E", d8, eb);
                    var ec,
                    eg,
                    ed;
                    ea = J.attributes;
                    for (var ef = 0, ej = ea && ea.length; ef < ej; ef++) {
                        var d5 = !1,
                        ee = !1;
                        ec = ea[ef];
                        if (!b5 || 8 <= b5 || ec.specified) {
                            eg = ec.name;
                            ed = bm(eg);
                            dP.test(ed) && (eg = b(ed.substr(6), "-"));
                            var eh = ed.replace(/(Start|End)$/, "");
                            ed === eh + "Start" && (d5 = eg, ee = eg.substr(0, eg.length - 5) + "end", eg = eg.substr(0, eg.length - 6));
                            ed = bm(eg.toLowerCase());
                            d9[ed] = eg;
                            d7[ed] = ec = q(b5 && "href" == eg ? decodeURIComponent(J.getAttribute(eg, 2)) : ec.value);
                            dx(J, ed) && (d7[ed] = !0);
                            H(J, d6, ec, ed);
                            d4(d6, ed, "A", d8, eb, d5, ee)
                        }
                    }
                    J = J.className;
                    if (aq(J) && "" !== J) {
                        for (; ea = x.exec(J);) {
                            ed = bm(ea[2]),
                            d4(d6, ed, "C", d8, eb) && (d7[ed] = q(ea[3])),
                            J = J.substr(ea.index + ea[0].length)
                        }
                    }
                    break;
                case 3:
                    d1(d6, J.nodeValue);
                    break;
                case 8:
                    try {
                        if (ea = w.exec(J.nodeValue)) {
                            ed = bm(ea[1]),
                            d4(d6, ed, "M", d8, eb) && (d7[ed] = q(ea[2]))
                        }
                    } catch(ei) {}
                }
                d6.sort(dS);
                return d6
            }
            function X(J, d5, d6) {
                var d7 = [],
                d8 = 0;
                if (d5 && J.hasAttribute && J.hasAttribute(d5)) {
                    do {
                        if (!J) {
                            throw aM("uterdir", d5, d6)
                        }
                        1 == J.nodeType && (J.hasAttribute(d5) && d8++, J.hasAttribute(d6) && d8--);
                        d7.push(J);
                        J = J.nextSibling
                    }
                    while (0 < d8)
                } else {
                    d7.push(J)
                }
                return dh(d7)
            }
            function dd(J, d5, d6) {
                return function(d7, d8, d9, ea) {
                    d8 = X(d8[0], d5, d6);
                    return J(d7, d8, d9, ea)
                }
            }
            function dN(d5, d6, d8, eb, ed, ee, ek, eh, ef) {
                function ep(J, ex, ey, ez) {
                    J && (ey && (J = dd(J, ey, ez)), J.require = ei.require, ek.push(J));
                    ex && (ey && (ex = dd(ex, ey, ez)), ex.require = ei.require, eh.push(ex))
                }
                function er(J, ex) {
                    var ey,
                    ez = "data",
                    eA = !1;
                    if (aq(J)) {
                        for (;
                        "^" == (ey = J.charAt(0)) || "?" == ey;) {
                            J = J.substr(1),
                            "^" == ey && (ez = "inheritedData"),
                            eA = eA || "?" == ey
                        }
                        ey = ex[ez]("$" + J + "Controller");
                        8 == ex[0].nodeType && ex[0].$$controller && (ey = ey || ex[0].$$controller, ex[0].$$controller = null);
                        if (!ey && !eA) {
                            throw aM("ctreq", J, ew)
                        }
                    } else {
                        aL(J) && (ey = [], bV(J, 
                        function(eB) {
                            ey.push(er(eB, ex))
                        }))
                    }
                    return ey
                }
                function et(ex, eA, eB, eC, eD) {
                    var eG,
                    J,
                    eI,
                    ey,
                    eJ;
                    eG = d6 === eB ? d8: bi(d8, new d3(dh(eB), d8.$attr));
                    J = eG.$$element;
                    if (es) {
                        var ez = /^\s*([@=&])(\??)\s*(\w*)\s*$/,
                        eH = eA.$parent || eA;
                        bV(es.scope, 
                        function(eK, eL) {
                            var eM = eK.match(ez) || [],
                            eN = eM[3] || eL,
                            eO = "?" == eM[2],
                            eM = eM[1],
                            eR,
                            eP,
                            eQ;
                            eA.$$isolateBindings[eL] = eM + eN;
                            switch (eM) {
                            case "@":
                                eG.$observe(eN, 
                                function(eS) {
                                    eA[eL] = eS
                                });
                                eG.$$observers[eN].$$scope = eH;
                                eG[eN] && (eA[eL] = de(eG[eN])(eH));
                                break;
                            case "=":
                                if (eO && !eG[eN]) {
                                    break
                                }
                                eP = d2(eG[eN]);
                                eQ = eP.assign || 
                                function() {
                                    eR = eA[eL] = eP(eH);
                                    throw aM("nonassign", eG[eN], es.name)
                                };
                                eR = eA[eL] = eP(eH);
                                eA.$watch(function() {
                                    var eS = eP(eH);
                                    eS !== eA[eL] && (eS !== eR ? eR = eA[eL] = eS: eQ(eH, eS = eR = eA[eL]));
                                    return eS
                                });
                                break;
                            case "&":
                                eP = d2(eG[eN]);
                                eA[eL] = function(eS) {
                                    return eP(eH, eS)
                                };
                                break;
                            default:
                                throw aM("iscp", es.name, eL, eK)
                            }
                        })
                    }
                    eu && bV(eu, 
                    function(eK) {
                        var eL = {
                            $scope: eA,
                            $element: J,
                            $attrs: eG,
                            $transclude: eD
                        },
                        eM;
                        eJ = eK.controller;
                        "@" == eJ && (eJ = eG[eK.name]);
                        eM = a(eJ, eL);
                        8 == J[0].nodeType ? J[0].$$controller = eM: J.data("$" + eK.name + "Controller", eM);
                        eK.controllerAs && (eL.$scope[eK.controllerAs] = eM)
                    });
                    eC = 0;
                    for (eI = ek.length; eC < eI; eC++) {
                        try {
                            ey = ek[eC],
                            ey(eA, J, eG, ey.require && er(ey.require, J))
                        } catch(eF) {
                            dV(eF, aC(J))
                        }
                    }
                    ex && ex(eA, eB.childNodes, cp, eD);
                    for (eC = eh.length - 1; 0 <= eC; eC--) {
                        try {
                            ey = eh[eC],
                            ey(eA, J, eG, ey.require && er(ey.require, J))
                        } catch(eE) {
                            dV(eE, aC(J))
                        }
                    }
                }
                ef = ef || {};
                var d9 = -Number.MAX_VALUE,
                ea,
                es = ef.newIsolateScopeDirective,
                en = ef.templateDirective,
                em = d8.$$element = dh(d6),
                ei,
                ew,
                ev;
                ef = ef.transcludeDirective;
                for (var eq = eb, eu, d7, eo = 0, el = d5.length; eo < el; eo++) {
                    ei = d5[eo];
                    var eg = ei.$$start,
                    ec = ei.$$end;
                    eg && (em = X(d6, eg, ec));
                    ev = cp;
                    if (d9 > ei.priority) {
                        break
                    }
                    if (ev = ei.scope) {
                        ea = ea || ei,
                        ei.templateUrl || (dT("new/isolated scope", es, ei, em), cq(ev) && (dX(em, "ng-isolate-scope"), es = ei), dX(em, "ng-scope"))
                    }
                    ew = ei.name; ! ei.templateUrl && ei.controller && (ev = ei.controller, eu = eu || {},
                    dT("'" + ew + "' controller", eu[ew], ei, em), eu[ew] = ei);
                    if (ev = ei.transclude) {
                        "ngRepeat" !== ew && (dT("transclusion", ef, ei, em), ef = ei),
                        "element" == ev ? (d9 = ei.priority, ev = X(d6, eg, ec), em = d8.$$element = dh(cf.createComment(" " + ew + ": " + d8[ew] + " ")), d6 = em[0], Y(ed, dh(cJ.call(ev, 0)), d6), eq = dW(ev, eb, d9, ee && ee.name, {
                            newIsolateScopeDirective: es,
                            transcludeDirective: ef,
                            templateDirective: en
                        })) : (ev = dh(c7(d6)).contents(), em.html(""), eq = dW(ev, eb))
                    }
                    if (ei.template) {
                        if (dT("template", en, ei, em), en = ei, ev = ag(ei.template) ? ei.template(em, d8) : ei.template, ev = V(ev), ei.replace) {
                            ee = ei;
                            ev = dh("<div>" + q(ev) + "</div>").contents();
                            d6 = ev[0];
                            if (1 != ev.length || 1 !== d6.nodeType) {
                                throw aM("tplrt", ew, "")
                            }
                            Y(ed, em, d6);
                            el = {
                                $attr: {}
                            };
                            d5 = d5.concat(dR(d6, d5.splice(eo + 1, d5.length - (eo + 1)), el));
                            F(d8, el);
                            el = d5.length
                        } else {
                            em.html(ev)
                        }
                    }
                    if (ei.templateUrl) {
                        dT("template", en, ei, em),
                        en = ei,
                        ei.replace && (ee = ei),
                        et = Q(d5.splice(eo, d5.length - eo), em, d8, ed, eq, ek, eh, {
                            newIsolateScopeDirective: es,
                            transcludeDirective: ef,
                            templateDirective: en
                        }),
                        el = d5.length
                    } else {
                        if (ei.compile) {
                            try {
                                d7 = ei.compile(em, d8, eq),
                                ag(d7) ? ep(null, d7, eg, ec) : d7 && ep(d7.pre, d7.post, eg, ec)
                            } catch(ej) {
                                dV(ej, aC(em))
                            }
                        }
                    }
                    ei.terminal && (et.terminal = !0, d9 = Math.max(d9, ei.priority))
                }
                et.scope = ea && ea.scope;
                et.transclude = ef && eq;
                return et
            }
            function d4(d6, d7, d8, d9, ea, eb, ec) {
                if (d7 === ea) {
                    return null
                }
                ea = null;
                if (p.hasOwnProperty(d7)) {
                    var ed;
                    d7 = G.get(d7 + v);
                    for (var ee = 0, J = d7.length; ee < J; ee++) {
                        try {
                            ed = d7[ee],
                            (d9 === cp || d9 > ed.priority) && -1 != ed.restrict.indexOf(d8) && (eb && (ed = aR(ed, {
                                $$start: eb,
                                $$end: ec
                            })), d6.push(ed), ea = ed)
                        } catch(d5) {
                            dV(d5)
                        }
                    }
                }
                return ea
            }
            function F(J, d5) {
                var d6 = d5.$attr,
                d7 = J.$attr,
                d8 = J.$$element;
                bV(J, 
                function(d9, ea) {
                    "$" != ea.charAt(0) && (d5[ea] && (d9 += ("style" === ea ? ";": " ") + d5[ea]), J.$set(ea, d9, !0, d6[ea]))
                });
                bV(d5, 
                function(d9, ea) {
                    "class" == ea ? (dX(d8, d9), J["class"] = (J["class"] ? J["class"] + " ": "") + d9) : "style" == ea ? d8.attr("style", d8.attr("style") + ";" + d9) : "$" == ea.charAt(0) || J.hasOwnProperty(ea) || (J[ea] = d9, d7[ea] = d6[ea])
                })
            }
            function Q(J, d6, d7, d9, ea, eb, ee, ec) {
                var ed = [],
                ef,
                eg,
                ei = d6[0],
                d5 = J.shift(),
                d8 = aB({},
                d5, {
                    templateUrl: null,
                    transclude: null,
                    replace: null
                }),
                eh = ag(d5.templateUrl) ? d5.templateUrl(d6, d7) : d5.templateUrl;
                d6.html("");
                dU.get(dZ.getTrustedResourceUrl(eh), {
                    cache: dQ
                }).success(function(ej) {
                    var ek;
                    ej = V(ej);
                    if (d5.replace) {
                        ej = dh("<div>" + q(ej) + "</div>").contents();
                        ek = ej[0];
                        if (1 != ej.length || 1 !== ek.nodeType) {
                            throw aM("tplrt", d5.name, eh)
                        }
                        ej = {
                            $attr: {}
                        };
                        Y(d9, d6, ek);
                        dR(ek, J, ej);
                        F(d7, ej)
                    } else {
                        ek = ei,
                        d6.html(ej)
                    }
                    J.unshift(d8);
                    ef = dN(J, ek, d7, ea, d6, d5, eb, ee, ec);
                    bV(d9, 
                    function(ep, eq) {
                        ep == ek && (d9[eq] = d6[0])
                    });
                    for (eg = S(d6[0].childNodes, ea); ed.length;) {
                        ej = ed.shift();
                        var eo = ed.shift(),
                        em = ed.shift(),
                        el = ed.shift(),
                        en = d6[0];
                        eo !== ei && (en = c7(ek), Y(em, dh(eo), en));
                        ef(eg, ej, en, d9, el)
                    }
                    ed = null
                }).error(function(ej, ek, el, em) {
                    throw aM("tpload", em.url)
                });
                return function(ej, ek, el, em, en) {
                    ed ? (ed.push(ek), ed.push(el), ed.push(em), ed.push(en)) : ef(eg, ek, el, em, en)
                }
            }
            function dS(J, d5) {
                var d6 = d5.priority - J.priority;
                return 0 !== d6 ? d6: J.name !== d5.name ? J.name < d5.name ? -1: 1: J.index - d5.index
            }
            function dT(J, d5, d6, d7) {
                if (d5) {
                    throw aM("multidir", d5.name, d6.name, J, aC(d7))
                }
            }
            function d1(J, d5) {
                var d6 = de(d5, !0);
                d6 && J.push({
                    priority: 0,
                    compile: g(function(d7, d8) {
                        var d9 = d8.parent(),
                        ea = d9.data("$binding") || [];
                        ea.push(d6);
                        dX(d9.data("$binding", ea), "ng-binding");
                        d7.$watch(d6, 
                        function(eb) {
                            d8[0].nodeValue = eb
                        })
                    })
                })
            }
            function d0(J, d5) {
                if ("xlinkHref" == d5 || "IMG" != Z(J) && ("src" == d5 || "ngSrc" == d5)) {
                    return dZ.RESOURCE_URL
                }
            }
            function H(J, d5, d6, d7) {
                var d8 = de(d6, !0);
                if (d8) {
                    if ("multiple" === d7 && "SELECT" === Z(J)) {
                        throw aM("selmulti", aC(J))
                    }
                    d5.push({
                        priority: -100,
                        compile: g(function(d9, ea, eb) {
                            ea = eb.$$observers || (eb.$$observers = {});
                            if (D.test(d7)) {
                                throw aM("nodomevents")
                            }
                            if (d8 = de(eb[d7], !0, d0(J, d7))) {
                                eb[d7] = d8(d9),
                                (ea[d7] || (ea[d7] = [])).$$inter = !0,
                                (eb.$$observers && eb.$$observers[d7].$$scope || d9).$watch(d8, 
                                function(ec) {
                                    eb.$set(d7, ec)
                                })
                            }
                        })
                    })
                }
            }
            function Y(J, d5, d6) {
                var d7 = d5[0],
                d8 = d5.length,
                d9 = d7.parentNode,
                eb,
                ec;
                if (J) {
                    for (eb = 0, ec = J.length; eb < ec; eb++) {
                        if (J[eb] == d7) {
                            J[eb++] = d6;
                            ec = eb + d8 - 1;
                            for (var ea = J.length; eb < ea; eb++, ec++) {
                                ec < ea ? J[eb] = J[ec] : delete J[eb]
                            }
                            J.length -= d8 - 1;
                            break
                        }
                    }
                }
                d9 && d9.replaceChild(d6, d7);
                J = cf.createDocumentFragment();
                J.appendChild(d7);
                d6[dh.expando] = d7[dh.expando];
                d7 = 1;
                for (d8 = d5.length; d7 < d8; d7++) {
                    d9 = d5[d7],
                    dh(d9).remove(),
                    J.appendChild(d9),
                    delete d5[d7]
                }
                d5[0] = d6;
                d5.length = 1
            }
            var d3 = function(J, d5) {
                this.$$element = J;
                this.$attr = d5 || {}
            };
            d3.prototype = {
                $normalize: bm,
                $addClass: function(J) {
                    J && 0 < J.length && dO.addClass(this.$$element, J)
                },
                $removeClass: function(J) {
                    J && 0 < J.length && dO.removeClass(this.$$element, J)
                },
                $set: function(J, d5, d6, d7) {
                    function d8(ea, eb) {
                        var ec = [],
                        ed = ea.split(/\s+/),
                        ee = eb.split(/\s+/),
                        eg = 0;
                        ea: for (; eg < ed.length; eg++) {
                            for (var eh = ed[eg], ef = 0; ef < ee.length; ef++) {
                                if (eh == ee[ef]) {
                                    continue ea
                                }
                            }
                            ec.push(eh)
                        }
                        return ec
                    }
                    if ("class" == J) {
                        d5 = d5 || "",
                        d6 = this.$$element.attr("class") || "",
                        this.$removeClass(d8(d6, d5).join(" ")),
                        this.$addClass(d8(d5, d6).join(" "))
                    } else {
                        var d9 = dx(this.$$element[0], J);
                        d9 && (this.$$element.prop(J, d5), d7 = d9);
                        this[J] = d5;
                        d7 ? this.$attr[J] = d7: (d7 = this.$attr[J]) || (this.$attr[J] = d7 = b(J, "-"));
                        d9 = Z(this.$$element);
                        if ("A" === d9 && "href" === J || "IMG" === d9 && "src" === J) {
                            if (!b5 || 8 <= b5) {
                                d9 = c5(d5).href,
                                "" !== d9 && ("href" === J && !d9.match(z) || "src" === J && !d9.match(B)) && (this[J] = d5 = "unsafe:" + d9)
                            }
                        } ! 1 !== d6 && (null === d5 || d5 === cp ? this.$$element.removeAttr(d7) : this.$$element.attr(d7, d5))
                    } (d6 = this.$$observers) && bV(d6[J], 
                    function(ea) {
                        try {
                            ea(d5)
                        } catch(eb) {
                            dV(eb)
                        }
                    })
                },
                $observe: function(J, d5) {
                    var d6 = this,
                    d7 = d6.$$observers || (d6.$$observers = {}),
                    d8 = d7[J] || (d7[J] = []);
                    d8.push(d5);
                    R.$evalAsync(function() {
                        d8.$$inter || d5(d6[J])
                    });
                    return d5
                }
            };
            var W = de.startSymbol(),
            dc = de.endSymbol(),
            V = "{{" == W || "}}" == dc ? dE: function(J) {
                return J.replace(/\{\{/g, W).replace(/}}/g, dc)
            },
            dP = /^ngAttr[A-Z]/;
            return dW
        }]
    }
    function bm(a) {
        return bv(a.replace(A, ""))
    }
    function O() {
        var s = {},
        p = /^(\S+)(\s+as\s+(\w+))?$/;
        this.register = function(v, w) {
            bW(v, "controller");
            cq(v) ? aB(s, v) : s[v] = w
        };
        this.$get = ["$injector", "$window", 
        function(a, v) {
            return function(w, x) {
                var z,
                B,
                D;
                aq(w) && (z = w.match(p), B = z[1], D = z[3], w = s.hasOwnProperty(B) ? s[B] : ct(x.$scope, B, !0) || ct(v, B, !0), bn(w, B, !0));
                z = a.instantiate(w, x);
                if (D) {
                    if (!x || "object" != typeof x.$scope) {
                        throw U("$controller")("noscp", B || w.name, D)
                    }
                    x.$scope[D] = z
                }
                return z
            }
        }]
    }
    function ad() {
        this.$get = ["$window", 
        function(a) {
            return dh(a.document)
        }]
    }
    function an() {
        this.$get = ["$log", 
        function(a) {
            return function(p, s) {
                a.error.apply(a, arguments)
            }
        }]
    }
    function ab(s) {
        var p = {},
        v,
        w,
        x;
        if (!s) {
            return p
        }
        bV(s.split("\n"), 
        function(a) {
            x = a.indexOf(":");
            v = o(q(a.substr(0, x)));
            w = q(a.substr(x + 1));
            v && (p[v] = p[v] ? p[v] + (", " + w) : w)
        });
        return p
    }
    function al(s) {
        var p = cq(s) ? s: cp;
        return function(a) {
            p || (p = ab(s));
            return a ? p[o(a)] || null: p
        }
    }
    function aw(s, p, v) {
        if (ag(v)) {
            return v(s, p)
        }
        bV(v, 
        function(a) {
            s = a(s, p)
        });
        return s
    }
    function ay() {
        var s = /^\s*(\[|\{[^\{])/,
        p = /[\}\]]\s*$/,
        v = /^\)\]\}',?\n/,
        w = {
            "Content-Type": "application/json;charset=utf-8"
        },
        x = this.defaults = {
            transformResponse: [function(a) {
                aq(a) && (a = a.replace(v, ""), s.test(a) && p.test(a) && (a = bP(a)));
                return a
            }],
            transformRequest: [function(D) {
                return cq(D) && "[object File]" !== c6.apply(D) ? bM(D) : D
            }],
            headers: {
                common: {
                    Accept: "application/json, text/plain, */*"
                },
                post: w,
                put: w,
                patch: w
            },
            xsrfCookieName: "XSRF-TOKEN",
            xsrfHeaderName: "X-XSRF-TOKEN"
        },
        z = this.interceptors = [],
        B = this.responseInterceptors = [];
        this.$get = ["$httpBackend", "$browser", "$cacheFactory", "$rootScope", "$q", "$injector", 
        function(D, F, G, J, S, R) {
            function Q(X) {
                function Y(dP) {
                    var dQ = aB({},
                    dP, {
                        data: aw(dP.data, dP.headers, dc.transformResponse)
                    });
                    return 200 <= dP.status && 300 > dP.status ? dQ: S.reject(dQ)
                }
                var dc = {
                    transformRequest: x.transformRequest,
                    transformResponse: x.transformResponse
                },
                dd = function(dP) {
                    function dQ(dV) {
                        var dW;
                        bV(dV, 
                        function(a, dX) {
                            ag(a) && (dW = a(), null != dW ? dV[dX] = dW: delete dV[dX])
                        })
                    }
                    var dR = x.headers,
                    dS = aB({},
                    dP.headers),
                    dT,
                    dU,
                    dR = aB({},
                    dR.common, dR[o(dP.method)]);
                    dQ(dR);
                    dQ(dS);
                    dP: for (dT in dR) {
                        dP = o(dT);
                        for (dU in dS) {
                            if (o(dU) === dP) {
                                continue dP
                            }
                        }
                        dS[dT] = dR[dT]
                    }
                    return dS
                } (X);
                aB(dc, X);
                dc.headers = dd;
                dc.method = ai(dc.method); (X = M(dc.url) ? F.cookies()[dc.xsrfCookieName || x.xsrfCookieName] : cp) && (dd[dc.xsrfHeaderName || x.xsrfHeaderName] = X);
                var dN = [function(dP) {
                    dd = dP.headers;
                    var dQ = aw(dP.data, al(dd), dP.transformRequest);
                    dD(dP.data) && bV(dd, 
                    function(dR, dS) {
                        "content-type" === o(dS) && delete dd[dS]
                    });
                    dD(dP.withCredentials) && !dD(x.withCredentials) && (dP.withCredentials = x.withCredentials);
                    return W(dP, dQ, dd).then(Y, Y)
                },
                cp],
                dO = S.when(dc);
                for (bV(V, 
                function(dP) { (dP.request || dP.requestError) && dN.unshift(dP.request, dP.requestError); (dP.response || dP.responseError) && dN.push(dP.response, dP.responseError)
                }); dN.length;) {
                    X = dN.shift();
                    var de = dN.shift(),
                    dO = dO.then(X, de)
                }
                dO.success = function(dP) {
                    dO.then(function(a) {
                        dP(a.data, a.status, a.headers, dc)
                    });
                    return dO
                };
                dO.error = function(dP) {
                    dO.then(null, 
                    function(a) {
                        dP(a.data, a.status, a.headers, dc)
                    });
                    return dO
                };
                return dO
            }
            function W(a, X, Y) {
                function dd(dS, dT, dU) {
                    dN && (200 <= dS && 300 > dS ? dN.put(dP, [dS, dT, ab(dU)]) : dN.remove(dP));
                    dc(dT, dS, dU);
                    J.$$phase || J.$apply()
                }
                function dc(dS, dT, dU) {
                    dT = Math.max(dT, 0); (200 <= dT && 300 > dT ? dO.resolve: dO.reject)({
                        data: dS,
                        status: dT,
                        headers: al(dU),
                        config: a
                    })
                }
                function de() {
                    var dS = dv(Q.pendingRequests, a); - 1 !== dS && Q.pendingRequests.splice(dS, 1)
                }
                var dO = S.defer(),
                dR = dO.promise,
                dN,
                dQ,
                dP = E(a.url, a.params);
                Q.pendingRequests.push(a);
                dR.then(de, de); (a.cache || x.cache) && (!1 !== a.cache && "GET" == a.method) && (dN = cq(a.cache) ? a.cache: cq(x.cache) ? x.cache: H);
                if (dN) {
                    if (dQ = dN.get(dP), c3(dQ)) {
                        if (dQ.then) {
                            return dQ.then(de, de),
                            dQ
                        }
                        aL(dQ) ? dc(dQ[1], dQ[0], ar(dQ[2])) : dc(dQ, 200, {})
                    } else {
                        dN.put(dP, dR)
                    }
                }
                dD(dQ) && D(a.method, dP, X, dd, Y, a.timeout, a.withCredentials, a.responseType);
                return dR
            }
            function E(X, Y) {
                if (!Y) {
                    return X
                }
                var dc = [];
                aH(Y, 
                function(dd, de) {
                    null != dd && dd != cp && (aL(dd) || (dd = [dd]), bV(dd, 
                    function(dN) {
                        cq(dN) && (dN = bM(dN));
                        dc.push(cU(de) + "=" + cU(dN))
                    }))
                });
                return X + ( - 1 == X.indexOf("?") ? "?": "&") + dc.join("&")
            }
            var H = G("$http"),
            V = [];
            bV(z, 
            function(X) {
                V.unshift(aq(X) ? R.get(X) : R.invoke(X))
            });
            bV(B, 
            function(X, Y) {
                var dc = aq(X) ? R.get(X) : R.invoke(X);
                V.splice(Y, 0, {
                    response: function(dd) {
                        return dc(S.when(dd))
                    },
                    responseError: function(dd) {
                        return dc(S.reject(dd))
                    }
                })
            });
            Q.pendingRequests = []; (function(X) {
                bV(arguments, 
                function(Y) {
                    Q[Y] = function(a, dc) {
                        return Q(aB(dc || {},
                        {
                            method: Y,
                            url: a
                        }))
                    }
                })
            })("get", "delete", "head", "jsonp"); (function(X) {
                bV(arguments, 
                function(Y) {
                    Q[Y] = function(a, dc, dd) {
                        return Q(aB(dd || {},
                        {
                            method: Y,
                            url: a,
                            data: dc
                        }))
                    }
                })
            })("post", "put");
            Q.defaults = x;
            return Q
        }]
    }
    function aI() {
        this.$get = ["$browser", "$window", "$document", 
        function(s, p, v) {
            return aS(s, a1, s.defer, p.angular.callbacks, v[0], p.location.protocol.replace(":", ""))
        }]
    }
    function aS(s, p, v, w, x, z) {
        function B(D, E) {
            var F = x.createElement("script"),
            G = function() {
                x.body.removeChild(F);
                E && E()
            };
            F.type = "text/javascript";
            F.src = D;
            b5 ? F.onreadystatechange = function() { / loaded | complete / .test(F.readyState) && G()
            }: F.onload = F.onerror = G;
            x.body.appendChild(F);
            return G
        }
        return function(E, H, F, G, S, R, Q, dc) {
            function a() {
                Y = -1;
                J && J();
                W && W.abort()
            }
            function D(dd, de, dN, dP) {
                var dO = z || c5(H).protocol;
                X && v.cancel(X);
                J = W = null;
                de = "file" == dO ? dN ? 200: 404: de;
                dd(1223 == de ? 204: de, dN, dP);
                s.$$completeOutstandingRequest(cS)
            }
            var Y;
            s.$$incOutstandingRequestCount();
            H = H || s.url();
            if ("jsonp" == o(E)) {
                var V = "_" + (w.counter++).toString(36);
                w[V] = function(dd) {
                    w[V].data = dd
                };
                var J = B(H.replace("JSON_CALLBACK", "angular.callbacks." + V), 
                function() {
                    w[V].data ? D(G, 200, w[V].data) : D(G, Y || -2);
                    delete w[V]
                })
            } else {
                var W = new p;
                W.open(E, H, !0);
                bV(S, 
                function(dd, de) {
                    c3(dd) && W.setRequestHeader(de, dd)
                });
                W.onreadystatechange = function() {
                    if (4 == W.readyState) {
                        var dd = W.getAllResponseHeaders();
                        D(G, Y || W.status, W.responseType ? W.response: W.responseText, dd)
                    }
                };
                Q && (W.withCredentials = !0);
                dc && (W.responseType = dc);
                W.send(F || null)
            }
            if (0 < R) {
                var X = v(a, R)
            } else {
                R && R.then && R.then(a)
            }
        }
    }
    function bb() {
        var s = "{{",
        p = "}}";
        this.startSymbol = function(v) {
            return v ? (s = v, this) : s
        };
        this.endSymbol = function(a) {
            return a ? (p = a, this) : p
        };
        this.$get = ["$parse", "$exceptionHandler", "$sce", 
        function(a, v, w) {
            function x(E, F, G) {
                for (var R, Q, H = 0, V = [], D = E.length, J = !1, S = []; H < D;) { - 1 != (R = E.indexOf(s, H)) && -1 != (Q = E.indexOf(p, R + z)) ? (H != R && V.push(E.substring(H, R)), V.push(H = a(J = E.substring(R + z, Q))), H.exp = J, H = Q + B, J = !0) : (H != D && V.push(E.substring(H)), H = D)
                } (D = V.length) || (V.push(""), D = 1);
                if (G && 1 < V.length) {
                    throw aG("noconcat", E)
                }
                if (!F || J) {
                    return S.length = D,
                    H = function(W) {
                        try {
                            for (var X = 0, Y = D, dd; X < Y; X++) {
                                "function" == typeof(dd = V[X]) && (dd = dd(W), dd = G ? w.getTrusted(G, dd) : w.valueOf(dd), null == dd || dd == cp ? dd = "": "string" != typeof dd && (dd = bM(dd))),
                                S[X] = dd
                            }
                            return S.join("")
                        } catch(dc) {
                            W = aG("interr", E, dc.toString()),
                            v(W)
                        }
                    },
                    H.exp = E,
                    H.parts = V,
                    H
                }
            }
            var z = s.length,
            B = p.length;
            x.startSymbol = function() {
                return s
            };
            x.endSymbol = function() {
                return p
            };
            return x
        }]
    }
    function bj() {
        this.$get = ["$rootScope", "$window", "$q", 
        function(s, p, v) {
            function w(a, z, B, F) {
                var D = p.setInterval,
                E = p.clearInterval,
                J = v.defer(),
                H = J.promise;
                B = c3(B) ? B: 0;
                var G = 0,
                Q = c3(F) && !F;
                H.then(null, null, a);
                H.$$intervalId = D(function() {
                    J.notify(G++);
                    0 < B && G >= B && (J.resolve(G), E(H.$$intervalId), delete x[H.$$intervalId]);
                    Q || s.$apply()
                },
                z);
                x[H.$$intervalId] = J;
                return H
            }
            var x = {};
            w.cancel = function(z) {
                return z && z.$$intervalId in x ? (x[z.$$intervalId].reject("canceled"), clearInterval(z.$$intervalId), delete x[z.$$intervalId], !0) : !1
            };
            return w
        }]
    }
    function bs() {
        this.$get = function() {
            return {
                id: "en-us",
                NUMBER_FORMATS: {
                    DECIMAL_SEP: ".",
                    GROUP_SEP: ",",
                    PATTERNS: [{
                        minInt: 1,
                        minFrac: 0,
                        maxFrac: 3,
                        posPre: "",
                        posSuf: "",
                        negPre: "-",
                        negSuf: "",
                        gSize: 3,
                        lgSize: 3
                    },
                    {
                        minInt: 1,
                        minFrac: 2,
                        maxFrac: 2,
                        posPre: "\u00a4",
                        posSuf: "",
                        negPre: "(\u00a4",
                        negSuf: ")",
                        gSize: 3,
                        lgSize: 3
                    }],
                    CURRENCY_SYM: "$"
                },
                DATETIME_FORMATS: {
                    MONTH: "January February March April May June July August September October November December".split(" "),
                    SHORTMONTH: "Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec".split(" "),
                    DAY: "Sunday Monday Tuesday Wednesday Thursday Friday Saturday".split(" "),
                    SHORTDAY: "Sun Mon Tue Wed Thu Fri Sat".split(" "),
                    AMPMS: ["AM", "PM"],
                    medium: "MMM d, y h:mm:ss a",
                    "short": "M/d/yy h:mm a",
                    fullDate: "EEEE, MMMM d, y",
                    longDate: "MMMM d, y",
                    mediumDate: "MMM d, y",
                    shortDate: "M/d/yy",
                    mediumTime: "h:mm:ss a",
                    shortTime: "h:mm a"
                },
                pluralCat: function(a) {
                    return 1 === a ? "one": "other"
                }
            }
        }
    }
    function aQ(s) {
        s = s.split("/");
        for (var p = s.length; p--;) {
            s[p] = b8(s[p])
        }
        return s.join("/")
    }
    function aZ(s, p) {
        var v = c5(s);
        p.$$protocol = v.protocol;
        p.$$host = v.hostname;
        p.$$port = c4(v.port) || bA[v.protocol] || null
    }
    function a9(s, p) {
        var v = "/" !== s.charAt(0);
        v && (s = "/" + s);
        var w = c5(s);
        p.$$path = decodeURIComponent(v && "/" === w.pathname.charAt(0) ? w.pathname.substring(1) : w.pathname);
        p.$$search = b9(w.search);
        p.$$hash = decodeURIComponent(w.hash);
        p.$$path && "/" != p.$$path.charAt(0) && (p.$$path = "/" + p.$$path)
    }
    function bD(s, p) {
        if (0 == p.indexOf(s)) {
            return p.substr(s.length)
        }
    }
    function cB(s) {
        var p = s.indexOf("#");
        return - 1 == p ? s: s.substr(0, p)
    }
    function aa(a) {
        return a.substr(0, cB(a).lastIndexOf("/") + 1)
    }
    function bh(s, p) {
        this.$$html5 = !0;
        p = p || "";
        var v = aa(s);
        aZ(s, this);
        this.$$parse = function(w) {
            var x = bD(v, w);
            if (!aq(x)) {
                throw ak("ipthprfx", w, v)
            }
            a9(x, this);
            this.$$path || (this.$$path = "/");
            this.$$compose()
        };
        this.$$compose = function() {
            var w = cj(this.$$search),
            x = this.$$hash ? "#" + b8(this.$$hash) : "";
            this.$$url = aQ(this.$$path) + (w ? "?" + w: "") + x;
            this.$$absUrl = v + this.$$url.substr(1)
        };
        this.$$rewrite = function(a) {
            var w;
            if ((w = bD(s, a)) !== cp) {
                return a = w,
                (w = bD(p, w)) !== cp ? v + (bD("/", w) || w) : s + a
            }
            if ((w = bD(v, a)) !== cp) {
                return v + w
            }
            if (v == a + "/") {
                return v
            }
        }
    }
    function av(s, p) {
        var v = aa(s);
        aZ(s, this);
        this.$$parse = function(a) {
            var w = bD(s, a) || bD(v, a),
            w = "#" == w.charAt(0) ? bD(p, w) : this.$$html5 ? w: "";
            if (!aq(w)) {
                throw ak("ihshprfx", a, p)
            }
            a9(w, this);
            this.$$compose()
        };
        this.$$compose = function() {
            var a = cj(this.$$search),
            w = this.$$hash ? "#" + b8(this.$$hash) : "";
            this.$$url = aQ(this.$$path) + (a ? "?" + a: "") + w;
            this.$$absUrl = s + (this.$$url ? p + this.$$url: "")
        };
        this.$$rewrite = function(w) {
            if (cB(s) == cB(w)) {
                return w
            }
        }
    }
    function bq(s, p) {
        this.$$html5 = !0;
        av.apply(this, arguments);
        var v = aa(s);
        this.$$rewrite = function(a) {
            var w;
            if (s == cB(a)) {
                return a
            }
            if (w = bD(v, a)) {
                return s + p + w
            }
            if (v === a + "/") {
                return v
            }
        }
    }
    function aj(a) {
        return function() {
            return this[a]
        }
    }
    function by(s, p) {
        return function(a) {
            if (dD(a)) {
                return this[s]
            }
            this[s] = p(a);
            this.$$compose();
            return this
        }
    }
    function bJ() {
        var s = "",
        p = !1;
        this.hashPrefix = function(v) {
            return c3(v) ? (s = v, this) : s
        };
        this.html5Mode = function(a) {
            return c3(a) ? (p = a, this) : p
        };
        this.$get = ["$rootScope", "$browser", "$sniffer", "$rootElement", 
        function(a, v, w, x) {
            function z(G) {
                a.$broadcast("$locationChangeSuccess", B.absUrl(), G)
            }
            var B,
            F = v.baseHref(),
            D = v.url();
            p ? (F = D.substring(0, D.indexOf("/", D.indexOf("//") + 2)) + (F || "/"), w = w.history ? bh: bq) : (F = cB(D), w = av);
            B = new w(F, "#" + s);
            B.$$parse(B.$$rewrite(D));
            x.on("click", 
            function(G) {
                if (!G.ctrlKey && !G.metaKey && 2 != G.which) {
                    for (var H = dh(G.target);
                    "a" !== o(H[0].nodeName);) {
                        if (H[0] === x[0] || !(H = H.parent())[0]) {
                            return
                        }
                    }
                    var J = H.prop("href"),
                    Q = B.$$rewrite(J);
                    J && (!H.attr("target") && Q && !G.isDefaultPrevented()) && (G.preventDefault(), Q != v.url() && (B.$$parse(Q), a.$apply(), dt.angular["ff-684208-preventDefault"] = !0))
                }
            });
            B.absUrl() != D && v.url(B.absUrl(), !0);
            v.onUrlChange(function(G) {
                B.absUrl() != G && (a.$broadcast("$locationChangeStart", G, B.absUrl()).defaultPrevented ? v.url(B.absUrl()) : (a.$evalAsync(function() {
                    var H = B.absUrl();
                    B.$$parse(G);
                    z(H)
                }), a.$$phase || a.$digest()))
            });
            var E = 0;
            a.$watch(function() {
                var G = v.url(),
                H = B.$$replace;
                E && G == B.absUrl() || (E++, a.$evalAsync(function() {
                    a.$broadcast("$locationChangeStart", B.absUrl(), G).defaultPrevented ? B.$$parse(G) : (v.url(B.absUrl(), H), z(G))
                }));
                B.$$replace = !1;
                return E
            });
            return B
        }]
    }
    function bS() {
        var s = !0,
        p = this;
        this.debugEnabled = function(v) {
            return c3(v) ? (s = v, this) : s
        };
        this.$get = ["$window", 
        function(a) {
            function v(x) {
                x instanceof Error && (x.stack ? x = x.message && -1 === x.stack.indexOf(x.message) ? "Error: " + x.message + "\n" + x.stack: x.stack: x.sourceURL && (x = x.message + "\n" + x.sourceURL + ":" + x.line));
                return x
            }
            function w(x) {
                var z = a.console || {},
                B = z[x] || z.log || cS;
                return B.apply ? 
                function() {
                    var D = [];
                    bV(arguments, 
                    function(E) {
                        D.push(v(E))
                    });
                    return B.apply(z, D)
                }: function(D, E) {
                    B(D, null == E ? "": E)
                }
            }
            return {
                log: w("log"),
                info: w("info"),
                warn: w("warn"),
                error: w("error"),
                debug: function() {
                    var x = w("debug");
                    return function() {
                        s && x.apply(p, arguments)
                    }
                } ()
            }
        }]
    }
    function b6(s, p) {
        if ("constructor" === s) {
            throw dj("isecfld", p)
        }
        return s
    }
    function au(s, p) {
        if (s && s.constructor === s) {
            throw dj("isecfn", p)
        }
        if (s && s.document && s.location && s.alert && s.setInterval) {
            throw dj("isecwindow", p)
        }
        if (s && (s.nodeName || s.on && s.find)) {
            throw dj("isecdom", p)
        }
        return s
    }
    function aE(s, p, v, w, x) {
        x = x || {};
        p = p.split(".");
        for (var z, B = 0; 1 < p.length; B++) {
            z = b6(p.shift(), w);
            var D = s[z];
            D || (D = {},
            s[z] = D);
            s = D;
            s.then && x.unwrapPromises && (cg(w), "$$v" in s || 
            function(E) {
                E.then(function(a) {
                    E.$$v = a
                })
            } (s), s.$$v === cp && (s.$$v = {}), s = s.$$v)
        }
        z = b6(p.shift(), w);
        return s[z] = v
    }
    function bH(s, p, v, w, x, z, B) {
        b6(s, z);
        b6(p, z);
        b6(v, z);
        b6(w, z);
        b6(x, z);
        return B.unwrapPromises ? 
        function(D, a) {
            var E = a && a.hasOwnProperty(s) ? a: D,
            F;
            if (null === E || E === cp) {
                return E
            } (E = E[s]) && E.then && (cg(z), "$$v" in E || (F = E, F.$$v = cp, F.then(function(G) {
                F.$$v = G
            })), E = E.$$v);
            if (!p || null === E || E === cp) {
                return E
            } (E = E[p]) && E.then && (cg(z), "$$v" in E || (F = E, F.$$v = cp, F.then(function(G) {
                F.$$v = G
            })), E = E.$$v);
            if (!v || null === E || E === cp) {
                return E
            } (E = E[v]) && E.then && (cg(z), "$$v" in E || (F = E, F.$$v = cp, F.then(function(G) {
                F.$$v = G
            })), E = E.$$v);
            if (!w || null === E || E === cp) {
                return E
            } (E = E[w]) && E.then && (cg(z), "$$v" in E || (F = E, F.$$v = cp, F.then(function(G) {
                F.$$v = G
            })), E = E.$$v);
            if (!x || null === E || E === cp) {
                return E
            } (E = E[x]) && E.then && (cg(z), "$$v" in E || (F = E, F.$$v = cp, F.then(function(G) {
                F.$$v = G
            })), E = E.$$v);
            return E
        }: function(a, D) {
            var E = D && D.hasOwnProperty(s) ? D: a;
            if (null === E || E === cp) {
                return E
            }
            E = E[s];
            if (!p || null === E || E === cp) {
                return E
            }
            E = E[p];
            if (!v || null === E || E === cp) {
                return E
            }
            E = E[v];
            if (!w || null === E || E === cp) {
                return E
            }
            E = E[w];
            return x && null !== E && E !== cp ? E = E[x] : E
        }
    }
    function bQ(s, p, v) {
        if (aF.hasOwnProperty(s)) {
            return aF[s]
        }
        var w = s.split("."),
        x = w.length,
        z;
        if (p.csp) {
            z = 6 > x ? bH(w[0], w[1], w[2], w[3], w[4], v, p) : function(a, E) {
                var G = 0,
                F;
                do {
                    F = bH(w[G++], w[G++], w[G++], w[G++], w[G++], v, p)(a, E),
                    E = cp,
                    a = F
                }
                while (G < x);
                return F
            }
        } else {
            var B = "var l, fn, p;\n";
            bV(w, 
            function(a, E) {
                b6(a, v);
                B += "if(s === null || s === undefined) return s;\nl=s;\ns=" + (E ? "s": '((k&&k.hasOwnProperty("' + a + '"))?k:s)') + '["' + a + '"];\n' + (p.unwrapPromises ? 'if (s && s.then) {\n pw("' + v.replace(/\"/g, '\\"') + '");\n if (!("$$v" in s)) {\n p=s;\n p.$$v = undefined;\n p.then(function(v) {p.$$v=v;});\n}\n s=s.$$v\n}\n': "")
            });
            var B = B + "return s;",
            D = Function("s", "k", "pw", B);
            D.toString = function() {
                return B
            };
            z = function(E, F) {
                return D(E, F, cg)
            }
        }
        "hasOwnProperty" !== s && (aF[s] = z);
        return z
    }
    function b2() {
        var s = {},
        p = {
            csp: !1,
            unwrapPromises: !1,
            logPromiseWarnings: !0
        };
        this.unwrapPromises = function(a) {
            return c3(a) ? (p.unwrapPromises = !!a, this) : p.unwrapPromises
        };
        this.logPromiseWarnings = function(a) {
            return c3(a) ? (p.logPromiseWarnings = a, this) : p.logPromiseWarnings
        };
        this.$get = ["$filter", "$sniffer", "$log", 
        function(a, v, w) {
            p.csp = v.csp;
            cg = function(x) {
                p.logPromiseWarnings && !b0.hasOwnProperty(x) && (b0[x] = !0, w.warn("[$parse] Promise found in the expression `" + x + "`. Automatic unwrapping of promises in Angular expressions is deprecated."))
            };
            return function(x) {
                var z;
                switch (typeof x) {
                case "string":
                    if (s.hasOwnProperty(x)) {
                        return s[x]
                    }
                    z = new aP(p);
                    z = (new cK(z, a, p)).parse(x, !1);
                    "hasOwnProperty" !== x && (s[x] = z);
                    return z;
                case "function":
                    return x;
                default:
                    return cS
                }
            }
        }]
    }
    function cc() {
        this.$get = ["$rootScope", "$exceptionHandler", 
        function(s, p) {
            return cm(function(v) {
                s.$evalAsync(v)
            },
            p)
        }]
    }
    function cm(s, p) {
        function v(D) {
            return D
        }
        function w(D) {
            return B(D)
        }
        var x = function() {
            var a = [],
            E,
            D;
            return D = {
                resolve: function(F) {
                    if (a) {
                        var G = a;
                        a = cp;
                        E = z(F);
                        G.length && s(function() {
                            for (var H, J = 0, Q = G.length; J < Q; J++) {
                                H = G[J],
                                E.then(H[0], H[1], H[2])
                            }
                        })
                    }
                },
                reject: function(F) {
                    D.resolve(B(F))
                },
                notify: function(F) {
                    if (a) {
                        var G = a;
                        a.length && s(function() {
                            for (var H, J = 0, Q = G.length; J < Q; J++) {
                                H = G[J],
                                H[2](F)
                            }
                        })
                    }
                },
                promise: {
                    then: function(G, H, J) {
                        var Q = x(),
                        S = function(V) {
                            try {
                                Q.resolve((ag(G) ? G: v)(V))
                            } catch(W) {
                                Q.reject(W),
                                p(W)
                            }
                        },
                        F = function(V) {
                            try {
                                Q.resolve((ag(H) ? H: w)(V))
                            } catch(W) {
                                Q.reject(W),
                                p(W)
                            }
                        },
                        R = function(V) {
                            try {
                                Q.notify((ag(J) ? J: v)(V))
                            } catch(W) {
                                p(W)
                            }
                        };
                        a ? a.push([S, F, R]) : E.then(S, F, R);
                        return Q.promise
                    },
                    "catch": function(F) {
                        return this.then(null, F)
                    },
                    "finally": function(F) {
                        function G(J, Q) {
                            var R = x();
                            Q ? R.resolve(J) : R.reject(J);
                            return R.promise
                        }
                        function H(J, Q) {
                            var S = null;
                            try {
                                S = (F || v)()
                            } catch(R) {
                                return G(R, !1)
                            }
                            return S && ag(S.then) ? S.then(function() {
                                return G(J, Q)
                            },
                            function(V) {
                                return G(V, !1)
                            }) : G(J, Q)
                        }
                        return this.then(function(J) {
                            return H(J, !0)
                        },
                        function(J) {
                            return H(J, !1)
                        })
                    }
                }
            }
        },
        z = function(D) {
            return D && ag(D.then) ? D: {
                then: function(a) {
                    var E = x();
                    s(function() {
                        E.resolve(a(D))
                    });
                    return E.promise
                }
            }
        },
        B = function(a) {
            return {
                then: function(D, E) {
                    var F = x();
                    s(function() {
                        try {
                            F.resolve((ag(E) ? E: w)(a))
                        } catch(G) {
                            F.reject(G),
                            p(G)
                        }
                    });
                    return F.promise
                }
            }
        };
        return {
            defer: x,
            reject: B,
            when: function(a, F, D, E) {
                var Q = x(),
                J,
                G = function(S) {
                    try {
                        return (ag(F) ? F: v)(S)
                    } catch(V) {
                        return p(V),
                        B(V)
                    }
                },
                R = function(S) {
                    try {
                        return (ag(D) ? D: w)(S)
                    } catch(V) {
                        return p(V),
                        B(V)
                    }
                },
                H = function(S) {
                    try {
                        return (ag(E) ? E: v)(S)
                    } catch(V) {
                        p(V)
                    }
                };
                s(function() {
                    z(a).then(function(S) {
                        J || (J = !0, Q.resolve(z(S).then(G, R, H)))
                    },
                    function(S) {
                        J || (J = !0, Q.resolve(R(S)))
                    },
                    function(S) {
                        J || Q.notify(H(S))
                    })
                });
                return Q.promise
            },
            all: function(D) {
                var E = x(),
                F = 0,
                G = aL(D) ? [] : {};
                bV(D, 
                function(H, J) {
                    F++;
                    z(H).then(function(Q) {
                        G.hasOwnProperty(J) || (G[J] = Q, --F || E.resolve(G))
                    },
                    function(Q) {
                        G.hasOwnProperty(J) || E.reject(Q)
                    })
                });
                0 === F && E.resolve(G);
                return E.promise
            }
        }
    }
    function cx() {
        var s = 10,
        p = U("$rootScope");
        this.digestTtl = function(v) {
            arguments.length && (s = v);
            return s
        };
        this.$get = ["$injector", "$exceptionHandler", "$parse", "$browser", 
        function(a, v, w, x) {
            function z() {
                this.$id = cV();
                this.$$phase = this.$parent = this.$$watchers = this.$$nextSibling = this.$$prevSibling = this.$$childHead = this.$$childTail = null;
                this["this"] = this.$root = this;
                this.$$destroyed = !1;
                this.$$asyncQueue = [];
                this.$$postDigestQueue = [];
                this.$$listeners = {};
                this.$$isolateBindings = {}
            }
            function B(G) {
                if (E.$$phase) {
                    throw p("inprog", E.$$phase)
                }
                E.$$phase = G
            }
            function F(G, H) {
                var J = w(G);
                bn(J, H);
                return J
            }
            function D() {}
            z.prototype = {
                constructor: z,
                $new: function(G) {
                    G ? (G = new z, G.$root = this.$root, G.$$asyncQueue = this.$$asyncQueue, G.$$postDigestQueue = this.$$postDigestQueue) : (G = function() {},
                    G.prototype = this, G = new G, G.$id = cV());
                    G["this"] = G;
                    G.$$listeners = {};
                    G.$parent = this;
                    G.$$watchers = G.$$nextSibling = G.$$childHead = G.$$childTail = null;
                    G.$$prevSibling = this.$$childTail;
                    this.$$childHead ? this.$$childTail = this.$$childTail.$$nextSibling = G: this.$$childHead = this.$$childTail = G;
                    return G
                },
                $watch: function(G, H, J) {
                    var Q = F(G, "watch"),
                    R = this.$$watchers,
                    S = {
                        fn: H,
                        last: D,
                        get: Q,
                        exp: G,
                        eq: !!J
                    };
                    if (!ag(H)) {
                        var V = F(H || cS, "listener");
                        S.fn = function(X, Y, dc) {
                            V(dc)
                        }
                    }
                    if ("string" == typeof G && Q.constant) {
                        var W = S.fn;
                        S.fn = function(X, Y, dc) {
                            W.call(this, X, Y, dc);
                            aW(R, S)
                        }
                    }
                    R || (R = this.$$watchers = []);
                    R.unshift(S);
                    return function() {
                        aW(R, S)
                    }
                },
                $watchCollection: function(G, H) {
                    var J = this,
                    Q,
                    R,
                    S = 0,
                    V = w(G),
                    W = [],
                    X = {},
                    Y = 0;
                    return this.$watch(function() {
                        R = V(J);
                        var dc,
                        dd;
                        if (cq(R)) {
                            if (bF(R)) {
                                for (Q !== W && (Q = W, Y = Q.length = 0, S++), dc = R.length, Y !== dc && (S++, Q.length = Y = dc), dd = 0; dd < dc; dd++) {
                                    Q[dd] !== R[dd] && (S++, Q[dd] = R[dd])
                                }
                            } else {
                                Q !== X && (Q = X = {},
                                Y = 0, S++);
                                dc = 0;
                                for (dd in R) {
                                    R.hasOwnProperty(dd) && (dc++, Q.hasOwnProperty(dd) ? Q[dd] !== R[dd] && (S++, Q[dd] = R[dd]) : (Y++, Q[dd] = R[dd], S++))
                                }
                                if (Y > dc) {
                                    for (dd in S++, Q) {
                                        Q.hasOwnProperty(dd) && !R.hasOwnProperty(dd) && (Y--, delete Q[dd])
                                    }
                                }
                            }
                        } else {
                            Q !== R && (Q = R, S++)
                        }
                        return S
                    },
                    function() {
                        H(R, Q, J)
                    })
                },
                $digest: function() {
                    var H,
                    Q,
                    R,
                    S,
                    X = this.$$asyncQueue,
                    dd = this.$$postDigestQueue,
                    de,
                    dP,
                    Y = s,
                    dN,
                    dQ = [],
                    dO,
                    dc,
                    J;
                    B("$digest");
                    do {
                        dP = !1;
                        for (dN = this; X.length;) {
                            try {
                                J = X.shift(),
                                J.scope.$eval(J.expression)
                            } catch(V) {
                                v(V)
                            }
                        }
                        do {
                            if (S = dN.$$watchers) {
                                for (de = S.length; de--;) {
                                    try { (H = S[de]) && ((Q = H.get(dN)) !== (R = H.last) && !(H.eq ? h(Q, R) : "number" == typeof Q && "number" == typeof R && isNaN(Q) && isNaN(R))) && (dP = !0, H.last = H.eq ? ar(Q) : Q, H.fn(Q, R === D ? Q: R, dN), 5 > Y && (dO = 4 - Y, dQ[dO] || (dQ[dO] = []), dc = ag(H.exp) ? "fn: " + (H.exp.name || H.exp.toString()) : H.exp, dc += "; newVal: " + bM(Q) + "; oldVal: " + bM(R), dQ[dO].push(dc)))
                                    } catch(W) {
                                        v(W)
                                    }
                                }
                            }
                            if (! (S = dN.$$childHead || dN !== this && dN.$$nextSibling)) {
                                for (; dN !== this && !(S = dN.$$nextSibling);) {
                                    dN = dN.$parent
                                }
                            }
                        }
                        while (dN = S);
                        if (dP && !Y--) {
                            throw E.$$phase = null,
                            p("infdig", s, bM(dQ))
                        }
                    }
                    while (dP || X.length);
                    for (E.$$phase = null; dd.length;) {
                        try {
                            dd.shift()()
                        } catch(G) {
                            v(G)
                        }
                    }
                },
                $destroy: function() {
                    if (E != this && !this.$$destroyed) {
                        var G = this.$parent;
                        this.$broadcast("$destroy");
                        this.$$destroyed = !0;
                        G.$$childHead == this && (G.$$childHead = this.$$nextSibling);
                        G.$$childTail == this && (G.$$childTail = this.$$prevSibling);
                        this.$$prevSibling && (this.$$prevSibling.$$nextSibling = this.$$nextSibling);
                        this.$$nextSibling && (this.$$nextSibling.$$prevSibling = this.$$prevSibling);
                        this.$parent = this.$$nextSibling = this.$$prevSibling = this.$$childHead = this.$$childTail = null
                    }
                },
                $eval: function(G, H) {
                    return w(G)(this, H)
                },
                $evalAsync: function(G) {
                    E.$$phase || E.$$asyncQueue.length || x.defer(function() {
                        E.$$asyncQueue.length && E.$digest()
                    });
                    this.$$asyncQueue.push({
                        scope: this,
                        expression: G
                    })
                },
                $$postDigest: function(G) {
                    this.$$postDigestQueue.push(G)
                },
                $apply: function(G) {
                    try {
                        return B("$apply"),
                        this.$eval(G)
                    } catch(H) {
                        v(H)
                    } finally {
                        E.$$phase = null;
                        try {
                            E.$digest()
                        } catch(J) {
                            throw v(J),
                            J
                        }
                    }
                },
                $on: function(G, H) {
                    var J = this.$$listeners[G];
                    J || (this.$$listeners[G] = J = []);
                    J.push(H);
                    return function() {
                        J[dv(J, H)] = null
                    }
                },
                $emit: function(G, H) {
                    var J = [],
                    Q,
                    R = this,
                    S = !1,
                    V = {
                        name: G,
                        targetScope: R,
                        stopPropagation: function() {
                            S = !0
                        },
                        preventDefault: function() {
                            V.defaultPrevented = !0
                        },
                        defaultPrevented: !1
                    },
                    W = [V].concat(cJ.call(arguments, 1)),
                    X,
                    Y;
                    do {
                        Q = R.$$listeners[G] || J;
                        V.currentScope = R;
                        X = 0;
                        for (Y = Q.length; X < Y; X++) {
                            if (Q[X]) {
                                try {
                                    Q[X].apply(null, W)
                                } catch(dc) {
                                    v(dc)
                                }
                            } else {
                                Q.splice(X, 1),
                                X--,
                                Y--
                            }
                        }
                        if (S) {
                            break
                        }
                        R = R.$parent
                    }
                    while (R);
                    return V
                },
                $broadcast: function(G, H) {
                    var J = this,
                    Q = this,
                    R = {
                        name: G,
                        targetScope: this,
                        preventDefault: function() {
                            R.defaultPrevented = !0
                        },
                        defaultPrevented: !1
                    },
                    S = [R].concat(cJ.call(arguments, 1)),
                    V,
                    W;
                    do {
                        J = Q;
                        R.currentScope = J;
                        Q = J.$$listeners[G] || [];
                        V = 0;
                        for (W = Q.length; V < W; V++) {
                            if (Q[V]) {
                                try {
                                    Q[V].apply(null, S)
                                } catch(X) {
                                    v(X)
                                }
                            } else {
                                Q.splice(V, 1),
                                V--,
                                W--
                            }
                        }
                        if (! (Q = J.$$childHead || J !== this && J.$$nextSibling)) {
                            for (; J !== this && !(Q = J.$$nextSibling);) {
                                J = J.$parent
                            }
                        }
                    }
                    while (J = Q);
                    return R
                }
            };
            var E = new z;
            return E
        }]
    }
    function cG(a) {
        if ("self" === a) {
            return a
        }
        if (aq(a)) {
            if ( - 1 < a.indexOf("***")) {
                throw cr("iwcard", a)
            }
            a = a.replace(/([-()\[\]{}+?*.$\^|,:#<!\\])/g, "\\$1").replace(/\x08/g, "\\x08").replace("\\*\\*", ".*").replace("\\*", "[^:/.?&;]*");
            return RegExp("^" + a + "$")
        }
        if (dk(a)) {
            return RegExp("^" + a.source + "$")
        }
        throw cr("imatcher")
    }
    function ca(s) {
        var p = [];
        c3(s) && bV(s, 
        function(a) {
            p.push(cG(a))
        });
        return p
    }
    function cP() {
        this.SCE_CONTEXTS = ah;
        var s = ["self"],
        p = [];
        this.resourceUrlWhitelist = function(v) {
            arguments.length && (s = ca(v));
            return s
        };
        this.resourceUrlBlacklist = function(a) {
            arguments.length && (p = ca(a));
            return p
        };
        this.$get = ["$log", "$document", "$injector", 
        function(a, v, w) {
            function x(E) {
                var F = function(G) {
                    this.$$unwrapTrustedValue = function() {
                        return G
                    }
                };
                E && (F.prototype = new E);
                F.prototype.valueOf = function() {
                    return this.$$unwrapTrustedValue()
                };
                F.prototype.toString = function() {
                    return this.$$unwrapTrustedValue().toString()
                };
                return F
            }
            var z = function(E) {
                throw cr("unsafe")
            };
            w.has("$sanitize") && (z = w.get("$sanitize"));
            var B = x(),
            D = {};
            D[ah.HTML] = x(B);
            D[ah.CSS] = x(B);
            D[ah.URL] = x(B);
            D[ah.JS] = x(B);
            D[ah.RESOURCE_URL] = x(D[ah.URL]);
            return {
                trustAs: function(E, F) {
                    var G = D.hasOwnProperty(E) ? D[E] : null;
                    if (!G) {
                        throw cr("icontext", E, F)
                    }
                    if (null === F || F === cp || "" === F) {
                        return F
                    }
                    if ("string" !== typeof F) {
                        throw cr("itype", E)
                    }
                    return new G(F)
                },
                getTrusted: function(E, F) {
                    if (null === F || F === cp || "" === F) {
                        return F
                    }
                    var G = D.hasOwnProperty(E) ? D[E] : null;
                    if (G && F instanceof G) {
                        return F.$$unwrapTrustedValue()
                    }
                    if (E === ah.RESOURCE_URL) {
                        var G = c5(F.toString()),
                        H,
                        J,
                        Q = !1;
                        H = 0;
                        for (J = s.length; H < J; H++) {
                            if ("self" === s[H] ? M(G) : s[H].exec(G.href)) {
                                Q = !0;
                                break
                            }
                        }
                        if (Q) {
                            for (H = 0, J = p.length; H < J; H++) {
                                if ("self" === p[H] ? M(G) : p[H].exec(G.href)) {
                                    Q = !1;
                                    break
                                }
                            }
                        }
                        if (Q) {
                            return F
                        }
                        throw cr("insecurl", F.toString())
                    }
                    if (E === ah.HTML) {
                        return z(F)
                    }
                    throw cr("unsafe")
                },
                valueOf: function(E) {
                    return E instanceof B ? E.$$unwrapTrustedValue() : E
                }
            }
        }]
    }
    function c0() {
        var a = !0;
        this.enabled = function(p) {
            arguments.length && (a = !!p);
            return a
        };
        this.$get = ["$parse", "$document", "$sceDelegate", 
        function(p, s, v) {
            if (a && b5 && (s = s[0].documentMode, s !== cp && 8 > s)) {
                throw cr("iequirks")
            }
            var w = ar(ah);
            w.isEnabled = function() {
                return a
            };
            w.trustAs = v.trustAs;
            w.getTrusted = v.getTrusted;
            w.valueOf = v.valueOf;
            a || (w.trustAs = w.getTrusted = function(D, E) {
                return E
            },
            w.valueOf = dE);
            w.parseAs = function(D, E) {
                var F = p(E);
                return F.literal && F.constant ? F: function(G, H) {
                    return w.getTrusted(D, F(G, H))
                }
            };
            var x = w.parseAs,
            z = w.getTrusted,
            B = w.trustAs;
            bV(ah, 
            function(D, E) {
                var F = o(E);
                w[bv("parse_as_" + F)] = function(G) {
                    return x(D, G)
                };
                w[bv("get_trusted_" + F)] = function(G) {
                    return z(D, G)
                };
                w[bv("trust_as_" + F)] = function(G) {
                    return B(D, G)
                }
            });
            return w
        }]
    }
    function db() {
        this.$get = ["$window", "$document", 
        function(s, p) {
            var v = {},
            w = c4((/android (\d+)/.exec(o((s.navigator || {}).userAgent)) || [])[1]),
            x = /Boxee/i.test((s.navigator || {}).userAgent),
            z = p[0] || {},
            B,
            D = /^(Moz|webkit|O|ms)(?=[A-Z])/,
            G = z.body && z.body.style,
            E = !1,
            F = !1;
            if (G) {
                for (var H in G) {
                    if (E = D.exec(H)) {
                        B = E[0];
                        B = B.substr(0, 1).toUpperCase() + B.substr(1);
                        break
                    }
                }
                B || (B = "WebkitOpacity" in G && "webkit");
                E = !!("transition" in G || B + "Transition" in G);
                F = !!("animation" in G || B + "Animation" in G); ! w || E && F || (E = aq(z.body.style.webkitTransition), F = aq(z.body.style.webkitAnimation))
            }
            return {
                history: !(!s.history || !s.history.pushState || 4 > w || x),
                hashchange: "onhashchange" in s && (!z.documentMode || 7 < z.documentMode),
                hasEvent: function(J) {
                    if ("input" == J && 9 == b5) {
                        return ! 1
                    }
                    if (dD(v[J])) {
                        var Q = z.createElement("div");
                        v[J] = "on" + J in Q
                    }
                    return v[J]
                },
                csp: z.securityPolicy ? z.securityPolicy.isActive: !1,
                vendorPrefix: B,
                transitions: E,
                animations: F
            }
        }]
    }
    function dq() {
        this.$get = ["$rootScope", "$browser", "$q", "$exceptionHandler", 
        function(s, p, v, w) {
            function x(a, B, F) {
                var D = v.defer(),
                E = D.promise,
                G = c3(F) && !F;
                B = p.defer(function() {
                    try {
                        D.resolve(a())
                    } catch(H) {
                        D.reject(H),
                        w(H)
                    } finally {
                        delete z[E.$$timeoutId]
                    }
                    G || s.$apply()
                },
                B);
                E.$$timeoutId = B;
                z[B] = D;
                return E
            }
            var z = {};
            x.cancel = function(a) {
                return a && a.$$timeoutId in z ? (z[a.$$timeoutId].reject("canceled"), delete z[a.$$timeoutId], p.defer.cancel(a.$$timeoutId)) : !1
            };
            return x
        }]
    }
    function c5(a) {
        b5 && (cT.setAttribute("href", a), a = cT.href);
        cT.setAttribute("href", a);
        return {
            href: cT.href,
            protocol: cT.protocol ? cT.protocol.replace(/:$/, "") : "",
            host: cT.host,
            search: cT.search ? cT.search.replace(/^\?/, "") : "",
            hash: cT.hash ? cT.hash.replace(/^#/, "") : "",
            hostname: cT.hostname,
            port: cT.port,
            pathname: cT.pathname && "/" === cT.pathname.charAt(0) ? cT.pathname: "/" + cT.pathname
        }
    }
    function M(a) {
        a = aq(a) ? c5(a) : a;
        return a.protocol === ck.protocol && a.host === ck.host
    }
    function dA() {
        this.$get = g(dt)
    }
    function cv(s) {
        function p(a, w) {
            if (cq(a)) {
                var x = {};
                bV(a, 
                function(z, B) {
                    x[B] = p(B, z)
                });
                return x
            }
            return s.factory(a + v, w)
        }
        var v = "Filter";
        this.register = p;
        this.$get = ["$injector", 
        function(w) {
            return function(a) {
                return w.get(a + v)
            }
        }];
        p("currency", cE);
        p("date", cN);
        p("filter", dK);
        p("json", m);
        p("limitTo", C);
        p("lowercase", P);
        p("number", cY);
        p("orderBy", c9);
        p("uppercase", ae)
    }
    function dK() {
        return function(s, p, v) {
            if (!aL(s)) {
                return s
            }
            var w = [];
            w.check = function(F) {
                for (var G = 0; G < w.length; G++) {
                    if (!w[G](F)) {
                        return ! 1
                    }
                }
                return ! 0
            };
            switch (typeof v) {
            case "function":
                break;
            case "boolean":
                if (!0 == v) {
                    v = function(F, G) {
                        return dF.equals(F, G)
                    };
                    break
                }
            default:
                v = function(F, G) {
                    G = ("" + G).toLowerCase();
                    return - 1 < ("" + F).toLowerCase().indexOf(G)
                }
            }
            var x = function(F, G) {
                if ("string" == typeof G && "!" === G.charAt(0)) {
                    return ! x(F, G.substr(1))
                }
                switch (typeof F) {
                case "boolean":
                case "number":
                case "string":
                    return v(F, G);
                case "object":
                    switch (typeof G) {
                    case "object":
                        return v(F, G);
                    default:
                        for (var H in F) {
                            if ("$" !== H.charAt(0) && x(F[H], G)) {
                                return ! 0
                            }
                        }
                    }
                    return ! 1;
                case "array":
                    for (H = 0; H < F.length; H++) {
                        if (x(F[H], G)) {
                            return ! 0
                        }
                    }
                    return ! 1;
                default:
                    return ! 1
                }
            };
            switch (typeof p) {
            case "boolean":
            case "number":
            case "string":
                p = {
                    $: p
                };
            case "object":
                for (var z in p) {
                    "$" == z ? 
                    function() {
                        if (p[z]) {
                            var a = z;
                            w.push(function(F) {
                                return x(F, p[a])
                            })
                        }
                    } () : function() {
                        if ("undefined" != typeof p[z]) {
                            var a = z;
                            w.push(function(F) {
                                return x(ct(F, a), p[a])
                            })
                        }
                    } ()
                }
                break;
            case "function":
                w.push(p);
                break;
            default:
                return s
            }
            for (var B = [], D = 0; D < s.length; D++) {
                var E = s[D];
                w.check(E) && B.push(E)
            }
            return B
        }
    }
    function cE(s) {
        var p = s.NUMBER_FORMATS;
        return function(a, v) {
            dD(v) && (v = p.CURRENCY_SYM);
            return dn(a, p.PATTERNS[1], p.GROUP_SEP, p.DECIMAL_SEP, 2).replace(/\u00A4/g, v)
        }
    }
    function cY(s) {
        var p = s.NUMBER_FORMATS;
        return function(a, v) {
            return dn(a, p.PATTERNS[0], p.GROUP_SEP, p.DECIMAL_SEP, v)
        }
    }
    function dn(s, p, v, w, x) {
        if (isNaN(s) || !isFinite(s)) {
            return ""
        }
        var z = 0 > s;
        s = Math.abs(s);
        var B = s + "",
        D = "",
        G = [],
        E = !1;
        if ( - 1 !== B.indexOf("e")) {
            var F = B.match(/([\d\.]+)e(-?)(\d+)/);
            F && "-" == F[2] && F[3] > x + 1 ? B = "0": (D = B, E = !0)
        }
        if (E) {
            0 < x && ( - 1 < s && 1 > s) && (D = s.toFixed(x))
        } else {
            B = (B.split(dy)[1] || "").length;
            dD(x) && (x = Math.min(Math.max(p.minFrac, B), p.maxFrac));
            B = Math.pow(10, x);
            s = Math.round(s * B) / B;
            s = ("" + s).split(dy);
            B = s[0];
            s = s[1] || "";
            var E = 0,
            F = p.lgSize,
            J = p.gSize;
            if (B.length >= F + J) {
                for (var E = B.length - F, H = 0; H < E; H++) {
                    0 === (E - H) % J && 0 !== H && (D += v),
                    D += B.charAt(H)
                }
            }
            for (H = E; H < B.length; H++) {
                0 === (B.length - H) % F && 0 !== H && (D += v),
                D += B.charAt(H)
            }
            for (; s.length < x;) {
                s += "0"
            }
            x && "0" !== x && (D += w + s.substr(0, x))
        }
        G.push(z ? p.negPre: p.posPre);
        G.push(D);
        G.push(z ? p.negSuf: p.posSuf);
        return G.join("")
    }
    function aY(s, p, v) {
        var w = "";
        0 > s && (w = "-", s = -s);
        for (s = "" + s; s.length < p;) {
            s = "0" + s
        }
        v && (s = s.substr(s.length - p));
        return w + s
    }
    function di(s, p, v, w) {
        v = v || 0;
        return function(a) {
            a = a["get" + s]();
            if (0 < v || a > -v) {
                a += v
            }
            0 === a && -12 == v && (a = 12);
            return aY(a, p, w)
        }
    }
    function aO(s, p) {
        return function(a, v) {
            var w = a["get" + s](),
            x = ai(p ? "SHORT" + s: s);
            return v[x][w]
        }
    }
    function cN(s) {
        function p(w) {
            var x;
            if (x = w.match(v)) {
                w = new Date(0);
                var z = 0,
                B = 0,
                D = x[8] ? w.setUTCFullYear: w.setFullYear,
                E = x[8] ? w.setUTCHours: w.setHours;
                x[9] && (z = c4(x[9] + x[10]), B = c4(x[9] + x[11]));
                D.call(w, c4(x[1]), c4(x[2]) - 1, c4(x[3]));
                z = c4(x[4] || 0) - z;
                B = c4(x[5] || 0) - B;
                D = c4(x[6] || 0);
                x = Math.round(1000 * parseFloat("0." + (x[7] || 0)));
                E.call(w, z, B, D, x)
            }
            return w
        }
        var v = /^(\d{4})-?(\d\d)-?(\d\d)(?:T(\d\d)(?::?(\d\d)(?::?(\d\d)(?:\.(\d+))?)?)?(Z|([+-])(\d\d):?(\d\d))?)?$/;
        return function(a, w) {
            var x = "",
            z = [],
            B,
            D;
            w = w || "mediumDate";
            w = s.DATETIME_FORMATS[w] || w;
            aq(a) && (a = ao.test(a) ? c4(a) : p(a));
            bO(a) && (a = new Date(a));
            if (!aN(a)) {
                return a
            }
            for (; w;) { (D = az.exec(w)) ? (z = z.concat(cJ.call(D, 1)), w = z.pop()) : (z.push(w), w = null)
            }
            bV(z, 
            function(E) {
                B = aJ[E];
                x += B ? B(a, s.DATETIME_FORMATS) : E.replace(/(^'|'$)/g, "").replace(/''/g, "'")
            });
            return x
        }
    }
    function m() {
        return function(a) {
            return bM(a, !0)
        }
    }
    function C() {
        return function(s, p) {
            if (!aL(s) && !aq(s)) {
                return s
            }
            p = c4(p);
            if (aq(s)) {
                return p ? 0 <= p ? s.slice(0, p) : s.slice(p, s.length) : ""
            }
            var v = [],
            w,
            x;
            p > s.length ? p = s.length: p < -s.length && (p = -s.length);
            0 < p ? (w = 0, x = p) : (w = s.length + p, x = s.length);
            for (; w < x; w++) {
                v.push(s[w])
            }
            return v
        }
    }
    function c9(a) {
        return function(p, s, v) {
            function w(B, D) {
                return be(D) ? 
                function(E, F) {
                    return B(F, E)
                }: B
            }
            if (!aL(p) || !s) {
                return p
            }
            s = aL(s) ? s: [s];
            s = ba(s, 
            function(B) {
                var D = !1,
                E = B || dE;
                if (aq(B)) {
                    if ("+" == B.charAt(0) || "-" == B.charAt(0)) {
                        D = "-" == B.charAt(0),
                        B = B.substring(1)
                    }
                    E = a(B)
                }
                return w(function(F, G) {
                    var H;
                    H = E(F);
                    var J = E(G),
                    Q = typeof H,
                    R = typeof J;
                    Q == R ? ("string" == Q && (H = H.toLowerCase(), J = J.toLowerCase()), H = H === J ? 0: H < J ? -1: 1) : H = Q < R ? -1: 1;
                    return H
                },
                D)
            });
            for (var x = [], z = 0; z < p.length; z++) {
                x.push(p[z])
            }
            return x.sort(w(function(B, D) {
                for (var E = 0; E < s.length; E++) {
                    var F = s[E](B, D);
                    if (0 !== F) {
                        return F
                    }
                }
                return 0
            },
            v))
        }
    }
    function cA(a) {
        ag(a) && (a = {
            link: a
        });
        a.restrict = a.restrict || "AC";
        return g(a)
    }
    function dI(s, p) {
        function v(E, F) {
            F = F ? "-" + b(F, "-") : "";
            s.removeClass((E ? aX: a7) + F).addClass((E ? a7: aX) + F)
        }
        var w = this,
        x = s.parent().controller("form") || bf,
        z = 0,
        B = w.$error = {},
        D = [];
        w.$name = p.name || p.ngForm;
        w.$dirty = !1;
        w.$pristine = !0;
        w.$valid = !0;
        w.$invalid = !1;
        x.$addControl(w);
        s.addClass(at);
        v(!0);
        w.$addControl = function(E) {
            bW(E.$name, "input");
            D.push(E);
            E.$name && (w[E.$name] = E)
        };
        w.$removeControl = function(E) {
            E.$name && w[E.$name] === E && delete w[E.$name];
            bV(B, 
            function(a, F) {
                w.$setValidity(F, !0, E)
            });
            aW(D, E)
        };
        w.$setValidity = function(E, F, G) {
            var H = B[E];
            if (F) {
                H && (aW(H, G), H.length || (z--, z || (v(F), w.$valid = !0, w.$invalid = !1), B[E] = !1, v(!0, E), x.$setValidity(E, !0, w)))
            } else {
                z || v(F);
                if (H) {
                    if ( - 1 != dv(H, G)) {
                        return
                    }
                } else {
                    B[E] = H = [],
                    z++,
                    v(!1, E),
                    x.$setValidity(E, !1, w)
                }
                H.push(G);
                w.$valid = !1;
                w.$invalid = !0
            }
        };
        w.$setDirty = function() {
            s.removeClass(at).addClass(bo);
            w.$dirty = !0;
            w.$pristine = !1;
            x.$setDirty()
        };
        w.$setPristine = function() {
            s.removeClass(bo).addClass(at);
            w.$dirty = !1;
            w.$pristine = !0;
            bV(D, 
            function(E) {
                E.$setPristine()
            })
        }
    }
    function bw(s, p, v, w, x, z) {
        var B = function() {
            var a = p.val();
            be(v.ngTrim || "T") && (a = q(a));
            w.$viewValue !== a && s.$apply(function() {
                w.$setViewValue(a)
            })
        };
        if (x.hasEvent("input")) {
            p.on("input", B)
        } else {
            var D,
            G = function() {
                D || (D = z.defer(function() {
                    B();
                    D = null
                }))
            };
            p.on("keydown", 
            function(Q) {
                Q = Q.keyCode;
                91 === Q || (15 < Q && 19 > Q || 37 <= Q && 40 >= Q) || G()
            });
            p.on("change", B);
            if (x.hasEvent("paste")) {
                p.on("paste cut", G)
            }
        }
        w.$render = function() {
            p.val(w.$isEmpty(w.$viewValue) ? "": w.$viewValue)
        };
        var E = v.ngPattern,
        F = function(Q, R) {
            if (w.$isEmpty(R) || Q.test(R)) {
                return w.$setValidity("pattern", !0),
                R
            }
            w.$setValidity("pattern", !1);
            return cp
        };
        E && ((x = E.match(/^\/(.*)\/([gim]*)$/)) ? (E = RegExp(x[1], x[2]), x = function(Q) {
            return F(E, Q)
        }) : x = function(a) {
            var Q = s.$eval(E);
            if (!Q || !Q.test) {
                throw U("ngPattern")("noregexp", E, Q, aC(p))
            }
            return F(Q, a)
        },
        w.$formatters.push(x), w.$parsers.push(x));
        if (v.ngMinlength) {
            var J = c4(v.ngMinlength);
            x = function(Q) {
                if (!w.$isEmpty(Q) && Q.length < J) {
                    return w.$setValidity("minlength", !1),
                    cp
                }
                w.$setValidity("minlength", !0);
                return Q
            };
            w.$parsers.push(x);
            w.$formatters.push(x)
        }
        if (v.ngMaxlength) {
            var H = c4(v.ngMaxlength);
            x = function(Q) {
                if (!w.$isEmpty(Q) && Q.length > H) {
                    return w.$setValidity("maxlength", !1),
                    cp
                }
                w.$setValidity("maxlength", !0);
                return Q
            };
            w.$parsers.push(x);
            w.$formatters.push(x)
        }
    }
    function a8(s, p) {
        s = "ngClass" + s;
        return function() {
            return {
                restrict: "AC",
                link: function(a, v, w) {
                    function x(D) {
                        if (!0 === p || a.$index % 2 === p) {
                            B && !h(D, B) && w.$removeClass(z(B)),
                            w.$addClass(z(D))
                        }
                        B = ar(D)
                    }
                    function z(D) {
                        if (aL(D)) {
                            return D.join(" ")
                        }
                        if (cq(D)) {
                            var E = [];
                            bV(D, 
                            function(F, G) {
                                F && E.push(G)
                            });
                            return E.join(" ")
                        }
                        return D
                    }
                    var B = cp;
                    a.$watch(w[s], x, !0);
                    w.$observe("class", 
                    function(D) {
                        x(a.$eval(w[s]))
                    });
                    "ngClass" !== s && a.$watch("$index", 
                    function(D, E) {
                        var F = D & 1;
                        F !== E & 1 && (F === p ? (F = a.$eval(w[s]), w.$addClass(z(F))) : (F = a.$eval(w[s]), w.$removeClass(z(F))))
                    })
                }
            }
        }
    }
    var o = function(a) {
        return aq(a) ? a.toLowerCase() : a
    },
    ai = function(a) {
        return aq(a) ? a.toUpperCase() : a
    },
    b5,
    dh,
    r,
    cJ = [].slice,
    aT = [].push,
    c6 = Object.prototype.toString,
    a6 = U("ng"),
    dF = dt.angular || (dt.angular = {}),
    ch,
    Z,
    aV = ["0", "0", "0"];
    b5 = c4((/msie (\d+)/.exec(o(navigator.userAgent)) || [])[1]);
    isNaN(b5) && (b5 = c4((/trident\/.*; rv:(\d+)/.exec(o(navigator.userAgent)) || [])[1]));
    cS.$inject = [];
    dE.$inject = [];
    var q = function() {
        return String.prototype.trim ? 
        function(a) {
            return aq(a) ? a.trim() : a
        }: function(a) {
            return aq(a) ? a.replace(/^\s*/, "").replace(/\s*$/, "") : a
        }
    } ();
    Z = 9 > b5 ? 
    function(a) {
        a = a.nodeName ? a: a[0];
        return a.scopeName && "HTML" != a.scopeName ? ai(a.scopeName + ":" + a.nodeName) : a.nodeName
    }: function(a) {
        return a.nodeName ? a.nodeName: a[0].nodeName
    };
    var bI = /[A-Z]/g,
    a2 = {
        full: "1.2.0-rc.3",
        major: 1,
        minor: 2,
        dot: 0,
        codeName: "ferocious-twitch"
    },
    bN = a4.cache = {},
    i = a4.expando = "ng-" + (new Date).getTime(),
    cl = 1,
    k = dt.document.addEventListener ? 
    function(s, p, v) {
        s.addEventListener(p, v, !1)
    }: function(s, p, v) {
        s.attachEvent("on" + p, v)
    },
    dl = dt.document.removeEventListener ? 
    function(s, p, v) {
        s.removeEventListener(p, v, !1)
    }: function(s, p, v) {
        s.detachEvent("on" + p, v)
    },
    b1 = /([\:\-\_]+(.))/g,
    cb = /^moz([A-Z])/,
    cL = U("jqLite"),
    cs = a4.prototype = {
        ready: function(s) {
            function p() {
                v || (v = !0, s())
            }
            var v = !1;
            "complete" === cf.readyState ? setTimeout(p) : (this.on("DOMContentLoaded", p), a4(dt).on("load", p))
        },
        toString: function() {
            var a = [];
            bV(this, 
            function(p) {
                a.push("" + p)
            });
            return "[" + a.join(", ") + "]"
        },
        eq: function(a) {
            return 0 <= a ? dh(this[a]) : dh(this[this.length + a])
        },
        length: 0,
        push: aT,
        sort: [].sort,
        splice: [].splice
    },
    L = {};
    bV("multiple selected checked disabled readOnly required open".split(" "), 
    function(a) {
        L[o(a)] = a
    });
    var dH = {};
    bV("input select option textarea button form details".split(" "), 
    function(a) {
        dH[ai(a)] = !0
    });
    bV({
        data: c8,
        inheritedData: t,
        scope: function(a) {
            return t(a, "$scope")
        },
        controller: dm,
        injector: function(a) {
            return t(a, "$injector")
        },
        removeAttr: function(s, p) {
            s.removeAttribute(p)
        },
        hasClass: dw,
        css: function(s, p, v) {
            p = bv(p);
            if (c3(v)) {
                s.style[p] = v
            } else {
                var w;
                8 >= b5 && (w = s.currentStyle && s.currentStyle[p], "" === w && (w = "auto"));
                w = w || s.style[p];
                8 >= b5 && (w = "" === w ? cp: w);
                return w
            }
        },
        attr: function(s, p, v) {
            var w = o(p);
            if (L[w]) {
                if (c3(v)) {
                    v ? (s[p] = !0, s.setAttribute(p, w)) : (s[p] = !1, s.removeAttribute(w))
                } else {
                    return s[p] || (s.attributes.getNamedItem(p) || cS).specified ? w: cp
                }
            } else {
                if (c3(v)) {
                    s.setAttribute(p, v)
                } else {
                    if (s.getAttribute) {
                        return s = s.getAttribute(p, 2),
                        null === s ? cp: s
                    }
                }
            }
        },
        prop: function(s, p, v) {
            if (c3(v)) {
                s[p] = v
            } else {
                return s[p]
            }
        },
        text: function() {
            function s(a, v) {
                var w = p[a.nodeType];
                if (dD(v)) {
                    return w ? a[w] : ""
                }
                a[w] = v
            }
            var p = [];
            9 > b5 ? (p[1] = "innerText", p[3] = "nodeValue") : p[1] = p[3] = "textContent";
            s.$dv = "";
            return s
        } (),
        val: function(s, p) {
            if (dD(p)) {
                if ("SELECT" === Z(s) && s.multiple) {
                    var v = [];
                    bV(s.options, 
                    function(w) {
                        w.selected && v.push(w.value || w.text)
                    });
                    return 0 === v.length ? null: v
                }
                return s.value
            }
            s.value = p
        },
        html: function(s, p) {
            if (dD(p)) {
                return s.innerHTML
            }
            for (var v = 0, w = s.childNodes; v < w.length; v++) {
                bE(w[v])
            }
            s.innerHTML = p
        }
    },
    function(s, p) {
        a4.prototype[p] = function(v, w) {
            var x,
            z;
            if ((2 == s.length && s !== dw && s !== dm ? v: w) === cp) {
                if (cq(v)) {
                    for (x = 0; x < this.length; x++) {
                        if (s === c8) {
                            s(this[x], v)
                        } else {
                            for (z in v) {
                                s(this[x], z, v[z])
                            }
                        }
                    }
                    return this
                }
                x = s.$dv;
                z = x == cp ? Math.min(this.length, 1) : this.length;
                for (var B = 0; B < z; B++) {
                    var D = s(this[B], v, w);
                    x = x ? x + D: D
                }
                return x
            }
            for (x = 0; x < this.length; x++) {
                s(this[x], v, w)
            }
            return this
        }
    });
    bV({
        removeData: cM,
        dealoc: bE,
        on: function f(a, p, s, v) {
            if (c3(v)) {
                throw cL("onargs")
            }
            var w = a5(a, "events"),
            x = a5(a, "handle");
            w || a5(a, "events", w = {});
            x || a5(a, "handle", x = cw(a, w));
            bV(p.split(" "), 
            function(z) {
                var B = w[z];
                if (!B) {
                    if ("mouseenter" == z || "mouseleave" == z) {
                        var D = cf.body.contains || cf.body.compareDocumentPosition ? 
                        function(E, F) {
                            var G = 9 === E.nodeType ? E.documentElement: E,
                            H = F && F.parentNode;
                            return E === H || !!(H && 1 === H.nodeType && (G.contains ? G.contains(H) : E.compareDocumentPosition && E.compareDocumentPosition(H) & 16))
                        }: function(E, F) {
                            if (F) {
                                for (; F = F.parentNode;) {
                                    if (F === E) {
                                        return ! 0
                                    }
                                }
                            }
                            return ! 1
                        };
                        w[z] = [];
                        f(a, {
                            mouseleave: "mouseout",
                            mouseenter: "mouseover"
                        } [z], 
                        function(E) {
                            var F = E.relatedTarget;
                            F && (F === this || D(this, F)) || x(E, z)
                        })
                    } else {
                        k(a, z, x),
                        w[z] = []
                    }
                    B = w[z]
                }
                B.push(s)
            })
        },
        off: cX,
        replaceWith: function(p, s) {
            var v,
            w = p.parentNode;
            bE(p);
            bV(new a4(s), 
            function(a) {
                v ? w.insertBefore(a, v.nextSibling) : w.replaceChild(a, p);
                v = a
            })
        },
        children: function(p) {
            var s = [];
            bV(p.childNodes, 
            function(v) {
                1 === v.nodeType && s.push(v)
            });
            return s
        },
        contents: function(p) {
            return p.childNodes || []
        },
        append: function(p, s) {
            bV(new a4(s), 
            function(a) {
                1 !== p.nodeType && 11 !== p.nodeType || p.appendChild(a)
            })
        },
        prepend: function(p, s) {
            if (1 === p.nodeType) {
                var v = p.firstChild;
                bV(new a4(s), 
                function(a) {
                    p.insertBefore(a, v)
                })
            }
        },
        wrap: function(p, s) {
            s = dh(s)[0];
            var v = p.parentNode;
            v && v.replaceChild(s, p);
            s.appendChild(p)
        },
        remove: function(p) {
            bE(p);
            var s = p.parentNode;
            s && s.removeChild(p)
        },
        after: function(p, s) {
            var v = p,
            w = p.parentNode;
            bV(new a4(s), 
            function(x) {
                w.insertBefore(x, v.nextSibling);
                v = x
            })
        },
        addClass: j,
        removeClass: dG,
        toggleClass: function(p, s, v) {
            dD(v) && (v = !dw(p, s)); (v ? j: dG)(p, s)
        },
        parent: function(p) {
            return (p = p.parentNode) && 11 !== p.nodeType ? p: null
        },
        next: function(p) {
            if (p.nextElementSibling) {
                return p.nextElementSibling
            }
            for (p = p.nextSibling; null != p && 1 !== p.nodeType;) {
                p = p.nextSibling
            }
            return p
        },
        find: function(p, s) {
            return p.getElementsByTagName(s)
        },
        clone: c7,
        triggerHandler: function(p, s, v) {
            s = (a5(p, "events") || {})[s];
            v = v || [];
            var w = [{
                preventDefault: cS,
                stopPropagation: cS
            }];
            bV(s, 
            function(a) {
                a.apply(p, w.concat(v))
            })
        }
    },
    function(p, s) {
        a4.prototype[s] = function(a, v, w) {
            for (var x, z = 0; z < this.length; z++) {
                x == cp ? (x = p(this[z], a, v, w), x !== cp && (x = dh(x))) : cW(x, p(this[z], a, v, w))
            }
            return x == cp ? this: x
        };
        a4.prototype.bind = a4.prototype.on;
        a4.prototype.unbind = a4.prototype.off
    });
    bX.prototype = {
        put: function(p, s) {
            this[K(p)] = s
        },
        get: function(p) {
            return this[K(p)]
        },
        remove: function(p) {
            var s = this[p = K(p)];
            delete this[p];
            return s
        }
    };
    var cO = /^function\s*[^\(]*\(\s*([^\)]*)\)/m,
    cZ = /,/,
    da = /^\s*(_?)(\S+?)\1\s*$/,
    cF = /((\/\/.*$)|(\/\*[\s\S]*?\*\/))/mg,
    b7 = U("$injector"),
    bc = U("$animate"),
    bk = ["$provide", 
    function(p) {
        this.$$selectors = {};
        this.register = function(a, s) {
            var v = a + "-animation";
            if (a && "." != a.charAt(0)) {
                throw bc("notcsel", a)
            }
            this.$$selectors[a.substr(1)] = v;
            p.factory(v, s)
        };
        this.$get = ["$timeout", 
        function(s) {
            return {
                enter: function(a, v, w, x) {
                    w = w && w[w.length - 1];
                    var z = v && v[0] || w && w.parentNode,
                    B = w && w.nextSibling || null;
                    bV(a, 
                    function(D) {
                        z.insertBefore(D, B)
                    });
                    x && s(x, 0, !1)
                },
                leave: function(a, v) {
                    a.remove();
                    v && s(v, 0, !1)
                },
                move: function(v, w, x, z) {
                    this.enter(v, w, x, z)
                },
                addClass: function(a, v, w) {
                    v = aq(v) ? v: aL(v) ? v.join(" ") : "";
                    bV(a, 
                    function(x) {
                        j(x, v)
                    });
                    w && s(w, 0, !1)
                },
                removeClass: function(a, v, w) {
                    v = aq(v) ? v: aL(v) ? v.join(" ") : "";
                    bV(a, 
                    function(x) {
                        dG(x, v)
                    });
                    w && s(w, 0, !1)
                },
                enabled: cS
            }
        }]
    }],
    aM = U("$compile");
    u.$inject = ["$provide"];
    var A = /^(x[\:\-_]|data[\:\-_])/i,
    a1 = dt.XMLHttpRequest || 
    function() {
        try {
            return new ActiveXObject("Msxml2.XMLHTTP.6.0")
        } catch(p) {}
        try {
            return new ActiveXObject("Msxml2.XMLHTTP.3.0")
        } catch(s) {}
        try {
            return new ActiveXObject("Msxml2.XMLHTTP")
        } catch(v) {}
        throw U("$httpBackend")("noxhr")
    },
    aG = U("$interpolate"),
    bt = /^([^\?#]*)(\?([^#]*))?(#(.*))?$/,
    bA = {
        http: 80,
        https: 443,
        ftp: 21
    },
    ak = U("$location");
    bq.prototype = av.prototype = bh.prototype = {
        $$html5: !1,
        $$replace: !1,
        absUrl: aj("$$absUrl"),
        url: function(p, s) {
            if (dD(p)) {
                return this.$$url
            }
            var v = bt.exec(p);
            v[1] && this.path(decodeURIComponent(v[1])); (v[2] || v[1]) && this.search(v[3] || "");
            this.hash(v[5] || "", s);
            return this
        },
        protocol: aj("$$protocol"),
        host: aj("$$host"),
        port: aj("$$port"),
        path: by("$$path", 
        function(p) {
            return "/" == p.charAt(0) ? p: "/" + p
        }),
        search: function(p, s) {
            switch (arguments.length) {
            case 0:
                return this.$$search;
            case 1:
                if (aq(p)) {
                    this.$$search = b9(p)
                } else {
                    if (cq(p)) {
                        this.$$search = p
                    } else {
                        throw ak("isrcharg")
                    }
                }
                break;
            default:
                s == cp || null == s ? delete this.$$search[p] : this.$$search[p] = s
            }
            this.$$compose();
            return this
        },
        hash: by("$$hash", dE),
        replace: function() {
            this.$$replace = !0;
            return this
        }
    };
    var dj = U("$parse"),
    b0 = {},
    cg,
    aD = {
        "null": function() {
            return null
        },
        "true": function() {
            return ! 0
        },
        "false": function() {
            return ! 1
        },
        undefined: cS,
        "+": function(p, s, v, w) {
            v = v(p, s);
            w = w(p, s);
            return c3(v) ? c3(w) ? v + w: v: c3(w) ? w: cp
        },
        "-": function(p, s, v, w) {
            v = v(p, s);
            w = w(p, s);
            return (c3(v) ? v: 0) - (c3(w) ? w: 0)
        },
        "*": function(p, s, v, w) {
            return v(p, s) * w(p, s)
        },
        "/": function(p, s, v, w) {
            return v(p, s) / w(p, s)
        },
        "%": function(p, s, v, w) {
            return v(p, s) % w(p, s)
        },
        "^": function(p, s, v, w) {
            return v(p, s) ^ w(p, s)
        },
        "=": cS,
        "===": function(p, s, v, w) {
            return v(p, s) === w(p, s)
        },
        "!==": function(p, s, v, w) {
            return v(p, s) !== w(p, s)
        },
        "==": function(p, s, v, w) {
            return v(p, s) == w(p, s)
        },
        "!=": function(p, s, v, w) {
            return v(p, s) != w(p, s)
        },
        "<": function(p, s, v, w) {
            return v(p, s) < w(p, s)
        },
        ">": function(p, s, v, w) {
            return v(p, s) > w(p, s)
        },
        "<=": function(p, s, v, w) {
            return v(p, s) <= w(p, s)
        },
        ">=": function(p, s, v, w) {
            return v(p, s) >= w(p, s)
        },
        "&&": function(p, s, v, w) {
            return v(p, s) && w(p, s)
        },
        "||": function(p, s, v, w) {
            return v(p, s) || w(p, s)
        },
        "&": function(p, s, v, w) {
            return v(p, s) & w(p, s)
        },
        "|": function(p, s, v, w) {
            return w(p, s)(p, s, v(p, s))
        },
        "!": function(p, s, v) {
            return ! v(p, s)
        }
    },
    bB = {
        n: "\n",
        f: "\f",
        r: "\r",
        t: "\t",
        v: "\v",
        "'": "'",
        '"': '"'
    },
    aP = function(p) {
        this.options = p
    };
    aP.prototype = {
        constructor: aP,
        lex: function(p) {
            this.text = p;
            this.index = 0;
            this.ch = cp;
            this.lastCh = ":";
            this.tokens = [];
            var s;
            for (p = []; this.index < this.text.length;) {
                this.ch = this.text.charAt(this.index);
                if (this.is("\"'")) {
                    this.readString(this.ch)
                } else {
                    if (this.isNumber(this.ch) || this.is(".") && this.isNumber(this.peek())) {
                        this.readNumber()
                    } else {
                        if (this.isIdent(this.ch)) {
                            this.readIdent(),
                            this.was("{,") && ("{" === p[0] && (s = this.tokens[this.tokens.length - 1])) && (s.json = -1 === s.text.indexOf("."))
                        } else {
                            if (this.is("(){}[].,;:?")) {
                                this.tokens.push({
                                    index: this.index,
                                    text: this.ch,
                                    json: this.was(":[,") && this.is("{[") || this.is("}]:,")
                                }),
                                this.is("{[") && p.unshift(this.ch),
                                this.is("}]") && p.shift(),
                                this.index++
                            } else {
                                if (this.isWhitespace(this.ch)) {
                                    this.index++;
                                    continue
                                } else {
                                    var v = this.ch + this.peek(),
                                    w = v + this.peek(2),
                                    x = aD[this.ch],
                                    z = aD[v],
                                    B = aD[w];
                                    B ? (this.tokens.push({
                                        index: this.index,
                                        text: w,
                                        fn: B
                                    }), this.index += 3) : z ? (this.tokens.push({
                                        index: this.index,
                                        text: v,
                                        fn: z
                                    }), this.index += 2) : x ? (this.tokens.push({
                                        index: this.index,
                                        text: this.ch,
                                        fn: x,
                                        json: this.was("[,:") && this.is("+-")
                                    }), this.index += 1) : this.throwError("Unexpected next character ", this.index, this.index + 1)
                                }
                            }
                        }
                    }
                }
                this.lastCh = this.ch
            }
            return this.tokens
        },
        is: function(p) {
            return - 1 !== p.indexOf(this.ch)
        },
        was: function(p) {
            return - 1 !== p.indexOf(this.lastCh)
        },
        peek: function(p) {
            p = p || 1;
            return this.index + p < this.text.length ? this.text.charAt(this.index + p) : !1
        },
        isNumber: function(p) {
            return "0" <= p && "9" >= p
        },
        isWhitespace: function(p) {
            return " " === p || "\r" === p || "\t" === p || "\n" === p || "\v" === p || "\u00a0" === p
        },
        isIdent: function(p) {
            return "a" <= p && "z" >= p || "A" <= p && "Z" >= p || "_" === p || "$" === p
        },
        isExpOperator: function(p) {
            return "-" === p || "+" === p || this.isNumber(p)
        },
        throwError: function(p, s, v) {
            v = v || this.index;
            s = c3(s) ? "s " + s + "-" + this.index + " [" + this.text.substring(s, v) + "]": " " + v;
            throw dj("lexerr", p, s, this.text)
        },
        readNumber: function() {
            for (var p = "", s = this.index; this.index < this.text.length;) {
                var v = o(this.text.charAt(this.index));
                if ("." == v || this.isNumber(v)) {
                    p += v
                } else {
                    var w = this.peek();
                    if ("e" == v && this.isExpOperator(w)) {
                        p += v
                    } else {
                        if (this.isExpOperator(v) && w && this.isNumber(w) && "e" == p.charAt(p.length - 1)) {
                            p += v
                        } else {
                            if (!this.isExpOperator(v) || w && this.isNumber(w) || "e" != p.charAt(p.length - 1)) {
                                break
                            } else {
                                this.throwError("Invalid exponent")
                            }
                        }
                    }
                }
                this.index++
            }
            p *= 1;
            this.tokens.push({
                index: s,
                text: p,
                json: !0,
                fn: function() {
                    return p
                }
            })
        },
        readIdent: function() {
            for (var p = this, s = "", v = this.index, w, x, z, B; this.index < this.text.length;) {
                B = this.text.charAt(this.index);
                if ("." === B || this.isIdent(B) || this.isNumber(B)) {
                    "." === B && (w = this.index),
                    s += B
                } else {
                    break
                }
                this.index++
            }
            if (w) {
                for (x = this.index; x < this.text.length;) {
                    B = this.text.charAt(x);
                    if ("(" === B) {
                        z = s.substr(w - v + 1);
                        s = s.substr(0, w - v);
                        this.index = x;
                        break
                    }
                    if (this.isWhitespace(B)) {
                        x++
                    } else {
                        break
                    }
                }
            }
            v = {
                index: v,
                text: s
            };
            if (aD.hasOwnProperty(s)) {
                v.fn = aD[s],
                v.json = aD[s]
            } else {
                var D = bQ(s, this.options, this.text);
                v.fn = aB(function(E, F) {
                    return D(E, F)
                },
                {
                    assign: function(a, E) {
                        return aE(a, s, E, p.text, p.options)
                    }
                })
            }
            this.tokens.push(v);
            z && (this.tokens.push({
                index: w,
                text: ".",
                json: !1
            }), this.tokens.push({
                index: w + 1,
                text: z,
                json: !1
            }))
        },
        readString: function(p) {
            var s = this.index;
            this.index++;
            for (var v = "", w = p, x = !1; this.index < this.text.length;) {
                var z = this.text.charAt(this.index),
                w = w + z;
                if (x) {
                    "u" === z ? (z = this.text.substring(this.index + 1, this.index + 5), z.match(/[\da-f]{4}/i) || this.throwError("Invalid unicode escape [\\u" + z + "]"), this.index += 4, v += String.fromCharCode(parseInt(z, 16))) : v = (x = bB[z]) ? v + x: v + z,
                    x = !1
                } else {
                    if ("\\" === z) {
                        x = !0
                    } else {
                        if (z === p) {
                            this.index++;
                            this.tokens.push({
                                index: s,
                                text: w,
                                string: v,
                                json: !0,
                                fn: function() {
                                    return v
                                }
                            });
                            return
                        }
                        v += z
                    }
                }
                this.index++
            }
            this.throwError("Unterminated quote", s)
        }
    };
    var cK = function(p, s, v) {
        this.lexer = p;
        this.$filter = s;
        this.options = v
    };
    cK.ZERO = function() {
        return 0
    };
    cK.prototype = {
        constructor: cK,
        parse: function(p, s) {
            this.text = p;
            this.json = s;
            this.tokens = this.lexer.lex(p);
            s && (this.assignment = this.logicalOR, this.functionCall = this.fieldAccess = this.objectIndex = this.filterChain = function() {
                this.throwError("is not valid json", {
                    text: p,
                    index: 0
                })
            });
            var v = s ? this.primary() : this.statements();
            0 !== this.tokens.length && this.throwError("is an unexpected token", this.tokens[0]);
            v.literal = !!v.literal;
            v.constant = !!v.constant;
            return v
        },
        primary: function() {
            var p;
            if (this.expect("(")) {
                p = this.filterChain(),
                this.consume(")")
            } else {
                if (this.expect("[")) {
                    p = this.arrayDeclaration()
                } else {
                    if (this.expect("{")) {
                        p = this.object()
                    } else {
                        var s = this.expect(); (p = s.fn) || this.throwError("not a primary expression", s);
                        s.json && (p.constant = !0, p.literal = !0)
                    }
                }
            }
            for (var v; s = this.expect("(", "[", ".");) {
                "(" === s.text ? (p = this.functionCall(p, v), v = null) : "[" === s.text ? (v = p, p = this.objectIndex(p)) : "." === s.text ? (v = p, p = this.fieldAccess(p)) : this.throwError("IMPOSSIBLE")
            }
            return p
        },
        throwError: function(p, s) {
            throw dj("syntax", s.text, p, s.index + 1, this.text, this.text.substring(s.index))
        },
        peekToken: function() {
            if (0 === this.tokens.length) {
                throw dj("ueoe", this.text)
            }
            return this.tokens[0]
        },
        peek: function(p, s, v, w) {
            if (0 < this.tokens.length) {
                var x = this.tokens[0],
                z = x.text;
                if (z === p || z === s || z === v || z === w || !(p || s || v || w)) {
                    return x
                }
            }
            return ! 1
        },
        expect: function(p, s, v, w) {
            return (p = this.peek(p, s, v, w)) ? (this.json && !p.json && this.throwError("is not valid json", p), this.tokens.shift(), p) : !1
        },
        consume: function(p) {
            this.expect(p) || this.throwError("is unexpected, expecting [" + p + "]", this.peek())
        },
        unaryFn: function(p, s) {
            return aB(function(a, v) {
                return p(a, v, s)
            },
            {
                constant: s.constant
            })
        },
        ternaryFn: function(p, s, v) {
            return aB(function(a, w) {
                return p(a, w) ? s(a, w) : v(a, w)
            },
            {
                constant: p.constant && s.constant && v.constant
            })
        },
        binaryFn: function(p, s, v) {
            return aB(function(a, w) {
                return s(a, w, p, v)
            },
            {
                constant: p.constant && v.constant
            })
        },
        statements: function() {
            for (var p = [];;) {
                if (0 < this.tokens.length && !this.peek("}", ")", ";", "]") && p.push(this.filterChain()), !this.expect(";")) {
                    return 1 === p.length ? p[0] : function(a, s) {
                        for (var v, w = 0; w < p.length; w++) {
                            var x = p[w];
                            x && (v = x(a, s))
                        }
                        return v
                    }
                }
            }
        },
        filterChain: function() {
            for (var p = this.expression(), s;;) {
                if (s = this.expect("|")) {
                    p = this.binaryFn(p, s.fn, this.filter())
                } else {
                    return p
                }
            }
        },
        filter: function() {
            for (var p = this.expect(), s = this.$filter(p.text), v = [];;) {
                if (p = this.expect(":")) {
                    v.push(this.expression())
                } else {
                    var w = function(x, z, B) {
                        B = [B];
                        for (var D = 0; D < v.length; D++) {
                            B.push(v[D](x, z))
                        }
                        return s.apply(x, B)
                    };
                    return function() {
                        return w
                    }
                }
            }
        },
        expression: function() {
            return this.assignment()
        },
        assignment: function() {
            var p = this.ternary(),
            s,
            v;
            return (v = this.expect("=")) ? (p.assign || this.throwError("implies assignment but [" + this.text.substring(0, v.index) + "] can not be assigned to", v), s = this.ternary(), 
            function(a, w) {
                return p.assign(a, s(a, w), w)
            }) : p
        },
        ternary: function() {
            var p = this.logicalOR(),
            s,
            v;
            if (this.expect("?")) {
                s = this.ternary();
                if (v = this.expect(":")) {
                    return this.ternaryFn(p, s, this.ternary())
                }
                this.throwError("expected :", v)
            } else {
                return p
            }
        },
        logicalOR: function() {
            for (var p = this.logicalAND(), s;;) {
                if (s = this.expect("||")) {
                    p = this.binaryFn(p, s.fn, this.logicalAND())
                } else {
                    return p
                }
            }
        },
        logicalAND: function() {
            var p = this.equality(),
            s;
            if (s = this.expect("&&")) {
                p = this.binaryFn(p, s.fn, this.logicalAND())
            }
            return p
        },
        equality: function() {
            var p = this.relational(),
            s;
            if (s = this.expect("==", "!=", "===", "!==")) {
                p = this.binaryFn(p, s.fn, this.equality())
            }
            return p
        },
        relational: function() {
            var p = this.additive(),
            s;
            if (s = this.expect("<", ">", "<=", ">=")) {
                p = this.binaryFn(p, s.fn, this.relational())
            }
            return p
        },
        additive: function() {
            for (var p = this.multiplicative(), s; s = this.expect("+", "-");) {
                p = this.binaryFn(p, s.fn, this.multiplicative())
            }
            return p
        },
        multiplicative: function() {
            for (var p = this.unary(), s; s = this.expect("*", "/", "%");) {
                p = this.binaryFn(p, s.fn, this.unary())
            }
            return p
        },
        unary: function() {
            var p;
            return this.expect("+") ? this.primary() : (p = this.expect("-")) ? this.binaryFn(cK.ZERO, p.fn, this.unary()) : (p = this.expect("!")) ? this.unaryFn(p.fn, this.unary()) : this.primary()
        },
        fieldAccess: function(p) {
            var s = this,
            v = this.expect().text,
            w = bQ(v, this.options, this.text);
            return aB(function(a, x, z) {
                return w(z || p(a, x), x)
            },
            {
                assign: function(a, x, z) {
                    return aE(p(a, z), v, x, s.text, s.options)
                }
            })
        },
        objectIndex: function(p) {
            var s = this,
            v = this.expression();
            this.consume("]");
            return aB(function(a, w) {
                var x = p(a, w),
                z = v(a, w),
                B;
                if (!x) {
                    return cp
                } (x = au(x[z], s.text)) && (x.then && s.options.unwrapPromises) && (B = x, "$$v" in x || (B.$$v = cp, B.then(function(D) {
                    B.$$v = D
                })), x = x.$$v);
                return x
            },
            {
                assign: function(a, w, x) {
                    var z = v(a, x);
                    return au(p(a, x), s.text)[z] = w
                }
            })
        },
        functionCall: function(p, s) {
            var v = [];
            if (")" !== this.peekToken().text) {
                do {
                    v.push(this.expression())
                }
                while (this.expect(","))
            }
            this.consume(")");
            var w = this;
            return function(a, x) {
                for (var z = [], D = s ? s(a, x) : a, B = 0; B < v.length; B++) {
                    z.push(v[B](a, x))
                }
                B = p(a, x, D) || cS;
                au(B, w.text);
                z = B.apply ? B.apply(D, z) : B(z[0], z[1], z[2], z[3], z[4]);
                return au(z, w.text)
            }
        },
        arrayDeclaration: function() {
            var p = [],
            s = !0;
            if ("]" !== this.peekToken().text) {
                do {
                    var v = this.expression();
                    p.push(v);
                    v.constant || (s = !1)
                }
                while (this.expect(","))
            }
            this.consume("]");
            return aB(function(a, w) {
                for (var x = [], z = 0; z < p.length; z++) {
                    x.push(p[z](a, w))
                }
                return x
            },
            {
                literal: !0,
                constant: s
            })
        },
        object: function() {
            var p = [],
            s = !0;
            if ("}" !== this.peekToken().text) {
                do {
                    var v = this.expect(),
                    v = v.string || v.text;
                    this.consume(":");
                    var w = this.expression();
                    p.push({
                        key: v,
                        value: w
                    });
                    w.constant || (s = !1)
                }
                while (this.expect(","))
            }
            this.consume("}");
            return aB(function(a, x) {
                for (var z = {},
                D = 0; D < p.length; D++) {
                    var B = p[D];
                    z[B.key] = B.value(a, x)
                }
                return z
            },
            {
                literal: !0,
                constant: s
            })
        }
    };
    var aF = {},
    cr = U("$sce"),
    ah = {
        HTML: "html",
        CSS: "css",
        URL: "url",
        RESOURCE_URL: "resourceUrl",
        JS: "js"
    },
    cT = cf.createElement("a"),
    ck = c5(dt.location.href, !0);
    cv.$inject = ["$provide"];
    cE.$inject = ["$locale"];
    cY.$inject = ["$locale"];
    var dy = ".",
    aJ = {
        yyyy: di("FullYear", 4),
        yy: di("FullYear", 2, 0, !0),
        y: di("FullYear", 1),
        MMMM: aO("Month"),
        MMM: aO("Month", !0),
        MM: di("Month", 2, 1),
        M: di("Month", 1, 1),
        dd: di("Date", 2),
        d: di("Date", 1),
        HH: di("Hours", 2),
        H: di("Hours", 1),
        hh: di("Hours", 2, -12),
        h: di("Hours", 1, -12),
        mm: di("Minutes", 2),
        m: di("Minutes", 1),
        ss: di("Seconds", 2),
        s: di("Seconds", 1),
        sss: di("Milliseconds", 3),
        EEEE: aO("Day"),
        EEE: aO("Day", !0),
        a: function(p, s) {
            return 12 > p.getHours() ? s.AMPMS[0] : s.AMPMS[1]
        },
        Z: function(p) {
            p = -1 * p.getTimezoneOffset();
            return p = (0 <= p ? "+": "") + (aY(Math[0 < p ? "floor": "ceil"](p / 60), 2) + aY(Math.abs(p % 60), 2))
        }
    },
    az = /((?:[^yMdHhmsaZE']+)|(?:'(?:[^']|'')*')|(?:E+|y+|M+|d+|H+|h+|m+|s+|a|Z))(.*)/,
    ao = /^\-?\d+$/;
    cN.$inject = ["$locale"];
    var P = g(o),
    ae = g(ai);
    c9.$inject = ["$parse"];
    var bK = g({
        restrict: "E",
        compile: function(p, s) {
            8 >= b5 && (s.href || s.name || s.$set("href", ""), p.append(cf.createComment("IE fix")));
            return function(v, w) {
                w.on("click", 
                function(x) {
                    w.attr("href") || x.preventDefault()
                })
            }
        }
    }),
    bg = {};
    bV(L, 
    function(p, s) {
        if ("multiple" != p) {
            var v = bm("ng-" + s);
            bg[v] = function() {
                return {
                    priority: 100,
                    compile: function() {
                        return function(w, x, z) {
                            w.$watch(z[v], 
                            function(B) {
                                z.$set(s, !!B)
                            })
                        }
                    }
                }
            }
        }
    });
    bV(["src", "srcset", "href"], 
    function(p) {
        var s = bm("ng-" + p);
        bg[s] = function() {
            return {
                priority: 99,
                link: function(a, v, w) {
                    w.$observe(s, 
                    function(x) {
                        x && (w.$set(p, x), b5 && v.prop(p, w[p]))
                    })
                }
            }
        }
    });
    var bf = {
        $addControl: cS,
        $removeControl: cS,
        $setValidity: cS,
        $setDirty: cS,
        $setPristine: cS
    };
    dI.$inject = ["$element", "$attrs", "$scope"];
    var y = function(p) {
        return ["$timeout", 
        function(a) {
            return {
                name: "form",
                restrict: p ? "EAC": "E",
                controller: dI,
                compile: function() {
                    return {
                        pre: function(s, v, w, x) {
                            if (!w.action) {
                                var z = function(E) {
                                    E.preventDefault ? E.preventDefault() : E.returnValue = !1
                                };
                                k(v[0], "submit", z);
                                v.on("$destroy", 
                                function() {
                                    a(function() {
                                        dl(v[0], "submit", z)
                                    },
                                    0, !1)
                                })
                            }
                            var D = v.parent().controller("form"),
                            B = w.name || w.ngForm;
                            B && aE(s, B, x, B);
                            if (D) {
                                v.on("$destroy", 
                                function() {
                                    D.$removeControl(x);
                                    B && aE(s, B, cp, B);
                                    aB(x, bf)
                                })
                            }
                        }
                    }
                }
            }
        }]
    },
    bT = y(),
    b3 = y(!0),
    cd = /^(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?$/,
    cn = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,6}$/,
    cy = /^\s*(\-|\+)?(\d+|(\d*(\.\d*)))\s*$/,
    N = {
        text: bw,
        number: function(p, s, v, w, x, z) {
            bw(p, s, v, w, x, z);
            w.$parsers.push(function(E) {
                var F = w.$isEmpty(E);
                if (F || cy.test(E)) {
                    return w.$setValidity("number", !0),
                    "" === E ? null: F ? E: parseFloat(E)
                }
                w.$setValidity("number", !1);
                return cp
            });
            w.$formatters.push(function(E) {
                return w.$isEmpty(E) ? "": "" + E
            });
            if (v.min) {
                var B = parseFloat(v.min);
                p = function(E) {
                    if (!w.$isEmpty(E) && E < B) {
                        return w.$setValidity("min", !1),
                        cp
                    }
                    w.$setValidity("min", !0);
                    return E
                };
                w.$parsers.push(p);
                w.$formatters.push(p)
            }
            if (v.max) {
                var D = parseFloat(v.max);
                v = function(E) {
                    if (!w.$isEmpty(E) && E > D) {
                        return w.$setValidity("max", !1),
                        cp
                    }
                    w.$setValidity("max", !0);
                    return E
                };
                w.$parsers.push(v);
                w.$formatters.push(v)
            }
            w.$formatters.push(function(E) {
                if (w.$isEmpty(E) || bO(E)) {
                    return w.$setValidity("number", !0),
                    E
                }
                w.$setValidity("number", !1);
                return cp
            })
        },
        url: function(p, s, v, w, x, z) {
            bw(p, s, v, w, x, z);
            p = function(B) {
                if (w.$isEmpty(B) || cd.test(B)) {
                    return w.$setValidity("url", !0),
                    B
                }
                w.$setValidity("url", !1);
                return cp
            };
            w.$formatters.push(p);
            w.$parsers.push(p)
        },
        email: function(p, s, v, w, x, z) {
            bw(p, s, v, w, x, z);
            p = function(B) {
                if (w.$isEmpty(B) || cn.test(B)) {
                    return w.$setValidity("email", !0),
                    B
                }
                w.$setValidity("email", !1);
                return cp
            };
            w.$formatters.push(p);
            w.$parsers.push(p)
        },
        radio: function(p, s, v, w) {
            dD(v.name) && s.attr("name", cV());
            s.on("click", 
            function() {
                s[0].checked && p.$apply(function() {
                    w.$setViewValue(v.value)
                })
            });
            w.$render = function() {
                s[0].checked = v.value == w.$viewValue
            };
            v.$observe("value", w.$render)
        },
        checkbox: function(p, s, v, w) {
            var x = v.ngTrueValue,
            z = v.ngFalseValue;
            aq(x) || (x = !0);
            aq(z) || (z = !1);
            s.on("click", 
            function() {
                p.$apply(function() {
                    w.$setViewValue(s[0].checked)
                })
            });
            w.$render = function() {
                s[0].checked = w.$viewValue
            };
            w.$isEmpty = function(B) {
                return B !== x
            };
            w.$formatters.push(function(B) {
                return B === x
            });
            w.$parsers.push(function(B) {
                return B ? x: z
            })
        },
        hidden: cS,
        button: cS,
        submit: cS,
        reset: cS
    },
    ac = ["$browser", "$sniffer", 
    function(p, s) {
        return {
            restrict: "E",
            require: "?ngModel",
            link: function(a, v, w, x) {
                x && (N[o(w.type)] || N.text)(a, v, w, x, s, p)
            }
        }
    }],
    a7 = "ng-valid",
    aX = "ng-invalid",
    at = "ng-pristine",
    bo = "ng-dirty",
    cH = ["$scope", "$exceptionHandler", "$attrs", "$element", "$parse", 
    function(p, s, v, w, x) {
        function z(J, Q) {
            Q = Q ? "-" + b(Q, "-") : "";
            w.removeClass((J ? aX: a7) + Q).addClass((J ? a7: aX) + Q)
        }
        this.$modelValue = this.$viewValue = Number.NaN;
        this.$parsers = [];
        this.$formatters = [];
        this.$viewChangeListeners = [];
        this.$pristine = !0;
        this.$dirty = !1;
        this.$valid = !0;
        this.$invalid = !1;
        this.$name = v.name;
        var B = x(v.ngModel),
        F = B.assign;
        if (!F) {
            throw U("ngModel")("nonassign", v.ngModel, aC(w))
        }
        this.$render = cS;
        this.$isEmpty = function(J) {
            return dD(J) || "" === J || null === J || J !== J
        };
        var D = w.inheritedData("$formController") || bf,
        E = 0,
        H = this.$error = {};
        w.addClass(at);
        z(!0);
        this.$setValidity = function(J, Q) {
            H[J] !== !Q && (Q ? (H[J] && E--, E || (z(!0), this.$valid = !0, this.$invalid = !1)) : (z(!1), this.$invalid = !0, this.$valid = !1, E++), H[J] = !Q, z(Q, J), D.$setValidity(J, Q, this))
        };
        this.$setPristine = function() {
            this.$dirty = !1;
            this.$pristine = !0;
            w.removeClass(bo).addClass(at)
        };
        this.$setViewValue = function(a) {
            this.$viewValue = a;
            this.$pristine && (this.$dirty = !0, this.$pristine = !1, w.removeClass(at).addClass(bo), D.$setDirty());
            bV(this.$parsers, 
            function(J) {
                a = J(a)
            });
            this.$modelValue !== a && (this.$modelValue = a, F(p, a), bV(this.$viewChangeListeners, 
            function(J) {
                try {
                    J()
                } catch(Q) {
                    s(Q)
                }
            }))
        };
        var G = this;
        p.$watch(function() {
            var a = B(p);
            if (G.$modelValue !== a) {
                var J = G.$formatters,
                Q = J.length;
                for (G.$modelValue = a; Q--;) {
                    a = J[Q](a)
                }
                G.$viewValue !== a && (G.$viewValue = a, G.$render())
            }
        })
    }],
    cQ = function() {
        return {
            require: ["ngModel", "^?form"],
            controller: cH,
            link: function(p, s, v, w) {
                var x = w[0],
                z = w[1] || bf;
                z.$addControl(x);
                s.on("$destroy", 
                function() {
                    z.$removeControl(x)
                })
            }
        }
    },
    c1 = g({
        require: "ngModel",
        link: function(p, s, v, w) {
            w.$viewChangeListeners.push(function() {
                p.$eval(v.ngChange)
            })
        }
    }),
    am = function() {
        return {
            require: "?ngModel",
            link: function(p, s, v, w) {
                if (w) {
                    v.required = !0;
                    var x = function(z) {
                        if (v.required && w.$isEmpty(z)) {
                            w.$setValidity("required", !1)
                        } else {
                            return w.$setValidity("required", !0),
                            z
                        }
                    };
                    w.$formatters.push(x);
                    w.$parsers.unshift(x);
                    v.$observe("required", 
                    function() {
                        x(w.$viewValue)
                    })
                }
            }
        }
    },
    df = function() {
        return {
            require: "ngModel",
            link: function(p, s, v, w) {
                var x = (p = /\/(.*)\//.exec(v.ngList)) && RegExp(p[1]) || v.ngList || ",";
                w.$parsers.push(function(z) {
                    if (!dD(z)) {
                        var B = [];
                        z && bV(z.split(x), 
                        function(D) {
                            D && B.push(q(D))
                        });
                        return B
                    }
                });
                w.$formatters.push(function(z) {
                    return aL(z) ? z.join(", ") : cp
                });
                w.$isEmpty = function(z) {
                    return ! z || !z.length
                }
            }
        }
    },
    dr = /^(true|false|\d+)$/,
    dB = function() {
        return {
            priority: 100,
            compile: function(p, s) {
                return dr.test(s.ngValue) ? 
                function(v, w, x) {
                    x.$set("value", v.$eval(x.ngValue))
                }: function(v, w, x) {
                    v.$watch(x.ngValue, 
                    function(z) {
                        x.$set("value", z)
                    })
                }
            }
        }
    },
    dL = cA(function(p, s, v) {
        s.addClass("ng-binding").data("$binding", v.ngBind);
        p.$watch(v.ngBind, 
        function(w) {
            s.text(w == cp ? "": w)
        })
    }),
    e = ["$interpolate", 
    function(p) {
        return function(a, s, v) {
            a = p(s.attr(v.$attr.ngBindTemplate));
            s.addClass("ng-binding").data("$binding", a);
            v.$observe("ngBindTemplate", 
            function(w) {
                s.text(w)
            })
        }
    }],
    n = ["$sce", "$parse", 
    function(p, s) {
        return function(a, v, w) {
            v.addClass("ng-binding").data("$binding", w.ngBindHtml);
            var x = s(w.ngBindHtml);
            a.$watch(function() {
                return (x(a) || "").toString()
            },
            function(z) {
                v.html(p.getTrustedHtml(x(a)) || "")
            })
        }
    }],
    I = a8("", !0),
    T = a8("Odd", 0),
    af = a8("Even", 1),
    ap = cA({
        compile: function(p, s) {
            s.$set("ngCloak", cp);
            p.removeClass("ng-cloak")
        }
    }),
    aA = [function() {
        return {
            scope: !0,
            controller: "@"
        }
    }],
    aK = ["$sniffer", 
    function(p) {
        return {
            priority: 1000,
            compile: function() {
                p.csp = !0
            }
        }
    }],
    ax = {};
    bV("click dblclick mousedown mouseup mouseover mouseout mousemove mouseenter mouseleave keydown keyup keypress submit focus blur copy cut paste".split(" "), 
    function(p) {
        var s = bm("ng-" + p);
        ax[s] = ["$parse", 
        function(a) {
            return function(v, w, x) {
                var z = a(x[s]);
                w.on(o(p), 
                function(B) {
                    v.$apply(function() {
                        z(v, {
                            $event: B
                        })
                    })
                })
            }
        }]
    });
    var aU = ["$animate", 
    function(p) {
        return {
            transclude: "element",
            priority: 600,
            terminal: !0,
            restrict: "A",
            compile: function(a, s, v) {
                return function(w, x, z) {
                    var D,
                    B;
                    w.$watch(z.ngIf, 
                    function(E) {
                        D && (p.leave(D), D = cp);
                        B && (B.$destroy(), B = cp);
                        be(E) && (B = w.$new(), v(B, 
                        function(F) {
                            D = F;
                            p.enter(F, x.parent(), x)
                        }))
                    })
                }
            }
        }
    }],
    a3 = ["$http", "$templateCache", "$anchorScroll", "$compile", "$animate", "$sce", 
    function(p, s, v, w, x, z) {
        return {
            restrict: "ECA",
            priority: 400,
            terminal: !0,
            transclude: "element",
            compile: function(a, E, B) {
                var D = E.ngInclude || E.src,
                F = E.onload || "",
                G = E.autoscroll;
                return function(J, Q) {
                    var R = 0,
                    H,
                    S,
                    V = function() {
                        H && (H.$destroy(), H = null);
                        S && (x.leave(S), S = null)
                    };
                    J.$watch(z.parseAsResourceUrl(D), 
                    function(W) {
                        var X = ++R;
                        W ? (p.get(W, {
                            cache: s
                        }).success(function(Y) {
                            if (X === R) {
                                var dc = J.$new();
                                B(dc, 
                                function(dd) {
                                    V();
                                    H = dc;
                                    S = dd;
                                    S.html(Y);
                                    x.enter(S, null, Q);
                                    w(S.contents())(H); ! c3(G) || G && !J.$eval(G) || v();
                                    H.$emit("$includeContentLoaded");
                                    J.$eval(F)
                                })
                            }
                        }).error(function() {
                            X === R && V()
                        }), J.$emit("$includeContentRequested")) : V()
                    })
                }
            }
        }
    }],
    bd = cA({
        compile: function() {
            return {
                pre: function(p, s, v) {
                    p.$eval(v.ngInit)
                }
            }
        }
    }),
    bl = cA({
        terminal: !0,
        priority: 1000
    }),
    bu = ["$locale", "$interpolate", 
    function(p, s) {
        var v = /{}/g;
        return {
            restrict: "EA",
            link: function(a, w, x) {
                var z = x.count,
                E = x.$attr.when && w.attr(x.$attr.when),
                B = x.offset || 0,
                D = a.$eval(E) || {},
                H = {},
                G = s.startSymbol(),
                F = s.endSymbol(),
                J = /^when(Minus)?(.+)$/;
                bV(x, 
                function(Q, R) {
                    J.test(R) && (D[o(R.replace("when", "").replace("Minus", "-"))] = w.attr(x.$attr[R]))
                });
                bV(D, 
                function(Q, R) {
                    H[R] = s(Q.replace(v, G + z + "-" + B + F))
                });
                a.$watch(function() {
                    var Q = parseFloat(a.$eval(z));
                    if (isNaN(Q)) {
                        return ""
                    }
                    Q in D || (Q = p.pluralCat(Q - B));
                    return H[Q](a, w, !0)
                },
                function(Q) {
                    w.text(Q)
                })
            }
        }
    }],
    bC = ["$parse", "$animate", 
    function(p, s) {
        function v(x) {
            if (x.startNode === x.endNode) {
                return dh(x.startNode)
            }
            var z = x.startNode,
            B = [z];
            do {
                z = z.nextSibling;
                if (!z) {
                    break
                }
                B.push(z)
            }
            while (z !== x.endNode);
            return dh(B)
        }
        var w = U("ngRepeat");
        return {
            transclude: "element",
            priority: 1000,
            terminal: !0,
            compile: function(a, x, z) {
                return function(G, J, Q) {
                    var V = Q.ngRepeat,
                    S = V.match(/^\s*(.+)\s+in\s+(.*?)\s*(\s+track\s+by\s+(.+)\s*)?$/),
                    R,
                    W,
                    dd,
                    E,
                    Y,
                    dc,
                    D,
                    X = {
                        $id: K
                    };
                    if (!S) {
                        throw w("iexp", V)
                    }
                    Q = S[1];
                    Y = S[2]; (S = S[4]) ? (R = p(S), W = function(B, F, de) {
                        D && (X[D] = B);
                        X[dc] = F;
                        X.$index = de;
                        return R(G, X)
                    }) : (dd = function(B, F) {
                        return K(F)
                    },
                    E = function(B) {
                        return B
                    });
                    S = Q.match(/^(?:([\$\w]+)|\(([\$\w]+)\s*,\s*([\$\w]+)\))$/);
                    if (!S) {
                        throw w("iidexp", Q)
                    }
                    dc = S[3] || S[1];
                    D = S[2];
                    var H = {};
                    G.$watchCollection(Y, 
                    function(B) {
                        var dP,
                        dT,
                        dQ = J[0],
                        dV,
                        dU = {},
                        de,
                        F,
                        dR,
                        dS,
                        dN,
                        dO,
                        dW = [];
                        if (bF(B)) {
                            dN = B,
                            dV = W || dd
                        } else {
                            dV = W || E;
                            dN = [];
                            for (dR in B) {
                                B.hasOwnProperty(dR) && "$" != dR.charAt(0) && dN.push(dR)
                            }
                            dN.sort()
                        }
                        de = dN.length;
                        dT = dW.length = dN.length;
                        for (dP = 0; dP < dT; dP++) {
                            if (dR = B === dN ? dP: dN[dP], dS = B[dR], dS = dV(dR, dS, dP), bW(dS, "`track by` id"), H.hasOwnProperty(dS)) {
                                dO = H[dS],
                                delete H[dS],
                                dU[dS] = dO,
                                dW[dP] = dO
                            } else {
                                if (dU.hasOwnProperty(dS)) {
                                    throw bV(dW, 
                                    function(dX) {
                                        dX && dX.startNode && (H[dX.id] = dX)
                                    }),
                                    w("dupes", V, dS)
                                }
                                dW[dP] = {
                                    id: dS
                                };
                                dU[dS] = !1
                            }
                        }
                        for (dR in H) {
                            H.hasOwnProperty(dR) && (dO = H[dR], dP = v(dO), s.leave(dP), bV(dP, 
                            function(dX) {
                                dX.$$NG_REMOVED = !0
                            }), dO.scope.$destroy())
                        }
                        dP = 0;
                        for (dT = dN.length; dP < dT; dP++) {
                            dR = B === dN ? dP: dN[dP];
                            dS = B[dR];
                            dO = dW[dP];
                            dW[dP - 1] && (dQ = dW[dP - 1].endNode);
                            if (dO.startNode) {
                                F = dO.scope;
                                dV = dQ;
                                do {
                                    dV = dV.nextSibling
                                }
                                while (dV && dV.$$NG_REMOVED);
                                dO.startNode != dV && s.move(v(dO), null, dh(dQ));
                                dQ = dO.endNode
                            } else {
                                F = G.$new()
                            }
                            F[dc] = dS;
                            D && (F[D] = dR);
                            F.$index = dP;
                            F.$first = 0 === dP;
                            F.$last = dP === de - 1;
                            F.$middle = !(F.$first || F.$last);
                            F.$odd = !(F.$even = 0 == dP % 2);
                            dO.startNode || z(F, 
                            function(dX) {
                                dX[dX.length++] = cf.createComment(" end ngRepeat: " + V + " ");
                                s.enter(dX, null, dh(dQ));
                                dQ = dX;
                                dO.scope = F;
                                dO.startNode = dQ && dQ.endNode ? dQ.endNode: dX[0];
                                dO.endNode = dX[dX.length - 1];
                                dU[dO.id] = dO
                            })
                        }
                        H = dU
                    })
                }
            }
        }
    }],
    bL = ["$animate", 
    function(p) {
        return function(a, s, v) {
            a.$watch(v.ngShow, 
            function(w) {
                p[be(w) ? "removeClass": "addClass"](s, "ng-hide")
            })
        }
    }],
    bU = ["$animate", 
    function(p) {
        return function(a, s, v) {
            a.$watch(v.ngHide, 
            function(w) {
                p[be(w) ? "addClass": "removeClass"](s, "ng-hide")
            })
        }
    }],
    b4 = cA(function(p, s, v) {
        p.$watch(v.ngStyle, 
        function(w, x) {
            x && w !== x && bV(x, 
            function(z, B) {
                s.css(B, "")
            });
            w && s.css(w)
        },
        !0)
    }),
    ce = ["$animate", 
    function(p) {
        return {
            restrict: "EA",
            require: "ngSwitch",
            controller: ["$scope", 
            function() {
                this.cases = {}
            }],
            link: function(a, s, v, w) {
                var x,
                z,
                B = [];
                a.$watch(v.ngSwitch || v.on, 
                function(D) {
                    for (var E = 0, F = B.length; E < F; E++) {
                        B[E].$destroy(),
                        p.leave(z[E])
                    }
                    z = [];
                    B = [];
                    if (x = w.cases["!" + D] || w.cases["?"]) {
                        a.$eval(v.change),
                        bV(x, 
                        function(G) {
                            var H = a.$new();
                            B.push(H);
                            G.transclude(H, 
                            function(J) {
                                var Q = G.element;
                                z.push(J);
                                p.enter(J, Q.parent(), Q)
                            })
                        })
                    }
                })
            }
        }
    }],
    co = cA({
        transclude: "element",
        priority: 800,
        require: "^ngSwitch",
        compile: function(p, s, v) {
            return function(w, x, z, B) {
                B.cases["!" + s.ngSwitchWhen] = B.cases["!" + s.ngSwitchWhen] || [];
                B.cases["!" + s.ngSwitchWhen].push({
                    transclude: v,
                    element: x
                })
            }
        }
    }),
    cz = cA({
        transclude: "element",
        priority: 800,
        require: "^ngSwitch",
        compile: function(p, s, v) {
            return function(w, x, z, B) {
                B.cases["?"] = B.cases["?"] || [];
                B.cases["?"].push({
                    transclude: v,
                    element: x
                })
            }
        }
    }),
    cI = cA({
        controller: ["$element", "$transclude", 
        function(p, s) {
            if (!s) {
                throw U("ngTransclude")("orphan", aC(p))
            }
            this.$transclude = s
        }],
        link: function(p, s, v, w) {
            w.$transclude(function(x) {
                s.html("");
                s.append(x)
            })
        }
    }),
    cR = ["$templateCache", 
    function(p) {
        return {
            restrict: "E",
            terminal: !0,
            compile: function(a, s) {
                "text/ng-template" == s.type && p.put(s.id, a[0].text)
            }
        }
    }],
    c2 = U("ngOptions"),
    dg = g({
        terminal: !0
    }),
    ds = ["$compile", "$parse", 
    function(p, s) {
        var v = /^\s*(.*?)(?:\s+as\s+(.*?))?(?:\s+group\s+by\s+(.*))?\s+for\s+(?:([\$\w][\$\w]*)|(?:\(\s*([\$\w][\$\w]*)\s*,\s*([\$\w][\$\w]*)\s*\)))\s+in\s+(.*?)(?:\s+track\s+by\s+(.*?))?$/,
        w = {
            $setViewValue: cS
        };
        return {
            restrict: "E",
            require: ["select", "?ngModel"],
            controller: ["$element", "$scope", "$attrs", 
            function(x, z, B) {
                var F = this,
                D = {},
                E = w,
                G;
                F.databound = B.ngModel;
                F.init = function(H, J, Q) {
                    E = H;
                    G = Q
                };
                F.addOption = function(a) {
                    bW(a, '"option value"');
                    D[a] = !0;
                    E.$viewValue == a && (x.val(a), G.parent() && G.remove())
                };
                F.removeOption = function(H) {
                    this.hasOption(H) && (delete D[H], E.$viewValue == H && this.renderUnknownOption(H))
                };
                F.renderUnknownOption = function(a) {
                    a = "? " + K(a) + " ?";
                    G.val(a);
                    x.prepend(G);
                    x.val(a);
                    G.prop("selected", !0)
                };
                F.hasOption = function(H) {
                    return D.hasOwnProperty(H)
                };
                z.$on("$destroy", 
                function() {
                    F.renderUnknownOption = cS
                })
            }],
            link: function(H, R, V, Y) {
                function W(B, D, E, F) {
                    E.$render = function() {
                        var G = E.$viewValue;
                        F.hasOption(G) ? (dN.parent() && dN.remove(), D.val(G), "" === G && dO.prop("selected", !0)) : dD(G) && dO ? D.val("") : F.renderUnknownOption(G)
                    };
                    D.on("change", 
                    function() {
                        B.$apply(function() {
                            dN.parent() && dN.remove();
                            E.$setViewValue(D.val())
                        })
                    })
                }
                function X(B, D, E) {
                    var F;
                    E.$render = function() {
                        var G = new bX(E.$viewValue);
                        bV(D.find("option"), 
                        function(dR) {
                            dR.selected = c3(G.get(dR.value))
                        })
                    };
                    B.$watch(function() {
                        h(F, E.$viewValue) || (F = ar(E.$viewValue), E.$render())
                    });
                    D.on("change", 
                    function() {
                        B.$apply(function() {
                            var G = [];
                            bV(D.find("option"), 
                            function(dR) {
                                dR.selected && G.push(dR.value)
                            });
                            E.$setViewValue(G)
                        })
                    })
                }
                function de(B, D, F) {
                    function E() {
                        var dZ = {
                            "": []
                        },
                        d2 = [""],
                        d3,
                        d7,
                        d8,
                        d9,
                        ea;
                        d9 = F.$modelValue;
                        ea = dW(B) || [];
                        var d1 = dT ? bp(ea) : ea,
                        d5,
                        eb,
                        d0;
                        eb = {};
                        d8 = !1;
                        var d4,
                        d6;
                        if (dQ) {
                            if (dX && aL(d9)) {
                                for (d8 = new bX([]), d0 = 0; d0 < d9.length; d0++) {
                                    eb[dS] = d9[d0],
                                    d8.put(dX(B, eb), d9[d0])
                                }
                            } else {
                                d8 = new bX(d9)
                            }
                        }
                        for (d0 = 0; d5 = d1.length, d0 < d5; d0++) {
                            d7 = d0;
                            if (dT) {
                                d7 = d1[d0];
                                if ("$" === d7.charAt(0)) {
                                    continue
                                }
                                eb[dT] = d7
                            }
                            eb[dS] = ea[d7];
                            d3 = dV(B, eb) || ""; (d7 = dZ[d3]) || (d7 = dZ[d3] = [], d2.push(d3));
                            dQ ? d3 = d8.remove(dX ? dX(B, eb) : dU(B, eb)) !== cp: (dX ? (d3 = {},
                            d3[dS] = d9, d3 = dX(B, d3) === dX(B, eb)) : d3 = d9 === dU(B, eb), d8 = d8 || d3);
                            d4 = dR(B, eb);
                            d4 = d4 === cp ? "": d4;
                            d7.push({
                                id: dX ? dX(B, eb) : dT ? d1[d0] : d0,
                                label: d4,
                                selected: d3
                            })
                        }
                        dQ || (x || null === d9 ? dZ[""].unshift({
                            id: "",
                            label: "",
                            selected: !d8
                        }) : d8 || dZ[""].unshift({
                            id: "?",
                            label: "",
                            selected: !0
                        }));
                        eb = 0;
                        for (d1 = d2.length; eb < d1; eb++) {
                            d3 = d2[eb];
                            d7 = dZ[d3];
                            dY.length <= eb ? (d9 = {
                                element: Q.clone().attr("label", d3),
                                label: d7.label
                            },
                            ea = [d9], dY.push(ea), D.append(d9.element)) : (ea = dY[eb], d9 = ea[0], d9.label != d3 && d9.element.attr("label", d9.label = d3));
                            d4 = null;
                            d0 = 0;
                            for (d5 = d7.length; d0 < d5; d0++) {
                                d8 = d7[d0],
                                (d3 = ea[d0 + 1]) ? (d4 = d3.element, d3.label !== d8.label && d4.text(d3.label = d8.label), d3.id !== d8.id && d4.val(d3.id = d8.id), d4[0].selected !== d8.selected && d4.prop("selected", d3.selected = d8.selected)) : ("" === d8.id && x ? d6 = x: (d6 = z.clone()).val(d8.id).attr("selected", d8.selected).text(d8.label), ea.push({
                                    element: d6,
                                    label: d8.label,
                                    id: d8.id,
                                    selected: d8.selected
                                }), d4 ? d4.after(d6) : d9.element.append(d6), d4 = d6)
                            }
                            for (d0++; ea.length > d0;) {
                                ea.pop().element.remove()
                            }
                        }
                        for (; dY.length > eb;) {
                            dY.pop()[0].element.remove()
                        }
                    }
                    var G;
                    if (! (G = dP.match(v))) {
                        throw c2("iexp", dP, aC(D))
                    }
                    var dR = s(G[2] || G[1]),
                    dS = G[4] || G[6],
                    dT = G[5],
                    dV = s(G[3] || ""),
                    dU = s(G[2] ? G[1] : dS),
                    dW = s(G[7]),
                    dX = G[8] ? s(G[8]) : null,
                    dY = [[{
                        element: D,
                        label: ""
                    }]];
                    x && (p(x)(B), x.removeClass("ng-scope"), x.remove());
                    D.html("");
                    D.on("change", 
                    function() {
                        B.$apply(function() {
                            var dZ,
                            d0 = dW(B) || [],
                            d1 = {},
                            d2,
                            d3,
                            d4,
                            d5,
                            d6,
                            d7,
                            d8;
                            if (dQ) {
                                for (d3 = [], d5 = 0, d7 = dY.length; d5 < d7; d5++) {
                                    for (dZ = dY[d5], d4 = 1, d6 = dZ.length; d4 < d6; d4++) {
                                        if ((d2 = dZ[d4].element)[0].selected) {
                                            d2 = d2.val();
                                            dT && (d1[dT] = d2);
                                            if (dX) {
                                                for (d8 = 0; d8 < d0.length && (d1[dS] = d0[d8], dX(B, d1) != d2); d8++) {}
                                            } else {
                                                d1[dS] = d0[d2]
                                            }
                                            d3.push(dU(B, d1))
                                        }
                                    }
                                }
                            } else {
                                if (d2 = D.val(), "?" == d2) {
                                    d3 = cp
                                } else {
                                    if ("" == d2) {
                                        d3 = null
                                    } else {
                                        if (dX) {
                                            for (d8 = 0; d8 < d0.length; d8++) {
                                                if (d1[dS] = d0[d8], dX(B, d1) == d2) {
                                                    d3 = dU(B, d1);
                                                    break
                                                }
                                            }
                                        } else {
                                            d1[dS] = d0[d2],
                                            dT && (d1[dT] = d2),
                                            d3 = dU(B, d1)
                                        }
                                    }
                                }
                            }
                            F.$setViewValue(d3)
                        })
                    });
                    F.$render = E;
                    B.$watch(E)
                }
                if (Y[1]) {
                    var dd = Y[0],
                    dc = Y[1],
                    dQ = V.multiple,
                    dP = V.ngOptions,
                    x = !1,
                    dO,
                    z = dh(cf.createElement("option")),
                    Q = dh(cf.createElement("optgroup")),
                    dN = z.clone();
                    Y = 0;
                    for (var a = R.children(), S = a.length; Y < S; Y++) {
                        if ("" == a[Y].value) {
                            dO = x = a.eq(Y);
                            break
                        }
                    }
                    dd.init(dc, x, dN);
                    if (dQ && (V.required || V.ngRequired)) {
                        var J = function(B) {
                            dc.$setValidity("required", !V.required || B && B.length);
                            return B
                        };
                        dc.$parsers.push(J);
                        dc.$formatters.unshift(J);
                        V.$observe("required", 
                        function() {
                            J(dc.$viewValue)
                        })
                    }
                    dP ? de(H, R, dc) : dQ ? X(H, R, dc) : W(H, R, dc, dd)
                }
            }
        }
    }],
    dC = ["$interpolate", 
    function(p) {
        var s = {
            addOption: cS,
            removeOption: cS
        };
        return {
            restrict: "E",
            priority: 100,
            compile: function(a, v) {
                if (dD(v.value)) {
                    var w = p(a.text(), !0);
                    w || v.$set("value", a.text())
                }
                return function(x, z, B) {
                    var D = z.parent(),
                    E = D.data("$selectController") || D.parent().data("$selectController");
                    E && E.databound ? z.prop("selected", !1) : E = s;
                    w ? x.$watch(w, 
                    function(F, G) {
                        B.$set("value", F);
                        F !== G && E.removeOption(G);
                        E.addOption(F)
                    }) : E.addOption(B.value);
                    z.on("$destroy", 
                    function() {
                        E.removeOption(B.value)
                    })
                }
            }
        }
    }],
    dM = g({
        restrict: "E",
        terminal: !0
    }); (r = dt.jQuery) ? (dh = r, aB(r.fn, {
        scope: cs.scope,
        controller: cs.controller,
        injector: cs.injector,
        inheritedData: cs.inheritedData
    }), cC("remove", !0, !0, !1), cC("empty", !1, !1, !1), cC("html", !1, !1, !0)) : dh = a4;
    dF.element = dh; (function(p) {
        aB(p, {
            bootstrap: cu,
            copy: ar,
            extend: aB,
            equals: h,
            element: dh,
            forEach: bV,
            injector: cD,
            noop: cS,
            bind: bY,
            toJson: bM,
            fromJson: bP,
            identity: dE,
            isUndefined: dD,
            isDefined: c3,
            isString: aq,
            isFunction: ag,
            isObject: cq,
            isNumber: bO,
            isElement: a0,
            isArray: aL,
            $$minErr: U,
            version: a2,
            isDate: aN,
            lowercase: o,
            uppercase: ai,
            callbacks: {
                counter: 0
            }
        });
        ch = bR(dt);
        try {
            ch("ngLocale")
        } catch(s) {
            ch("ngLocale", []).provider("$locale", bs)
        }
        ch("ng", ["ngLocale"], ["$provide", 
        function(v) {
            v.provider("$compile", u).directive({
                a: bK,
                input: ac,
                textarea: ac,
                form: bT,
                script: cR,
                select: ds,
                style: dM,
                option: dC,
                ngBind: dL,
                ngBindHtml: n,
                ngBindTemplate: e,
                ngClass: I,
                ngClassEven: af,
                ngClassOdd: T,
                ngCsp: aK,
                ngCloak: ap,
                ngController: aA,
                ngForm: b3,
                ngHide: bU,
                ngIf: aU,
                ngInclude: a3,
                ngInit: bd,
                ngNonBindable: bl,
                ngPluralize: bu,
                ngRepeat: bC,
                ngShow: bL,
                ngStyle: b4,
                ngSwitch: ce,
                ngSwitchWhen: co,
                ngSwitchDefault: cz,
                ngOptions: dg,
                ngTransclude: cI,
                ngModel: cQ,
                ngList: df,
                ngChange: c1,
                required: am,
                ngRequired: am,
                ngValue: dB
            }).directive(bg).directive(ax);
            v.provider({
                $anchorScroll: dp,
                $animate: bk,
                $browser: dJ,
                $cacheFactory: d,
                $controller: O,
                $document: ad,
                $exceptionHandler: an,
                $filter: cv,
                $interpolate: bb,
                $interval: bj,
                $http: ay,
                $httpBackend: aI,
                $location: bJ,
                $log: bS,
                $parse: b2,
                $rootScope: cx,
                $q: cc,
                $sce: c0,
                $sceDelegate: cP,
                $sniffer: db,
                $templateCache: l,
                $timeout: dq,
                $window: dA
            })
        }])
    })(dF);
    dh(cf).ready(function() {
        bz(cf, cu)
    })
})(window, document);
angular.element(document).find("head").prepend('<style type="text/css">@charset "UTF-8";[ng\\:cloak],[ng-cloak],[data-ng-cloak],[x-ng-cloak],.ng-cloak,.x-ng-cloak,.ng-hide{display:none !important;}ng\\:form{display:block;}</style>'); (function(e, a, d) {
    var c = a.module("ngTouch", []);
    c.factory("$swipe", [function() {
        var g = 10;
        function f(i) {
            var j = i.touches && i.touches.length ? i.touches: [i];
            var h = (i.changedTouches && i.changedTouches[0]) || (i.originalEvent && i.originalEvent.changedTouches && i.originalEvent.changedTouches[0]) || j[0].originalEvent || j[0];
            return {
                x: h.clientX,
                y: h.clientY
            }
        }
        return {
            bind: function(i, j) {
                var m,
                n;
                var l;
                var k;
                var h = false;
                i.on("touchstart mousedown", 
                function(o) {
                    l = f(o);
                    h = true;
                    m = 0;
                    n = 0;
                    k = l;
                    j.start && j.start(l, o)
                });
                i.on("touchcancel", 
                function(o) {
                    h = false;
                    j.cancel && j.cancel(o)
                });
                i.on("touchmove mousemove", 
                function(p) {
                    if (!h) {
                        return
                    }
                    if (!l) {
                        return
                    }
                    var o = f(p);
                    m += Math.abs(o.x - k.x);
                    n += Math.abs(o.y - k.y);
                    k = o;
                    if (m < g && n < g) {
                        return
                    }
                    if (n > m) {
                        h = false;
                        j.cancel && j.cancel(p);
                        return
                    } else {
                        p.preventDefault();
                        j.move && j.move(o, p)
                    }
                });
                i.on("touchend mouseup", 
                function(o) {
                    if (!h) {
                        return
                    }
                    h = false;
                    j.end && j.end(f(o), o)
                })
            }
        }
    }]);
    c.config(["$provide", 
    function(f) {
        f.decorator("ngClickDirective", ["$delegate", 
        function(g) {
            g.shift();
            return g
        }])
    }]);
    c.directive("ngClick", ["$parse", "$timeout", "$rootElement", 
    function(f, h, g) {
        var s = 750;
        var n = 12;
        var q = 2500;
        var k = 25;
        var i = "ng-click-active";
        var m;
        var t;
        function l(u, w, v, x) {
            return Math.abs(u - v) < k && Math.abs(w - x) < k
        }
        function j(v, w, z) {
            for (var u = 0; u < v.length; u += 2) {
                if (l(v[u], v[u + 1], w, z)) {
                    v.splice(u, u + 2);
                    return true
                }
            }
            return false
        }
        function o(u) {
            if (Date.now() - m > q) {
                return
            }
            var v = u.touches && u.touches.length ? u.touches: [u];
            var w = v[0].clientX;
            var z = v[0].clientY;
            if (w < 1 && z < 1) {
                return
            }
            if (j(t, w, z)) {
                return
            }
            u.stopPropagation();
            u.preventDefault();
            u.target && u.target.blur()
        }
        function p(u) {
            var v = u.touches && u.touches.length ? u.touches: [u];
            var w = v[0].clientX;
            var z = v[0].clientY;
            t.push(w, z);
            h(function() {
                for (var x = 0; x < t.length; x += 2) {
                    if (t[x] == w && t[x + 1] == z) {
                        t.splice(x, x + 2);
                        return
                    }
                }
            },
            q, false)
        }
        function r(u, v) {
            if (!t) {
                g[0].addEventListener("click", o, true);
                g[0].addEventListener("touchstart", p, true);
                t = []
            }
            m = Date.now();
            j(t, u, v)
        }
        return function(y, w, u) {
            var v = f(u.ngClick),
            B = false,
            A,
            z,
            C,
            D;
            function x() {
                B = false;
                w.removeClass(i)
            }
            w.on("touchstart", 
            function(F) {
                B = true;
                A = F.target ? F.target: F.srcElement;
                if (A.nodeType == 3) {
                    A = A.parentNode
                }
                w.addClass(i);
                z = Date.now();
                var G = F.touches && F.touches.length ? F.touches: [F];
                var E = G[0].originalEvent || G[0];
                C = E.clientX;
                D = E.clientY
            });
            w.on("touchmove", 
            function(E) {
                x()
            });
            w.on("touchcancel", 
            function(E) {
                x()
            });
            w.on("touchend", 
            function(H) {
                var E = Date.now() - z;
                var I = (H.changedTouches && H.changedTouches.length) ? H.changedTouches: ((H.touches && H.touches.length) ? H.touches: [H]);
                var G = I[0].originalEvent || I[0];
                var J = G.clientX;
                var K = G.clientY;
                var F = Math.sqrt(Math.pow(J - C, 2) + Math.pow(K - D, 2));
                if (B && E < s && F < n) {
                    r(J, K);
                    if (A) {
                        A.blur()
                    }
                    if (!a.isDefined(u.disabled) || u.disabled === false) {
                        w.triggerHandler("click", [H])
                    }
                }
                x()
            });
            w.onclick = function(E) {};
            w.on("click", 
            function(E, F) {
                y.$apply(function() {
                    v(y, {
                        $event: (F || E)
                    })
                })
            });
            w.on("mousedown", 
            function(E) {
                w.addClass(i)
            });
            w.on("mousemove mouseup", 
            function(E) {
                w.removeClass(i)
            })
        }
    }]);
    function b(g, f, h) {
        c.directive(g, ["$parse", "$swipe", 
        function(i, j) {
            var k = 75;
            var l = 0.3;
            var m = 30;
            return function(p, o, n) {
                var r = i(n[g]);
                var q,
                s;
                function t(u) {
                    if (!q) {
                        return false
                    }
                    var w = Math.abs(u.y - q.y);
                    var v = (u.x - q.x) * f;
                    return s && w < k && v > 0 && v > m && w / v < l
                }
                j.bind(o, {
                    start: function(u, v) {
                        q = u;
                        s = true
                    },
                    cancel: function(u) {
                        s = false
                    },
                    end: function(u, v) {
                        if (t(u)) {
                            p.$apply(function() {
                                o.triggerHandler(h);
                                r(p, {
                                    $event: v
                                })
                            })
                        }
                    }
                })
            }
        }])
    }
    b("ngSwipeLeft", -1, "swipeleft");
    b("ngSwipeRight", 1, "swiperight")
})(window, window.angular);

var TestShareCtrl = (function () {
    var that;
    var obj = function () {
        that = this;
        //that.insId = insId;//重要
    };

    obj.prototype = {
        ctrl: function ($scope, $http) {
            that.$http = $http;
            that.$scope = $scope;
            that.$scope.proInfo = DataCache.currentProject;
            //that.$scope.getRTime = that.getRTime;
            that.$scope.showShare = that.showShare;
            //that.$scope.flag = flag > 0 ? true : false;
            that.$scope.showDuihuan = that.showDuihuan;
            that.$scope.showDuihuannull = that.showDuihuannull;
            //that.$scope.shareCount = shareCount;
            that.$scope.infoHtml = that.infoHtml;
            that.$scope.backHtml = that.backHtml;
            that.$scope.showContent = that.showContent;
            //that.$scope.participateIn = that.participateIn;
            that.$scope.toTop = that.toTop;
            //that.participateIn();
            //that.prizeList();
            //that.isList();
            that.showguize = false;
            that.showgonglue = false;
            that.$scope.group = {};
            that.$scope.listItem = {};
            that.$scope.listBusiness = {};
            that.$scope.isget = {};
            that.$scope.readlist = {};
            //that.$scope.isGetPrize = isGetPrize > 0 ? true : false;
            //Util.getShareParam(that.$http, that.insId);
            //Util.getShareParam2(that.insId);
            //that.init();
            that.$scope.showtel = that.showtel;
            that.isShowtel = true;


        },
        infoHtml: function (index) {
            if (index == 0) {
                $(".ym").removeClass("hide");
                $(".guize").removeClass("fadeInUp1");
                $(".guize_box").removeClass("fadeInUp");
                $(".setToShare_box").removeClass("fadeInUp");

                //$(".phone_box_top").addClass("hide");
                $("#readshare").removeClass("hide");
                //$("#back").addClass("hide");
                $("#read1").addClass("hide");
                $("#read3").addClass("hide");
            }

            else if (index == 1) {
                that.toTop();
                $(".ym").addClass("hide");
                $(".guize").addClass("fadeInUp1");
                $(".guize_box").addClass("fadeInUp");
                $(".setToShare_box").addClass("fadeInUp");
                $("#back").addClass("hide");

                setTimeout(function () {
                    $("#readshare").addClass("hide");
                    $("#read1").addClass("fadeInShow");
                    $("#r1").addClass("fadeInLeft1");
                    $("#r31").addClass("fadeInLeft3");
                    //$("#retun").addClass("fadeInLeft");
                    $("#back").removeClass("hide");
                    $("#read1").removeClass("hide");
                    $('.back').show();/////////
                }, 1000);
                $(".phone_box_top").removeClass("hide");

                $(".app").css("height", "120%");
                $("#read3").addClass("hide");
                $("#r1").removeClass("hide");
                $("#r11").addClass("hide");
                $("#r31").removeClass("hide");
                $("#r3").addClass("hide");
            }

            else if (index == 3) {
                $(".ym").addClass("hide");
                $(".guize").addClass("fadeInUp1");
                $(".guize_box").addClass("fadeInUp");
                $(".setToShare_box").addClass("fadeInUp");
                setTimeout(function () {
                    $("#readshare").addClass("hide");
                    $("#r11").addClass("fadeInLeft1");
                    $("#r3").addClass("fadeInLeft3");
                    //$("#retun").addClass("fadeInLeft");
                    $(".read1").addClass("fadeInBack1");
                    $(".read2").addClass("fadeInBack2");
                    $(".read3").addClass("fadeInBack3");
                }, 1000);
                $(".app").css("height", "130%");
                $("#read1").addClass("hide");
                $("#read2").addClass("hide");
                //$("#back").removeClass("hide");
                $("#read3").removeClass("hide");
                $("#r1").addClass("hide");
                $("#r11").removeClass("hide");
                $("#r3").removeClass("hide");
                $("#r31").addClass("hide");
            }
        },
        backHtml: function () {
            //var h = ViewBag.height;
            $("#r1").removeClass("fadeInLeft1");
            $("#r11").removeClass("fadeInLeft1");
            $("#r31").removeClass("fadeInLeft3");
            $("#r3").removeClass("fadeInLeft3");
            $(".ym").removeClass("hide");
            $(".guize").removeClass("fadeInUp1");
            $(".guize_box").removeClass("fadeInUp");
            $(".phone_box_top").addClass("hide");
            $(".setToShare_box").removeClass("fadeInUp");
            //$("body").css("height", height + "px");
            $("#back").addClass("hide");
            $("#readshare").removeClass("hide");
            $("#read1").addClass("hide");
            $("#read2").addClass("hide");
            $("#read3").addClass("hide");
            $('.back').hide();

        },
        showContent: function (index) {
            if (index == 1) {
                $("#r1").removeClass("fadeInLeft1");
                $("#r11").removeClass("fadeInLeft1");
                $("#r31").removeClass("fadeInLeft3");
                $("#r3").removeClass("fadeInLeft3");

                $("#read2").addClass("hide");
                $("#read3").addClass("hide");
                $("#back").removeClass("hide");
                $("#read1").removeClass("hide");
                $("#r1").removeClass("hide");
                $("#r11").addClass("hide");
                $("#r31").removeClass("hide");
                $("#r3").addClass("hide");
            }

            else if (index == 3) {
                $("#r1").removeClass("fadeInLeft1");
                $("#r11").removeClass("fadeInLeft1");
                $("#r31").removeClass("fadeInLeft3");
                $("#r3").removeClass("fadeInLeft3");

                $(".app").css("height", "800px");
                $("#read1").addClass("hide");
                $("#read2").addClass("hide");
                $("#back").removeClass("hide");
                $("#read3").removeClass("hide");
                $("#r1").addClass("hide");
                $("#r11").removeClass("hide");
                $("#r3").removeClass("hide");
                $("#r31").addClass("hide");
            }
        },
        showShare: function () {
            $('.ar_fade').show();
        },
        showDuihuan: function (item) {
            $('.back').hide();
            $('.adimg').hide();
            $('.duihuan_fade').show();
            var html = "<div class='duihuan_box'><div style='padding:20px 0;font-size:14px;'>每人只能兑换一样奖品，你确定兑换么？</div>" +
                       " <div class='dhbutton gray' style='width:35%;margin-right:20px;' onclick='TestShareCtrl.cancelduihuan()'>取消</div>" +
                       "<div class='dhbutton orange'  style='width:35%;' onclick=\"TestShareCtrl.duihuan('" + item + "')\">确定</div></div>";
            $('.duihuan_fade').html(html);

        },
        showDuihuannull: function () {
            $('.back').hide();
            $('.adimg').hide();
            $('.duihuan_fade').show();
            var html = "<div class='duihuan_box'><div style='padding:20px 0;font-size:14px;'>您所积攒的阅读数不足以兑换该奖品</div>" +
                       "<div class='dhbutton orange'  style='width:35%;' onclick='TestShareCtrl.cancelduihuan()'>确定</div></div>";
            $('.duihuan_fade').html(html);

        },
        cancelduihuan: function () {
            $('.back').show();
            $('.adimg').show();
            $('.duihuan_fade').hide();
        },
        duihuan: function (itemId) {
            Util.ajax(that, {
                method: "POST",
                data: {
                    itemId: itemId,
                    replyId:replyId,
                },
                url: djurl
            }, function (data) {
                $('.back').show();
                $('.adimg').show();
                //$('.duihuan_fade').hide();
                if (data.Result) {
                   var html = "<div class='duihuan_box'><div style='padding:20px 0;font-size:14px;'>领取成功，您的兑换码为" + data.Result + "</div>" +
                       "<div class='dhbutton orange'  style='width:35%;' onclick='location.reload();'>确定</div></div>";
            		$('.duihuan_fade').html(html);
                }else{
                	var html = "<div class='duihuan_box'><div style='padding:20px 0;font-size:14px;'>" + data.msg + "</div>" +
                       "<div class='dhbutton orange'  style='width:35%;' onclick='TestShareCtrl.cancelduihuan()'>确定</div></div>";
            		$('.duihuan_fade').html(html);
                }
            });
        },
        showtel: function () {
            if (that.isShowtel) {
                $('.phone_box').show();
                that.isShowtel = false;
            } else {
                $('.phone_box').hide();
                that.isShowtel = true;
            }

        },
        toTop: function () {
            window.scrollTo(0, 0);
        },
    };

    return new obj();
})();
var app = angular.module('AngularApp', ['ngTouch']);