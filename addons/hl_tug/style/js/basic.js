// JavaScript Document
var content_html = '';
$(document).ready(function(){

	/********** 链接 去边样式 **********/
	window.onload=function(){
		for(var ii = 0; ii < document.links.length; ii ++)
			document.links[ii].onfocus=function(){this.blur();}
	};

	/****************************** 发布框 ******************************/
	/********** 图片 预览样式 **********/
	$('.publish .cpic').hover(function(){
		$(this).find('img').show();
	}, function(){
		$(this).find('img').hide();
	});


	/********** 控制器 显示样式 **********/
	$('.publish a.duotu, .publish a.biaoqing, .publish a.shipin, .publish a.yinyue').live('click', function(){
		var bar = $(this).parent().hasClass('hover');
			p = $(this).position();
		$('.publish a.duotu, .publish a.biaoqing, .publish a.shipin, .publish a.yinyue').each(function(){
			var cla = 'pub_' + $(this).attr('class');
			$(this).parent().removeClass('hover');
			$('.' + cla).hide();
		});

		if(!bar)
		{
			var cla = 'pub_' + $(this).attr('class');
			$(this).parent().addClass('hover');
			$('.' + cla).css({'top':p.top + 42, 'left':p.left}).show();
		}
	});

	$('.pub .close').live('click', function(){
		$('.publish a.duotu, .publish a.biaoqing, .publish a.shipin, .publish a.yinyue').each(function(){
			$(this).parent().removeClass('hover');
		});
		$(this).parents('.pub').hide();
	});

	/****************************** 单账号 ******************************/
	$('#single_account').live('click',function(){
		var father = $(this).closest('.other');
		 father.find('.share').hide();
		 father.find('.opic').show();

		 $('#gid').val(0);
	});
	$('.other .opic').live('click',function(){
		var father = $(this).closest('.publish');
		father.find('.choice').toggle();

	});
	$('.choice .close').live('click',function(){
		var father = $(this).closest('.choice');
		 father.hide();
	});
	$('.choice dd:first').show();
	$('.choice .pic a').live('click',function(){
		var index = $(this).index();
		$(this).addClass('hover').siblings().removeClass('hover');
		$('.choice dd').eq(index).show().siblings('dd').hide();
	});

	$('.choice  .name a:not(".btn")').live('mouseenter',function(){//鼠标移上效果
		$(this).closest('.name').find('a').removeClass('hover');
		$(this).addClass('hover');
	}).live('mouseleave',function(){//鼠标移出效果
		$(this).closest('.name').find('a').removeClass('hover');
	}).live('click',function(){//点击选中单账号
		var name = $(this).text();
		var str = '<span><em></em></span><i></i>';
		var classname = $('.choice .pic .hover').attr('class').split(' ')[0];
		$('.opic a').html(str+name).attr({'class':classname});
		$('#single_type').val($(this).attr('type'));
		$('#single_bind_username').val($(this).attr('bind_username'));
		$(this).closest('.choice').hide();
	});

	// 搜狐客户端
	$('.icon_sohu').bind('click', function(){
		if( parseInt($('#hidsohuclient').val()) == 0 ){
			$(this).addClass('hover'); $('#hidsohuclient').val(1);
			$(this).children(".select").show();
		}else{
			$(this).removeClass('hover'); $('#hidsohuclient').val(0);
			$(this).children(".select").hide();
		}
	});

    content_html = $("#content_list").html();

    $('#picture_lab').click(function() {
        var tid = cookie('picture_tid') != null ? cookie('picture_tid') : 0;
        var tab = cookie('picture_tab') != null ? cookie('picture_tab') : 'content';
        var page = cookie('picture_page') != null ? cookie('picture_page') : 0;

        show_picture(tid, tab, page);
    });


	$('#share li em').each(function(){$(this).next('a').css('opacity',1)});

	//短文的图片显示问题
    $(".pic_name span,.pic_name span.pic_box").mouseenter(function(){
    	$(".pic_name span.pic_box").show();
    }).mouseleave(function(){
    	$(".pic_name span.pic_box").hide();
    });

	//短文输入框的字数验证
	var _timer = 0;
    $("#weibo_content").unbind();
    $("#weibo_content").bind({
        keydown: function(event) {
            clearTimeout(_timer);
            _timer = setTimeout(function() {
            	weibo_check_input();
            }, 100);
        },
        paste: function() {
            clearTimeout(_timer);
            _timer = setTimeout(function() {
            	weibo_check_input();
            }, 100);
        },
        click: function() {
            clearTimeout(_timer);
            _timer = setTimeout(function() {
            	weibo_check_input();
            }, 100);
        },
        keyup: function(e) {
            clearTimeout(_timer);
            _timer = setTimeout(function() {
            	weibo_check_input();
            }, 100);
        }
    });
    //短文上传图片
	$("#uploadpic").change(function(){
		upload_weibo_image();
	});

	var site = SITE_URL.replace('http://', '');
	site = site.replace('/', '');
	var ignore =[SITE_URL+'site/stepone.htm',SITE_URL+'site/steptwo.htm',SITE_URL + 'site/steptwo/finsh.htm',SITE_URL+'member/bind/weixin?from=site/steptwo.htm'];
	var url = document.location.href;
	var exsits = $.inArray(url, ignore);
	if (document.location.host == site && exsits < 0) {
		check_ismail();
		//check_usertype();
	}

});

