 function shows(){
       
        	   var Request = new Object();
        	    Request = GetRequestParam();
        	    var fromUserName = Request['fromUserName'];
        	    var param={};
        	  param.univercity=1;
              param.fromUserName=fromUserName;
              param._url="swno//getSession";
              if(Request['toUserName']) {
                param.toUser = Request['toUserName'];
              }
              request(param, true,function(data){
            	    if(data.retCode== 200){
            	       showSwno(data.value.result);
            	       pageAmount = data.value.pageAmount;
            	    	 var  i='${sessionScope.adminID}';
                   // 	 alert(i);
                    		$("#userId").val(i);
            	      }else{
            	        //alert(data.message);
            	       alert("当前用户访问太过拥挤，请稍后刷新重试！");
            	      }
            	    });
        
         }

function getSwno(pn,pagetypenum){
    var Request = new Object();
    Request = GetRequestParam();
    var fromUserName = Request['fromUserName'];
    var param={};
    param.pageSize=5;
    param.pageNum = pn;
    /*index*/
/*    if(pagetypenum == 1) {
    	shows();
      param.univercity=1;
      param.fromUserName=fromUserName;
      param._url="swno/searchSwno";
      if(Request['toUserName']) {
        param.toUser = Request['toUserName'];
      }
    }*/
    /*qingquan*/
    if(pagetypenum == 2) {
      param.limit=$('#key').val();
      param._url=$('#myurl').val();
      if(Request['toUserName']) {
        param.toUser = Request['toUserName'];
      }
    }
    /*my*/
    if(pagetypenum == 3) {
      param.univercity=1;
      param.fromUserName=fromUserName;
      param._url = $('#ajax_url_my').val();
      var userId = $("#userId").val();
      if (userId != "") {
        param.userId = $("#userId").val();
      } else {
      //  alert("请登录！");
     //   return;
      }
    }
    /*myin*/
    if(pagetypenum == 4) {
      param.univercity=1;
      param.fromUserName=fromUserName;
      param._url = "swno/myjoinSwno";
      var userId = $("#userId").val();
      if (userId != "") {
        param.userId = $("#userId").val();
      } else {
       //alert("请登录！");
      //  return;
      }
    }
    request(param, true,function(data){
    if(data.retCode== 200){
       showSwno(data.value.result);
       pageAmount = data.value.pageAmount;
      }else{
        alert(data.message);
        //alert("当前用户访问太过拥挤，请稍后刷新重试！222222");
      }
    });
  }
    /*显示一个页的情话*/
  function showSwno(data){
    for(var i=0;i<data.length;i++){
      showOneSwno(data[i]);
    }
    $(".ui-listview").listview('refresh');
  }
   /*显示列表中的一个情话*/
  function showOneSwno(data){
    /*var topdiv = $("<div/>").addClass("topflag").text("顶");*/
    var sharea= $("<a/>").attr({"data-role":"button","onclick":"share("+ data.swnoId + ")",}).text("分享");
    var reporta = $("<a/>").attr({"data-role":"button","onclick":"report('"+ data.userId + "'," + data.swnoId + ")",}).text("举报");   
    /*if(data.stickie){
      var settopa = $("<a/>").attr({"data-role":"button","onclick":"settop("+data.swnoId+",0,this)",}).text("取消置顶");
      topdiv.attr("style","display:block");
    } else {
      var settopa = $("<a/>").attr({"data-role":"button","onclick":"settop("+data.swnoId+",1,this)",}).text("置顶");
      topdiv.attr("style","display:none");
    }
    var dela = $("<a/>").attr({"data-role":"button","onclick":"deleteqh("+data.swnoId+",this)",}).text("删除");*/
    var moremenu = $("<span/>").addClass("moremenu");
    if($("#roleId").val() == 9 ) {
      /*moremenu.append(sharea).append(reporta).append(settopa).append(dela);*/
    } else if($("#roleId").val() == 6 && $("#univId").val() == data.univId) {
      /*moremenu.append(sharea).append(reporta).append(settopa).append(dela);*/
    } else if($("#userId").val() == data.userId){
      /*moremenu.append(sharea).append(reporta).append(dela);*/
      moremenu.append(reporta).append(dela);
    } else {
      moremenu.append(sharea).append(reporta);
    }
    if(data.praised==true) {
    	var praiseNum=0
    	if(data.praiseNum!=null){
    		praiseNum=data.praiseNum;
    	}
      var $divhudong=$("<div>").addClass("hudong")
          .append($("<img/>").addClass("on-heart").attr({
        "src":""+getUrl()+"/template/style/images/bgimg/icon-hudong-heart-on.png",
        "onclick":"praise("+data.swnoId+",this)",
        "ownerUserId":data.userId,
      })).append($("<span>").text(praiseNum))
      .append($("<img/>").attr({
        "src":""+getUrl()+"/template/style/images/bgimg/icon-hudong-chat.png",
        "onclick":"window.location.href = 'index.php?c=entry&m=jdg_luvwhispers&i=" + data.uniacid + "&do=showdetail&swnoId=" + data.swnoId + "'"}
      )).append($("<span>").text(data.commentNum))
      .append($("<img/>").attr({
        "src":""+getUrl()+"/template/style/images/bgimg/icon-hudong-more.png",
        "onclick":"menupop(this)",}))
      .append(moremenu);
    } else {
    	var praiseNum=0
    	if(data.praiseNum!=null){
    		praiseNum=data.praiseNum;
    	}
      var $divhudong=$("<div>").addClass("hudong")
          .append($("<img/>").attr({
        "src":""+getUrl()+"/template/style/images/bgimg/icon-hudong-heart.png",
        "onclick":"praise("+data.swnoId+",this)",
        "ownerUserId":data.userId,
      })).append($("<span>").text(praiseNum))
      .append($("<img/>").attr({
        "src":""+getUrl()+"/template/style/images/bgimg/icon-hudong-chat.png",
        "onclick":"window.location.href = 'index.php?c=entry&m=jdg_luvwhispers&i=" + data.uniacid + "&do=showdetail&swnoId=" + data.swnoId + "'"}
      )).append($("<span>").text(data.commentNum))
      .append($("<img/>").attr({
        "src":""+getUrl()+"/template/style/images/bgimg/icon-hudong-more.png",
        "onclick":"menupop(this)",}))
      .append(moremenu);
    }
    var $toUser=$("<div/>").addClass("tosomeone").text("To:"+data.toUser);
    var $divsaywhat=$("<div/>").append($("<table/>").append(($("<tr/>").append($("<td>").text(data.content))))).addClass("saywhat");
     var $a= $("<a/>").attr({
      "href":"index.php?c=entry&m=jdg_luvwhispers&i=" + data.uniacid + "&do=showdetail&swnoId=" + data.swnoId,
      "class":"ui-btn","data-ajax":"true","data-transition":"flip"
    })
    $a.append($toUser).append($divsaywhat);
    var $divbottomarea=$("<div/>").addClass("bottom-area");
    var $univer=$("<div/>").text(data.univName).addClass("list-school");
    $divbottomarea.append($univer).append($divhudong);
  var $li=$("<li/>").append($a);
  $li.attr("style","background-color:"+data.nowColor)
  .append($divbottomarea);
  if(data.nowColor == "#F0F0F0") {
    $a.attr("style","color:#333!important");
    $divbottomarea
    $toUser.attr("style","color:#333!important");
    $li.attr("style","color:#333!important;background-color:"+data.nowColor+"!important");
  }
  /*$li.append(topdiv);*/
  nowListName.append($li);
  //$li.insertBefore(refreshBtn);
  }

  
  /*点赞*/  
  function praise(info,obj){//点赞\
    var param={};
    param.swnoId=info;
    if( $(obj).hasClass("on-heart") ) {
      param._url=$('#unlike').val();
      request(param, false,function(data){
        if(data.retCode== 200){
          $(obj).removeClass("on-heart");
          $(obj).attr("src",""+getUrl()+"/template/style/images/bgimg/icon-hudong-heart.png");
          $(obj).next().text(parseInt($(obj).next().text()) - 1 );
          /*updReplayNum($(obj).attr("ownerUserId"),-1);*/
          //alert($(obj).attr("ownerUserId"));
             //alert("取消赞!");
        }else{
          //alert(data.message);
          alert("当前用户访问太过拥挤，请稍后刷新重试！");
        }
      });
    } else {
      param._url= $('#like').val();
      request(param, false,function(data){
      if(data.retCode== 200){
        $(obj).addClass("on-heart");
        $(obj).attr("src",""+getUrl()+"/template/style/images/bgimg/icon-hudong-heart-on.png");
        $(obj).next().text(parseInt($(obj).next().text()) + 1 );
        /*updReplayNum($(obj).attr("ownerUserId"),1);*/
        //alert($(obj).attr("ownerUserId"));
           //alert("您的祝福已经收到!");
        }else{
       //   alert(data.message);
         alert("当前用户访问太过拥挤，请稍后刷新重试！");
        }
      });
    }
  }
  /*添加情话*/
  function add(){
    if(valueIsEmpty( $("#input_To").val() ) ){
      alert("你要对谁说呢？");
      //alert("你要对谁说呢？");
    } else if (valueIsEmpty($("#text-content").val())) {
      alert("说点什么吧？");
      //alert("说点什么吧？");
    } else if($("#text-content").val().match(/<script>|<SCRIPT>|alert|ALERT/g)|| $("#input_To").val().match(/<script>|<SCRIPT>|alert|ALERT/g) ) {
      alert("禁止输入script语句！");
      //alert("禁止输入非法语句！");
    } else {
      var param={};
      var touser = $("#input_To").val();
      var textcontent = $("#text-content").val();
      var limit = 0;
      if($("#limit").prop("checked")==true) {
        limit = 1;
      }
      param.toUser = touser;
      param.content = textcontent;
      param.limit = limit;
      param.nowColor = nowcolor;
      param._url = $("#myurl_add").val();
      request(param, true,function(data){
        if(data.retCode== 200) {
          alert("表白成功..请耐心等待我们的审核...");
          window.location.href=$("#nexturl_add").val();
        } else {
            alert(data.message);
        }
      });
    }
  }

  /*剩余字数*/
  function setShowLength(obj, maxlength, id) { 
    var rem = maxlength - obj.value.length; 
    var wid = id; 
    if (rem < 0){ 
    rem = 0; 
    } 
    document.getElementById("wordLess").innerHTML =rem + "/120"; 
  } 
  /*改变颜色*/
  function changecolor (color,obj) {
    if(color == "random") {
      color = randomcolor (color);
    }
    if(color == "#F0F0F0") {
      $("#text-content").css("color","#666");
      $("#wordLess").css("color","#666");
    } else {
      $("#text-content").css("color","#fff");
      $("#wordLess").css("color","#fff");
    }
    $("#color_List").find(".coloron").removeClass("coloron");
    $(obj).addClass("coloron");
    nowcolor = color;
    $(".add-conten").css("background-color",color);
  }
  /*获得随机颜色*/
  function randomcolor (color) {
    var somecolor = ["#F5D475", "#F59694", "#EAD5AA", "#DBA6A2","#C7A6C1","#8FCBE3","#6E7E8E"]; 
    var icolor = (Math.random() * 7)%6 ;
    icolor = parseInt(icolor);
    color = somecolor[icolor];
    return color;
  }
    /*弹窗提示*/
  function err_page_popup ( msg , callback ){
          //hideLoading();
          $(".err_msg").html( msg ? msg : "页面错误" );
          $( "#prompt" ).popup( "open" );
          if(callback) callback();
        }
 /*微信分享的操作*/ 
  function getApp(){
        	var appId="";
          var param={};
          param._url = "user/getApp";
          request(param, true,function(data){
          if(data.retCode== 200){
        	  appId=data.value;
            }else{
              alert("当前用户访问太过拥挤，请稍后刷新重试！");
            }
          });
          return appId;
        }
  function getTitle(){
  	var title="";
    var param={};
    param._url = "copyright/getTitle";
    request(param, false,function(data){
    if(data.retCode== 200){
    	title=data.value;
    	if(title==null){
    		title="看看Ta对你表白了什么吧";
    	}
      }else{
        alert("当前用户访问太过拥挤，请稍后刷新重试！");
      }
    });
    return title;
  }
  function getDesc(){
	  	var desc="";
	    var param={};
	    param._url = "copyright/getDesc";
	    request(param, false,function(data){
	    if(data.retCode== 200){
	    	desc=data.value;
	    	if(desc==null){
	    		desc=" 再也不要一个人过情人节思密达。";
	    	}
	      }else{
	        alert("当前用户访问太过拥挤，请稍后刷新重试！");
	      }
	    });
	    return desc;
	  }
 /*WeixinApi.ready(function(Api) {
  	Api.showOptionMenu();
  	var wxData = {
  		"appId": "",
  		"imgUrl" : 'http://www.liangboy.com/CampusPlusHoney/honey/images/qinghua.jpg',
  		"link" : '',
  		"desc" :'',
  		"title" : ''
  	};
  	// 分享的回调
  	var wxCallbacks = {
  		// 分享被用户自动取消
  		cancel : function(resp) {
  			alert("您取消了分享，你的朋友就不知道您被表白哦~");
  		},
  		// 分享失败了
  		fail : function(resp) {
  			alert("分享失败，可能是网络问题，一会儿再试试？");
  		},
  		// 分享成功
  		confirm : function(resp) {
  			window.location.href='http://www.liangboy.com/CampusPlusHoney/honey/index.jsp';
  		},
  	};
  	Api.shareToFriend(wxData,wxCallbacks);
  	Api.shareToTimeline(wxData,wxCallbacks);
  	Api.shareToWeibo(wxData,wxCallbacks);
  });*/
  /*清除页面的更多菜单的操作*/ 
  function clearmenu () {
        $(".moremenu").css("display","none");
  }
  /*弹出更多菜单的操作*/ 
  function menupop (obj) {
    if( $(obj).next().css("display") == "none") {
      $(".moremenu").css("display","none");
      $(obj).next().css({"width":"0px"}).animate({"display":"block","width":"80px"},"fast");
    } else {
      $(obj).next().css("display","none");
    }
  } 
  /*分享的操作*/ 
  function share() {
          var t = $(".bigTxt-weixin");
          $('.moremenu').css('display','none');
            t.addClass("z-show"),
             t.on("click",function() {
                $(this).removeClass("z-show"),
                $(this).off("click")
                });
  }
  /*举报的操作*/ 
  function report(userId,swnoId) {
    var param={};
    param.userId = userId;
    param.swnoId = swnoId;
    param._url= $('#report_url').val();
    request(param, false,function(data){
      if(data.retCode== 200) {
        //alert("举报成功！");
        alert("举报成功！");
      }else if(data.retCode==1){
        //alert(data.message);
        alert("不要自己举报自己好吗?");
       }else if(data.retCode==2){
        //alert(data.message);
        alert("您的举报我们已收到..");
      }else{
        alert("当前用户访问太过拥挤，请稍后刷新重试！");
      }
    });
    $(".moremenu").css("display","none");
  }
  /*置顶的操作*/ 
  /* num = 1 置顶操作
     num = 0  取消置顶操作*/
  function settop (id,num,obj) {
    param = {};
    param.swnoId = id;
    param._url = "swno/stickieSwno";
    if(num == 0) {
      param.stickie = 0;
      request(param, false,function(data){
        if(data.retCode == 200) {
          alert("取消置顶成功！");
          $(obj).parentsUntil("li").parent().find(".topflag").attr("style","display:none");
          $(obj).attr({"onclick":"settop("+id+",1,this)"}).text("置顶");
        } else if (data.retCode == 110){
          alert("无权操作！");
        } else {
          alert("当前用户访问太过拥挤，请稍后刷新重试！");
        }
      });
    } else {
      request(param, false,function(data){
        if(data.retCode == 200) {
          alert("置顶成功！");
          $(obj).parentsUntil("li").parent().find(".topflag").attr("style","display:block");
          $(obj).attr({"onclick":"settop("+id+",0,this)"}).text("取消置顶");
        } else if (data.retCode == 110){
          alert("无权操作！");
        } else {
          alert("当前用户访问太过拥挤，请稍后刷新重试！");
        }
      });
    }
    $(".moremenu").css("display","none");
  }
  /*删除的操作*/ 
  function deleteqh(id,obj) {
    if(confirm("删除这条情话，别人就看不到了，确定要这么做吗？")){
      var param = {};
      param.swnoId = id;
      param._url = "swno/deleteSwno";
      request(param, false,function(data){
        if(data.retCode == 200) {
          $(obj).parentsUntil("li").parent().remove();
          alert("删除成功！");
        } else if(data.retCode == 110) {
          alert(data.message);
        } else {
          alert("当前用户访问太过拥挤，请稍后刷新重试！");
        }
      });
    }
    $(".moremenu").css("display","none");
  }
  
    var num = 1;
    /*获得详细页一条情话*/
		function getOneSwno() {
			var Request = new Object();
			Request = GetRequestParam();
			var swnoId = Request['swnoId'];
			//var swno = Request['swno'];
			var param = {};
			//param.pageSize=10;
			param.swnoId = $('#swnoId_de').val();
      param._url = $('#my_url_de').val();
			request(param, false,  function(data) {
				if (data.retCode == 200) {
					showOneSwnoDet(data.value.result[0])
					getSwnoc(data.value.result[0].swnoId);
				} else {
					//alert(data.message);
          alert("哎呦,等等哦,现在有点忙哦...");
				}
			});
		}
     /*显示一条情话详情*/
		function showOneSwnoDet(data) {
			$(".tosomeone").text("To:" + data.toUser);
			$("#swnoContent").text(data.content);
			$("#userID").val(data.userId);
			$(".list-school").text(data.univName);
			var praiseNum=0;
			if(data.praiseNum!=null){
				praiseNum=data.praiseNum;
			}
      $(".mainqh").css("background-color",data.nowColor);
      if(data.nowColor == "#F0F0F0") {
        $(".mainqh").css("color","#333");
        $("#swnoContent").css("color","#333");
        $(".tosomeone").css("color","#333");
      }
      if(data.praised==true) { 
        $(".heart-img").addClass("on-heart").attr("src",""+getUrl()+"/template/style/images/bgimg/icon-hudong-heart-on.png");
      } else {
        $(".heart-img").attr("src",""+getUrl()+"/template/style/images/bgimg/icon-hudong-heart.png");
      }
      $(".heart-img").attr({"ontouchstart":"praise("+data.swnoId+",this)","ownerUserId":data.userId});
      $(".heart-span").text(praiseNum);
      $(".chat-span").text(data.commentNum);
		}
    /*根据id  get 情话的评论*/
		function getSwnoc(swnoId) {
			var param = {};
			param.pageSize = 10;
			param.swnoId = swnoId;
      param._url = $('#my_url_de1').val();
			request(param, false,  function(data) {
				if (data.retCode == 200) {
					if (data.value != null) {
						showSwnoc(data.value);
					}
				} else {
					alert(data.message);
				}
			});
		}
    /*显示多个情话的评论*/
		function showSwnoc(data) {
			$(".qh-chat").remove();
                num = 1;
			for ( var i = 0; i < data.length; i++) {
				showOneSwnoc(data[i]);
			}
		}
     /*显示一个情话的评论*/
		function showOneSwnoc(data) {
			var chaTime = data.createTime;
			var head=data.swnocoPic;
			var $img = $("<img>").attr({
				"src" :head,
			});
			var lou = num + "楼";
			num++;
			var $imgChartMore = $("<img>").attr({
				"src" : ""+getUrl()+"/template/style/images/bgimg/icon-reword.png",
				"onclick" : "reply('回复给" + lou + ":','"+data.swnocoId+"')",
			});
		//	var replay=getReplay(data.swnocoId);
			var $chattextmain = $("<div>").addClass("chat-text-main").text(
					data.swnocoContent);
			var $chattextinfo = $("<div>").addClass("chat-text-info").append(
					$("<span>").addClass("lou").append(lou)).append(
					$("<span>").append(chaTime));
          //.append($("<span>").append("删除"));
			var $tableContent = $("<table/>")
					.append(
							$("<tr/>").append(
									$("<td>").addClass("headimg").append(
											$("<div/>").append($img))).append(
									$("<td>").addClass("chat-text").append(
											$chattextmain)
											.append($chattextinfo)).append(
									$("<td>").addClass("chat-more").append(
											$("<div/>").append($imgChartMore))));
			var $liContent = $("<li/>");
			$liContent.addClass("qh-chat").attr({
				"style" : "background-color: rgb(244,244,244)!important"
			});
			$liContent.append($tableContent);
			$("#Oneqh").append($liContent);
      //$liContent.insertBefore($("#nextListBtn"));
			if(replay!=""){
				//$(".ui-listview").append(replay);
			}
			$("#Oneqh").listview("refresh");
		}
    
		function time(sometime) {
			var myDate = new Date();
			myDate.getTime();
			var chaDate = new Date();
			chaDate = myDate - sometime;
			var chaMin = chaDate / 60000;
			return chaMin;
		}
    
    /*回复评论*/
		function reply(text,swnocoId) {
			//var text = $(obj).value();
			//  $("#replay").val("1");
			$("#replayswnocoId").val(swnocoId);
			$("#swnocContent").focus();
			if ($("#swnocContent").val() == null
					|| $("#swnocContent").val() == "") {
				$("#swnocContent").val(text);
			} else if ($("#swnocContent").val().match(/回复给\d+楼:/g)) {
				text = $("#swnocContent").val().replace(/回复给\d+楼:/g, text)
				//$("#swnocContent").val("");
				$("#swnocContent").val(text);
			} else {
				var oldvalue = $("#swnocContent").val();
				$("#swnocContent").val(text + oldvalue);
			}
		}
    /*添加评论*/
		function addswnoc() {
			var Request = new Object();
			Request = GetRequestParam();
			var swnoId = Request['swnoId'];
			var swnocoContent = $("#swnocContent").val();
			var testswnocoContent = swnocoContent.replace(/\s/g, "");
			if (swnocoContent == "" || testswnocoContent == "") {
				//alert("亲，说点什么吧！");
        alert("亲，说点什么吧！");
			} else if (swnocoContent.match(/回复给\d+楼:/g)
					&& swnocoContent.length == swnocoContent.match(/回复给\d+楼:/g)[0].length) {
        alert("亲，对Ta说点什么吧！");
			} else if (swnocoContent.match(/回复给\d+楼:/g)
					&& testswnocoContent.length == swnocoContent
							.match(/回复给\d+楼:/g)[0].length) {
        alert("亲，对Ta说空格这样好吗？");
			} else {
				var param = {};
				var userId = $("#userId").val();
				if (userId != "") {
					param.userId = $("#userId").val();
				} else {
              // alert("请登录！");
				//	return;
				}
				param.swnoId = swnoId; 
				if ($("#replay").val() =="" ) {
					param.swnocoContent = swnocoContent;
					/*param.swnocoPic=getRandomHead(swnoId);*/
          param._url = $('#my_url_de2').val();
					request(param, false,  function(data) {
						if (data.retCode == 200) {
							//alert("发布成功 ");
							$(".qh-chat").remove();
							num = 1;
							getOneSwno();
							$("#swnocContent").val("");
							$("#replay").val("");
							/*updReplayNum($(".heart-img").attr("ownerUserId"),1);*/
						}else if (data.retCode == 112) {
              alert("你已被禁言，禁止操作！");
            }else{
							alert(data.message);
              //alert("当前用户访问太过拥挤，请稍后刷新重试！");
						}
					});
				}else{
					param.swnocoId=	$("#replayswnocoId").val();
					param.replayContent=swnocoContent;
          param._url = "replay/addReplay";
					request(param, false, function(data) {
						if (data.retCode == 200) {
							//alert("发布成功 ");
							$(".qh-chat").remove();
							num = 1;
							getOneSwno();
							$("#swnocContent").val("");
							$("#replay").val("");
              /*updReplayNum($(".heart-img").attr("ownerUserId"),1);*/
						}else if (data.retCode == 112) {
              alert("你已被禁言，禁止操作！");
            }else {
							//alert(data.message);
              alert("当前用户访问太过拥挤，请稍后刷新重试！");
						}
					});
				}
			}
		}
   /*获取随机头像*/
    function getRandomHead(swnoId){
    	var param={};
    	var param1={};
    	var headImage="";
    	var userId = $("#userId").val();;
      if (userId != "") {
        param1.userId = $("#userId").val();
      } else {
        //alert("请登录！");
       // return;
      }
      if(userId==$("#userID").val()){
        headImage="lord.png";
        return headImage;
      }
      param1.swnoId=swnoId;
      param1._url = "swnoc/getSwnochead";
    	request(param1, false,function(data){
      if(data.retCode== 200){
       if(data.value!=null){
         headImage=data.value.swnocoPic;	 
       }else{
          param._url = "head/getHeadImage";
          request(param, false,function(data){
                  if(data.retCode== 200){
                    headImage=data.value.headSrc;
                    }else{
                     // alert(data.message);
                     alert("当前用户访问太过拥挤，请稍后刷新重试！");
                    }
                  });
       }

       }else{
         //alert(data.message);
         alert("当前用户访问太过拥挤，请稍后刷新重试！");
       }
      });
      return headImage; 
    }
  var remindpopdiv;
  function showremindpop(rnum) {
    if( rnum == 0) {
      remindpopdiv.attr("style","display:none!important");
    } else {
      remindpopdiv.attr("style","display:block!important").text(rnum);
    }
  }  
  
  var  timer = 1 ;
  /*每个页面的时钟函数*/
