// 公共方法 diaoshuang 创建于 2012/12/06 //
var Util=(function(){
    return {
        // 测试用图片地址
        //imgUrl : "http://p.test.com/resize/photo/y/n/n/",
        imgUrl : "http://webphoto.youyuan.com/resize/photo/n/n/n/",

        // 验证手机号 (11位手机号)
        isMobil : function(s){
            var patrn = /^1[3|4|5|6|7|8|9][0-9]{9}$/;
            return patrn.test(s);
        },
        // 验证密码 (只能是数字和字母)
        testPass : function(s){
            var patrn = /^[0-9A-Za-z]{3,16}$/;
            return patrn.test(s);
        },
        // 验证邮箱(x@x.x)
        testMail : function(s){
            var patrn = /(\S)+[@]{1}(\S)+[.]{1}(\w)+/;
            return patrn.test(s);
        },
        isNum : function(s){
            var patrn = /^[0-9]{0,}$/;
            return patrn.test(s);
        },
        // 根据date对象获取时间串YYYY-MM-DD hh:mm:ss
        getDateFromObj : function(dateObj){
            if(dateObj){
                var date = new Date(dateObj.time);

                var year = date.getFullYear();
                var month = date.getMonth()+1;
                if(month>=0 && month<9){
                    month='0'+month;
                }
                var day = date.getDate();
                if(day>=0 && day<9){
                    day='0'+day;
                }
                var hour = date.getHours();
                if(hour>=0 && hour<9){
                    hour='0'+hour;
                }
                var minutes = date.getMinutes();
                if(minutes>=0 && minutes<9){
                    minutes='0'+minutes;
                }
                var second = date.getSeconds();
                if(second>=0 && second<9){
                    second='0'+second;
                }

                return year+'-'+month+'-'+day+' '+hour+':'+minutes+':'+second;
            }else{
                return '';
            }
        },
        // 获取字符串字节长度
        getStrByteLen : function(str){
            if(str == ""){
                return true;
            }else{
                var patrn = /[^\x00-\xff]/g;
                var out = str.replace(patrn,"**");
                return out.length;
            }
        },

        // 显示简约信息提示
        showTips : function(msg){
            gUITools.showTip(msg);
        },

        // 显示信息窗口 div显示，自身关闭
        showTipsWin : function(msg,bakFun){
            alert(msg);
        },

        // 图片按比例缩放
        autoResizeImage : function(maxWidth,maxHeight,objImg){
            var img = new Image();
            img.src = objImg.src;
            var hRatio;
            var wRatio;
            var Ratio = 1;
            var w = img.width;
            var h = img.height;
            if(w == 0){
                w = objImg.naturalWidth?objImg.naturalWidth:maxWidth;
            }
            if(h == 0){
                h = objImg.naturalHeight?objImg.naturalHeight:maxHeight;
            }
            wRatio = maxWidth / w;
            hRatio = maxHeight / h;
            if (maxWidth ==0 && maxHeight==0){
            Ratio = 1;
            }else if (maxWidth==0){//
            if (hRatio<1) Ratio = hRatio;
            }else if (maxHeight==0){
            if (wRatio<1) Ratio = wRatio;
            }else if (wRatio<1 || hRatio<1){
            Ratio = (wRatio<=hRatio?wRatio:hRatio);
            }
            if (Ratio<1){
            w = w * Ratio;
            h = h * Ratio;
            }
            objImg.height = h;
            objImg.width = w;
            $(objImg).css({"height":h,"width":w});
            return Ratio;
        },
        // 预览图片处理
        preview : function(imgFile,imgObj){
            //预览代码，支持 IE6、IE7。
            var t = "" ;
            if(document.all){
                //IE
                t = imgFile.value;
            }else{
                t = window.URL.createObjectURL(imgFile.files[0]); //FF
            }
            imgObj.src = t;
        },
        // 图片文件上传
        fireupLoad : function(){
        },
        // 锁屏处理。flg:true 锁屏; flg:false 解锁;
        lockWindow : function(flg){
            if(flg){
                if($(".blockUI")[0]){
                    $(".blockUI").show();
                }else{
                    // 锁屏控件不存在，创建锁屏控件
                    var divHtml = '<div id="blockUI" class="blockUI"></div>';
                    $("body").append(divHtml);
                }
            }else{
                // 取消锁屏
                $(".blockUI").hide();
                //$(".blockUI").remove();
            }
        },
        // 弹出层设定
        popUpWin : function(objId){
            var x = $("#"+objId);
            // 打招呼，成功后再设置flg
            var top = $(window).height()/2-x.height()/2;
            var left = $(window).width()/2-x.width()/2;
            if(Sys.ie && !window.XMLHttpRequest){
                var _t = x.position().top;
                top = _t + top;
            }
            x.css({'top':top,'left':left});
            x.show();
        },
        // 转码处理，用于AJAX通讯参数转换
        transcoding : function(argJSON){
            // 根据参数个数设定
            var returnV = "";
            for(var i in argJSON){
                if(returnV != ""){
                    returnV += "&" + argJSON[i].name + "=" + encodeURIComponent(encodeURIComponent(argJSON[i].value));
                }else
                {
                    returnV += argJSON[i].name + "=" + encodeURIComponent(encodeURIComponent(argJSON[i].value));
                }
            }
            return returnV;
        },
        // 正则判断
        patternTest : function(exp,strT){
            // 电话号码判断
            // 身份证号判断
            // 邮箱判断
            // 数字判断
        },
        // 兼容功能性判断
        // 浏览器判断

        // 动态导入js文件
        importJS : function(src) {
            src=src.replace(/\./g,'\/');
            if(src.lastIndexOf(".js")!=(src.length-2) && src.lastIndexOf(".JS")!=(src.length-2)){
                jpath=src+'.js';
            }else{
                jpath=src;
            }
            var headerDom = document.getElementsByTagName('head').item(0);
            var jsDom = document.createElement('script');
            jsDom.src = jpath;
            // 异步加载
            jsDom.async = true;
            headerDom.appendChild(jsDom);
        },
        // 背景替换
        inputReplaceClass : function(objId,clearC,addC){
            var removeStr="",removeStrL="",removeStrR="",addM,addL,addR;
            var clearArr = clearC.split(" ");
            var index = 0;
            for(;index<clearArr.length;index++){
                removeStr +=  clearArr[index] + "Middle ";
                removeStrL += clearArr[index] + "Left ";
                removeStrR += clearArr[index] + "Right ";
            }
            addM = addC + "Middle";
            addL = addC + "Left";
            addR = addC + "Right";
            // 替换左侧样式
            $(objId).prev().removeClass(removeStrL).addClass(addL);
            // 替换样式
            $(objId).removeClass(removeStr).addClass(addM);
            // 替换右侧样式
            $(objId).next().removeClass(removeStrR).addClass(addR);

            removeStr=removeStrL=removeStrR=addM=addL=addR=null;
        },
        // AJAX请求
        ajax : function(path,backFun,postData){
            if(!backFun){
                backFun = function(){};
            }
            if(!postData){
                $.ajax({
                    url:path,
                    type:"post",
                    dataType:"json",
                    success:backFun
                });
            }else{
                $.ajax({
                    url:path,
                    type:"post",
                    dataType:"json",
                    data:postData,
                    success:backFun
                });
            }
        },

       /* // 用户登录检查
        // 当前客户端已经登录了
        testLogined : function(){
            var path = "/json/sign_in.html";
            var bor = false;
            $.ajax({
                url:path,
                type:"post",
                dataType:"json",
                async:false,
                success:function(loginFlg){
                    bor = loginFlg;
                }
            });
            return bor;
        },*/
        // 从服务器获取当前用户信息
        getUserInfo : function(){
            var path = "/json/user.html";
            var userInfo = null;
            $.ajax({
                url:path,
                type:"post",
                dataType:"json",
                async:false,
                success:function(data){
                    userInfo = data; 
                }
            });
            return userInfo;
        },
        // 界面跳转 flg:false or undefined
        // 新界面打开 flg:true 
        gotoWin : function(url,flg){
            if(flg){
                //window.openDialog(url,);
            }else{
                window.location.href= url;
            }
        },
        // 切割字符串
        killStr : function(str,len){
            if(typeof(str) != "string"){
                return str;
            }
            if(str.length < len){return str}
            return str.substring(0,4) + "..." + str.substring(str.length-3);
        },
        // log---测试用
        log : function(msg){
            if(window.console){
                console.log(msg);
            }else{
                alert(msg);
            }
        }
    };
})();