$(document).ready(function(){
	//1024分辨率适配
	fitscreen();
	$(window).resize(function(){
		fitscreen();
	});

	/********** 虚拟通用选择框 **********/
	//多选
	$(".send_chck label").click(function(){
		if($(this).prev().prop("checked") != true){
			$(this).addClass("pick");
		}else{
			$(this).removeClass("pick");
		}
	});

	//单选
	$(".send_radio label").click(function(){
		if($(this).prev().prop("checked") != true){
			$(this).addClass("pick").siblings("label").removeClass("pick");
		}
	});

	//通用下拉框 下拉样式
	$('.menu').click(function(){
		$(this).find('.active').toggle();
	});

	$('.menu').mouseleave(function(){
		$(this).find('.active').hide();
	});

	$('.menu .active a').live('click', function(){
		var val = $(this).attr('title');
		$(this).parent().hide();
		$(this).parent().next().attr('title', val).html('<i></i>' + val);
	});

	//导航 高度自适应样式
	var h = document.documentElement.clientHeight || document.body.clientHeight;
	$('.navout').css({'min-height' : h - 58});

});

// 检查是否验证邮箱
function check_ismail()
{
	$.post(SITE_URL +'member/home/checkismail',{},function(response){
		if (response.ismail == 0) {
			$('.head').after('<div class="point point_allweb" id="email_verify_tips"><div class="point_cen"><a href="javascript:;" class="close" title="不再提示">×</a>为了您的账户安全，请立即验证您的注册邮箱:<span>'+response.email+'</span><a href="javascript:;" onclick="send_verify_email()" class="verification">立即验证</a></div></div>');
			$('#email_verify_tips .close').click(function(){
				$("#email_verify_tips").remove();
			});
		}
	}, 'json');

}

//检查是否设置会员类型
function check_usertype()
{
	$.post(SITE_URL +'member/home/checkusertype',{},function(response){
		if (response.usertype == 0) {
			$('body').append('<div class= "pop_pub slect_sort" id= "select_usertype"><h2><a href= "javascript:;" title= "关闭"> × </a>选择品牌类型 </h2><div class= "pop_pub-main"><p> 为了更好、更精确的服务您，请选择您的品牌类型！ </p>'
					        +'<p><a href= "javascript:;" class= "pc" onclick= "set_usertype(1);"> 个人 </a><a href= "javascript:;" class= "org" onclick= "set_usertype(2);"> 企业/机构/网站 </a></p></div></div>');
			$('.mes_bar').height($(document).height()).show();
			$('#select_usertype').show();
			$("#select_usertype h2 a").click(function(){
				$('#select_usertype, .mes_bar').hide();
			});
		}
	}, 'json');


}

// 发送验证邮件
function send_verify_email()
{
	$.post(SITE_URL+'member/profile/sendverifyemail',{},function(response){
		if (response.error_code) {
			if (response.error_code == 10021) {
				window.location.href = response.email_url;
			} else {
				infotips(response.msg);
				return false;
			}

		} else {
			//infotips('验证邮箱发送成功，请到您的邮箱里验证', $('.submit_tips'), 'right');
			  // setTimeout(function() {
                   window.location.href = response.email_url;
              //}, 1000);
		}
	}, 'json');
}

