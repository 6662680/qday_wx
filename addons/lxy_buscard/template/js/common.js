jQuery.extend({   
	/**  
	 * 清除当前选择内容  
	 */  
	unselectContents: function(){   
		if(window.getSelection)   
			window.getSelection().removeAllRanges();   
		else if(document.selection)   
			document.selection.empty();   
	}
});
jQuery.fn.extend({
	/**  
	 * 选中内容  
	 */  
	selectContents: function(){   
		$(this).each(function(i){   
			var node = this;   
			var selection, range, doc, win;   
			if ((doc = node.ownerDocument) &&   
				(win = doc.defaultView) &&   
				typeof win.getSelection != 'undefined' &&   
				typeof doc.createRange != 'undefined' &&   
				(selection = window.getSelection()) &&   
				typeof selection.removeAllRanges != 'undefined')   
			{   
				range = doc.createRange();   
				range.selectNode(node);   
				if(i == 0){   
					selection.removeAllRanges();   
				}   
				selection.addRange(range);   
			}   
			else if (document.body &&   
					 typeof document.body.createTextRange != 'undefined' &&   
					 (range = document.body.createTextRange()))   
			{   
				range.moveToElementText(node);   
				range.select();   
			}   
		});   
	},   
	/**  
	 * 初始化对象以支持光标处插入内容  
	 */  
	setCaret: function(){   
		if(!$.browser.msie) return;   
		var initSetCaret = function(){   
			var textObj = $(this).get(0);   
			textObj.caretPos = document.selection.createRange().duplicate();   
		};   
		$(this)   
		.click(initSetCaret)   
		.select(initSetCaret)   
		.keyup(initSetCaret);   
	},   
	/**  
	 * 在当前对象光标处插入指定的内容  
	 */  
	insertAtCaret: function(textFeildValue){   
	   var textObj = $(this).get(0);   
	   if(document.all && textObj.createTextRange && textObj.caretPos){   
		   var caretPos=textObj.caretPos;   
		   caretPos.text = caretPos.text.charAt(caretPos.text.length-1) == '' ?   
							   textFeildValue+'' : textFeildValue;   
	   }   
	   else if(textObj.setSelectionRange){   
		   var rangeStart=textObj.selectionStart;   
		   var rangeEnd=textObj.selectionEnd;   
		   var tempStr1=textObj.value.substring(0,rangeStart);   
		   var tempStr2=textObj.value.substring(rangeEnd);   
		   textObj.value=tempStr1+textFeildValue+tempStr2;   
		   textObj.focus();   
		   var len=textFeildValue.length;   
		   textObj.setSelectionRange(rangeStart+len,rangeStart+len);   
		   textObj.blur();   
	   }   
	   else {   
		   textObj.value+=textFeildValue;   
	   }   
	}   
}); 

var cookie= {
	'prefix' : '',
	// 保存 Cookie
	'set' : function(name, value, seconds) {
		expires = new Date();
		expires.setTime(expires.getTime() + (1000 * seconds));
		document.cookie = this.name(name) + "=" + escape(value) + "; expires=" + expires.toGMTString() + "; path=/";
	},
	// 获取 Cookie
	'get' : function(name) {
		cookie_name = this.name(name) + "=";
		cookie_length = document.cookie.length;
		cookie_begin = 0;
		while (cookie_begin < cookie_length)
		{
			value_begin = cookie_begin + cookie_name.length;
			if (document.cookie.substring(cookie_begin, value_begin) == cookie_name)
			{
				var value_end = document.cookie.indexOf ( ";", value_begin);
				if (value_end == -1)
				{
					value_end = cookie_length;
				}
				return unescape(document.cookie.substring(value_begin, value_end));
			}
			cookie_begin = document.cookie.indexOf ( " ", cookie_begin) + 1;
			if (cookie_begin == 0)
			{
				break;
			}
		}
		return null;
	},
	// 清除 Cookie
	'del' : function(name) {
		var expireNow = new Date();
		document.cookie = this.name(name) + "=" + "; expires=Thu, 01-Jan-70 00:00:01 GMT" + "; path=/";
	},
	'name' : function(name) {
		return this.prefix + name;
	}
};

