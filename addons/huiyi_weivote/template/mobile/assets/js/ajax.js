//
//    Main script of Ajax v1.0 Bootstrap Theme
//
"use strict";
/*-------------------------------------------
	Dynamically load plugin scripts
---------------------------------------------*/

function LoadDataDemoScript(callback){
//  function LoadDataDemo(){
//		$.getScript('js/jquery.datatables.min.js', function(){
//			$.getScript('js/datatable-editable.js', callback);
//		});
//	}
//	if (!$.fn.dataDemo){
//		LoadDataDemo();
//	}
//	else {
//		if (callback && typeof(callback) === "function") {
//			callback();
//		}
//	}
}

function LoadDefaultScript(callback){
	if (callback && typeof(callback) === "function") {
        callback();
    }
}

function LoadRegScript(callback){
      
    function LoadReg(){
		$.getScript(mobile_root + 'assets/js/jqBootstrapValidation.js', function(){
		  $.getScript(mobile_root + 'assets/js/bounce.js');
		});
	}
	if (!$.fn.reg){
		LoadReg();
    } else {
		if (callback && typeof(callback) === "function") {
			callback();
		}
	}
}

function LoadResultScript(callback){
    if (callback && typeof(callback) === "function") {
        callback();
    }
}

function LoadVoterScript(callback){
    if (callback && typeof(callback) === "function") {
        callback();
    }
}

function LoadScreenScript(callback){
    if (callback && typeof(callback) === "function") {
        callback();
    }
}