// 设置会员类型
function set_usertype(usertype)
{
	$.post(SITE_URL+'member/profile/setusertype',{"usertype":usertype},function(response){
		if (response.error_code) {
			infotips(response.msg);
			return false;
		} else {
			$('.mes_bar, #select_usertype').hide();
		}
	}, 'json');
}

function initPagination(target, num_entries, pre_page, pageSelectCallBack)
{
	$(target).pagination(num_entries, {
    		num_edge_entries: 1,
    		num_display_entries: 4, //主体页数
    		callback: pageSelectCallBack,
    		items_per_page: pre_page, //每页显示1项
    		prev_text: "上一页",
    		next_text: "下一页",
    		first_init: false
	});

	return true;
}


// 显示内容库
function show_library(tid)
{
	$.get(SITE_URL+'information/weibo/library', {tid:tid}, function(response){
		$('#content_list').html(response);
	});
}

function show_picture(tid, tab, page)
{
	$.get(SITE_URL+'information/weibo/picture', {tid:tid, tab:tab, page: page}, function(response){
		$('#content_list').html(response).show();
        cookie('picture_tab', tab);
        cookie('picture_tid', tid);
	});
	return false;
}

// 关闭内容库/图片库
function close_library()
{
	$('#content_list').html(content_html);
}

function upload_weibo_image()
{
	$.ajaxFileUpload({
          url:SITE_URL + 'information/article/doupload/',	//需要链接到服务器地址
          secureuri:false,
          fileElementId:'uploadpic',				//文件选择框的id属性
          dataType: 'json',
          success: function (data, status) {
		    	var show_name = cutstr(data.title, 6, 'suffix');
		    	$("#upload_preview_img").html('<span>' + show_name + '</span><a onclick="upload_weibo_cancel();return false;" href="javascript:;" title="删除" class="close">X</a><span class="pic_box"><img src="' + PIC_URL + 'attachment/temp/' + data.title + '?random=' + Math.random() + '" width="150" /></span>').show();
		        $("#image_name").val(data.title);
		        $("#image_partpath").val('');
		        $(".pic_name span,.pic_name span.pic_box").mouseenter(function(){
		        	$(".pic_name span.pic_box").show();
		        }).mouseleave(function(){
		        	$(".pic_name span.pic_box").hide();
		        });
				// 重新调用 change
    	  		$("#uploadpic").change(function(){
					upload_weibo_image();
				});
          }
    });
}


function upload_weibo_cancel()
{
	$("#upload_preview_img").html('').hide();
    $("#image_name, #image_partpath").val('');
}

function weibo_check_input()
{
	var len = strlen($("#weibo_content").val());
	var num = (280 - len) / 2;;
	if (num < 0) {
		text = '已超出 <em style="color:red;">' + Math.ceil(Math.abs(num)) + '</em> 个字';
	} else {
		text = '还能输入<em>' + Math.floor(num) + '</em> 个字';
	}
	$("#weibo_tips").html(text);
}

//计算内容长度
function strlen(a)
{
    var b = 0;
	for (var i=0; i < a.length; i++)
	{
		if ((a.charCodeAt(i) < 0) || (a.charCodeAt(i) > 255))
		{
			b = b + 2;
		}
		else
		{
			b = b + 1;
		}
	}
	return b;
}

function loopBackground(id, num, total)
{
	if (total) {
		if (num > 0) {
			$("#" + id).css("background", "#FFBDBF");
			setTimeout('loopBackground(\'' + id + '\', ' + (num - 1)+ ', ' + (total- 1)+ ')', 200);
		} else {
			$("#" + id).css("background", "#FFFFFF");
			setTimeout('loopBackground(\'' + id + '\', ' + (num + 1)+ ', ' + (total- 1)+ ')', 200);
		}
	}
}

