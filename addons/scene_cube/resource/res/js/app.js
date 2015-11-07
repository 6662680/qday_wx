/* 
 *  云+轻App项目js（页面级）
 * ----------------------------------
 *  作者：Charles
 *  时间：2014-04-01
 *  准则：NEC CSS规范
 *  联系：16295853（qq）
 *****************************************************************************************/

 //前端与webview交互
	//调用移动端接口 JS-->IOS、ANDROID
	function yunlaiWebCallMobileTerminal(type,infoId)
	{
		//IOS
		var app_comment_url = window.location.protocol+'//'+window.location.host+'/yunpai/index/comments/'+type+'-'+infoId;
		var param = 'app_comment_url='+app_comment_url;
		yunlaiWebCallIos(param);

		//ANDROID
		yunlaiWebCallAndroid(app_comment_url);
	}

	//端调用接口 JS-->IOS
	function yunlaiWebCallIos(paramString)
	{
		//url的格式为: yunlai://?
		var url = 'yunlai://?'+paramString;
		var iFrame;
		iFrame = document.createElement("iframe");
		iFrame.setAttribute("src", url);
		iFrame.setAttribute("style", "display:none;");
		iFrame.setAttribute("height", "0px");
		iFrame.setAttribute("width", "0px");
		iFrame.setAttribute("frameborder", "0");
		document.body.appendChild(iFrame);
		// 发起请求后这个iFrame就没用了，所以把它从dom上移除掉
		iFrame.parentNode.removeChild(iFrame);
		iFrame = null;
	}

	//端调用接口 JS-->ANDROID
	function yunlaiWebCallAndroid(app_comment_url)
	{
		try
		{
			if (typeof(window.YunlaiAndroidClass) != "undefined")
			{
				window.YunlaiAndroidClass.yunlaiAndroidAppstoreInfoIdCallback(app_comment_url);
			}
		}
		catch (e)
		{
		}
	}

	//ANDROID端调用接口 ANDROID-->JS
	function yunlaiAndroidCall()
	{
		try
		{
			if (typeof(window.YunlaiAndroidClass) != "undefined")
			{
				window.YunlaiAndroidClass.yunlaiAndroidVarCallback(YUNLAI_TOP_VIEW,YUNLAI_UNSHOW_TOOL);
			}
		}
		catch (e)
		{
		}
	}
	
	
//app脚本
var app = {

	//查看更多页面
	viewMore:{
		//初始化方法
		init: function () {
 			//初始化App列表模块
			this.initAppList();
 			//初始化第三方点击统计
 		},

		//初始化App列表模块
		initAppList: function () {
			//应用滚动数据加载插件
			var pageIndex = 2;
			$('#panel-appList').IScrollLoading({
				//当向上推动结束时
				onPullUpEnd: function ($list, iScrollObj) {
					$.ajax({
						url : document.location.href,
						data : {page : pageIndex},
						type : 'post',
						dataType : 'json',
						success : function (data) {
							$list.find('.u-listTextItem').remove();
							if($list.find('li').length == 0 && data.length == 0){
								$list.append('<li class="u-listTextItem">暂无数据</li>');
							}else if(data.length == 0){
								$list.append('<li class="u-listTextItem">没有更多</li>');
							}else{
								//更新列表
								var itemsHtml = baidu.template('template-appList', {list: data});
								$list.append(itemsHtml);
								//更新iScrollObj
								iScrollObj.refresh();
								//更新下一次请求的页码
								pageIndex ++;
							}
						}
					});
				}
			});
		}
	},
 
};