function VoterSearch(voteKey) {

    var aurl = 'ajaxVoterSearch';
    $.ajax({
        mimeType: 'text/html; charset=utf-8',
        url: mobile_url + aurl,
        type: 'POST',
        data: {voteKey: voteKey},
        success: function(adata) {
            if (adata.rcode == 100) {
                _data.weivote_options_view = adata.rdata.weivote_options_view;
                _data.page_switch = "1";
                if (optionOrderDefault && typeof(optionOrderDefault) === "function") {
                    optionOrderDefault('code', 'asc');
                }

                lazyimg();
            } else {
                alertModal(adata.rmsg);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        dataType: "json",
        async: false
    });
    return false;
}

function VoterPage(pageNo) {

    var aurl = 'ajaxVoterPage';
    $.ajax({
        mimeType: 'text/html; charset=utf-8',
        url: mobile_url + aurl,
        type: 'POST',
        data: {pageNo: pageNo},
        success: function(adata) {
            if (adata.rcode == 100) {
                _data.weivote_options_view = adata.rdata.weivote_options_view;

                _data.page_switch = adata.rdata.page_switch;
                _data.page_count = adata.rdata.page_count;
                _data.page_no = adata.rdata.page_no;
                _data.page_start = adata.rdata.page_start;
                _data.page_end = adata.rdata.page_end;
                _data.page_nos = adata.rdata.page_nos;

                if (optionOrderDefault && typeof(optionOrderDefault) === "function") {
                    optionOrderDefault('code', 'asc');
                }

                lazyimg();
            } else {
                alertModal(adata.rmsg);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        dataType: "json",
        async: false
    });
    return false;
}


function VoterSubmit(oid) {

    var aurl = 'ajaxSubmitVoter';
    $.ajax({
        mimeType: 'text/html; charset=utf-8',
        url: mobile_url + aurl,
        type: 'POST',
        data: {oid: oid},
        success: function(adata) {
            //alertDebugModal(adata);
            alertModal(adata.rmsg);
            if (adata.rurl != '') {
                LoadAjaxContent(adata.rurl);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        dataType: "json",
        async: false
    });
    return false;
}

function RegSubmit(realname, mobile, qq, weixinno) {

    var aurl = 'ajaxSubmitReg';
    $.ajax({
        mimeType: 'text/html; charset=utf-8',
        url: mobile_url + aurl,
        type: 'POST',
        data: {realname: realname, mobile: mobile, qq: qq, weixinno: weixinno},
        success: function(adata) {
            alertModal(adata.rmsg);

            if (adata.rurl != '') {
                LoadAjaxContent(adata.rurl);
            }

        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        dataType: "json",
        async: false
    });
    return false;
}

function bubbleSort(arr, typeby , orderby){
    for(var i=0;i<arr.length;i++){
        for(var j=i;j<arr.length;j++){
            if (orderby != null && orderby == 'desc') {
                if (typeby != null && typeby == 'code') {
                    if(parseInt(arr[i].code) < parseInt(arr[j].code)){
                        //交换两个元素的位置
                        var temp=arr[i];
                        arr[i]=arr[j];
                        arr[j]=temp;
                    }
                } else {
                    if(parseInt(arr[i].log_count) < parseInt(arr[j].log_count)){
                        //交换两个元素的位置
                        var temp=arr[i];
                        arr[i]=arr[j];
                        arr[j]=temp;
                    } else if(parseInt(arr[i].log_count) == parseInt(arr[j].log_count)){
                        if(parseInt(arr[i].code) > parseInt(arr[j].code)){
                            //交换两个元素的位置
                            var temp=arr[i];
                            arr[i]=arr[j];
                            arr[j]=temp;
                        }
                    }
                }

            } else {
                if (typeby != null && typeby == 'code') {
                    if(parseInt(arr[i].code) > parseInt(arr[j].code)){
                        //交换两个元素的位置
                        var temp=arr[i];
                        arr[i]=arr[j];
                        arr[j]=temp;
                    }
                } else {
                    if(parseInt(arr[i].log_count) > parseInt(arr[j].log_count)){
                        //交换两个元素的位置
                        var temp=arr[i];
                        arr[i]=arr[j];
                        arr[j]=temp;
                    } else if(parseInt(arr[i].log_count) == parseInt(arr[j].log_count)){
                        if(parseInt(arr[i].code) > parseInt(arr[j].code)){
                            //交换两个元素的位置
                            var temp=arr[i];
                            arr[i]=arr[j];
                            arr[j]=temp;
                        }
                    }
                }
            }

        }
    }
}

function initimg(id, src) {
    require(['jquery', 'util'], function($, util){
        $(function(){
            alert(typeof(id));
            if (typeof(id) == "object") {
                $(id).attr('src', src);
            } else {
                $('#' + id).attr('src', src);
            }
        });
    });
}

//图片延迟加载 <img class="lazy" src="./addons/huiyi_weivote/template/mobile/assets/Lib/echo/images/loading.gif" data-echo="{$item['picture']}">
function lazyimg() {
    Echo.init({
        offset: 0,
        throttle: 0
    });
}

/*-------------------------------------------
	Main scripts used by theme
---------------------------------------------*/
//
//  Function for load content from url and put in $('.ajax-content') block
//
function LoadAjaxContent(url){

    oldLocation = location.href;//防止重复加载
    if (url != null && url != ''  && url != 'ajax/default.html') {
        window.location.hash = url;
    }

	//$('.preloader').show();

    //分析url
    var num = url.indexOf("?");
    var oid = -1;
    if (num != -1) {
        var str = url.substr(num + 1);
        num = str.indexOf("=");
        if (num > 0){
            oid = str.substr(num + 1);
        }
        url = url.substr(0, url.indexOf("?"));
    }

    //标记被选中的Nav项
    var nava = url.substr(url.indexOf('/'));
    var nava = nava.substr(1, nava.indexOf('.') - 1);
    if (nava != null && nava != '') {//排除子页面voter
        if (nava == 'voter') {
            nava = 'default';
        }
        $('.nav li a').removeClass("current");
        $('#nava-' + nava).addClass("current");
    }

	$.ajax({
		mimeType: 'text/html; charset=utf-8', // ! Need set mimeType only when run from local file
		url: mobile_root + url + '?date=' + new Date().getTime(),
		type: 'GET',
		success: function(data) {

            //设定选项id
            var oid_html = '';
            if (oid != -1 && $('#voter-oid')) {
               //$('#voter-oid').val(oid)
               oid_html = '<input type="hidden" id="voter-oid" name="voter-oid" value="' + oid + '">';
            }

			$('#ajax-content').html(oid_html + data);
			//$('.preloader').hide();
		},
		error: function (jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
		},
		dataType: "html",
		async: false
	});
}



//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//
//      MAIN DOCUMENT READY SCRIPT OF DEVOOPS THEME
//
//      In this script main logic of theme
//
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
$(document).ready(function () {
    var ajax_url = location.hash.replace(/^#/, '').replace('#mp.weixin.qq.com', '').replace('mp.weixin.qq.com', '');

    if (ajax_url == 'wechat_redirect' || ajax_url.length < 1) {//微信
		ajax_url = 'ajax/default.html';
	}

	LoadAjaxContent(ajax_url);
    $('body').on('click', 'a', function (e) {
		if ($(this).hasClass('ajax-link')) {
			e.preventDefault();
			if ($(this).hasClass('add-full')) {
				//$('#content').addClass('full-content');
			}
			else {
				//$('#content').removeClass('full-content');
			}
			var url = $(this).attr('href');
            LoadAjaxContent(url);
		}
		if ($(this).attr('href') == '#') {
			e.preventDefault();
		}
	});
});

//监测浏览器url变化
var oldLocation = location.href;
setInterval(function() {

    if(location.href != oldLocation) {
        var url = location.href;
        if (url.indexOf(".html", 0) != -1 || url.indexOf("?oid=", 0) != -1) {
            var ajax_url = location.href.substring(url.lastIndexOf('#') + 1 , url.length);
            oldLocation = location.href;
            LoadAjaxContent(ajax_url);
        } else {
            oldLocation = location.href;
            LoadAjaxContent('ajax/default.html');
        }
    }

}, 499);