function resend_weibo(cid, type, username)
{
	$.post(SITE_URL+'information/article/resend', {cid:cid, type:type, bind_username:username}, function(data){
		$(".status_pop, .mes_bar").hide();
		if(data.error_code){
			$(".essay_pop, .tb_time, .mes_bar").hide();
			infotips(data.msg, $('.submit_tips') );
		}else{
			infotips('发送成功', $('.submit_tips'), 'right');
		}
	}, 'json');
	return false;
}

//添加账号绑定
function add_bind(type, app, jump)
{
	var jump = $("#jump").val();// ? jump : 'bind';
    $.get('/member/bind/geturl', {"type":type, "app":app, "jump":jump}, function(url) {
       window.location.href = url;
    });
}

$.dialoga = {
    open: function(options) {
        this.options = {
            width: 620,
            height: 340,
            title: '',
            modal: true,
            resizable: false,
            is_url: false,
            content: '',
            buttons: false,
			draggable:true
        };

        var options = $.extend(this.options, options);

        var callback = function(data) {
            $("#dialog-modal").html(data);
        };

        $("#dialog-modal").dialog({
            modal: options.modal,
            resizable: options.resizable,
            bgiframe: true,
            width: options.width,
            height: options.height,
            title: options.title,
            buttons: options.buttons,
            closeOnEscape: options.closeOnEscape,
			draggable:options.draggable
        });

        if (options.is_url) {
	        $.ajax({
	            url: options.content,
	            success: function(html) {
	                callback(html);
	            }
	        });
        } else {
        	callback(options.content);
        }

        return false;
    }
};

var insert_url = function(btn, textarea, callback,drag){
    $(btn).live('click', function(){
        $.dialoga.open({
            height: 260,
            width: 420,
            content: '<div class="url_pop"><div class="url">标题：<input type="text" id="insert_title" class="txt"></div><div class="url">链接：<input type="text" id="insert_url" class="txt" value="http://"></div></div>',
            title: '插入链接',
            modal:false,
			draggable:drag=='nodrag' ? false:true,
            buttons: {
                '插入': function() {
                    var url = $.trim($("#insert_url").val());
                    var title = $.trim($("#insert_title").val());

                    if(url.length <= 7){
                        infotips('url格式不正确');
                    }else{
                    	var new_value = $(textarea).val() + "[url"+ ( title.length > 0 ? ' title="'+title+'"' : '' ) +"]" + url + "[/url]";
                        $(textarea).val(new_value);
                        $( this ).dialog("close");
                        if(callback) callback(new_value);
                    }
                },
                '取消': function() {
                    $( this ).dialog( "close" );
                }
            }
        });
    });
}

/**      
 * 对Date的扩展，将 Date 转化为指定格式的String      
 * 月(M)、日(d)、12小时(h)、24小时(H)、分(m)、秒(s)、周(E)、季度(q) 可以用 1-2 个占位符      
 * 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)      
 * eg:      
 * (new Date()).pattern("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423      
 * (new Date()).pattern("yyyy-MM-dd E HH:mm:ss") ==> 2009-03-10 二 20:09:04      
 * (new Date()).pattern("yyyy-MM-dd EE hh:mm:ss") ==> 2009-03-10 周二 08:09:04      
 * (new Date()).pattern("yyyy-MM-dd EEE hh:mm:ss") ==> 2009-03-10 星期二 08:09:04      
 * (new Date()).pattern("yyyy-M-d h:m:s.S") ==> 2006-7-2 8:9:4.18      
 */        
Date.prototype.pattern=function(fmt) {         
    var o = {         
        "M+" : this.getMonth()+1, //月份         
        "d+" : this.getDate(), //日         
        "h+" : this.getHours()%12 == 0 ? 12 : this.getHours()%12, //小时         
        "H+" : this.getHours(), //小时         
        "m+" : this.getMinutes(), //分         
        "s+" : this.getSeconds(), //秒         
        "q+" : Math.floor((this.getMonth()+3)/3), //季度         
        "S" : this.getMilliseconds() //毫秒         
    }; 
    var week = {         
    "0" : "/u65e5",         
    "1" : "/u4e00",         
    "2" : "/u4e8c",         
    "3" : "/u4e09",         
    "4" : "/u56db",         
    "5" : "/u4e94",         
    "6" : "/u516d"        
    };         
    if(/(y+)/.test(fmt)){         
        fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));         
    }         
    if(/(E+)/.test(fmt)){         
        fmt=fmt.replace(RegExp.$1, ((RegExp.$1.length>1) ? (RegExp.$1.length>2 ? "/u661f/u671f" : "/u5468") : "")+week[this.getDay()+""]);         
    }         
    for(var k in o){         
        if(new RegExp("("+ k +")").test(fmt)){         
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));         
        }         
    }         
    return fmt;         
}