var QueryString = {
    data: {},
    Initial: function() {
        var aPairs, aTmp;
        var queryString = new String(window.location.search);
        queryString = queryString.substr(1, queryString.length); //remove   "?"
        aPairs = queryString.split("&");
        for (var i = 0; i < aPairs.length; i++) {
            aTmp = aPairs[i].split("=");
            this.data[aTmp[0]] = aTmp[1];
        }
    },
    GetValue: function(key) {
        return QueryString.data[key];
    }
};

// 用户对象模型 diaoshuang 创建于 2012/12/08
var User = (function(){
    return {
        userInfo:{},
        // 登录状态
        loginStatus : -1,
        // 保存用户信息
        saveInfo:function(data){
            this.userInfo = data;
            for(var i in data){
                // 存cookie
                $.cookie(i,data[i],{"expires":10,"path":"/"})
            }
        },
        // 删除用户信息
        deleteInfo : function(){
            for(var i in this.userInfo){
                // 清cookie
                $.deleteCookie(i);
            }
            // 清本地
            User.userInfo = {};
            User.loginStatus = -1;
        }
    };
})();
$(function(){
    // 判断本地用户登录状态
    /*if(Util.testLogined()){
        // 已经登录
        var userInfo = Util.getUserInfo();
        // 保存用户信息
        User.saveInfo(userInfo);
        User.loginStatus = 1;
        var showName = userInfo.id;
        if(userInfo.nickname && $.trim(userInfo.nickname) != ""){
            showName = userInfo.nickname;
        }
        // 对应界面处理
        if(window["windowLoadFun"]){
            windowLoadFun();
        }
    }*/
});