/**
 * 名片公共JS
 */
//是否手机端
function isMobile(){
	var flag = null;
	var ua = navigator.userAgent.toLowerCase();
	if(ua.indexOf("mobile")>-1){
		flag = true;
	}else{
		flag = false; 
	}
	return flag;
}
//是否微信
function isWeixin(){
	var flag = null;
	var ua = navigator.userAgent.toLowerCase();
	if(ua.indexOf("micromessenger")>-1){
		flag = true;
	}else{
		flag = false; 
	}
	return flag;
}

//二维码
function ajaxGetQr(openId,callback) {
	var picUrl = null;
	$.ajax({
		type : "post",
		url : "/app/bizcard/ajaxQrCode.do",
		data : {"openId":openId},
		dataType:"json",
		success : function(datas) {
			if(datas.success == true){
				picUrl = datas.data;
				callback(picUrl);
			}else{
				$.flytip("好像有什么不对劲哦！");
			}
		}
	});
}

//like
function likeAction(data) {
	var likeObj = $("#jsClickLike").find(".info-number");
	if(data == true) {
		likeObj.html(parseInt(likeObj.html()) + 1);
		$.vConfirm({
			"msg" : "好棒喔！点赞成功^_^",
			"tit" : "提示",
			"titLine" : true,
			"width": "90%",
			"okBtnText" : "马上收藏",
			"closeBtnText" : "关 闭",
			"ok" : {
				"callback" : function(){
					$(".js-collect").click();
				},
				"isClose" : true
			}
		});
	} else if(data == false){
		$.flytip("同一个人最多只能点赞一次");
	}
}

function ajaxUserAction(friendId, type, callBack){
	$.ajax({
		async : true,
		type : "post",
		url : "/app/bizcard/ajaxUserAction.do",
		data : {"friendId":friendId, "type":type},
		error: function(){
			$.flytip("好像有什么不对劲哦！");
		},
		success : function(data) {
			if(callBack){
				callBack(data);
			}
		}
	});
}

//侧边栏滑动提示
function sideNavTip(){
    if(!$.readCookie("sideNavTip")){
        $.writeCookie("sideNavTip","1","4800h");
        var $sideNavTip = $("#sideNavTip");
        $sideNavTip
            .show()
            .click(function(){
                $(this).hide();
            });
    }
}
//侧边栏显示
function togglePage(){
	$(".js-moreList,.item-back").toggleClass("more-list-sh");
	//$(".namecard-page-relative, .js-sharebox").toggleClass("toggle-page");
	$("#superMask").toggleClass("show");
}

//隐私设置：电话不能拨打并提示
function forbiddenTell(){
	$.flytip("对方设置了隐私保护，不能拨打电话");
}

//-----------生成参数二维码-------------
function createSence(id,type){
	$.ajax({
		type : "post",
		url : "/sence/tempticket.do",
		data : {"id":id,type:type},
		error: function(){
			$.flytip("好像有什么不对劲哦！");
		},
		success : function(data) {
			if(data && data.success){
				var ticket = data.data;
				$.vAlert({
					"msg" : "<img width=210 height=210 src='https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket="+ticket+"'><br>临时二维码，不要用于印刷",
					"tit" : "微信扫码收藏我的名片",
					"width": "320px",
					"okBtnText" : "关 闭"
				});
			}else{
				$.flytip("好像有什么不对劲哦！");
			}
		}
	});
}

//播放背景音乐
function playBgMusic(){
    if($("#musicBox").length > 0){
        var musicPlayer = document.getElementById("musicPlayer");
        var tsflag = true;

        $("body").on("touchstart",function(){
            if(tsflag){
                musicPlayer.play();
                tsflag = false;
            }
        });

        $("#musicBox").click(function() {
            var $this = $(this);
            if ($this.hasClass("play")) {
                musicPlayer.pause();
                $this.addClass("stop").removeClass("play");
            } else {
                musicPlayer.play();
                $this.addClass("play").removeClass("stop");
            }

        });
    }
}

