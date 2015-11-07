/**
 * author arvin
 * 2014-03-03
 * version 2.0
 */
$(function($) {
    var pageLoad = function(el, options) {
        //初始化
        this.initOptions(options);
		this.initLoadMask();
        this.initContainer();
        this.initPopstate();
		this.initResize();
    }
    $.fn.pageLoad = function(options) {
        return new pageLoad(this, options);
    };
    pageLoad.prototype = {
        initOptions: function(options) {
            options = options || {};
            var defaults = {
                load: function() {
                    //页面加载后
                },
                error: function() {
                    //页面加载失败
                },
                beforeload: function() {
                    //页面加载前
                },
                _load: function(pageId, response, status, xhr) {
					this.setMinHeight();
					this.showLoadMask(false);
                    this.options.load.call(this, pageId, response, status, xhr);
                },
                _beforeload: function(pageId) {
                    this.options.beforeload.call(this, pageId);
                },
                _error: function(response, status, xhr) {
					this.showLoadMask(false);
                    this.options.error.call(this, response, status, xhr);
                },
                transition: 'slide',
                popstate: true,
                changeHash: true,
                cache: false,
                container: '.page-load-container',
                page: '.page-load-page',
                loadImageCls: "page-load-mask-img"
            }
            this.options = $.extend({}, defaults, options);

            $.ajaxSetup({
                cache: this.options.cache //关闭AJAX相应的缓存
            });
        },
		initResize: function(){
			var _this = this;
			$(window).bind('resize', function(){
				_this.setMinHeight();
			})
		},
		setMinHeight: function(){
			var _this = this;
			$(_this.options.container).css('min-height', $(window).height());
			$(_this.options.container + ' ' + _this.options.page).css('min-height', $(window).height())
		},
        initPopstate: function() {
            var _this = this;
            if (!this.options.popstate) return;
            $(window).bind('popstate', function(e) {
                var state = e.originalEvent.state;
                if (state) {
                    _this.isHistory = true;
                    _this.changePage(state.url, state.back);
                }
            })
        },
        initContainer: function() {
            var _this = this;
            var  options = _this.options;
            var container = $(_this.options.container);
            var pageId = container.find(_this.options.page).attr('data-id');
			container.find(_this.options.page).attr('data-current', true);
            if (typeof options._load === 'function') {
                options._load.call(_this, pageId);
            }
            _this.initLinkClick();

        },
        loadPage: function(url, pageId, title, html) {
            var _this = this;
            var container = $(_this.options.container);
			$(html).hide();
            container.append(html);
            container.find('div[data-id="' + pageId + '"]').attr('data-url', url).attr('data-current', true).siblings().attr('data-current', false);
			title && $('title').html(title);
        },
        requestPage: function(url, back, data) {
            var _this = this;
            var options = _this.options;
            _this.showLoadMask(true);
            $('<div></div>').load(url ,data, function(response, status, xhr) {
                response = $('<div>' + response + '</div>');
                var title = response.find('title').html();
                var html = response.find(_this.options.page).clone(true);
                var pageId = response.find(_this.options.page).attr('data-id')
                if (status == "error") {
                    if (typeof options._error === 'function') {
                        options._error.call(_this, response, status, xhr);
                    }
                    return;
                }
				response.remove();
                _this.loadPage(url, pageId, title, html);

                setTimeout(function() {
                    _this.transition(back, function() {
                        options.changeHash && _this.changeHash(url, true);
                        if (typeof options._load === 'function') {
                            options._load.call(_this, pageId, response, status, xhr);
                        }
                        _this.initLinkClick();
						
                    });
                }, 50);
            });

        },
        transition: function(back, callback) {
            var _this = this,
                options = _this.options;
            var transitions = pageLoad.transitions;
            if (typeof transitions[options.transition] === 'function') {
                transitions[options.transition].call(this, back, function() {
                    _this.removePage();
					_this.removePageStyle();
                    if (typeof callback === 'function') {
                        callback.call(_this);
                    }
                });
            }
        },
        initLoadMask: function() {
            var _this = this;
            if ($('.page-load-mask')[0]) 
                return;
            var mask = $('<div class="page-load-mask"></div>');
            mask.html('<div style="display:table; width:100%; height:100%;"><div style="display:table-row"><div style="display:table-cell; vertical-align:middle;"><span class="' + this.options.loadImageCls + '">&nbsp</span><br/><span class="page-load-mask-text"></span></div></div></div>');
            $('body').append(mask);
        },
        showLoadMask: function(show, text) {
            var _this = this;
			text ? $('.page-load-mask .page-load-mask-text').html(text) : $('.page-load-mask .page-load-mask-text').html('');
            show ? $('.page-load-mask').show() : $('.page-load-mask').hide();
        },
        changeHash: function(url, push) {
            var _this = this,
                dBody = _this.body;
            var id = $(_this.options.container + ' ' + _this.options.page + '[data-current="true"]').attr('data-id');
            if (_this.isHistory === true) {
                _this.isHistory = false;
                return;
            }
            push !== false && history && history.pushState && history.pushState({}, '', url);
            history && history.replaceState && history.replaceState({
                id: id,
                url: url
            }, document.title, url);
        },
        removePage: function() {
			var _this = this;
            $(_this.options.container + ' ' + _this.options.page + '[data-current="true"]').siblings().remove();
        },
        initLinkClick: function() {
            var _this = this;
            $('a[data-page="true"]').bind('click', function(e) {
                var ajax = $(this).attr('data-ajax') !== 'false';
                var back = $(this).attr('data-back') === 'true';
                var url = this.href;
                var options = _this.options;
				_this.showLoadMask(true);
                if (!ajax) {
                    return;
                }
                _this._changePage(url, back);
                return false;
            })
        },
        _changePage: function(url, back, data) {
            if (!url) {
                return;
            }
            var _this = this;
            var options = _this.options;

            if (typeof options._beforeload === 'function') {
                options._beforeload.call(_this);
            }
            _this.removePage();
            _this.requestPage(url, back, data);
        },
        changePage: function(url, back, ajax, data) {
            if (!url) {
                return;
            }
            var _this = this;
			_this.showLoadMask(true);
            if (ajax === false) {
                window.location.href = url;
            }
            _this._changePage(url, back, data);
        },
		removePageStyle: function(){
			var _this = this;
			$(_this.options.container + ' ' + _this.options.page).removeAttr('style');
			$(_this.options.container).removeAttr('style');
			_this.setMinHeight();
		},
        translate: function(target, x, y, speed, timing) {
            var slide = target;
            var style = slide && slide.style;

            if (!style)
                return;

            style.webkitTransitionDuration = style.MozTransitionDuration = style.msTransitionDuration = style.OTransitionDuration = style.transitionDuration = speed + 'ms';
            style.webkitTransitionProperty = style.MozTransitionProperty = style.msTransitionProperty = style.OTransitionProperty = style.transitionProperty = 'all';
            style.webkitTransitionFunction = style.MozTransitionTimingFunction = style.msTransitionTimingFunction = style.OTransitionTimingFunction = style.transitionTimingFunction = timing || 'ease';
            style.webkitTransform = 'translate(' + x + ', ' + y + ')' + ' translateZ(0)';
            style.msTransform = style.MozTransform = style.OTransform = 'translateX(' + x + ')' + ' translateY(' + y + ')';

        },
        transform: function(target, css, speed, timing) {
            var slide = target;
            var style = slide && slide.style;

            if (!style)
                return;

            style.webkitTransitionDuration = style.MozTransitionDuration = style.msTransitionDuration = style.OTransitionDuration = style.transitionDuration = speed + 'ms';
            style.webkitTransitionProperty = style.MozTransitionProperty = style.msTransitionProperty = style.OTransitionProperty = style.transitionProperty = 'all';
            style.webkitTransitionFunction = style.MozTransitionTimingFunction = style.msTransitionTimingFunction = style.OTransitionTimingFunction = style.transitionTimingFunction = timing || 'ease';
            style.webkitTransform = style.msTransform = style.MozTransform = style.OTransform = css;

        }
    };

    //切换动画
    pageLoad.transitions = {
        slide: function(back, callback) {
            var _this = this;
            var prevPage = $(_this.options.container + ' ' + _this.options.page + '[data-current="false"]');
			var page = $(_this.options.container + ' ' + _this.options.page + '[data-current="true"]');
			$(_this.options.container).css({
				width: '100%',
				height: '100%'
			});
			$(_this.options.container + ' ' + _this.options.page).css({
				width: '100%',
				height: '100%',
				position: 'absolute',
				top: 0,
				left: 0,
				right: 0,
				bottom: 0
			})
            if (back) {
                _this.translate(page[0], '-100%', '0px', 0);
                page.show();
                _this.translate(prevPage[0], '0%', '0px', 0);

                setTimeout(function() {
                    _this.translate(page[0], '0%', '0px', 300);
                    _this.translate(prevPage[0], '100%', '0px', 300);
                })
            } else {
                _this.translate(page[0], '100%', '0px', 0);
                page.show();
                _this.translate(prevPage[0], '0%', '0px', 0);

                setTimeout(function() {
                    _this.translate(page[0], '0%', '0px', 300);
                    _this.translate(prevPage[0], '-100%', '0px', 300);
                })
            }
            setTimeout(function(){
                callback.call(_this);
            }, 350)
        }
    }
}($))