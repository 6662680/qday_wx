(function(a, l) {
    var d = new Sketch("sketch", {
        lineWidth: 5,
        color: "black",
        bgcolor: "white"
    }),
    c = a("#pad-tools");
    c.find("button").tap(function() {
        _hmt.push(["_trackEvent"].concat(a(this).attr("_trace").split(/,\s*/g), 1))
    });
    a(window).resize(function() {
        d.width = a("#pad").width();
        d.height = a("#pad").height();
        d.reDraw()
    }).resize();
    var f = 1,
    k = [1, 4, 8];
    c.find(".pen-w").tap(function() {
        f = ++f % 3;
        a(this).find("i").removeClass("active").eq(f).addClass("active");
        d.lineWidth = k[f]
    });
    var e, g = "#000000 #FFFFFF #FFC0CB #FF4500 #FF0000 #B8860B #FF1493 #00FF00 #008B00 #0000CD #EEEE00 #636363 #B2DFEE #4876FF #00E5EE".split(" ");
    c.find(".pen-c").tap(function() {
        var b = a(this);
        e || (e = new K.Panel, e.setContent('<a class="close"></a><ul>' + g.map(function(b) {
            return '<li style="background:' + b + ';"></li>'
        }).join("") + "</ul>").frame.addClass("color-pad"), e.frame.find(".close").tap(function() {
            e.hide()
        }).end().find("li").tap(function() {
            var c = a(this).index();
            d.color = g[c];
            b.css("border-color", g[c]);
            e.hide()
        }));
        e.show()
    });
    c.find(".pen-cancel").tap(function() {
        d.cancel()
    });
    c.find(".pen-clear").tap(function() {
        d.clear()
    });
    c.find(".pen-upload").tap(function() {
       // if (3 > d.steps) return K.alert("你画的太简单了哦，<br/>补充一下哦～么么哒");
        // var b = K.Panel().setContent('<a class="close"></a><div class="sysform"><p>请写上你的大名</p><p><input class="type-input" type="text" placeholder="姓名" /></p><p class="err"></p><p><button class="button red-button"><i class="l"></i>确定</button></p></div>'),
        // c = b.frame.find("input"),
        // e = b.frame.find(".err"),
        // f = b.frame.find("button"),
        g = d.toDataUrl();
        // b.frame.addClass("syswin").find(".close").tap(function() {
            // b.destroy()
        // });
		  $.post(getUrl(), {
                //qid: e.options.obId,
                image: g
            },
            function(imgid) {
                location.href = myImage(imgid);
            },
            "json");

  
       // b.show()
    });
    // a.post(productionsurl,
    // function(b) {
        // b && b.msg && a("#picTotal").text(b.msg)
    // });
    a(document).on("touchmove",
    function() {
        return ! 1
    })
})(Zepto);
