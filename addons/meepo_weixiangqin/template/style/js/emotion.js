var form_smiley = (function () {
    var fe = function () {
        this.spearate = 14
    }

    fe.prototype = {
        rend: function (options) {
            var that = this;
            var res = '';
            for (var i = 1; i <= options.count; i++) {
                if (i == 1 || i % 14 == 1) {
                    res += '<div>';
                }

                var ii = (i).toString();
                if (i < 10) {
                    ii = "0" + (i).toString();
                }
                res += '<dd><span data-type=' + options.key + ' data-key="' + options.key + '0' + ii + '" style="background: url(' + options.img + '0' + ii + '.' + options.extend + ') no-repeat 0 0;-webkit-background-size: auto ' + options.size + 'px;width:' + options.size + 'px;height:' + options.size + 'px"></span></dd>';
                if (i % 14 == 0 && i > 0) {
                    res += '</div>';
                }
            }
            if (options.count % 14 != 0) {
                res += "</div>";
            }
            $("#" + options.list).html(res);
            var nav_span = new Array(Math.ceil(options.count / that.spearate));
            $("#" + options.nav).html('<span class="on">' + nav_span.join("</span><span>") + '</span>');
            that.bind(options);

            window.swiper = new Swipe(document.getElementById(options.page), {
                speed: 500,
                callback: function () {
                    $("#" + options.nav + " span").removeClass("on").eq(this.index).addClass("on");
                }
            });
            return that;
        },
        bind: function (options) {
            $("#" + options.list).on("click", function (evt) {
                if ("SPAN" == evt.target.tagName) {
                    var type = evt.target.getAttribute("data-type");

                    var val = '[' + evt.target.getAttribute("data-key").split('_') + ']';
                    //if (type == "smiley") {
                    //    $("#sendtext").val($("#sendtext").val() + val);
                    //    $("#btnsend").removeClass("on");
                    //    this.focus();
                    //}
                    //else {
                        $("#btnsend").addClass("on");
                        sendMessage(AId, sWeimobId, type, val);
                   // }

                }
            });
        }
    }

    return new fe();
})();