function message(msg, redirect, type) {
	
	if (parent == window) {
		 _message(msg, redirect, type);
	} else {
		parent.message(msg, redirect, type);
	}
	function _message(msg, redirect, type) {
		var modalobj = $('#modal-message');
		if(modalobj.length == 0) {
			$(document.body).append('<div id="modal-message" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true"></div>');
			var modalobj = $('#modal-message');
		}
		if($.inArray(type, ['success', 'error', 'tips']) == -1) {
			type = '';
		}
		if(type == '') {
			type = redirect == '' ? 'error' : 'success';
		}
		html = '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="myModalLabel">系统提示</h3></div>' +
				'<div class="modal-body"><i class="icon-ok icon-large icon-3x pull-left"></i><div class="pull-left"><p>'+ msg +'</p>' +
				(redirect ? '<p><a href="' + redirect + '" target="main" data-dismiss="modal" aria-hidden="true">如果你的浏览器在<span id="timeout"></span>秒后没有自动跳转，请点击此链接</a></p>' : (redirect == 'back' ? '<p>[<a href="javascript:;" onclick="history.go(-1)">返回上一页</a>] &nbsp; [<a href="./?refresh">回首页</a>]</p></div></div>' : ''));
		modalobj.html(html);
		
		modalobj.addClass('alert alert-'+type);
		if(redirect) {
			var timer = '';
			timeout = 3;
			modalobj.find("#timeout").html(timeout);
			modalobj.on('shown', function(){doredirect();});
			modalobj.on('hide', function(){timeout = 0;doredirect(); });
			modalobj.on('hidden', function(){modalobj.remove();});
			function doredirect() {
				timer = setTimeout(function(){
					if (timeout <= 0) {
						modalobj.modal('hide');
						clearTimeout(timer);
						window.frames['main'].location.href = redirect;
						return;
					} else {
						timeout--;
						modalobj.find("#timeout").html(timeout);
						doredirect();
					}
				}, 1000);
			}
		}
		return modalobj.modal();
	}
}
/*
	请求远程地址
*/
function ajaxopen(url, callback) {
	$.getJSON(url+'&time='+new Date().getTime(), function(data){
		if (data.type == 'error') {
			message(data.message, data.redirect, data.type);
		} else {
			if (typeof callback == 'function') {
				callback(data.message, data.redirect, data.type);
			} else if(data.redirect) {
				location.href = data.redirect;	
			}
		}
	});	
	return false;
}
/*
	打开远程地址
	@params string url 目标远程地址
	@params string title 打开窗口标题，为空则不显示标题。可在返回的HTML定义<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>控制关闭
	@params object options 打开窗口的属性配置，可选项backdrop,show,keyboard,remote,width,height。具体参考bootcss模态对话框的options说明
	@params object events 窗口的一些回调事件，可选项show,shown,hide,hidden,confirm。回调函数第一个参数对话框JQ对象。具体参考bootcss模态对话框的on说明.

	@demo ajaxshow('url', 'title', {'show' : true}, {'hidden' : function(obj) {obj.remove();}});
*/
function ajaxshow(url, title, options, events) {
	var modalobj = $('#modal-message');
	var defaultoptions = {'remote' : url, 'show' : true};
	var defaultevents = {};
	var option = $.extend({}, defaultoptions, options);
	var events = $.extend({}, defaultevents, events);

	if(modalobj.length == 0) {
		$(document.body).append('<div id="modal-message" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" style="position:absolute;"></div>');
		var modalobj = $('#modal-message');
	}
	html = (typeof title != 'undefined' ? '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="myModalLabel">'+title+'</h3></div>' : '') +
			'<div class="modal-body"></div>' +
			'<div class="modal-footer">'+(typeof events['confirm'] == 'function' ? '<a href="#" class="btn btn-primary confirm">确定</a>' : '') + '<a href="#" class="btn" data-dismiss="modal" aria-hidden="true">关闭</a></div>';
	
	modalobj.html(html);
	if (typeof option['width'] != 'undeinfed' && option['width'] > 0) {
		modalobj.css({'width' : option['width'], 'marginLeft' : 0 - option['width'] / 2});
	}
	if (typeof option['height'] != 'undeinfed' && option['height'] > 0) {
		modalobj.find('.modal-body').css({'max-height' : option['height']});
	}
	if (events) {
		for (i in events) {
			if (typeof events[i] == 'function') {
				modalobj.on(i, events[i]);
			}
		}
	}
    modalobj.on('hidden', function(){modalobj.remove();});
	if (typeof events['confirm'] == 'function') {
		modalobj.find('.confirm', modalobj).on('click', events['confirm']);
	}
	return modalobj.modal(option);
}

