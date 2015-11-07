jq(function(){
	var qqreg = /^[1-9]{1}[0-9]{4,8}$/;
	jq('#messageform').submit(function(){
		if(jq(".message_name").val()=="请输入您的姓名" || jq(".message_name").val()==""){
			jq(".message_about").html("请输入您的姓名");
			return false;
		}else if(jq(".message_tel").val()=="请输入您的QQ" || !qqreg.test(jq(".message_tel").val()) ){
			jq(".message_about").html("请输入您的QQ");
			return false;
		}else if(jq(".message_content").val()=="请输入您的留言" || jq(".message_content").val()==""){
			jq(".message_content").html("请输入您的留言");
			return false;
		}else{
		$.ajax({
			url: "index.php?a=form",
			type:'POST',
			data:"title="+jq(".message_name").val()+"&qq="+jq(".message_tel").val()+"&content="+jq(".message_content").val(),
			success:function(data){
				if(data == 1){
					jq(".message_about").html("提交成功,我们将在1个工作日内回复。");
					jq(".message_name").val('请输入您的姓名');
					jq(".message_tel").val('请输入您的QQ');
					jq(".message_content").val('请输入您的留言');
				}else{
					jq(".message_about").html("提交失败,请直接联系QQ客服。");
				}
			}
		})
		}
		return false;
	})
})


jq(window).scroll(function(){
	if(jq(window).height() + jq(window).scrollTop() - jq("#project").offset().top > 100 && jq(window).height() + jq(window).scrollTop() - jq("#project").offset().top < jq(window).height() -30){
		jq(".project_title_more").stop().animate({"left":"50%"},500);
	}else{
		jq(".project_title_more").stop().animate({"left":"100%"},500);
	}
	if(jq(window).height() + jq(window).scrollTop() - jq(".about_int").offset().top > 100 && jq(window).height() + jq(window).scrollTop() - jq(".about_int").offset().top < jq(window).height() -30){
		jq(".about_btn").stop().animate({"left":"50%"},500);
	}else{
		jq(".about_btn").stop().animate({"left":"-5%"},500);
	}
	index1 = jq("#services").offset().top;
	index2 = jq("#project").offset().top;
	index3 = jq("#about").offset().top;
	index4 = jq("#news").offset().top;
	index5 = jq("#contact").offset().top;
	var scrolltop = jq(window).scrollTop() + 71;
	if(scrolltop < index1){
		indexnum = 0;
	}else if(index1 < scrolltop && scrolltop < index2){
		indexnum = 1;
	}else if(index2 < scrolltop && scrolltop < index3){
		indexnum = 2;
	}else if(index3 < scrolltop && scrolltop < index4){
		indexnum = 3;
	}else if(index4 < scrolltop && scrolltop < index5){
		indexnum = 4;
	}else if(scrolltop > index5){
		indexnum = 5;
	}
	jq("#nav li").removeClass("active").eq(indexnum).addClass("active");
})

function navmove(id,index){
	if(id == "#index"){ var headheight = 0; }else{ var headheight = 70; }
	var offsettop = jq(id).offset().top - headheight;
	jq('html,body').stop().animate({scrollTop: offsettop},1500, 'easeInOutQuint');
	return false;
}

function selfmove(){
	var navId = "";
	if(window.location.href.indexOf("#services") > 0 ){navId = "#services";}
	if(window.location.href.indexOf("#project") > 0 ){navId = "#project";}
	if(window.location.href.indexOf("#about") > 0 ) {navId = "#about";}
	if(window.location.href.indexOf("#news") > 0 ){navId = "#news";}
	if(window.location.href.indexOf("#contact") > 0 ){navId = "#contact";}
	if (navId != ""){
		jq(window).scrollTop(0);
		jq('html,body').animate({scrollTop:jq(navId).offset().top - 70},2000, 'easeInOutQuint');
	}
}

function LoadingHidden(){
	jq("#loading").animate({"opacity":"0"},500,function(){ jq("#loading").css({"left":"100%"})});
}