/*    function clock() {
      timer = setInterval(function(){
        var renum = geReplayNum();
        showremindpop(renum);
      },1200000);
    }
   function stopclock() {
    clearInterval(timer);
   }*/
  /*更新我的动态个数（气泡数） 0  归零  1  加1*/
/*		function updReplayNum(data,num){
      var param={};
      param.num=num;
      param.userId=data;
      param._url = "replay/updateReplay";
      request(param, false,function(data){
      if(data.retCode== 200){
        }else{
         // alert("当前用户访问太过拥挤，请稍后刷新重试(updReplayNum)！");
        }
      });
		}*/
     /*获取我的动态个数（气泡数） 0  归零  1  加1*/
/*    function geReplayNum(){
    	var  num=0;
      var param={};
      param._url = "replay/getReplayNum";
      param.userId = $("#userId").val();
      request(param, false, function(data){
      if(data.retCode== 200){
    	  num=data.value;
        }else{
          alert("当前用户访问太过拥挤，请稍后刷新重试！");
        }
      });
      return num;
    }*/
  
  /*搜索页点击跳转且搜索*/
  function toshowSwno(){
    if($('#search-4').val()==''){
      alert('哎呦..写点什么吧...');
      return;
    }
        window.location.href =encodeURI($('#search_url').val()+"&content="+$("#search-4").val());
    }
    function NextPageAction1() {//下一页事件
          setTimeout(function() {
            $("#refresh_Button1").text("正在加载中...");
          },500);
          setTimeout(function() {
            getSwno(++pageNum,1);
            if(pageNum >= pageAmount){
              $("#refresh_Button1").text("已经到最后一页了");
            }
            else{
              $("#refresh_Button1").text("点击加载更多");
            }
        }, 1000);
          
        }
        
    function NextPageAction2() {//下一页事件
          setTimeout(function() {
            $("#refresh_Button2").text("正在加载中...");
          },500);
          setTimeout(function() {
            if(pageNum >= pageAmount){
              $("#refresh_Button2").text("已经到最后一页了");
            }
            else{
              $("#refresh_Button2").text("点击加载更多");
              getSwno(++pageNum,2);
            }
        }, 1000);
          
        }
        
    function NextPageAction3() {//下一页事件
          setTimeout(function() {
            $("#refresh_Button3").text("正在加载中...");
          },500);
          setTimeout(function() {
            getSwno(++pageNum,3);
            if(pageNum >= pageAmount){
              $("#refresh_Button3").text("已经到最后一页了");
            }
            else{
              $("#refresh_Button3").text("点击加载更多");
            }
        }, 1000);
          
        }
        
    function NextPageAction4() {//下一页事件
          setTimeout(function() {
            $("#refresh_Button4").text("正在加载中...");
          },500);
          setTimeout(function() {
            getSwno(++pageNum,4);
            if(pageNum >= pageAmount){
              $("#refresh_Button4").text("已经到最后一页了");
            }
            else{
              $("#refresh_Button4").text("点击加载更多");
            }
        }, 1000);
          
        }
        function NextPageAction5() {//下一页事件
          setTimeout(function() {
            $("#refresh_Button5").text("正在加载中...");
          },500);
          setTimeout(function() {
            getSwno(++pageNum,1);
            if(pageNum >= pageAmount){
              $("#refresh_Button5").text("已经到最后一页了");
            }
            else{
              $("#refresh_Button5").text("点击加载更多");
            }
        }, 1000);
          
        }
        function NextPageActionDe() {//详细页评论下一页事件
          setTimeout(function() {
              $("#refresh_ButtonDe").text("正在加载中...");
            },500);
          setTimeout(function() {	
            $(".qh-chat").remove();
                num = 1;
                getOneSwno();
                $("#refresh_ButtonDe").text("点击加载更多评论");
          }, 1000);
        }
    
    
    
  /*每个页面加载的操作*/ 
  var pageNum = 1, pageAmount = 0;
  var nowListName;
  var nowcolor = "" ;
      (function( $, undefined ) {
        $( document ).on( "pageshow", "#qh_benxiao", function() {
          pageNum = 1; 
          pageAmount = 0;
          nowListName = $("#AddList1");
          nowListName.empty();
          getSwno(1,1);
          remindpopdiv = $(".remindpop1");
         /* stopclock();
          clock();*/
        });  
        $( document ).on( "pageshow", "#qh_qingquan", function() { 
          pageNum = 1;
          pageAmount = 0;
          nowListName = $("#AddList2");
          nowListName.empty();
          getSwno(1,2);
          remindpopdiv = $(".remindpop2");
          /*stopclock();
          clock();*/
        });
        $( document ).on( "pageshow", "#qh_my", function() { 
          pageNum = 1;
          pageAmount = 0;
          nowListName = $("#AddList3");
          nowListName.empty();
          getSwno(1,3);
          /*stopclock();*/
          /*updReplayNum($("#userId").val(),0);*/
        });
        $( document ).on( "pageshow", "#qh_myin", function() { 
          pageNum = 1;
          pageAmount = 0;
          nowListName = $("#AddList4");
          nowListName.empty();
          getSwno(1,4);
          /*stopclock();*/
          /*updReplayNum($("#userId").val(),0);*/
        });
        $( document ).on( "pageshow", "#Add_Home", function() { 
          document.getElementById('text-content').onkeyup = function () {
            setShowLength(this, 120, 'cost_tpl_title_length');
          }
          $("#submit_Button").bind("click", function(){
            add();
          });
           nowcolor = randomcolor("");
          $(".add-conten").css("background-color",nowcolor);
          
          $("#text-content").focus(function(){
            $(".ui-grid-d").attr("style","display:none!important");
          });
           $("#text-content").blur(function(){
            $(".ui-grid-d").attr("style","display:block!important")
          });
          $("#input_To").focus(function(){
            $(".ui-grid-d").attr("style","display:none!important");
          });
           $("#input_To").blur(function(){
            $(".ui-grid-d").attr("style","display:block!important")
          });
          remindpopdiv = $(".remindpop6");
          /*stopclock();
          clock();*/
        });
        
        $( document ).on( "pageshow", "#qh_benxiao2", function() {
          pageNum = 1; 
          pageAmount = 0;
          nowListName = $("#AddList5");
          nowListName.empty();
          getSwno(1,1);
          remindpopdiv = $(".remindpop5");
          /*stopclock();
          clock();*/
        });  
        $( document ).on( "pageshow", "#qh_detail", function() {
          getOneSwno();
        }); 
        $( document ).on( "pageshow", "#Search_Home", function() {
          $("#search-4").focus(function(){
            $(".ui-grid-d").attr("style","display:none!important");
          });
          $("#search-4").blur(function(){
            $(".ui-grid-d").attr("style","display:block!important");
          });
          remindpopdiv = $(".remindpop7");
          /*stopclock();
          clock();*/
        }); 
      })( jQuery );
      
      
      
      