$(function (){
	//input val
	var hasCollect = $("#hasCollect"),
		isFocus = $("#isFocus"),
		isFocusVal = isFocus.val(),
		cardId = $("#cardId").val(),
		personId = $("#personId").val(),
		openId = $("#openId").val(),
		fromUrl = $("#fromUrl").val(), //out
		isFirst = $("#isFirst").val(),
		showTip = $("#showTip").val();
		notWeixin = $("#notWeixin").val();
	
	//提示层
	var $sharetip = $(".sharetip"),
		$tipCfriend = $(".js-sharetip-cfriend"), //分享给朋友提示
		$tipCollect = $(".js-sharetip-collect"); //收藏提示

	$(".js-cfriend").click(function(){
		$tipCfriend.show();
	});

	$sharetip.click(function(){
		$sharetip.hide();
	});
	
	//蓝色小字关注页面
	var focusUrl = "http://mp.weixin.qq.com/s?__biz=MzAxMTAzNTc2Nw==&mid=201506745&idx=1&sn=53958df300392e7077bf1d222b211e7a#rd";
	
	function focusUsFunc(){
		var uagent = window.navigator.userAgent.toLowerCase();
		if(uagent.indexOf("iphone")>-1 && fromUrl != "out"){
			$tipCollect.show();
		}else{
			$.flytip({
				"msg": "您还没有关注千线微名片，<br>即将跳转到关注页面",
				"callback": function(){
					window.location.href=focusUrl;
				}
			});
		}
	}
	
	//第一次进入提示层
	if(isFirst=="true"){
		$tipCfriend.show()
		.click(function(){
			$.flytip("可以直接点击栏目编辑内容");
		});
	}
	if(showTip=="true"){
		$tipCfriend.show();
	}
	
	//关注判断 我也要
	var $focusUsLink = $(".focus-us");
	if(isFocusVal=="false" && $focusUsLink.length>0){
		$focusUsLink.attr("href","javascript:").click(function(){
			//非手机端
			if(!isMobile()){
				$.vAlert({
					"msg" : "<img width=210 height=210 src='/app_static/app/cardtemplate/images/qx-qrcode.jpg'>",
					"tit" : "微信扫码关注并创建你的微名片",
					"width": "320px",
					"okBtnText" : "关 闭"
				});
				return false;
			}else{
				if(!isWeixin()){
					window.location.href="http://res2.eqianxian.com/collect/want-step.html";
				}
			}
			//手机端
			$.get("/app/bizcard/tryCard.do");//先发个请求到后台
			focusUsFunc();
		});
	}
	
	//点击收藏按钮
	$(".js-collect").click(function(){
		if(!isMobile()){//非手机端
			createSence(personId,"SCANCARD");
			return false;
		}
		if(!isWeixin() && isMobile()){//手机上，微信外
			window.location.href="http://res2.eqianxian.com/collect/collect.html?url="+window.location.href; 
		}
		
		if(isFocusVal && isFocusVal !== "false"){
			if(hasCollect.val() && hasCollect.val() !== "false"){
				$.flytip("您已经收藏过了");
			}else{
				$.ajax({
					"type": "post",
					"url": "/app/bizcard/collect.do?cardId="+cardId,
					"dataType": "json",
					"error": function(){
						$.flytip("好像有什么不对劲哦！");
					},
					"success": function(data){
						if(data.success){
							$.flytip({
								"msg": "收藏成功",
								"callback": function(){
									if(data.data=='/app/bizcard/collectList.do'){
										window.location.href= window.location.href;
									}else{
										window.location.href= data.data;
									}
									
								}
							});
							hasCollect.val("true");
						}else{
							$.vAlert(data.desc);
						}
						
					}
				});
			}
		}else{
			$.ajax({
				"type": "post",
				"url": "/app/bizcard/tryCollect.do?openId="+openId,
				"dataType": "json",
				"error":function(){
					$.flytip("好像有什么不对劲哦！");
				},
				"success": function(){
					focusUsFunc();
				}
			});
			
		}
	});
	
	//取消收藏
	$(".js-cancel").click(function(){
		$.ajax({
			"type": "post",
			"url": "/app/bizcard/uncollect.do?openId="+openId,
			"dataType": "json",
			"error": function(){
				$.flytip("好像有什么不对劲哦！");
			},
			"success": function(data){
				if(data.success){
					$.flytip({
						"msg": "取消成功",
						"callback": function(){
							window.location.href= window.location;
						}
					});
				}else{
					$.vAlert(data.desc);
				}
				
			}
		});
	});
	
	//添加、取消星标
	$(".js-star").click(function(){
		var $this = $(this);
		var $itemtx = $this.find(".more-item-tx");
		var openId = $("#openId").val();
		if(!$this.hasClass("hasStar")){//加星标
			$.ajax({
				"type": "post",
				"url": "/app/bizcard/markStar.do",
				"data": {openId:openId},
				"dataType": "json",
				"error": function(){
					$.flytip("好像有什么不对劲哦！");
				},
				"success": function(data){
					if(data && data.success){
						$this.addClass("hasStar");
						$itemtx.text("取消星标");
						$.flytip("加星成功");
					}else{
						$.flytip(data.desc);
					}
				}
			});
		}else{//取消星标
			$.ajax({
				"type": "post",
				"url": "/app/bizcard/cancelStar.do",
				"data": {openId:openId},
				"dataType": "json",
				"error": function(){
					$.flytip("好像有什么不对劲哦！");
				},
				"success": function(data){
					if(data && data.success){
						$this.removeClass("hasStar");
						$itemtx.text("加入星标");
						$.flytip("取消成功");
					}else{
						$.flytip(data.desc);
					}
				}
			});
		}
	});
	
	//显示隐藏侧边栏
	$(".js-featureMore, .item-back, #superMask").click(function(){
		togglePage();
		$("html,body").toggleClass("oh");
	});
	
	//个人二维码
	var $ewmThumbnail = $('#ewmThumbnail');
	var ewmThumbnailSrc = $ewmThumbnail.data("src");
	var $ewmform = $(".js-ewmform");
	var ewmformLoaded = true;
	$(".js-ewm").click(function(){
		if(ewmformLoaded){
			if(ewmThumbnailSrc){
				$ewmThumbnail.attr("src",ewmThumbnailSrc);
			}else{
				ajaxGetQr(openId,function(data){
					$ewmThumbnail.attr("src",data);
				});
			}
		}
		$ewmform.fadeIn();
		ewmformLoaded = false;
	});
	$(".js-ewmClose").click(function(){
		$ewmform.fadeOut();
	});
	
	//like
	$("#jsClickLike").click(function() {
		if(notWeixin!="true"){
			ajaxUserAction(openId, 'LIKE', likeAction);
		}else{
			$.flytip("微信里打开才能点赞");
		}
	});
	//头像转动
	setTimeout(function(){
		$(".avatarPic").addClass("rolling");
	},500);

    //背景图片加载是否成功？
    setTimeout(function(){
        $(".vcardBg").each(function(){
            var $this = $(this);
            var errFlag = false;
            if(!this.complete){
                errFlag = true;
            }
            if(typeof this.naturalWidth != "undefined" && this.naturalWidth == 0) {
                errFlag = true;
            }
            if(errFlag){
                var timeStamp = (new Date).getTime();
                var bg = $this.data("bg");
                if(bg.indexOf("?") > -1){
                    bg = bg.split("?")[0];
                }
                var bgPath = bg+"?t="+timeStamp;
                $.vConfirm({
                    "msg" : "背景图片显示不出来吗？<br>点确定可重新加载",
                    "tit" : "提示",
                    "titLine" : true,
                    "width": "300px",
                    "okBtnText" : "确 定",
                    "closeBtnText" : "取 消",
                    "ok" : {
                        "callback" : function(){
                            //更新页面背景地址
                            $this.attr("src",$this.attr("src")+"?t="+timeStamp);
                            //保存到数据库
                            /*$.ajax({
                                "type": "post",
                                "url": "/app/bizcard/saveBizCardBg.do",
                                "data": {"bg": bgPath},
                                "success": function(data){
                                    //$.flytip("更新成功");
                                }
                            });*/
                        },
                        "isClose" : true
                    }
                });
            }
        });
    },10000);

    playBgMusic();
});