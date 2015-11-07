define("http://core.h5.lietou-static.com/v1/dialogs/mask.js", [ "http://core.h5.lietou-static.com/v1/zepto/zepto.js" ], function(require, exports) {
    var $ = require("http://core.h5.lietou-static.com/v1/zepto/zepto.js");
    function mask(obj) {
        if (!obj.el) {
            return;
        }
        this.option = {
            css: {},
            className: "js_mask",
            noTouch: true,
            unique: false
        };
        $.extend(this.option, obj);
        this.option.el = this.el = $(this.option.el);
        if (this.option.unique) {
            if (this.el[0].libMaskMask) {
                return this.el[0].libMaskMask;
            } else {
                this.el[0].libMaskMask = this;
            }
        }
        this.init();
    }
    mask.prototype.init = function() {
        this.orgPosition = this.el.css("position");
        this.create();
        this.bind();
    };
    mask.prototype.create = function() {
        var mask = this.option.el.children("." + this.option.className);
        if (this.option.unique) {
            this.mask = mask.length ? mask : null;
        }
        if (!this.mask) {
            this.mask = $('<div class="' + this.option.className + '">').css({
                position: "fixed",
                background: "rgba(0,0,0,0.5)",
                right: 0,
                top: 0,
                bottom: 0,
                left: 0
            }).appendTo(this.option.el);
            if (this.option.noTouch) {
                this.mask.on("touchmove", function() {
                    return false;
                });
            }
        }
        this.mask.css(this.option.css);
    };
    mask.prototype.show = function() {
        this.el.css({
            position: "relative"
        });
        if (this.mask) this.mask.show();
        return this;
    };
    mask.prototype.hide = function() {
        this.el.css({
            position: this.orgPosition
        });
        if (this.mask) this.mask.hide();
        return this;
    };
    mask.prototype.reset = function() {
        this.el.css({
            position: this.orgPosition
        });
        if (this.mask) this.mask.hide();
        return this;
    };
    mask.prototype.remove = function() {
        this.hide();
        if (this.mask) this.mask.remove();
        this.mask = null;
        this.el[0].libMaskMask = null;
        return this;
    };
    mask.prototype.bind = function() {
        var self = this;
        if (this.option.click) {
            this.el.find("." + self.option.className).click(function() {
                self.option.click.call($(this));
            });
        }
    };
    return mask;
});