function pager(url, page) {
	$.get(url+'&page='+page+'&time='+new Date().getTime(), function(data){
		if (data.type == 'error') {
			message(data.message, data.redirect, data.type);
		} else {
			ajaxpager(data);
		}
	});	
}

function ajaxpager(html) {
	$('#pager-content').html(html);
}

/*
	根据html数据创建一个ITEM节点
*/
function buildAddForm(id, targetwrap) {
	var sourceobj = $('#' + id);
	var html = $('<div class="item">');
	id = id.split('-')[0];
	var size = $('.item').size();
	var htmlid = id + '-item-' + size;
	while (targetwrap.find('#' + htmlid).size() >= 1) {
		var htmlid = id + '-item-' + size++;
	}
	html.html(sourceobj.html().replace(/\(itemid\)/gm, htmlid));
	html.attr('id', htmlid);
	targetwrap.append(html);
	return html;
}
/*
	切换一个节点的编辑状态和显示状态
*/
function doEditItem(itemid) {
	$('#append-list .item').each(function(){
		$('#form', $(this)).css('display', 'none');
		$('#show', $(this)).css('display', 'block');		
	});
	var parent = $('#' + itemid);
	$('#form', parent).css('display', 'block');
	$('#show', parent).css('display', 'none');	
}

function doDeleteItem(itemid, deleteurl) {
	if (confirm('删除操作不可恢复，确认删除吗？')){
		if (deleteurl) {
			ajaxopen(deleteurl, function(){
				$('#' + itemid).remove();
			});
		} else {
			$('#' + itemid).remove();
		}	
	}
	return false;
}

function doDeleteItemImage(obj, id) {
	var filename = $(obj).parent().parent().find('#' + id).val();
	ajaxopen('./center.php?act=attachment&do=delete&filename=' + filename, function(){
		$(obj).parent().parent().find('#upload-file-view').html('');
	});
	return false;
}

function ignoreSpaces(string) {
	var temp = "";
	string = '' + string;
	splitstring = string.split(" ");
	for(i = 0; i < splitstring.length; i++)
	temp += splitstring[i];
	return temp;
}

//初始化kindeditor编辑器
function kindeditor(selector) {
	var selector = selector ? selector : 'textarea[class="richtext"]';
	var option = {
		basePath : './resource/script/kindeditor/',
		themeType : 'simple',
		langType : 'zh_CN',
		uploadJson : './center.php?act=attachment&do=upload',
		resizeType : 1,
		allowImageUpload : true,
		items : [
			'undo', 'redo', '|', 'formatblock', 'fontname', 'fontsize', '|', 
			'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', 'strikethrough', '|', 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', '|',
			'image', 'multiimage', 'table', 'hr', 'emoticons', 'link', 'unlink', '|',
			'preview', 'plainpaste', '|', 'removeformat','source', 'fullscreen'
		]
	}
	if (typeof KindEditor == 'undefined') {
		$.getScript('./resource/script/kindeditor/kindeditor-min.js', function(){initKindeditor(selector, option)});
	} else {
		initKindeditor(selector, option);
	}
	function initKindeditor(selector, option) {
		var editor = KindEditor.create(selector, option);
	}
}

function kindeditorUploadBtn(obj) {
	if (typeof KindEditor == 'undefined') {
		$.getScript('./resource/script/kindeditor/kindeditor-min.js', initUploader);
	} else {
		initUploader();
	}
	function initUploader() {
		var uploadbutton = KindEditor.uploadbutton({
			button : obj,
			fieldName : 'imgFile',
			url : './center.php?act=attachment&do=upload',
			width : 100,
			afterUpload : function(data) {
				if (data.error === 0) {
					var url = KindEditor.formatUrl(data.url, 'absolute');
					$(uploadbutton.div.parent().parent()[0]).find('#upload-file-view').html('<input value="'+data.filename+'" type="hidden" name="'+obj.attr('fieldname')+'" id="'+obj.attr('id')+'-value" /><img src="'+url+'" width="100" />');
					$(uploadbutton.div.parent().parent()[0]).find('#upload-file-view').addClass('upload-view');
					$(uploadbutton.div.parent().parent()[0]).find('#upload-delete').show();
				} else {
					message('上传失败，错误信息：'+data.message);
				}
			},
			afterError : function(str) {
				message('上传失败，错误信息：'+str);
			}
		});	
		uploadbutton.fileBox.change(function(e) {
			uploadbutton.submit();
		});
	}
}