function fitscreen() {
	//帮助中心title全屏背景
	if($('.b_color').size()&&$('.indexout').size()){$('.b_color').css({height:$(document).height(),width:$('.indexout').offset().left})};
	//$('.indexout').css('min-height',$(document).height());
	if($('.tils_top').size()){$('.tils_top').css({left:$('.indexout').offset().left,top:$('.indexout').offset().top-2,width:$(window).width()-$('.indexout').offset().left})};
	var w = document.body.clientWidth;
	if (w<1094){
		$('.tongbu_link').hide();
		$('.navout').addClass('navoutA');
		$('button.act_list').live('click',function(){
		if($('button.act_list').html()=='收缩'){
			$('.indexs').width('17%');
			$('.index').width('83%');
			$(this).html('展开').width('40%');
			$('.note .tdate input').css({width:'63px',padding:'0 6px'}).prev('i').hide().closest('.datepick').css({margin:'10px 0 0'});
			$('#page a:not(".prev,.next")').hide();
			$('.tongbu,#article_list em').hide();
		}else{
			$('.indexs').width('36%');
			$('.index').width('64%');
			$(this).html('收缩').width('20%');
			$('.note .tdate input').removeAttr('style').prev('i').show().closest('.datepick').css({margin:'0'});
			$('#page a').show();
			$('#article_list em').show();
		}});

	} else if (w >= 1094) {
		if(w<1230&&$('button.act_list').html()=='展开'){$('.note .tdate input').css({width:'63px',padding:'0 6px'}).prev('i').hide().closest('.datepick').css({margin:'10px 0 0'});}
		$('.navout').removeClass('navoutA');
		$('button.act_list').live('click',function(){
		if($('button.act_list').html()=='收缩'){
			$('.navout').addClass('navoutA');
			$('.indexs').width('17%');
			$('.index').width('83%');
			$(this).html('展开').width('40%');
			$('.note .tdate input').closest('.datepick').css({margin:'10px 0 0'});
			$('#page a:not(".prev,.next")').hide();
			$('.tongbu_link,.tongbu,#article_list em').hide();
		}else{
			$('.navout').removeClass('navoutA');
			$('.indexs').width('36%');
			$('.index').width('64%');
			$(this).html('收缩').width('20%');
			$('.note .tdate input').removeAttr('style').prev('i').show().closest('.datepick').css({margin:'0'});
			$('#page a').show();
			$('.tongbu_link,#article_list em').show();
		}});

	}
}

/*
 * 提示
 * msg : 提示信息
 * obj : 附属目标
 * ty : 提示类型 rigth 正确 error 错误
 * direction : 插入目标方向 insertBefore
 * width : 宽度
 **/
function infotips(msg, obj, ty, direction, width) {
	var w = document.body.clientWidth;
	var width = width ? width : 500;
	if(ty == '') ty = 'right';

	if(typeof direction == 'undefined'){ direction = 'insertbefore'; }
	if($("#infotips").length > 0) $("#infotips").remove();

	var infodivobj = $('<div id="infotips" class="pp_point"></div>');
	infodivobj.appendTo($("body")).html(msg);
	var pot = $("#infotips").innerWidth();
	$("#infotips").css("margin-left",-pot/2).animate({top:'-37px'},150,function(){$(this).animate({top:'-48px'},600,function(){$(this).animate({top:'-48px'},3000,function(){$(this).animate({top:'-37px'},150,function(){$(this).animate({top:'-95px'},600,function(){$(this).css("display","none")})})})})});
    switch (ty) {
		case 'success':
		case 'right':
			$(".pp_point").addClass("send_success").removeClass("send_failure");
		break;

		case 'error':
		default :
			$(".pp_point").addClass("send_failure").removeClass("send_success");
		break;
	}
}