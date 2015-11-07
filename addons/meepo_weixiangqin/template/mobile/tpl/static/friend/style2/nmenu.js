function show(){
    var elm = document.getElementById("showImg");
    if(elm.getAttribute("data-key")=='show'){
        $("#tmLevel").show();
        // 显示
        elm.setAttribute("data-key","hide");
        elm.setAttribute("src", "/resources/css/images/menu_01active.png");

        $(".main_key").each(function(){
            var k_x = $(this).attr("data-x");
            var k_y = $(this).attr("data-y");
            $(this).css({
                left:k_x+"px",
                bottom:k_y+"px",
                "transform":"rotate(720deg)",
                "-moz-transform":"rotate(720deg)",
                "-webkit-transform":"rotate(720deg)",
                "-ms-transform":"rotate(720deg)"
            });
        });
        var k = $(".mail_count").attr("data-key");
        var kk = $(".main_key[data-key='"+k+"']");
        $(".mail_count").css({"left":(parseInt(kk.attr("data-x"))+kk.width())+"px","bottom":(parseInt(kk.attr("data-y"))+kk.height())+"px"});

    }else{
        $("#tmLevel").hide() ;
        // 隐藏
        elm.setAttribute("data-key","show");
        elm.setAttribute("src", "/resources/css/images/menu_01.png");
        $(".main_key").each(function(){
            var k_h = $(this).attr("data-s").split(",");
            $(this).css({
                "left":k_h[0]+"px",
                "bottom":k_h[1]+"px",
                "transform":"rotate(0deg)",
                "-moz-transform":"rotate(0deg)",
                "-webkit-transform":"rotate(0deg)",
                "-ms-transform":"rotate(0deg)"
            });
        });
        var mv = $("#showImg").width()
            ,v_l = $("#showImg").attr("data-left")?parseInt($("#showImg").attr("data-left")):0
            ,v_b = $("#showImg").attr("data-bottom")?parseInt($("#showImg").attr("data-bottom")):0;
        $(".mail_count").css({"left":(mv+v_l)+"px","bottom":(mv+v_b)+"px"});
    }
}
function showK(elm){
    $(elm).toggleClass("main_key key_click").css({
        "transform":"scale(1.8)",
        "-moz-transform":"scale(1.8)",
        "-webkit-transform":"scale(1.8)",
        "-ms-transform":"scale(1.8)"
    });
    setTimeout(function(){
        var k_h = $(elm).attr("data-s").split(',');
        $(elm).hide();
        $(elm).toggleClass("main_key key_click").css({
            "left":k_h[0]+"px",
            "bottom":k_h[1]+"px",
            "transform":"rotate(0deg)",
            "-moz-transform":"rotate(0deg)",
            "-webkit-transform":"rotate(0deg)",
            "-ms-transform":"rotate(0deg)"
        });
        setTimeout(function(){
            $(elm).show();
        },400);
    },600);
    // 隐藏
    var elm2 = document.getElementById("showImg");
    elm2.setAttribute("data-key","show");
    elm2.setAttribute("src", "/resources/css/images/menu_01.png");
    $(".main_key").each(function(){
        var k_h = $(this).attr("data-s").split(',');
        $(this).css({
            "left":k_h[0]+"px",
            "bottom":k_h[1]+"px",
            "transform":"rotate(0deg)",
            "-moz-transform":"rotate(0deg)",
            "-webkit-transform":"rotate(0deg)",
            "-ms-transform":"rotate(0deg)"
        });
    });
    var k = $(".mail_count").attr("data-key");
    if(elm.getAttribute("data-key")!=k){
        var mv = $("#showImg").width()
            ,v_l = $("#showImg").attr("data-left")?parseInt($("#showImg").attr("data-left")):0
            ,v_b = $("#showImg").attr("data-bottom")?parseInt($("#showImg").attr("data-bottom")):0;
        $(".mail_count").css({"left":(mv+v_l)+"px","bottom":(mv+v_b)+"px"});
    }else{
        $(".mail_count").css({
            "transform":"scale(1.8)",
            "-moz-transform":"scale(1.8)",
            "-webkit-transform":"scale(1.8)",
            "-ms-transform":"scale(1.8)",
            "opacity":0
        });
    }
}
function resite(){
    // 初始化，确定定位
    var mv = $("#showImg").width()
        ,sv = $(".main_key"),mc=$(".mail_count")
        ,lost_r = (mv-sv.width())/76*sv.width(),i=0
        ,v_l = $("#showImg").attr("data-left")?parseInt($("#showImg").attr("data-left")):0
        ,v_b = $("#showImg").attr("data-bottom")?parseInt($("#showImg").attr("data-bottom")):0;

    $("#showImg").css({left:v_l+"px",bottom:v_b+"px"});
    sv.css({left:(v_l+lost_r)+"px",bottom:(v_b+lost_r)+"px"}).attr("data-s",(v_l+lost_r)+","+(v_b+lost_r));
    setTimeout(function(){
        var host = (location.pathname).replace("/v10","");

        sv.each(function(){
            var idx = parseInt($(this).attr("data-key"));
            var pageId = $(this).attr("data-pageId");
            var src = $(this).attr("src");
            if(pageId.indexOf(host)>-1){
                $(this).css({
                    "transition":"all 0.15s linear "+idx*35+"ms",
                    "-moz-transition":"all 0.15s linear "+idx*35+"ms",
                    "-ms-transition":"all 0.15s linear "+idx*35+"ms",
                    "-webkit-transition":"all 0.15s linear "+idx*35+"ms"
                }).attr("src",src.replace((idx+1),(idx+1)+"_active"));
            }else{
                $(this).css({
                    "transition":"all 0.15s linear "+idx*35+"ms",
                    "-moz-transition":"all 0.15s linear "+idx*35+"ms",
                    "-ms-transition":"all 0.15s linear "+idx*35+"ms",
                    "-webkit-transition":"all 0.15s linear "+idx*35+"ms"
                });
            }
        });
    },100);
    var idx = $(mc).attr("data-key");
    mc.css({left:(v_l+mv)+"px",bottom:(v_b+mv)+"px",
        "transition":"all 0.15s linear "+idx*35+"ms",
        "-moz-transition":"all 0.15s linear "+idx*35+"ms",
        "-ms-transition":"all 0.15s linear "+idx*35+"ms",
        "-webkit-transition":"all 0.15s linear "+idx*35+"ms"
    });
    // 显示收起菜单图标
    setTimeout(function(){
        sv.show();
    },500);
    // 确定偏移量
    var r = Math.round(mv/2 + sv.width()*2.2);
    for(;i<5;i++){
        var k_x = Math.round(v_l + lost_r+r*Math.cos((4-i)*22.5/180*Math.PI));
        var k_y = Math.round(v_b + lost_r+r*Math.sin((4-i)*22.5/180*Math.PI));
        sv.eq(i).attr("data-x",k_x).attr("data-y",k_y);
    }
}
$(function(){
    $(window).resize(function(){
        resite();
    });
    resite();
});