function fetchChildCategory(cid) {
	var html = '<option value="0">请选择二级分类</option>';
	if (!category || !category[cid]) {
		$('#cate_2').html(html);
		return false;
	}
	for (i in category[cid]) {
		html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
	}
	$('#cate_2').html(html);
}

function closetips() {
	$('#we7_tips').slideUp(100);
	cookie.set('we7_tips', '0', 4*3600);
}

function selectall(obj, name){
	$('input[name="'+name+'[]"]:checkbox').each(function() {
		$(this).attr("checked", $(obj).attr('checked') ? true : false);
	});
}

function tokenGen() {
	var letters = 'abcdefghijklmnopqrstuvwxyz0123456789';
	var token = '';
	for(var i = 0; i < 32; i++) {
		var j = parseInt(Math.random() * (31 + 1));
		token += letters[j];
	}
	$(':text[name="wetoken"]').val(token);
}
//弹出层
var register = '<div class="online_content"><div style="font-size:16px;; line-height:24px; padding:5px;">';
register+="<p>联系客服申请&nbsp;<span class='recom'>推荐</span></p><p><a href='http://wpa.qq.com/msgrd?v=3&uin=1153468993&site=qq&menu=yes' target='_blank' class='qq_icon'>客服咨询</a><a href='http://wpa.qq.com/msgrd?v=3&uin=195536580&site=qq&menu=yes' target='_blank' class='qq_icon'>客服咨询</a></p>"+
		"<p>拨打热线申请&nbsp;<span class='recom'>推荐</span></p><div class='phone'><p>020-38357076 38357327</p><p>020-38357076 38357327</p><p>020-38357076 38357327</p></div>";
register+='</div></div>';

var neirong ="<div style='width:343px;'><div style='height:30px;background:#fcae21;line-height:30px;'><span class='close_box' id='close_box_1'>X</span></div><div class='pay_content'>"+
"<div class='pay_notice' >请选择您的付款方式<br />完成付款。</div><p>付款前请不要关闭此窗口。</p><p>根据您的情况点击下面的按钮：</p><div style='text-align:center;margin:10px 0 20px 0;'>"+
        	"<input class='pay_btn' id='close_box_2' type='button' value='立即订购' />&nbsp;&nbsp;<input class='pay_btn' id='transcaction' type='button' value='上门办理' />&nbsp;&nbsp;<input class='pay_btn' id='pay_question' type='button' value='遇到问题' /></div><a href='#' id='close_box_3'>返回选择其它支付方式</a></div></div>";
var frame ="<div style='width:700px;'><iframe width='700px' src='http://localhost/we7/business.php?act=order_list&'></iframe></div>";


