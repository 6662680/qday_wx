(function ($, window, document, undefined) {
    /**
     * 是否是移动设备
     * @type {boolean}
     */
    var ifMobileDev = /AppleWebKit.*Mobile/i.test(navigator.userAgent) || (/MIDP|SymbianOS|NOKIA|SAMSUNG|LG|NEC|TCL|Alcatel|BIRD|DBTEL|Dopod|PHILIPS|HAIER|LENOVO|MOT-|Nokia|SonyEricsson|SIE-|Amoi|ZTE/.test(navigator.userAgent));
    /**
     * 首字母大写 其它小写
     * @param str
     * @returns {string}
     */
    var firstLetterToBig = function (str) {
        str = str.toLocaleLowerCase();
        str = str.substring(0, 1).toLocaleUpperCase() + str.substring(1, str.length);
        return str;
    };
    var _dialogs = [];
    var _dialogOverlay = $('.app-dialog-overlay');
    var app = function () {
        return{
            //单击事件 因为在手机上click有200毫秒的延迟 所以移动设备用tap
            clickEvent: ifMobileDev ? 'click' : 'click',
            /**
             * 为$注入2css操作个方法 transform transition 2个事件监听方法transitionEnd animationEnd
             */
            methodInject: function () {
                $.fn.transform = function (transform) {
                    for (var i = 0; i < this.length; i++) {
                        var elStyle = this[i].style;
                        elStyle.webkitTransform = elStyle.MsTransform = elStyle.msTransform = elStyle.MozTransform = elStyle.OTransform = elStyle.transform = transform;
                    }
                    return this;
                };
                $.fn.transition = function (key, value) {
                    key = firstLetterToBig(key);
                    for (var i = 0; i < this.length; i++) {
                        var elStyle = this[i].style;
                        elStyle['webkitTransition' + key] = elStyle['MsTransition' + key] = elStyle['msTransition' + key] = elStyle['MozTransition' + key] = elStyle['OTransition' + key] = elStyle['transition' + key] = value;
                    }
                    return this;
                };
                $.fn.transitionEnd = function (callback) {
                    var events = ['webkitTransitionEnd', 'transitionend', 'oTransitionEnd', 'MSTransitionEnd', 'msTransitionEnd'],
                        i, j, dom = $(this[0]);

                    function fireCallBack(e) {
                        callback.call(this, e);
                        for (i = 0; i < events.length; i++) {
                            dom.off(events[i], fireCallBack);
                        }
                    }

                    if (callback) {
                        for (i = 0; i < events.length; i++) {
                            dom.on(events[i], fireCallBack);
                        }
                    }
                    return this;
                };
                $.fn.animationEnd = function (callback) {
                    var events = ['webkitAnimationEnd', 'OAnimationEnd', 'MSAnimationEnd', 'animationend'],
                        i, j, dom = $(this[0]);

                    function fireCallBack(e) {
                        callback.call(this, e);
                        for (i = 0; i < events.length; i++) {
                            dom.off(events[i], fireCallBack);
                        }
                    }

                    if (callback) {
                        for (i = 0; i < events.length; i++) {
                            dom.on(events[i], fireCallBack);
                        }
                    }
                    return this;
                };
            },
            init: function () {

            },
            dialog: function (options) {
                function model() {
                    var params = {
                        html: '',
                        outerClick: false,
                        lifespan: null,
                        overlayClassName: '',
                        inClassName: 'fx-dialogIn',
                        outClassName: 'fx-dialogOut'
                    };
                    var self = this;
                    var opt = $.extend(params, options);
                    this.outerClick = opt.outerClick;

                    if (_dialogOverlay.length == 0) {
                        _dialogOverlay = $('<div></div>');
                        $('body').append(_dialogOverlay);
                    }
                    _dialogOverlay.attr('class', 'app-dialog-overlay ' + opt.overlayClassName).addClass('fx-overlayIn');
                    var $dialog = $('<div class="app-dialog"></div>');
                    $('body').append($dialog.append(opt.html));
                    setTimeout(function () {
                        $dialog.addClass(opt.inClassName);
                    }, 0);

                    self.dialog = $dialog;
                    self.index = _dialogs.length;
                    self.time = null;

                    var time;

                    self.remove = function () {
                        clearTimeout(self.time);
                        _dialogs.splice(self.index, 1);
                        if (_dialogs.length == 0) {
                            _dialogOverlay.addClass('fx-overlayOut')
                        }
                        $dialog.removeClass(opt.inClassName).addClass(opt.outClassName);
                        var i = 0;
                        $dialog.transitionEnd(function () {
                            if (i === 0) {
                                $dialog.remove();
                            }
                            i++;
                        });
                    };
                    if (!!opt.lifespan) {
                        self.time = setTimeout(function () {
                            self.remove();
                        }, opt.lifespan);
                    }

                    return this;
                }

                return new model(options);
            },
            loading: function () {
                var temp = '<div class="loading"><span class="loader"></span></div>';
                return app.dialog({
                    html: temp,
                    outerClick: false,
                    overlayClassName: 's-lucency'
                });
            },
            alert: function (text, param) {
                var temp = '<div class="alert"><div class="text">' + text + '</div>';
                if (typeof(param) === 'undefined' || typeof(param) === 'function') {
                    temp += '<div class="button"><a href="javascript:;" class="yes">确定</a><a href="javascript:;" class="no">取消</a></div>';
                }
                temp += '</div>';
                var $temp = $(temp);
                var dialog = app.dialog({
                    html: $temp,
                    outerClick: false,
                    lifespan: typeof(param) === 'number' ? param : null
                });
                if (typeof(param) === 'undefined' || typeof(param) === 'function') {
                    param = param || function () {
                    };
                    $temp.find('.button a').on(app.clickEvent, function (e) {
                        if ($(this).hasClass('yes')) {
                            param(dialog);
                        }
                        dialog.remove();
                    });
                }
                return dialog
            },
            select: function (data, func) {
                var temp = '<div class="select"><div class="inner">';
                for (var key in data) {
                    temp += '<a class="option" href="javascript:;">' + key + '</a>';
                }
                temp += '</div></div>';
                var $temp = $(temp);
                var dialog = app.dialog({
                    html: $temp,
                    outerClick: false
                });
                func = func || function () {
                };
                $temp.find('.option').on(app.clickEvent, function () {
                    var text = $(this).html();
                    func(text, data[text]);
                    dialog.remove();
                });
            }
        }
    }();
    window.app = app;

    $(function () {
        app.methodInject();
        app.init();
    });

})
(window.jQuery || window.Zepto || window.$, window, document);

