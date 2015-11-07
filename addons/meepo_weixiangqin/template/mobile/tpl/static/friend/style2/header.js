var Header = (function(){
    return {
        // ie6判断
        isIE6 : !-[1,] && !window.XMLHttpRequest,
        // 关闭总体点击控制
        closeControlFlg:false,
        // 登录执行中标识
        loggingFlg : false,
        sayingFlg : false,
        //
        totalNumFlg:false,
        _interval : 0,//定义滚动间隙时间
        _moving : null,//需要清除的动画
        _movingFlg : true, // 移动标识
        index : 0,
        localTitle:"",// 标题原内容
        titleItr:null,// 标题闪动对象
        popupAutoCloseObj : null, //弹层自动关闭对象
        popupAutoCloseTime : 2000, // 弹层自动关闭时间间隔(毫秒)
        // 独白库
        popupCloseFlg : false,
        // 打招呼数据
        sayHiInfo : null,
        heartMsgList:[
            "生命中有你，感觉精彩；回忆中有你，感觉温馨；旅程中有你，感觉快乐；失落中有你，感觉坚毅；沉默中有你，感觉灿烂；朋友中有你，我不再孤单！",
            "我一直在这里，没有离开，我一直在等待，等着那个和我终生相伴相爱一生的你的出现。因缘而聚，我始终这样认为，所以觉得这里真的很好，美丽的缘分，也许就是给我的最美丽的暗示吧。",
            "每到一个地方，就爱带回些当地拾的花草碎石，天物所能传递的信息和能量，在我看来是对那里最深切的回味.生活就是用简单的方式来享受，是好好去拥有每一件而不是拥有更多————-希望和他过专注宁静的生活，当我们和自然同在，我们会充满希望.",
            "对整个世界来说，你是一个人，而对一个人来说，你就是整个世界。我会在这里等着你的到来，期待我们的爱情开花结果，期待把我们的小窝布置得浪漫温馨，一回到家就有幸福的感觉。",
            "爱是责任，爱是一辈子的承诺，爱是相互的扶持，有伟大的爱情，有轰轰烈烈的爱情，然而，我从来就相信爱情应该渗透在生活中的点点滴滴，关心，鼓励，两个人不离不弃，平凡而不平淡，浪漫温馨。",
            "感谢那些浏览我的资料的人，感谢那些关注我的人，感谢那些给我带来爱情希望的人，在有缘网，在寻找缘分的旅途上，我走得并不孤单。而最应该感谢的，就是你，我未来的所爱，我相信你一定在这里，在一个我还不曾到达的角落，在一个我们的目光不曾交汇的地方。",
            "开心厨房，温馨卧室，表达爱意的小装饰，恬静的香氛，甚至是自己亲手烘烤的蛋糕，花样翻新的烛光晚餐，情调独具的惬意下午茶，爱情不因岁月流逝而消减，爱情不因生活琐碎而平淡，我们一起来经营吧！",
            "如果让我在这里遇见你，会不会是个奇迹呢，曾经无数次幻想过未来另一半的样子，朦胧而亲切，找寻至今，我才相信，爱情终究是缘分，茫茫人海两个人的相识相爱，本身就是一种巧合，你会是我的那个巧合吗？",
            "家应该像冰冷冬天里的一杯热茶，让你在疲惫的时候感到温暖，家应该像黑夜里鹅黄的灯光，让你知道心的那头始终有份期盼，两个人在一起，搜集一点一滴的幸福。",
            "每天晚上疲劳地坐到椅子上时，才感觉真真切切地过了一天。人生最重要的并不是努力，而是方向。压力不是有人比你努力，而是比你牛叉几倍的人依然比你努力。即使看不到未来，即使看不到希望，也依然相信，自己错不了，自己选的人错不了，自己选的人生错不了。第二天叫醒我的不是闹钟，其实，还是梦想！"
        ],
        // 消息库
        // 显示消息列表
        showMsgList:[],
        // selectShowFlg
        selectShowFlg : false,
        // onceflg
        onceFlg : false,
        // 消息监听对象
        msgObs:null,
        msgObs2:null,
        msgFlg : true,
        moveBackG:null,
        cckCount : 0,
        sendPhoneFlg :false
        ,sendPhoneInv : null
        ,timeKey : 60
        ,sendTeskKeyFlg : false
        ,postCardFlg : false
        ,testAge:function(cardNo){
            if(!cardNo){
                return false;
            }
            var cardAge = cardNo.length==15?cardNo.substr(6,6):(cardNo.length==18?cardNo.substr(6,8):"");
            if(!cardAge){return false;}
            if(cardAge.length<8){
                var k = parseInt(cardAge.substr(0,2));
                if(k<50){
                    cardAge ="20" + cardAge;
                }else{
                    cardAge ="19" + cardAge;
                }
            }
            cardAge = cardAge.substr(0,4)+"/"+cardAge.substr(4,2)+"/"+cardAge.substr(6);
            var cardDate = new Date(cardAge);
            var nowDate = new Date();
            if(nowDate.getFullYear() - cardDate.getFullYear()>16){
                return true;
            }
            if((nowDate.getFullYear() - cardDate.getFullYear())==16 && (nowDate.getMonth() - cardDate.getMonth())>0){
                return true;
            }
            if((nowDate.getFullYear() - cardDate.getFullYear())==16 && (nowDate.getMonth() - cardDate.getMonth())==0 && (nowDate.getDate() - cardDate.getDate())>0){
                return true;
            }
            return false;
        },


        sendHi : function(userId,e){
            Header.closeSelect();
            var top = e.pageY+40;
            var left = e.pageX-350;
            Header.checkSaidHi(userId,function(checkFlg){
                if(checkFlg){
                    Util.lockWindow(true);
                    $(".saidHiErrDiv").show();
                }else{
                    if(!Header.sayHi(userId,function(sayHiFlg){
                        if(sayHiFlg){
                            Util.lockWindow(true);
                            // 打招呼成功
                            $(".saidHiOkDiv").show();
                        }
                    },top,left)){
                        // 有中断
                        return false;
                    }
                }
            });
        },
        // 打招呼回调
        sayHiCallBack : function(sayHiFlg){
            // 锁屏
            Util.lockWindow(true);
            if(sayHiFlg){
                Util.popUpWin("sayHiOk");
            }else{
                Util.popUpWin("sayHiErr");
            }
        },
        // 检查是否打过招呼
        checkSaidHi : function(userId, callBackF){
            var path = "/msg/say_hi/" + userId + ".html";
            Util.ajax(path,callBackF);
            path = null;
        },
        // 未登录或未注册验证
        showRL : function(top,left){
            if($("#loginDiv")[0]){
                //Util.lockWindow(true);
                if(top && left){
                    $("#loginDiv").css({"top":top,"left":left}).show();
                }else{
                    Util.popUpWin("loginDiv");
                }
                return false;
            }else{
                location.href = "/v10/login.html";
            }
        },
        // 用户登录，信息保存
        login : function(userId,password,openId,from,flg,saveFlg){
            if(Header.loggingFlg){
                return;
            }
            Header.loggingFlg = true;
            var path="/user/login.html";
            var postdata = {"username":userId,"password":password,"openId":openId,"from":from};
            Util.ajax(path,function(data){
                if(data){
                    Header.loginCallBack(data,flg,userId,password,saveFlg);
                }
            },postdata);
        },
        // 登录回调
        loginCallBack : function(data,flg,userId,password,saveFlg){
            if(data.STATUS == 1){
                // 统计用
                $.yyLog($.YYLOGKEY.LOGIN,"A");
                var url = "/v10/searcher.html";
                location.href=url;
                // --------界面转换-------end-------
                // 显示
            }else{
                // 登录失败
                // 用户名不存在
                $("#wrongError").text("请正确输入登录帐号或密码！");
                $("#wrongId").show();
                $("#unLogin").show();
                $("#login_load").hide();
               /* //$.yyLog("LOGIN_ERROR","A");
                // 用户名重复判断
                var path = "/json/username.html";
                $.ajax({
                    url:path,
                    type:"post",
                    dataType:"json",
                    data:{"username":userId},
                    success:function(data){
                        // 添加市列表
                        if(!data){
                            //$.yyLog("USER_NAME_NOT_EXIST","A",userId);
                        }
                    }
                });*/
            }
            Header.loggingFlg = false;
        },

        // 用户退出处理
        logout : function(){
            // 用户退出动作提交
            var path="/user/logout.html";
            Util.ajax(path,Header.logoutCalBack);
            $.cookie(User.userInfo.guid + "_showTodayLove","",{"expires":0,"path":"/"});
        },
        // 退出回调
        logoutCalBack : function(logoutFlg){
            if(logoutFlg){
                try{
                    $.cookie("hadSaidHiDay",false,{"expires":0,"path":"/"});
                }catch(e){}
                // 清除消息监测
                Header.msgObserve(false);
                // 清除用户信息
                User.deleteInfo();
                User.loginStatus = -1;
                // 退出成功
                Util.gotoWin($.getHost()+"/v10/home.html");
                // 界面状态转换
            }else{
                // 退出失败
                return Header.alert("退出失败",1);
                // 提示信息
            }
        }
        // 保存收藏夹
        ,addFavorite:function(){
            var url=location.href;
            var description=document.title;
            try{//IE
                window.external.addFavorite(url,description);
            }catch(e){//FF
                window.sidebar.addPanel(description,url,"");
            }
        }
        // 获取闪缘用户数据
        ,getFlashUser:function(){
            if(User.loginStatus==1 && $("#flashUser")[0]){
                var flashPath = "/json/user/online/1.html";
                Util.ajax(flashPath,function(data){
                    if(data && data.length>0){
                        // 有数据
                        $("#flashUser .showFlashFirst").show();
                        $("#flashUser .showFlashSecond").hide();

                        for(var i=0;i<data.length;i++){
                            var userInfo = data[i];
                            // 添加头像
                            var a = Util.imgUrl+"220/270";
                            if(userInfo.icon){
                                a += userInfo.icon;
                            }else{
                                a = "/resources/css/images/user-menu.jpg";
                            }
                            $("#flashUser .flashIcon").attr("src",a);
                            // 添加昵称
                            $("#flashUser .flashId").html("你好，我是 " + userInfo.nickname.substr(0,7)).attr("title", userInfo.nickname);
                            // 添加资料
                            a = "";
                            if(userInfo.height && userInfo.height!=0){
                                a = "，身高"+userInfo.height+"厘米";
                            }
                            $("#flashUser .flashInfo").html("今年"+userInfo.age+"岁"+a+"，未婚");
                            $("#flashUser .flashInfo2").html("居住在"+userInfo.province+(userInfo.city?"-"+userInfo.city:""));
                            // 征友条件
                            $("#flashUser .flashCon").html("年龄在"+((userInfo.age-3)<18?"18":(userInfo.age-3))+"-"+(userInfo.age+7)+"岁的男性");
                            // 设置打招呼信息
                            $("#flashUser .flashSayHiBtn").attr("data_userId",userInfo.guid);
                        }
                    }else{
                        $("#flashUser .showFlashFirst").hide();
                        $("#flashUser .showFlashSecond").show();
                    }
                });
            }
        }
        // 设置列表
        ,setList : function(objId,list,inputObj,oldC){
            if(list.length > 0){
                $(objId).html('');
                for(var i in list){
                    if(oldC && oldC == list[i].id){
                        $(objId).append('<option selected="true" value="'+list[i].id+'">'+list[i].name+'</option> ');
                        $(inputObj).val(list[i].id);
                    }else{
                        $(objId).append('<option value="'+list[i].id+'">'+list[i].name+'</option> ');
                    }
                }
                $(objId).show();
                $(objId.replace("#",".")).show();
            }else{
                $(objId).attr('<option value="0">请选择</option> ').hide();
                $(inputObj).val(0);
            }
        }
        ,setConList : function(objId,list,inputObj,oldC){
            if(list.length > 0){
                $(objId).html('<option value="0">不限制</option>');
                for(var i in list){
                    if(oldC && oldC == list[i].id){
                        $(objId).append('<option selected="true" value="'+list[i].id+'">'+list[i].name+'</option> ');
                        $(inputObj).val(list[i].id);
                    }else{
                        $(objId).append('<option value="'+list[i].id+'">'+list[i].name+'</option> ');
                    }
                }
                $(objId).show();
                $(objId.replace("#",".")).show();
            }else{
                $(objId).attr('<option value="0" selected="true">不限制</option> ').hide();
                $(inputObj).val(0);
            }
        }
        //  获取当月天数
        ,getDay:function(){
            if(!$("#sel_day")[0]){
                // 没有天数选择
                return;
            }
            // 已选择年月，设定可选择天数
            var y = $("#sel_year").val();
            var m = $("#sel_month").val();
            var d = $("#sel_day").val();
            if(y!=0){
                if(m==0){
                    $("#sel_month").val("01");
                    m=1;
                }
                $("#sel_day").html('<option value="01">1日</option>');
                var dayCount = Util.getMonthDate(y,m);
                for(var i=2; i<= dayCount; i++){
                    if(i != dayCount){
                        if(parseInt(d)==i){
                            i = i+"";
                            $("#sel_day").append('<option selected="true" value="'+(i<10?"0"+i:i)+'">'+i+'日</option> ');
                        }else{
                            i = i+"";
                            $("#sel_day").append('<option value="'+(i<10?"0"+i:i)+'">'+i+'日</option>');
                        }
                    }else{
                        if(parseInt(d)>=i){
                            i = i+"";
                            $("#sel_day").append('<option selected="true" value="'+(i<10?"0"+i:i)+'">'+i+'日</option> ');
                        }else{
                            i = i+"";
                            $("#sel_day").append('<option value="'+(i<10?"0"+i:i)+'">'+i+'日</option>');
                        }
                    }
                }
            }else{
                $("#sel_day").html('<option value="0">请选择日</option> ');
            }
        }
        ,getCity : function(oldC){
            if(!$("#sel_city")[0]){return;}
            var proV = parseInt($("#sel_province")[0]?$("#sel_province").val():$("#sel_prov").val());
            if(!proV){
                return;
            }
            /*if(proV < 5 || proV >31){
             $("#sel_city").val("0").html("请选择城市").hide();
             $("#city").val("0");
             return;
             }*/
            var path = "/json/province/"+proV+".html";
            Util.ajax(path,function(data){
                Header.setList("#sel_city",data,"#city",oldC);
                if($("#sel_area")[0]){
                    Header.getArea($("#sel_area").val());
                }
            });
            proV=path=null;
        }
        // 获取地区
        ,getArea : function(oldA){
            if(!$("#sel_area")[0]){return;}
            var prov = parseInt($("#sel_province")[0]?$("#sel_province").val():$("#sel_prov").val());
            var city = $("#sel_city").val();
            // 获取地区
            var path="/json/city/"+prov+"/"+city+".html";
            Util.ajax(path,function(data){
                Header.setList("#sel_area",data,"#area",oldA);
            });
            prov=city=path=null;
        }
        // 限定to年龄选择范围
        ,setToAge:function(inputObj){
            if(!$("#sel_age_min")[0]){
                // 没有年龄范围
                return;
            }
            var fromAgeKey = parseInt($("#sel_age_min").val());
            var toAgeKey = parseInt($("#sel_age_max").val());
            var html = '<option class="selectList" value="0">不限制</option>';
            if(fromAgeKey == "0"){
                html = '<option class="selectList" value="0" selected="true">不限制</option>';
                html += ' <option class="selectList" value="18">18岁</option> ';
                html += ' <option class="selectList" value="20">20岁</option> ';
                html += ' <option class="selectList" value="23">23岁</option> ';
                html += ' <option class="selectList" value="25">25岁</option> ';
                html += ' <option class="selectList" value="28">28岁</option> ';
                html += ' <option class="selectList" value="30">30岁</option> ';
                html += ' <option class="selectList" value="33">33岁</option> ';
                html += ' <option class="selectList" value="35">35岁</option> ';
                html += ' <option class="selectList" value="38">38岁</option> ';
                html += ' <option class="selectList" value="40">40岁</option> ';
                html += ' <option class="selectList" value="45">45岁</option> ';
                html += ' <option class="selectList" value="50">50岁</option> ';
                html += ' <option class="selectList" value="55">55岁</option> ';
                html += ' <option class="selectList" value="60">60岁</option> ';
                html += ' <option class="selectList" value="65">65岁</option> ';
                html += ' <option class="selectList" value="70">70岁</option> ';
                html += ' <option class="selectList" value="75">75岁</option> ';
                html += ' <option class="selectList" value="80">80岁</option> ';
                $("#sel_age_max").html(html);
                return;
            }

            if(toAgeKey != 0 && toAgeKey < fromAgeKey){
                toAgeKey = fromAgeKey;
            }
            var flg=false;
            while(fromAgeKey <= 80){
                if(fromAgeKey==toAgeKey){
                    html += '<option class="selectList" value="'+fromAgeKey+'" selected="true">'+fromAgeKey+'岁</option> ';
                    flg = true;
                }else{
                    html += '<option class="selectList" value="'+fromAgeKey+'">'+fromAgeKey+'岁</option> ';
                }
                if(fromAgeKey < 40){
                    fromAgeKey += (fromAgeKey%5==0?3:2);
                }else{
                    fromAgeKey += 5;
                }
            }
            if(!flg && toAgeKey!="0"){
                html += '<option class="selectList" value="'+toAgeKey+'" selected="true">'+toAgeKey+'岁</option>';
            }
            $("#sel_age_max").html(html);
            if(inputObj && $("#"+inputObj)[0]){
                $("#"+inputObj).val(toAgeKey);
            }
        }
        // 限定to身高选择范围
        ,setToHeight:function(){
            if(!$("#sel_height_max")[0]){
                // 没有身高范围
                return;
            }
            var fromHeightKey = parseInt($("#sel_height_min").val());
            var toHeightKey = parseInt($("#sel_height_max").val());
            var html = ' <option class="selectList" value="0">不限制</option> ';
            if(fromHeightKey == "0"){
                html = ' <option class="selectList" value="0" selected="true">不限制</option> ';
                html += '     <option class="selectList" value="140">140厘米</option> ';
                html += '     <option class="selectList" value="145">145厘米</option> ';
                html += '     <option class="selectList" value="150">150厘米</option> ';
                html += '     <option class="selectList" value="155">155厘米</option> ';
                html += '     <option class="selectList" value="160">160厘米</option> ';
                html += '     <option class="selectList" value="165">165厘米</option> ';
                html += '     <option class="selectList" value="170">170厘米</option> ';
                html += '     <option class="selectList" value="175">175厘米</option> ';
                html += '     <option class="selectList" value="180">180厘米</option> ';
                html += '     <option class="selectList" value="185">185厘米</option> ';
                html += '     <option class="selectList" value="190">190厘米</option> ';
                html += '     <option class="selectList" value="195">195厘米</option> ';
                html += '     <option class="selectList" value="200">200厘米</option> ';
                $("#sel_height_max").html(html);
                return;
            }
            if(toHeightKey != 0 && toHeightKey < fromHeightKey){
                toHeightKey = fromHeightKey;
            }
            var flg=false;
            while(fromHeightKey <= 200){
                if(fromHeightKey == toHeightKey){
                    html += ' <option class="selectList" value="'+fromHeightKey+'" selected="true">'+fromHeightKey+'厘米</option> ';
                    flg = true;
                }else{
                    html += ' <option class="selectList" value="'+fromHeightKey+'" >'+fromHeightKey+'厘米</option> ';
                }
                fromHeightKey += 5;
            }
            if(!flg && toHeightKey!="0"){
                html += '<option class="selectList" value="'+toHeightKey+'" selected="true">'+toHeightKey+'厘米</option>';
            }
            $("#sel_height_max").html(html);
            if(inputObj && $("#"+inputObj)[0]){
                $("#"+inputObj).val(toHeightKey);
            }
        }
        // 显示验证码弹层
        ,showTest:function(){
            var d = new Date();

            $("#testImg").attr("src","");
            $("#testImg").attr("src","/v21/captcha/image.html?d=" + d.getTime());
            $("#showTestDiv").show();
        }
        ,alert:function(msg,err){
            if(err==1){
                $(".hi_succeed img").hide();
            }else{
                $(".hi_succeed img").show();
            }
            $(".hi_succeed span").html(msg);
            $(".hi_succeed").show();
            setTimeout(function(){
                $(".hi_succeed").hide();
            },1000);
            return false;
        }
        // 公共confirm
        ,confirm:function(){
            var msg='',ok_callBack=null,cancel_back=null,ok_btn_font='确定',cancel_btn_font="取消",callBack=null,argL = arguments.length;
            if(argL!=2 && argL!=3 && argL!=5){return false;}
            msg = arguments[0];
            if(argL==2){
                if(arguments[1]  instanceof Function){
                    ok_callBack = arguments[1];
                }else{
                    return false;
                }
            }else if(argL==3){
                if(arguments[1]  instanceof Function){
                    ok_callBack = arguments[1];
                }else{
                    return false;
                }
                if(arguments[2]  instanceof Function){
                    cancel_back = arguments[2];
                }else{
                    return false;
                }
            }else if(argL==5){
                if(arguments[1]  instanceof Function){
                    ok_callBack = arguments[1];
                }else{
                    return false;
                }
                if(arguments[2]  instanceof Function){
                    cancel_back = arguments[2];
                }else{
                    return false;
                }
                if(typeof arguments[3]=="string"){
                    ok_btn_font = arguments[3];
                }
                if(typeof arguments[4]=="string"){
                    cancel_btn_font = arguments[4];
                }
            }

            var of = ok_btn_font?ok_btn_font:"确定";
            var cf = cancel_btn_font?cancel_btn_font:"取消";
            Util.lockSystemWindow(true);
            msg = msg.replace(/\\n/g,'<br/>');
            $("#confirm .message").html(msg);
            $("#confirm .sure .ok").html(of);
            $("#confirm .sure .cancel").html(cf);
            $("#confirm").show();
            var ok_btn_click = function(){
                Util.lockSystemWindow(false);
                $("#confirm").find(".message").html("").end().hide();
                $("#confirm .close").unbind("click",ok_btn_click);
                return ok_callBack?ok_callBack():false;
            };
            var cancel_btn_click = function(){
                Util.lockSystemWindow(false);
                $("#confirm").find(".message").html("").end().hide();
                $("#confirm .close").unbind("click",cancel_btn_click);
                return cancel_back?cancel_back():false;
            };
            $("#confirm .ok").bind("click",ok_btn_click);
            $("#confirm .close").bind("click",cancel_btn_click);
            return false;
        }
        // 公共prompt
        ,prompt:function(msg,ok_callBack,cancel_back,ok_btn_font,cancel_btn_font){
            var msg='',ok_callBack=null,cancel_back=null,ok_btn_font='确定',cancel_btn_font="取消",callBack=null,argL = arguments.length;
            if(argL!=2 && argL!=3 && argL!=5){return false;}
            msg = arguments[0];
            if(argL==2){
                if(arguments[1]  instanceof Function){
                    ok_callBack = arguments[1];
                }else{
                    return false;
                }
            }else if(argL==3){
                if(arguments[1]  instanceof Function){
                    ok_callBack = arguments[1];
                }else{
                    return false;
                }
                if(arguments[2]  instanceof Function){
                    cancel_back = arguments[2];
                }else{
                    return false;
                }
            }else if(argL==5){
                if(arguments[1]  instanceof Function){
                    ok_callBack = arguments[1];
                }else{
                    return false;
                }
                if(arguments[2]  instanceof Function){
                    cancel_back = arguments[2];
                }else{
                    return false;
                }
                if(typeof arguments[3]=="string"){
                    ok_btn_font = arguments[3];
                }
                if(typeof arguments[4]=="string"){
                    cancel_btn_font = arguments[4];
                }
            }

            var of = ok_btn_font?ok_btn_font:"确定";
            var cf = cancel_btn_font?cancel_btn_font:"取消";
            Util.lockSystemWindow(true);
            msg = msg.replace(/\\n/g,'<br/>');
            $("#prompt .message").html(msg);
            $("#prompt .sure .ok").html(of);
            $("#prompt .sure .cancel").html(cf);
            $("#prompt").show();
            var ok_btn_click = function(){
                var key = $("#promptMsg").val();
                $("#promptMsg").val("");
                Util.lockSystemWindow(false);
                $("#prompt").find(".message").html("").end().hide();
                $("#prompt .close").unbind("click",ok_btn_click);
                return ok_callBack?ok_callBack(key):false;
            };
            var cancel_btn_click = function(){
                Util.lockSystemWindow(false);
                $("#prompt").find(".message").html("").end().hide();
                $("#prompt .close").unbind("click",cancel_btn_click);
                return cancel_back?cancel_back():false;
            };
            $("#prompt .ok").bind("click",ok_btn_click);
            $("#prompt .close").bind("click",cancel_btn_click);
            return false;
        }
    };
})();
$(function(){
    $("#loginSubmit,.loginBtn_pop").click(function(){
        var userId = $("#loginUserId").val();
        var pass = $("#loginPass").val();
        var openId = $("#openId").val();
        var from = $("#from").val();
        var flg = $(this).attr("data_jump_flg");
        $("#unLogin").hide();
        $("#login_load").show();
        if(!userId || !pass){
            $("#wrongError").text("请正确输入登录帐号或密码！");
            $("#wrongId").show();
            return;
        }
        var saveFlg = false;
        if(!$("#savePass").hasClass("small_box_out")){
            saveFlg = true;
        }
        // 登录
        var userInfo = Header.login(userId,pass,openId,from,flg,saveFlg);
    });
    // login处理，密码栏回车登录处理
    $("#loginUserId,#loginPass").keypress(function(event){
        var key = 0;
        if(event.keyCode){
            key = event.keyCode;
        }else{
            key = event.which;
        }
        if(key == 13){
            if($(".loginBtn_pop")[0]){
                $(".loginBtn_pop").click();
            }else if($("#loginSubmit")[0]){
                $("#loginSubmit").click();
            }
        }
    });
    $(".loginBtn").click(function(e){
        if(User.loginStatus!=1){
            var top = e.pageY+10;
            var left = e.pageX-300;
            Header.showRL(top,left);
            return Header.alert("123");
        }
    });

    $("#loginUserId").focus(function(){
        if($(this).val()=='手机号/邮箱'){
            $(this).val('');
            $(this).addClass("input_out");
        }
    }).blur(function(){
            if($(this).val().length<1){
                $(this).val('手机号/邮箱');
                $(this).removeClass("input_out");
            }

        });
    $("#loginPassText").focus(function(){
        $(this).hide();
        $("#loginPass").show();
        $("#loginPass").addClass("input_out");
    }) .blur(function(){
            if( $(this).val().length<3){
                $(this).hide();
                $("#loginPass").show();
            }
        });
    $("#loginPass").focus(function(){
        $(this).val('');
    }) .blur(function(){
        if( $(this).val().length<3){
           $(this).hide();
           $("#loginPassText").show();
        }
    });
    $(".popupMailTip .closed").click(function(){
        $(".popupMailTip").slideUp("fast");
    });
    // 登录弹窗关闭按钮
    $("#popupCloseBtn").live("click",function(){
        // 清空内容
        //$("#loginUserId,#loginPass").val("");
        // 关闭登录框
        $(this).parent().parent().hide();
        // 收起锁屏
        Util.lockWindow(false);
        // 调整首部按钮状态
        $("#btnLogin").removeClass("headerImgClick").addClass("headerImg");
    });
    // 退出下拉列表
    $("#btnLogout").click(function(){
        Util.lockWindow(true);
        $("#logoutDiv").show();
    });




    try{
        if(User.loginStatus==1 && User.userInfo.tempUser){
            // 临时用户
            $(".showSetUserNameDiv").click(function(){
                //$.yyLog("SHOW_SIMPLE_INTERCEPT","A");
                Util.lockWindow(true);
                $("#setUserNameDiv").show();
                return false;
            });
            $("#simpleUserName").focus(function(){
                    if($(this).val() == "请输入正确的手机号/邮箱"){
                        $(this).val("");
                    }
                }).blur(function(){
                    if($.trim($(this).val())==""){
                        $(this).val("请输入正确的手机号/邮箱");
                    }
                    if(Util.isMobil($(this).val()) || Util.testMail($(this).val())){
                        var v = $(this).val();
                        // 用户名重复判断
                        var path = "/json/username.html";
                        $.ajax({
                            url:path,
                            type:"post",
                            dataType:"json",
                            data : {"username":v},
                            success:function(data){
                                // 添加市列表
                                if(data){
                                    $(".userErrorMsg").html("此手机号已经注册！").show();
                                    Mail.registerFlg = true;
                                }else{
                                    $("#errorMsg").html("").hide();
                                    v = v.substr(v.length-4);
                                    $("#simpleUserPass").val(v);
                                    $(".userErrorMsg").html("").hide();
                                }
                            }
                        });
                    }else{
                        $(".userErrorMsg").html("*请正确输入手机号").show();
                    }
                });
            $("#simpleUserPass").focus(function(){
                if($(this).val() == "3～10位数字或字母的组合"){
                    $(this).val("");
                }
            }).blur(function(){
                    if($.trim($(this).val())==""){
                        $(this).val("3～10位数字或字母的组合");
                    }
                }).keypress(function(e){
                    // 只能输入半角英文字母和数字
                    var a = $(this).val();
                    a = a.replace(/[^A-Za-z0-9]/ig,'');
                    $(this).val(a);
                });
            $("#userSubmit").click(function(){
                if(!Util.isMobil($("#simpleUserName").val()) && !Util.testMail($("#simpleUserName").val())){
                    $(".userErrorMsg").html("*请正确输入手机号").show();
                    return;
                }
                /*var p = $.trim($("#simpleUserPass").val());
                p = p.replace(/[^A-Za-z0-9]/ig,'');
                if(p=="" || p.length<3 || p.length>10){
                    $(".userErrorMsg").html("*请正确输入密码").show();
                    return;
                }*/
                // 用户名重复判断
                var path = "/json/username.html";
                $.ajax({
                    url:path,
                    type:"post",
                    dataType:"json",
                    data : {"username":$("#simpleUserName").val()},
                    success:function(data){
                        // 添加市列表
                        if(data){
                            $(".userErrorMsg").html("此手机号已经注册！").show();
                            Mail.registerFlg = true;
                        }else{
                            path = "/v21/un_reset.html";
                            Util.ajax(path,function(flg){
                                if(flg && flg=="1"){
                                    var userInfo = Util.getUserInfo();
                                    // 保存用户信息
                                    User.saveInfo(userInfo);
                                    //$.yyLog("SET_USER_NAME","A");
                                    Header.alert("绑定成功！",null,function(){
                                        Util.lockWindow(false);
                                        $("#setUserNameDiv").hide();
                                        location.reload();
                                    });
                                }
                            },{"username":$("#simpleUserName").val(),"password":p});
                        }
                    }
                });
            });
            if(User.userInfo.goldenUser){
                $("#setTelDiv").show();
            }
        }
    }catch(e){}
    $("#newPopMsg").click(function(){
        Util.ajax("/msg/close_pop.html",function(){});
    });
    $("#newPopMsg .msgInfo").click(function(){
        var h = $(this).find("a").attr("href");
        setTimeout(function(){location.href = h;},50);
    });
   //  setTimeout(function(){$.cookie("cckShow","false",{"expire":0.5,"path":"/"});},1000);


    /*层提示点击链接*/
    $(".popupMailTip .tan_mailinfo").click(function(){
        location.href = "/v10/msg/mail.html";
    });

    $("#normalMsgBtn").click(function(){
        // 普通打招呼
        Header.sayHiNext(Header.sayHiInfo.userArr,Header.sayHiInfo.callBackF,Header.sayHiInfo.t,Header.sayHiInfo.l);
        Util.lockWindow(false);
        $("#customMsgDiv").hide();
    });
    $("#saveCustomMsgBtn").click(function(){
        // 保存自定义招呼
        var msg = $("#customMsg").val();
        if(!msg){
            return Header.alert("请正确填写自定义招呼内容！");
        }
        Util.ajax("/v21/msg/add_hello_template.html",function(flg){
            if(flg==1){
                //$.yyLog("SAVE_CUSTOM_MSG_OK","A");
                $("#customMsgFlg").html("true");
                // 打招呼
                Header.sayHiNext(Header.sayHiInfo.userArr,Header.sayHiInfo.callBackF,Header.sayHiInfo.t,Header.sayHiInfo.l);
            }
        },{"text":msg});
        Util.lockWindow(false);
        $("#customMsgDiv").hide();
    });
    $("#customMsg").keydown(function(){
        var len = $(this).val().length;
        if(len>100){
            $(this).val($(this).val().substr(0,100));
        }
    });
    $("#autoMailSubmit").one("click",function(){
        /* 保存收信宝 */
        Util.ajax("/v21/customer/add_shouxinbao.html",function(flg){
            if(flg==1){
                $("#autoMailDiv").hide();
                $("#autoMailOKDiv").show();
                $("#showAutoMail").hide();
                //$.yyLog("AUTO_MAIL_OK","A");
            }else{
                return Header.alert("现在服务器忙，请稍候再获取！");
            }
        });
    });
    var showAutoFlg = true;
    $("#showAutoMail").click(function(){
        if(showAutoFlg){
            Util.lockWindow(true);
            $("#autoMailDiv").show();
            return false;
        }
    });
    $("#showAutoMail a").click(function(){
        showAutoFlg = false;
    });
    // 关闭select
    $(".blockUI").live("click",function(){
        if(Header.closeControlFlg){
            return;
        }
        // 关闭列表
        if(Header.selectShowFlg){
            Header.closeSelect();
        }
        // 关闭条件选择列表
        if($(".searchDiv")[0] && !Header.onceFlg){
            $(".searchDiv").hide();
        }
        Header.onceFlg = false;
        // 关闭设置列表
        if($(".userSet span").hasClass("spanH")){
            $(".userSet span").toggleClass("spanH");
            $(".userSetList").hide();
        }
        Util.lockWindow(false);
        /*$(".cation").hide();*/
        $(".popup").hide();
    });
    $("body").click(function(){
        if(Header.closeControlFlg){
            return;
        }
        // 关闭列表
        if(Header.selectShowFlg){
            Header.closeSelect();
        }
        // 关闭条件选择列表
        if($(".searchDiv")[0] && !Header.onceFlg){
            $(".searchDiv").hide();
        }
        Header.onceFlg = false;
        // 关闭设置列表
        if($(".userSet span").hasClass("spanH")){
            $(".userSet span").toggleClass("spanH");
            $(".userSetList").hide();
        }
        if(!$(".blockUI")[0] || $(".blockUI").css("display")=="none"){
            $(".popup").hide();
        }
    });
    $(".cation,.popup").live("click",function(){
        if(!Header.closeControlFlg){
            return false;
        }
    });
    $("#changeImg").click(function(){
        Header.showTest();
    });
    // 验证码
    $("#saveTestCode").click(function(){
        var code = $("#testCode").val();
        Util.ajax("/v21/captcha/validate.html",function(flg){
            if(flg==1){
                $("#showTestDiv #errorMsg").hide();
                $("#testCode").val("");
                Util.lockWindow(false);
                $("#showTestDiv").hide();
                //$.yyLog("YANZHENG_OK","A");
                setTimeout(function(){
                    location.reload();
                },100);
            }else{
                $("#showTestDiv #errorMsg").show();
            }
        },{"code":code});
    });
    try{
        if($(window).height()<560){
            $(".box").addClass("box_small");
        }
    }catch(e){}
    try{
        // 大版左侧菜单控制
        var w_h = $(document).height();
        var w_w = $(document).width();
        $("#main").height(w_h-34);
        var mr_h = $(".main_right").height();
        var pageId = $("#pageId").html();
        if(!Header.isIE6){
            if(mr_h<w_h-251 && pageId!="message"){
                $(".main_right").css("min-height",w_h-251);
            }
        }else{
            if(mr_h<w_h-251 && pageId!="message"){
                $(".main_right").css("height",w_h-251);
            }
            /*setTimeout(function(){
                $(".top").css("width",w_w-34);
            },100);*/
        }

        $(window).scroll(function(event){
            var w_t = $(document).scrollTop();
            var m_h = $(".main_right").height();
            var screenH = $(window).height();
            if(!Header.isIE6){
                $("#mainMenu").css({"top":(5+w_t)});
            }else{
                $("#mainMenu").css({"top":(5+w_t)});
                $(".top").css({"top":w_t,"position":"absolute"});
                $("body>.popup").each(function(){
                    var h2= $(this).height();
                    $(this).css("top",w_t+screenH/2-h2/2);
                });
            }
            if((m_h+225)<screenH){
                $("#main").height(screenH-34);
            }else{
                $("#main").height(m_h+237);
            }
        });
    }catch(e){}

    // 公共身份验证，手机验证
    try{
        if($("#telStep1Div")[0]){
            // 手机验证
            $("#approveTel").click(function(){
                if(!$(this).hasClass("gain")){
                    return;
                }
                $("#msg_line").hide();
                var v = $.trim($("#phoneNumber").val());
                if(v == ""){
                    $("#msg_line>font").html("电话号码不能为空!");
                    $("#reApproveTel").attr("href","###").attr("data-flg","false").html("").hide();
                    $("#msg_line").show();
                    return;
                }
                if(v == $("#telNum").val()){
                    $("#msg_line>font").html("提示：你已经验证此手机号，无需重复验证。");
                    $("#reApproveTel").attr("href","/v10/space/").attr("data-flg","false").html("返回空间").show();
                    $("#msg_line").show();
                    return;
                }
                if(!Util.isMobil(v)){
                    $("#msg_line>font").html("请输入正确有效的电话号码!");
                    $("#reApproveTel").attr("href","###").attr("data-flg","false").html("").hide();
                    $("#msg_line").show();
                    return;
                }
                if(!Header.sendPhoneFlg){
                    Header.sendPhoneFlg = true;
                    Util.ajax("/info/mobile_check_input.html",function(data){
                        if(data==1){
                            $("#msg_line").hide();
                        }else if(data == -2){
                            $("#msg_line>font").html("内容有误!");
                            $("#reApproveTel").attr("href","###").attr("data-flg","false").html("").hide();
                            $("#msg_line").show();
                            return;
                        }else if(data==-4){
                            // 已验证
                            $("#msg_line>font").html("提示：此手机号已经被验证！");
                            $("#reApproveTel").attr("href","###").attr("data-flg","true").html("找回绑定手机").show();
                            $("#msg_line").show();
                        }else if(data==-7){
                            $("#msg_line>font").html("提示：你已经验证此手机号，无需重复验证。");
                            $("#reApproveTel").attr("href","/v10/space/").attr("data-flg","false").html("返回空间").show();
                            $("#msg_line").show();
                            return;
                        }else if(data==-6){
                            // 黑名单
                            $("#msg_line>font").html("提示：此手机号无法被验证！");
                            $("#reApproveTel").attr("href","###").attr("data-flg","false").html("").hide();
                            $("#msg_line").show();
                            return;
                        }
                        if(data==1){
                            setTimeout(function(){
                                Util.ajax("/info/mobile_auth_input.html",function(data){
                                    if(data == -99){
                                        $("#msg_line>font").html("内容有误!");
                                        $("#reApproveTel").attr("href","###").attr("data-flg","false").html("").hide();
                                        $("#msg_line").show();
                                    }else if(data == -2){
                                        $("#msg_line>font").html("内容有误!");
                                        $("#reApproveTel").attr("href","###").attr("data-flg","false").html("").hide();
                                        $("#msg_line").show();
                                    }else if(data == -4){
                                        $("#msg_line>font").html("提示：此手机号已经被验证！");
                                        $("#reApproveTel").attr("href","###").attr("data-flg","true").html("找回绑定手机").show();
                                        $("#msg_line").show();
                                    }else if(data == -6){
                                        $("#msg_line>font").html("提示：此手机号无法被验证！");
                                        $("#reApproveTel").attr("href","###").attr("data-flg","false").html("").hide();
                                        $("#msg_line").show();
                                    }else if(data==-7){
                                        $("#msg_line>font").html("提示：你已经验证此手机号，无需重复验证。");
                                        $("#reApproveTel").attr("href","/v10/space/").attr("data-flg","false").html("返回空间").show();
                                        $("#msg_line").show();
                                    }else if(data == 1){
                                        // 显示验证码部分
                                        $("#msg_line>font").html("已发送验证码到您的手机，请查收短信。");
                                        $("#approveTel").html("60秒后再次获取").removeClass("gain");
                                        clearInterval(Header.sendPhoneInv);
                                        Header.timeKey = 60;
                                        Header.sendPhoneInv = setInterval(function(){
                                            $("#approveTel").html(Header.timeKey--+"秒后再次获取");
                                        },1000);
                                        setTimeout(function(){
                                            clearInterval(Header.sendPhoneInv);
                                            Header.timeKey = 60;
                                            $("#approveTel").html("免费获取验证码").addClass("gain");
                                        },60000);
                                        $("#code").attr("disabled",false);
                                        $("#postKey").addClass("a_out");
                                    }
                                    //$.yyLog("SEND_NOTE","A",data);
                                    Header.sendPhoneFlg = false;
                                },{"phoneNumber":v});
                            },200);
                        }else{
                            Header.sendPhoneFlg = false;
                        }
                    },{"phoneNumber":v});

                    setTimeout(function(){
                        Header.sendPhoneFlg = false;
                    },5000);
                }
            });
            $("#reApproveTel").click(function(){
                if($(this).attr("data-flg")!="true"){
                    return;
                }
                var v = $.trim($("#phoneNumber").val());
                if(v == ""){
                    $("#msg_line>font").html("电话号码不能为空!");
                    $("#reApproveTel").attr("href","###").attr("data-flg","false").html("").hide();
                    $("#msg_line").show();
                    return;
                }
                if(v == $("#telNum").val()){
                    $("#msg_line>font").html("提示：你已经验证此手机号，无需重复验证。");
                    $("#reApproveTel").attr("href","/v10/space/").attr("data-flg","false").html("返回空间").show();
                    $("#msg_line").show();
                    return;
                }
                if(!Util.isMobil(v)){
                    $("#msg_line>font").html("请输入正确有效的电话号码!");
                    $("#reApproveTel").attr("href","###").attr("data-flg","false").html("").hide();
                    $("#msg_line").show();
                    return;
                }
                if(!Header.sendPhoneFlg){
                    Header.sendPhoneFlg = true;
                    $("#reApproveTel").hide();
                    Util.ajax("/info/mobile_auth_input.html",function(data){
                        if(data == -99){
                            $("#msg_line>font").html("内容有误!");
                            $("#reApproveTel").attr("href","###").attr("data-flg","false").html("").hide();
                            $("#msg_line").show();
                        }else if(data == -2){
                            $("#msg_line>font").html("内容有误!");
                            $("#reApproveTel").attr("href","###").attr("data-flg","false").html("").hide();
                            $("#msg_line").show();
                        }else if(data == -4){
                            /* 超过当天验证次数 */
                            $("#msg_line>font").html("提示：已超过当天验证次数，请明天再试！");
                            $("#reApproveTel").attr("href","###").attr("data-flg","false").html("").hide();
                            $("#msg_line").show();
                        }else if(data == -6){
                            $("#msg_line>font").html("提示：此手机号无法被验证！");
                            $("#reApproveTel").attr("href","###").attr("data-flg","false").html("").hide();
                            $("#msg_line").show();
                        }else if(data==-7){
                            $("#msg_line>font").html("提示：你已经验证此手机号，无需重复验证。");
                            $("#reApproveTel").attr("href","/v10/space/").attr("data-flg","false").html("返回空间").show();
                            $("#msg_line").show();
                        }else if(data == 1){
                            // 显示验证码部分
                            $("#msg_line>font").html("已发送验证码到您的手机，请查收短信。");
                            $("#approveTel").html("60秒后再次获取").removeClass("gain");
                            clearInterval(Header.sendPhoneInv);
                            Header.timeKey = 60;
                            Header.sendPhoneInv = setInterval(function(){
                                $("#approveTel").html(Header.timeKey--+"秒后再次获取");
                            },1000);
                            setTimeout(function(){
                                clearInterval(Header.sendPhoneInv);
                                Header.timeKey = 60;
                                $("#approveTel").html("免费获取验证码").addClass("gain");
                                $("#msg_line>font").html("没有收到短信验证码？重新");
                                $("#reApproveTel").show();
                            },60000);
                            $("#code").attr("disabled",false);
                            $("#postKey").addClass("a_out");
                        }
                        //$.yyLog("SEND_NOTE","A",data);
                        //$("#reApproveTel").show();
                        Header.sendPhoneFlg = false;
                    },{"phoneNumber":$("#phoneNumber").val()});
                    setTimeout(function(){
                        Header.sendPhoneFlg = false;
                    },4000);
                }
            });
            $("#postKey").click(function(){
                if(!$(this).hasClass("a_out")){
                    return;
                }
                if(!Header.sendTeskKeyFlg){
                    Header.sendTeskKeyFlg = true;
                    setTimeout(function(){
                        Header.sendTeskKeyFlg = false;
                        $("#telStep1Div .loadingD").hide();
                    },30000);
                    $("#telStep1Div .loadingD").show();
                    $(".codeMsg").html("").hide();
                    Util.ajax("/info/mobile_auth_confirm.html",function(data){
                        if(data == -99){
                            $(".codeMsg").html("验证码错误！").show();
                        }else if(data == -2){
                            $(".codeMsg").html("验证失败！").show();
                        }else if(data == -4){
                            $("#msg_line>font").html("提示：此手机号已经被验证！");
                            $("#reApproveTel").attr("href","###").attr("data-flg","true").html("找回绑定手机").show();
                            $("#msg_line").show();
                        }else if(data == -6){
                            $("#msg_line>font").html("提示：此手机号无法被验证！");
                            $("#reApproveTel").attr("href","###").attr("data-flg","false").html("").hide();
                            $("#msg_line").show();
                        }else if(data==-7){
                            $("#msg_line>font").html("提示：你已经验证此手机号，无需重复验证。");
                            $("#reApproveTel").attr("href","/v10/space/").attr("data-flg","false").html("返回空间").show();
                            $("#msg_line").show();
                        }else if(data == 1){
                            var userInfo = Util.getUserInfo();
                            // 保存用户信息
                            User.saveInfo(userInfo);
                            //$.yyLog($.YYLOGKEY.UPLOAD_APPROVETEL,"A");
                            var path="/msg/pre_send/info/"+$("#userId").html()+".html";
                            Util.ajax(path,function(flg){});
                            $("#telStep1Div").hide();
                            $("#telOKDiv").show();

                            // 页面样式变化
                            location.reload();
                        }else{
                            $(".codeMsg").html("验证码错误！").show();
                        }
                        $("#telStep1Div .loadingD").hide();

                    },{"code":$("#code").val()});
                }else if(Header.sendTeskKeyFlg == "OK"){
                    $(".codeMsg").html("您已验证通过！").show();
                    var path="/msg/pre_send/info/"+$("#userId").html()+".html";
                    Util.ajax(path,function(flg){});
                    $("#telStep1Div").hide();
                    $("#telOKDiv").show();
                    setTimeout(location.reload,2000);
                }else{
                    $(".codeMsg").html("30秒内只能验证一次！").show();
                    $("#telStep1Div .loadingD").hide();
                }
            });
            $("#code").focus(function(){
                if($(this).val() == "输入您收到的验证码"){
                    $(this).val("");
                }
            });
            $("#phoneNumber").keypress(function(){
                var v = $(this).val();
                if(Util.isMobil(v)){
                    if(v == $("#telNum").val()){
                        $("#msg_line>font").html("提示：你已经验证此手机号，无需重复验证。");
                        $("#reApproveTel").attr("href","/v10/space/").attr("data-flg","false").html("返回空间").show();
                        $("#msg_line").show();
                        return;
                    }
                    Util.ajax("/info/mobile_check_input.html",function(data){
                        if(data==1){

                        }else if(data == -2){
                            $("#msg_line>font").html("内容有误!");
                            $("#reApproveTel").attr("href","###").attr("data-flg","false").html("").hide();
                            $("#msg_line").show();

                        }else if(data==-4){
                            // 已验证
                            $("#msg_line>font").html("提示：此手机号已经被验证！");
                            $("#reApproveTel").attr("href","###").attr("data-flg","true").html("找回绑定手机").show();
                            $("#msg_line").show();
                        }else if(data==-7){
                            $("#msg_line>font").html("提示：你已经验证此手机号，无需重复验证。");
                            $("#reApproveTel").attr("href","/v10/space/").attr("data-flg","false").html("返回空间").show();
                            $("#msg_line").show();

                        }else if(data==-6){
                            // 黑名单
                            $("#msg_line>font").html("提示：此手机号无法被验证！");
                            $("#reApproveTel").attr("href","###").attr("data-flg","false").html("").hide();
                            $("#msg_line").show();

                        }
                    },{"phoneNumber":v});
                }else{

                }
            });

            // 手机验证 -- 不要验证码
            $("#postKey2").click(function(){
                var v = $.trim($("#phoneNumber2").val());
                if(v == ""){
                    return Header.alert("电话号码不能为空!");
                }
                if(!Util.isMobil(v)){
                    return Header.alert("请输入正确有效的电话号码!");
                }
                if(!Header.sendTeskKeyFlg){
                    Header.sendTeskKeyFlg = true;

                    $("#telStep2Div .codeMsg").html("").hide();
                    Util.ajax("/info/mobile_auth_temp.html",function(data){
                        if(data == 1){
                            $("#tel2OKDiv .tel").html($("#phoneNumber2").val());
                            $("#telStep2Div").hide();
                            $("#tel2OKDiv").show();
                        }else if(data == -4){
                            $("#telStep2Div .codeMsg").html("此手机号已经被验证！").show();
                            //alert("此手机号已经被验证，如有疑问请联系客服！");
                            Header.sendTeskKeyFlg = false;
                        }else{
                            $("#telStep2Div .codeMsg").html("提交失败，请稍候再试！").show();
                            Header.sendTeskKeyFlg = false;
                        }
                    },{"phoneNumber":v});
                }

            });
            // 弹层显示
            $(".showTelPopup").click(function(){
                /*if($("#telStep1Div")[0]){
                 Util.lockWindow(true);
                 $("#telStep1Div").show();
                 return false;
                 }*/
                if($("#telStep1Div")[0]){
                    $(".popup").hide();
                    Util.lockWindow(true);
                    if(!Header.sendTeskKeyFlg){
                        $("#telStep1Div").show();
                    }else{
                        $("#telOKDiv").show();
                    }
                    return false;
                }
            });
        }
        if($("#ideDiv")[0]){
            // 身份验证
            $("#postCardNormal").click(function(){
                if(Header.postCardFlg){
                    return Header.alert("您已提交，正在核查！您先去寻找有缘人吧！");
                }
                var c = $("#normal_code").val();
                var n = $("#normal_name").val();
                if(!c || !n){
                    return Header.alert("姓名与身份证号不能为空！");
                }
                var patrnF =/[^\x00-\xff]/;
                if(patrnF.test(c)){
                    return Header.alert("身份证号应是半角数字或字母，请更改输入法，采用半角模式输入！");
                }
                if(c.length !=15 && c.length !=18){
                    return Header.alert("您的身份证号位数不对，请核查后再提交！");
                }
                if(!Header.testAge(c)){
                    return Header.alert("您的身份证不能验证！");
                }
                /*if(!window.confirm("请核对你的提交信息：\n姓名:"+n+"\n身份证号:"+c)){
                    return;
                }*/
                // 回调
                Header.postCardFlg = true;
                return Header.confirm("请核对你的提交信息：\n姓名:"+n+"\n身份证号:"+c,function(){
                    var options = {
                        success : function(data) {
                            Header.postCardFlg = false;
                            if(data == 1){
                                Header.alert("身份验证通过！",null,function(){
                                    location.reload();
                                });
                                /*// 获取用户完善度，确定跳转方向
                                 var userInfo = Util.getUserInfo();
                                 // 保存用户信息
                                 User.saveInfo(userInfo);*/
                                /*if(Header.backUrl != ""){
                                 Util.gotoWin(Header.backUrl);
                                 }else{
                                 // 跳转到成功界面
                                 Util.gotoWin($.getHost() + "/v21/" +"info/succeed.html");
                                 }*/
                                // 显示验证码部分
                            }else if(data==-5){
                                return Header.alert("身份验证不能连续提交，请稍后再试！")
                            }else if(data==-1 || data==-2){
                                return Header.alert("您填写的信息有误，请仔细检查！");
                            }else if(data==-3){
                                return Header.alert("帐号异常，请刷新页面再试！");
                            }else if(data==-4){
                                return Header.alert("抱歉,此身份证号已经被重复验证！\n若你提交的是你的真实身份证信息,请拨打我们的客服电话为你完成验证!\n客服电话:010-58103520（工作时间：9:00—21:00）");
                            }
                        },
                        error:function(data){
                            Header.postCardFlg = false;
                            return Header.alert("您的资料与身份证信息不符，验证失败！");
                        }
                    };
                    $("#testCardNormal").ajaxSubmit(options);

                },function(){
                    Header.postCardFlg = false;
                });
            });
            $(".showStep4").click(function(){
                $(".step4").show();
                $(".step4_normal,.step4_army").hide();
            });
            $("#approveIDC1").click(function(){
                var flg = false,msg="";
                if(User.userInfo.infoLevel <80){
                    /*alert("请先完善您的个人资料，完善度达至少到80%后！");*/
                    msg += "\n完善您的个人资料，完善度达至少到80%！";
                    flg = true;
                    return;
                }
                if(User.userInfo.photoCount <3){
                    /*alert("请至少上传3张照片，并等待验证通过后，再申请身份验证！");*/
                    msg += "\n至少上传3张照片，并验证通过！";
                    flg = true;
                    return;
                }
                if(!$(".mobileOk")[0]){
                    /*alert("请先通过手机验证后，再申请身份验证！");*/
                    msg += "\n通过手机验证！";
                    flg = true;
                    /*return;*/
                }
                if(flg){
                    return Header.alert("您要进行身份验证，需满足一下条件：" + msg);
                }
                $(".step4_normal").show();
                $("#normal_name,#normal_code").val("");
                $(".step4,.step4_army").hide();
            });
            $("#approveIDC2").click(function(){
                $(".step4_normal").show();
                $("#normal_name,#normal_name").val("");
                $(".step4,.step4_army").hide();
            });
            // 弹层显示
            $(".showIdePopup").live("click",function(){
                if($("#ideDiv")[0]){
                    $(".popup").hide();
                    Util.lockWindow(true);
                    $("#ideDiv").show();
                    return false;
                }
            });

        }
    }catch(e){}

    // 照片信息提示动画效果
    try{
        if($("#animate_user_list,.animate_user_list")[0]){
            var enterObj = null,leaveObj = null;
            $("#animate_user_list li,.animate_user_list li").mouseenter(function(){
                var h = $(this).height();
                var m_this = this;
                if(enterObj){
                    clearTimeout(enterObj);
                }

                enterObj = setTimeout(function(){
                    /*$(m_this).find(".home_user_icon").animate({"width":0},function(){
                     $(this).hide();
                     $(m_this).find(".home_user_info").css("width",0).show().animate({"width":w});
                     });*/
                    $(m_this).find(".animate_user_info").css("bottom",-1*h).show().animate({"bottom":0});
                    $(m_this).find(".animate_user_blockUI").css("opacity",0).show().animate({"opacity":0.5});
                    enterObj = null;
                },100);
            }).mouseleave(function(){
                    var h = $(this).height();
                    /*var m_this = this;
                     if(leaveObj){
                     clearTimeout(leaveObj);
                     }
                     leaveObj = setTimeout(function(){
                     $(m_this).find(".home_user_info").animate({"width":0},function(){
                     $(this).hide();
                     $(m_this).find(".home_user_icon").css("width",0).show().animate({"width":w});
                     });*/
                    $(this).find(".animate_user_info").stop().animate({"bottom":-1*h},function(){$(this).hide();});
                    $(this).find(".animate_user_blockUI").stop().animate({"opacity":0},function(){$(this).hide();});
                    leaveObj = null;
                    /*},100);*/
                });
        }
    }catch(e){}

});
// 基于jQuery的上下无缝滚动应用(单行或多行)
$(function(){
    setTimeout(function(){
        $.yyLog("N","V","N");
    },20);
    $('.cityusers').hover(function(){
        clearTimeout(Header._moving);//当鼠标在滚动区域中时,停止滚动
        Header._moving = null;
        Header._movingFlg = false;
    }).mouseleave(function(){
            Header._movingFlg = true;
            Header.loopList();
        }).trigger('mouseleave');//函数载入时,模拟执行mouseleave,即自动滚动
});
var fireworks = function(){
    this.size = 20;
    this.rise();
}
fireworks.prototype = {
    color:function(){
        var c = ['0','3','6','9','c','f'];
        var t = [c[Math.floor(Math.random()*100)%6],'0','f'];
        t.sort(function(){return Math.random()>0.5?-1:1;});
        return '#'+t.join('');
    },
    aheight:function(){
        var h = document.documentElement.clientHeight-250;
        return Math.abs(Math.floor(Math.random()*h-200))+201;
    },
    firecracker:function(){
        var b = document.createElement('div');
        var w = document.documentElement.clientWidth;
        b.style.position = 'absolute';
        b.style.color = this.color();
        b.style.bottom = 0;
        b.style.left = Math.floor(Math.random()*w)+1+'px';
        document.body.appendChild(b);
        return b;
    },
    rise:function(){
        var o = this.firecracker();
        var n = this.aheight();
        var c = this.color;
        var e = this.expl;
        var s = this.size;
        var k = n;
        var m = function(){
            o.style.bottom = parseFloat(o.style.bottom)+k*0.1+'px';
            k-=k*0.1;
            if(k<2){
                clearInterval(clear);
                e(o,n,s,c);
            }
        }
        o.innerHTML = '.';
        if(parseInt(o.style.bottom)<n){
            var clear = setInterval(m,20);
        }
    },
    expl:function(o,n,s,c){
        var R=n/3,Ri=n/6,Rii=n/9;
        var r=0,ri=0,rii=0;
        for(var i=0;i<s;i++){
            var span = document.createElement('span');
            var p = document.createElement('i');
            var a = document.createElement('a');
            span.style.position = 'absolute';
            span.style.fontSize = n/10+'px';
            span.style.left = 0;
            span.style.top = 0;
            span.innerHTML = '★';
            p.style.position = 'absolute';
            p.style.left = 0;
            p.style.top = 0;
            p.innerHTML = '★';
            a.style.position = 'absolute';
            a.style.left = 0;
            a.style.top = 0;
            a.innerHTML = '★';
            o.appendChild(span);
            o.appendChild(p);
            o.appendChild(a);
        }
        function spr(){
            r += R*0.1;
            ri+= Ri*0.06;
            rii+= Rii*0.06;
            sp = o.getElementsByTagName('span');
            p = o.getElementsByTagName('i');
            a = o.getElementsByTagName('a');
            for(var i=0; i<sp.length;i++){
                sp[i].style.color = c();
                p[i].style.color = c();
                a[i].style.color = c();
                sp[i].style.left=r*Math.cos(360/s*i)+'px';
                sp[i].style.top=r*Math.sin(360/s*i)+'px';
                sp[i].style.fontSize=parseFloat(sp[i].style.fontSize)*0.96+'px';
                p[i].style.left=ri*Math.cos(360/s*i)+'px';
                p[i].style.top=ri*Math.sin(360/s*i)+'px';
                p[i].style.fontSize=parseFloat(sp[i].style.fontSize)*0.96+'px';
                a[i].style.left=rii*Math.cos(360/s*i)+'px';
                a[i].style.top=rii*Math.sin(360/s*i)+'px';
                a[i].style.fontSize=parseFloat(sp[i].style.fontSize)*0.96+'px';
            }
            R-=R*0.1;
            if(R<2){
                o.innerHTML = '';
                o.parentNode.removeChild(o);
                clearInterval(clearI);
            }
        }
        var clearI = setInterval(spr,20);
    }
}