function C$(id){return document.getElementById(id);} 
//定义窗体对象 
var cwxbox = {}; 
cwxbox.box = function(){ 
var bg,wd,cn,ow,oh,o = true,time = null; 
return { 
show:function(c,t,w,h){ 
if(o){ 
close_box = document.getElementById('close_box');
bg = document.createElement('div'); bg.id = 'cwxBg'; 
wd = document.createElement('div'); wd.id = 'cwxWd'; 
cn = document.createElement('div'); cn.id = 'cwxCn'; 
document.body.appendChild(bg); 
document.body.appendChild(wd); 
wd.appendChild(cn); 
bg.onclick = cwxbox.box.hide; 
window.onresize = this.init; 
window.onscroll = this.scrolls; 
o = false; 
} 
if(w && h){ 
var inhtml = '<iframe src="'+ c +'" width="'+ w +'" height="'+ h +'" frameborder="0"></iframe>'; 
}else{ 
var inhtml = c; 
} 
cn.innerHTML = inhtml; 
oh = this.getCss(wd,'offsetHeight'); 
ow = this.getCss(wd,'offsetWidth'); 
this.init(); 
this.alpha(bg,50,1); 
this.drag(wd); 
if(t){ 
time = setTimeout(function(){cwxbox.box.hide()},t*1000); 
} 
}, 
hide:function(){ 
cwxbox.box.alpha(wd,0,-1); 
clearTimeout(time); 
}, 
init:function(){ 
bg.style.height = cwxbox.page.total(1)+'px'; 
bg.style.width = ''; 
bg.style.width = cwxbox.page.total(0)+'px'; 
var h = (cwxbox.page.height() - oh) /2; 
wd.style.top=(h+cwxbox.page.top())+'px'; 
wd.style.left=(cwxbox.page.width() - ow)/2+'px'; 
}, 
scrolls:function(){ 
var h = (cwxbox.page.height() - oh) /2; 
wd.style.top=(h+cwxbox.page.top())+'px'; 
}, 
alpha:function(e,a,d){ 
clearInterval(e.ai); 
if(d==1){ 
e.style.opacity=0; 
e.style.filter='alpha(opacity=0)'; 
e.style.display = 'block'; 
} 
e.ai = setInterval(function(){cwxbox.box.ta(e,a,d)},40); 
}, 
ta:function(e,a,d){ 
var anum = Math.round(e.style.opacity*100); 
if(anum == a){ 
clearInterval(e.ai); 
if(d == -1){ 
e.style.display = 'none'; 
if(e == wd){ 
this.alpha(bg,0,-1); 
} 
}else{ 
if(e == bg){ 
this.alpha(wd,100,1); 
} 
} 
}else{ 
var n = Math.ceil((anum+((a-anum)*.5))); 
n = n == 1 ? 0 : n; 
e.style.opacity=n/100; 
e.style.filter='alpha(opacity='+n+')'; 
} 
}, 
getCss:function(e,n){ 
var e_style = e.currentStyle ? e.currentStyle : window.getComputedStyle(e,null); 
if(e_style.display === 'none'){ 
var clonDom = e.cloneNode(true); 
clonDom.style.cssText = 'position:absolute; display:block; top:-3000px;'; 
document.body.appendChild(clonDom); 
var wh = clonDom[n]; 
clonDom.parentNode.removeChild(clonDom); 
return wh; 
} 
return e[n]; 
}, 
drag:function(e){ 
var startX,startY,mouse; 
mouse = { 
mouseup:function(){ 
if(e.releaseCapture) 
{ 
e.onmousemove=null; 
e.onmouseup=null; 
e.releaseCapture(); 
}else{ 
document.removeEventListener("mousemove",mouse.mousemove,true); 
document.removeEventListener("mouseup",mouse.mouseup,true); 
} 
}, 
mousemove:function(ev){ 
var oEvent = ev||event; 
e.style.left = oEvent.clientX - startX + "px"; 
e.style.top = oEvent.clientY - startY + "px"; 
} 
} 
e.onmousedown = function(ev){ 
var oEvent = ev||event; 
startX = oEvent.clientX - this.offsetLeft; 
startY = oEvent.clientY - this.offsetTop; 
if(e.setCapture) 
{ 
e.onmousemove= mouse.mousemove; 
e.onmouseup= mouse.mouseup; 
e.setCapture(); 
}else{ 
document.addEventListener("mousemove",mouse.mousemove,true); 
document.addEventListener("mouseup",mouse.mouseup,true); 
} 
} 

} 
} 
}() 

cwxbox.page = function(){ 
return{ 
top:function(){return document.documentElement.scrollTop||document.body.scrollTop}, 
width:function(){return self.innerWidth||document.documentElement.clientWidth||document.body.clientWidth}, 
height:function(){return self.innerHeight||document.documentElement.clientHeight||document.body.clientHeight}, 
total:function(d){ 
var b=document.body, e=document.documentElement; 
return d?Math.max(Math.max(b.scrollHeight,e.scrollHeight),Math.max(b.clientHeight,e.clientHeight)): 
Math.max(Math.max(b.scrollWidth,e.scrollWidth),Math.max(b.clientWidth,e.clientWidth)) 
} 
} 
}()

