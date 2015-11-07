(function() {
	var e = function(e) {
		e.synchronizeed = !1;
		var t = function() {
			e.synchronizeed = !1
		},
		n = function(e) {
			return Array.prototype.slice.call(e, 0)
		};
		return function() {
			if (e.synchronizeed) return;
			e.synchronizeed = !0;
			var r = n(arguments);
			r.push(t),
			e.apply(this, r)
		}
	},
	t = function() {
		var e = function() { ! window.timerProxyTimeout || window.clearTimeout(window.timerProxyTimeout)
		};
		return function n(r, i) {
			t.clearProxy = n.clearProxy = e,
			e(),
			window.timerProxyTimeout = window.setTimeout(function() {
				$.isFunction(r) && r()
			},
			i)
		}
	} (),
	n; (function() {
		var r = 0,
		i = function(e) {
			$(".changing-over .now").removeClass("now"),
			$(".changing-over>a").eq(e).addClass("now"),
			r = e
		},
		s = e(function(e) {
			var t = arguments[arguments.length - 1];
			if (r === e) {
				t();
				return
			}
			$(".hd-c .banner .pic-intro").hide(),
			$($(".hd-c .banner .pic-intro")[r]).fadeOut("fast",
			function() {
				$($(".hd-c .banner .pic-intro")[e]).fadeIn("fast"),
				i(e),
				t()
			})
		});
		n = function() {
			r = 0;
			var e = $(".changing-over>a").size();
			$(".changing-over>a").click(function(e) {
				var t = $(".changing-over>a").index($(this));
				s(t),
				e.preventDefault()
			}),
			$(".changing-over>a").mouseover(function(e) {
				var n = $(".changing-over>a").index($(this));
				t(function() {
					s(n)
				},
				200),
				e.preventDefault()
			}),
			$(".changing-over>a").mouseout(function(e) {
				t.clearProxy()
			}),
			$(".hd-c .banner .pre").click(function(t) {
				s((r + e - 1) % e),
				t.preventDefault()
			}),
			$(".hd-c .banner .next").click(function(t) {
				s((r + 1) % e),
				t.preventDefault()
			});
			var n = setInterval(function() {
				s((r + 1) % e)
			},
			4e3);
			$(".changing-over").hover(function() {
				clearInterval(n)
			},
			function() {
				n = setInterval(function() {
					s((r + 1) % e)
				},
				4e3)
			})
		}
	})();
	var r = function(e) {
		this.configSet = e,
		this.config()
	}; (function() {
		var e = function(e) {
			return e.replace(/(^\s*)|(\s*$)/g, "")
		},
		t = {
			email: /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/,
			url: /^(http|https|ftp):\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\/:+!]*([^<>\""])*$/,
			mobile: /^((\(\d{2,3}\))|(\d{3}\-))?(1[35][0-9]|189)\d{8}$/,
			"null": /.+/,
			less400: /^(.|\n){1,400}$/
		},
		n = [],
		i = {
			or: function(e, r) {
				for (var i in e) {
					if (t[i].exec(r)) return ! 0;
					n.push(e[i])
				}
				return n
			},
			and: function(e, r) {
				for (var i in e) if (!t[i].exec(r)) return n.push(e[i]),
				n;
				return ! 0
			}
		},
		s = function(e) {
			var t = "";
			for (var n = 0; n < e.length; n++) t += '<span class="additional-error-message">' + e[n] + "</span>";
			return t
		},
		o = function(t) {
			var r = t.types,
			o = t.elem,
			u = t.concat || "or";
			o.parent().find(".additional-error-message").remove();
			if ( !! t.additional && t.additional()) return ! 0;
			n = [];
			var a = e(o.val()),
			f = i[u](r, a);
			return $.isArray(f) ? (o.after(s(f)), !1) : !0
		},
		u = function(e) {
			var t = e.configs;
			for (var n = 0; n < t.length; n++)(function(e) {
				var n = t[e];
				n.elem.bind("change",
				function() {
					o(n)
				})
			})(n)
		},
		a = function(e) {
			var t = e.callback,
			n = e.configs,
			r = !0;
			for (var i = 0; i < n.length; i++) o(n[i]) || (r = !1);
			r && t()
		};
		r.prototype = {
			constructor: r,
			config: function() {
				u(this.configSet)
			},
			run: function() {
				a(this.configSet)
			}
		}
	})();
	var i; (function() {
		var e = 5,
		t = $("#share"),
		n = parseInt(t.css("top"));
		i = function() {
			$(window).scroll(function() {
				var r = document.body.scrollTop + document.documentElement.scrollTop;
				n - r < e ? t.css("top", e + r) : t.css("top", n)
			})
		}
	})(),
	$(function() {
		n()
	})
})();

var loginBox = (function() {
	var state = 0;
	return {
		toggle: function() {
			state = state ? 0 : 1;
			if (state) {
				$("#loginBox").addClass("on1");
			} else {
				$("#loginBox").removeClass("on1");
			}
		}
	}
})();