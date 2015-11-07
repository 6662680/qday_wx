showLoading();
/**
 * @author CIN
 * 
 * @function request -- get data in ajax ( base )
 * @function showLoading -- you can config showing content by param { theme, msgtext, textvisible, textonly, html } ( base )
 * @function hideLoading -- hideLoading ( base )
 * @function getUrlParam -- get the value of url name ( base )
 * 
 * 
 * 
 * 
 * 
 * 
 */

/**
 * @return null
 * @param params 提交参数
 * @param isAsync 是否异步
 * @param callback 请求成功时，回调的函数
 */
function request(params, isAsync, callback) {//封装与后台交互的方法，即将前台数据传到后台
	var _url = getRootPath()+'/';
  var _type = "post";
  var _data = "";
  if(params.hasOwnProperty("_url")){
    _url = _url + params["_url"];
    delete params._url;
  }
  if(params.hasOwnProperty("_type")){
    _type = params["_type"];
    delete params._type;
  }
  $.ajax({
		type: _type,
		url: _url,
		dataType: "json",
		data: {"data":params},
		async: isAsync,
		beforeSend: function(){
			if(isAsync){
				showLoading();
			}
		},
		success: callback,
		complete: function(){
			if(isAsync){
				hideLoading();
			}
		}
	});
}
function getRootPath(){
    //获取当前网址，如： http://localhost:8083/uimcardprj/share/meun.jsp
    var curWwwPath=window.document.location.href;
    //获取主机地址之后的目录，如： uimcardprj/share/meun.jsp
    var pathName=window.document.location.pathname;
    var pos=curWwwPath.indexOf(pathName);
    //获取主机地址，如： http://localhost:8083
    var localhostPaht=curWwwPath.substring(0,pos);
    //获取带"/"的项目名，如：/uimcardprj
    var projectName=pathName.substring(0,pathName.substr(1).indexOf('/')+1);
    return(localhostPaht+projectName);
}
function showLoading(config){
	if(!config)
		config = {};
	var theme = ifNull(config["theme"],"b"),
    msgText = ifNull(config["msgtext"],"加载中..."),
    textVisible = ifNull(config["textvisible"],true),
    textonly = ifNull(config["textonly"],false);
    html = ifNull(config["html"],"");
	$.mobile.loading( "show", {
		text: msgText,
		textVisible: textVisible,
		theme: theme,
		textonly: textonly,
		html: html
	});
}
function hideLoading(){
	$.mobile.loading( "hide" );
}
/**
 * @return string 返回url参数的值
 * @param name url参数名称
 * @param default_ 默认值
 */
function getUrlParam(name,default_){ //得到地址指定参数的值
	default_=(default_==null)?"":default_;
	var reg = new RegExp("(^|&)"+ name.toLocaleLowerCase() +"=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
	var r = window.location.search.substr(1).toLocaleLowerCase().match(reg);  //匹配目标参数
	if (r!=null) return decodeURI(r[2]); return default_; //返回参数值
}
function valueIsEmpty(val){
	return (val === null ||val === "");
}
function ifNull(val,replace){
	var returnStr = "";
	if(replace)
		returnStr = replace;
	return (val === null ||val === "" || typeof(val) == "undefined")?returnStr:val;
}
function err_page( msg , callback){
	hideLoading();
	$("#content").empty();
	$("#content").append($("<p/>").addClass("ui-body ui-body-a ui-corner-all center").html( msg ? msg : "页面错误" ));
	$(".header h1").text( "页面错误" );
	if(callback) callback();
}
function err_page_popup( msg , callback ){
  hideLoading();
  $(".err_msg").html( msg ? msg : "页面错误" );
  $( "#prompt" ).popup( "open" );
  if(callback) callback();
}
function checkDataStatus(data, code){
  if(code)
    return (data["retCode"] == code);
  return (data["retCode"] == "200");
}

function GetRequestParam() {
	var url = location.search;//获取url中"?"符后的字串
	var theRequest = new Object();
	if (url.indexOf("?") != -1) {
		var str = url.substr(1);
		strs = str.split("&");
		for ( var i = 0; i < strs.length; i++) {
			theRequest[strs[i].split("=")[0]] =decodeURI(strs[i].split("=")[1]);
		}
	}
	return theRequest;
}