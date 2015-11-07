;(function($) {
	/**
	 * 弹窗 vAlert
	 */
	$.vAlert = function(options) {

		var setting = {
			"msg" : null,
			"tit" : "提示",
			"width": "90%",
			"okBtnText" : "确定",
			"callback" : null
		};

		var settings = null;
		if (typeof options === "string") {
			settings = $.extend(setting, {
				"msg" : options
			});
		} else {
			settings = $.extend(setting, options);
		}

		var $temp = $("<div class='vpopup vpopup-alert'>"
				+ "<div class='vpopup-inner' style='width:"+settings.width+"'>" + "<div class='vpopup-title'>"
				+ settings.tit + "</div>" + "<div class='vpopup-content'></div>" + "<div class='vpopup-footer'>"
				+ "<span class='vpopup-btn vpopup-close grid-one'>"
				+ settings.okBtnText + "</span>" + "</div>" + "</div>"
				+ "<div class='vpopup-mask'></div>" + "</div>");

		$temp.find(".vpopup-content").append(settings.msg);
		$("body").append($temp);

		var vpopupInner = $temp.find(".vpopup-inner");
		vpopupInner.css({
			"margin-top" : -vpopupInner.height() / 2
		});
		vpopupInner.find(".vpopup-close").click(function() {
			if (typeof settings.callback === "function") {
				settings.callback();
			}
			$temp.remove();
		});
	};
})(jQuery);

;(function($) {
	/**
	 * 确认弹窗 vConfirm
	 */
	$.vConfirm = function(options, callback) {

		var setting = {
			"msg" : null,
			"tit" : "提示",
			"titLine" : false,
			"width": "90%",
			"okBtnText" : "确定",
			"closeBtnText" : "取消",
			"ok" : {
				"callback" : null,
				"isClose" : false
			}
		};

		var settings = null;
		if (typeof options === "string") {
			settings = $.extend(setting, {
				"msg" : options
			});
			if (typeof callback === "function") {
				settings = $.extend(setting, {
					"ok" : callback
				});
			}
		} else {
			settings = $.extend(setting, options);
		}

		var $temp = $("<div class='vpopup vpopup-confirm'>"
				+ "<div class='vpopup-inner' style='width:"+settings.width+"'>" + "<div class='vpopup-title'>"
				+ settings.tit + "</div>" + "<div class='vpopup-content'></div>" + "<div class='vpopup-footer'>"
				+ "<span class='vpopup-btn vpopup-close grid-two'>"
				+ settings.closeBtnText + "</span>"
				+ "<span class='vpopup-btn vpopup-ok grid-two'>"
				+ settings.okBtnText + "</span>" + "</div>" + "</div>"
				+ "<div class='vpopup-mask'></div>" + "</div>");

		$temp.find(".vpopup-content").append(settings.msg);
		$("body").append($temp);

		var vpopupInner = $temp.find(".vpopup-inner");
		vpopupInner.css({
			"margin-top" : -vpopupInner.height() / 2
		});
		if (settings.titLine === true) {
			vpopupInner.find(".vpopup-title").addClass("vpopup-title-line");
		}
		vpopupInner.find(" .vpopup-ok").click(function() {
			if (typeof settings.ok === "function") {
				settings.ok();
			} else if (typeof settings.ok === "object") {
				if (typeof settings.ok.callback === "function") {
					settings.ok.callback();
				}
				if (settings.ok.isClose === true) {
					$temp.remove();
				}
			}
		});
		vpopupInner.find(" .vpopup-close").click(function() {
			$temp.remove();
		});

	};
})(jQuery);