jq(function(){
	jq(".infocus").focus();
	jq(".infocus").hover(function(){
		jq(".left_btn",this).stop().animate({"left":"0"},300)
		jq(".right_btn",this).stop().animate({"right":"0"},300)
	},function(){
		jq(".left_btn",this).stop().animate({"left":"-50px"},300)
		jq(".right_btn",this).stop().animate({"right":"-50px"},300)
	})
})

jq(function(){
	jq("#services_ul li").hover(function(){
		jq(".services_ico div",this).stop().animate({"opacity":"1"},300);
	},function(){
		jq(".services_ico div",this).stop().animate({"opacity":"0"},300);
	})
	jq("#services_con").mousemove(function(e) {
		if(jq(window).width() > jq(this).width()){
			var leftWidth = (jq(window).width() - jq(this).width())/2;
		}else{
			var leftWidth = 0;
		}
		var offset=e.clientX - leftWidth;
		var x=0;
		var y=0;
		jq("#services_ul",this).css({"margin-left": -( (jq("#services_ul",this).width() - jq(this).width()) / jq(this).width())*offset +"px"});
	});
	jq(".services_popclose").click(function(){
		jq(this).parents("#services_pop").slideUp(200);
	})
})
function services_tab(popindex,slide){
	jq(".services_poptabcon li").css("zIndex","1").hide().eq(popindex).css("zIndex","2").show();
	jq(".services_poptabbtn a").removeClass("active").eq(popindex).addClass("active");
	if(slide == "true"){
		jq("#services_pop").slideDown(200);
	}
}

jq(function(){
	jq(".project_pic li a").hover(function(){
		jq(".project_pop",this).stop(false,true).slideDown("fast");
	},function(){
		jq(".project_pop",this).stop(false,true).slideUp("fast");
	})
})
function project_tab(popindex){
	jq(".project_pic li").hide().eq(popindex).fadeIn(300);
	jq(".project_btn a").removeClass("active").eq(popindex).addClass("active");
}

jq(function(){
	jq(".about_btn").hover(function(){
		jq("span",this).stop().animate({"opacity":"1"},300);
	},function(){
		jq("span",this).stop().animate({"opacity":"0"},300);
	})
	jq(".about_btn").click(function(){
		jq(".about_int").addClass("about_intbg");
		jq(".about_int").stop().animate({"height":"245"},1000,function(){
			jq("#about_pop").slideDown(500);
			jq(".about_int").stop().animate({"height":"1"},500,function(){
				jq(".about_int").removeClass("about_intbg");
				var abouttop = jq("#about_pop").offset().top - 70;
				jq('html,body').stop().animate({scrollTop: abouttop},1000, 'easeInQuint');
			});
		});

	})
	jq(".about_popclose").click(function(){
		jq("#about_pop").slideUp(500);
		abouttop = jq("#about").offset().top - 70;
		jq('html,body').animate({scrollTop: abouttop},1200, 'easeInOutQuint',function(){
			jq(".about_int").stop().animate({"height":"45"},1000)
		});
	})
})

function aboutpop_tab(popindex){
	jq(".about_pop_con li").slideUp(300).eq(popindex).slideDown(300);
	jq(".about_pop_tab li").removeClass("active").eq(popindex).addClass("active");
}

jq(function(){
	jq(".link_weixin_li").hover(function(){
		jq(".link_weixin_ewm",this).stop().animate({"height":"138px"},300);
	},function(){
		jq(".link_weixin_ewm",this).stop().animate({"height":"0"},300)
	})
})

jq(function(){
	jq(".workpopl").click(function(){
		jq(document).attr("title",jq(this).attr("title"));
		jq("#workpop").show();
		url = jq(this).attr("href");
		LoadingLeftShow(url);
		return false;
	});
	jq(".workpopr").click(function(){
		jq(document).attr("title",jq(this).attr("title"));
		jq("#workpop").show();
		url = jq(this).attr("href");
		LoadingRightShow(url);
		return false;
	});
	jq("#news li a").click(function(){
		jq(document).attr("title",jq(this).attr("title"));
		jq("#workpop").show();
		url = jq(this).attr("href");
		LoadingLeftShow(url);
		return false;
	});
})


