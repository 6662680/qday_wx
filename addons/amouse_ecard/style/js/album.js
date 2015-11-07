$(function() {
	var winH = $(window).height();

	var $appContent = $(".app-content"),
		$appPage = $appContent.find(".app-page"),
		$curPage = $appPage.filter('.current'),
		$coverItem = $appContent.find(".photo-item");
	
	//封面
	$coverItem.css({
		"max-height": (winH-150)+"px"
	});

	// 查看大图
	var $picsPage = $(".picsPage");

	var $slideLeftBtn = $(".picsPage-slideLeft"), 
		$slideRightBtn = $(".picsPage-slideRight");
	
	function photosScroll(){
		var $picsPageBox = $picsPage.find(".picsPage-box");
		var sLeng = $picsPageBox.length;// 图片总共有多少个
		
		$picsPageBox.height(winH).css("line-height",winH+"px");
		$(".js-total").text(sLeng);
		
		$picsPageBox.each(function(i){
			if(i>2){
				$picsPageBox.eq(i).addClass("normal").css({
					"z-index": 100-i
				});
			}
		});
		
		// 按屏滚动
		var scount = 0; // 滚动计数

		function scrollPic(s, dir) {
			
			if(s>1){
				$picsPageBox.eq(s).css({
					"transform": "rotateZ(0deg) scale(1) translate(0%,0)"
				}).show();
				$picsPageBox.eq(s+1).css({
					"transform": "rotateZ(1.6deg) scale(1) translate(0%,0)"
				}).show();
				$picsPageBox.eq(s+2).css({
					"transform": "rotateZ(3.2deg) scale(1) translate(0%,0)"
				}).show();
			}
			
			if(s==0){
				$picsPageBox.css({
					"transform" : "translateX(0)",
					"opacity": 1
				});
				$slideLeftBtn.hide();
				$slideRightBtn.show();
				return false;
			}
			if(dir=="left"){
				$picsPageBox.eq(s-1).css({
					"transform" : "translateX(-680px)",
					"opacity": 0
				});
			}else{
				$picsPageBox.eq(s).css({
					"transform" : "translateX(0)",
					"opacity": 1
				});
			}

			if (s > 0 && s < sLeng) {
				$slideRightBtn.show();
				$slideLeftBtn.show();
			}
			
		}

		function scrollDer(derection) {
			if (derection == "left") {
				if (scount >= sLeng - 1) {
					scount = -1;
				}
				scount++;
				scrollPic(scount, "left");
				$(".js-num").text(scount + 1);
			} else {
				if (scount == 0) {
					return false;
				}
				scount--;
				scrollPic(scount, "right");
				$(".js-num").text(scount + 1);
			}
		}

		var startPosi = 0, endPosi = 0;
		$picsPageBox.on({
			"touchstart" : function(e) {
				// e.preventDefault();
				startPosi = e.originalEvent.targetTouches[0].pageX;
			},
			"touchmove" : function(e) {
				e.preventDefault();
			},
			"touchend" : function(e) {
				endPosi = e.originalEvent.changedTouches[0].pageX;
				if (endPosi - startPosi > 10) {
					scrollDer("right");
				} else if (startPosi - endPosi > 10) {
					scrollDer("left");
				}
			}
		});
		
		$slideLeftBtn.click(function(e) {
			scrollDer("right");
		});
		$slideRightBtn.click(function(e) {
			scrollDer("left");
		});
	}

	// 加载图片
	function photosLi(obj) {
		var urls = $.trim(obj.data("url"));
		urls = urls.substr(0, urls.length - 1);
		urls = urls.split(",");
		urlsLen = urls.length;
		var plist = "";
		for ( var i = 0; i < urlsLen; i++) {
			plist += '<div class="picsPage-box"><div class="picsPage-box-pic"><img src="'+urls[i]+'"></div></div>';
		}
		$picsPage.html(plist);
		photosScroll();
		
		$(".js-pics .picTit").text(obj.find(".photo-title").text());
		if(urlsLen>1){
			$slideRightBtn.show();
		}
	}
	
	$(".js-photo").click(function(e) {
		photosLi($(this));
		$(".js-pics").addClass("showPage");
		$.flytip("←左右滑动查看图片→");
	});
	$(".js-back").click(function(e) {
		$(".js-pics").removeClass("showPage");
	});
	
	$(".js-photoBox").each(function(){
		var $this = $(this);
		var $photoNums = $this.find(".photoNums");
		var urls = $.trim($this.find(".js-photo").data("url"));
		var len = urls.substr(0, urls.length - 1).split(",").length;
		if(urls){
			$photoNums.text(len);
		}else{
			$this.hide();
		}
		
	});
});