//评分
(function ($, window, document, undefined) {
    var pluginName = "grade",
        defaults = {
            'classStore': 'u-grade',
            'maxScore': 5,
            'defaultScore': 5,
            'texts': [],
            'ifText': false,
            'ifEdit': true
        };

    function Plugin(element, options) {
        this.element = element;
        this.options = $.extend({}, defaults, options);
        this.init();
    }

    Plugin.prototype = {
        init: function () {
            var self = this;
            var opt = self.options;
            var html = '<div class="' + opt.classStore + '">';
            if (opt.ifEdit) {
                for (var i = 0; i < opt.maxScore; i++) {
                    html += '<span class="grid"></span>';
                }
            } else {
                html += '<div class="value"></div>';
            }
            if (opt.ifText) {
                html += '<div class="text"></div>'
            }
            html += '</div>';
            var $grade = $(html);
            $grade.insertBefore(self.element).append(self.element);

            self.gradeObj = $grade;
            self.value = opt.defaultScore;
            self.change();

            if (opt.ifEdit) {
                var $grids = $grade.find('.grid');
                $grids.on(app.clickEvent, function () {
                    self.value = $(this).index() + 1;
                    self.change();
                });
            }
        },
        change: function () {
            var self = this;
            var opt = self.options;
            var $grade = self.gradeObj;
            if (opt.ifEdit) {
                var $grids = $grade.find('.grid');
                $grids.removeClass('z-sel');
                for (var i = 0; i < self.value; i++) {
                    $grids.eq(i).addClass('z-sel');
                }
            } else {
                var $value = $grade.find('.value');
                $value.css('width', (opt.defaultScore / opt.maxScore * 100) + '%');
            }
            if (opt.ifText) {
                var $text = $grade.find('.text');
                if (opt.texts.length > 0) {
                    $text.html(opt.texts[self.value - 1]);
                }
            }

            $(self.element).val(self.value);
        }
    };
    $.fn[pluginName] = function (options) {
        var dataName = "plugin_" + pluginName;
        return this.each(function () {
            if (!$(this).data(dataName)) {
                $(this).data(dataName, new Plugin(this, options));
            }
        });
    };

})(window.jQuery || window.Zepto, window, document);
