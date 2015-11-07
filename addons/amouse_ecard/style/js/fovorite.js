$(function (){
	var winH = $(window).height();
	var $pbH = parseInt((winH-90)/28);
	var $searchInput =  $("#searchInput");
	var $searchLetter =  $(".js-searchLetter");
	var $searchSpan =  $searchLetter.children("span");
	var $searchCancel =  $(".search-cancel");
	var $searchClose =  $(".icon-close");
	var $searchSe =  $(".icon-search");
	var $serchBox = $(".js-serchLetterbox");
	var $favoriteColumn = $("#favoriteColumn");
	var $favoriteItem = $(".favorite-inner-item");
	var $favoriteItemLink = $favoriteItem.children(".favorite-item-link");
	var $favoriteItemLinkStar = $favoriteItem.find(".icon-star");
	var $searchFavorite = $("#searchFavorite");

	$searchSpan.css("padding-bottom",$pbH+"px");
	
	function cancel(){
		$searchClose.hide();
    	$searchInput.val("");
    	$searchInput.addClass("text-center");
    	$searchSe.addClass("cur");
    	$searchLetter.show();
    	$favoriteColumn.removeClass("cur").show();
    	$searchFavorite.hide();
    	$searchCancel.hide();
	}
	//快速搜索
    function searchInList(key){
    	$("#favoriteColumn .favorite-inner-item").show().each(function(){
   	    var $this = $(this);
   	    var thisName = $this.find(".favorite-name").text();
   	    var thisCompany = $this.find(".favorite-company").text();
   	    if(thisName.indexOf(key)<0 && thisCompany.indexOf(key)<0){
   	      $this.hide();
   	    }
   	    
   	  });
   	}
    function resetList(){
    	$("#favoriteColumn .favorite-inner-item").show();
    	$(".favor-title").show();
    }
	 
	//搜索聚焦
	$searchInput.focus(function(e) {
		var $this = $(this),
			pl = $this.attr("placeholder");
		if(pl){
			$searchCancel.show();
			$searchClose.show();
			$searchLetter.hide();
			$favoriteColumn.addClass("cur");
			$this.removeClass("text-center");
			$searchSe.removeClass("cur");
		}
		searchInList($searchInput.val());
    }).focusout(function(e) {
		var $this = $(this),
			pl = $this.attr("placeholder");
		
		if($this.val() === pl){
			$this.val("");
		}
		if(pl && !$this.val()){
			$this.addClass("text-center");
			cancel();
			resetList();
		}
    });
	
	//清除搜索内容
    $searchClose.click(function(){
    	$searchInput.val("");
    	resetList();
    });
	
	//取消搜索
    $searchCancel.click(function(){
    	cancel();
    	resetList();
    });

    $searchInput.on("change keyup keydown", function(){
   	  var $this = $(this);
   	  var val = $this.val();
   	  
   	  $(".favor-title").hide();
   	  
   	  if(val){
   		searchInList($this.val());
   	  }else{
   		resetList();
   	  }
   	});
   	
   	$("#searchForm").submit(function(){
   		searchInList($searchInput.val());
   		return false;
   	});
   	
   //滑动字母搜索
	
	$searchLetter.find("span").on("touchstart",function(e){
		$serchBox.text($(this).text());
		$serchBox.show();
	});
		
	$searchLetter
	.on("touchstart",function(e){
		e.preventDefault();
	})
	.on("touchmove",function(e){
		e.preventDefault();
		var touch = event.touches[0];
  		var	moveY = touch.pageY;
  		
		$searchLetter.find("span").each(function() {
			if($(this).offset().top == moveY){
					$serchBox.text($(this).text());
					$serchBox.show();
				}
		});
		$favoriteColumn.find(".favor-title").each(function(){
			var $this= $(this);
			var $text = $this.text();
			if($text == $serchBox.text()){
				var top = $this.parent(".favorite-box").offset().top;
				$("body").scrollTop(top);
			}
		});
	})
	.on("touchend",function(e){
		e.preventDefault();
		$serchBox.hide();
	});
	
	/*
	//滑动一个到左边
	$("#favoriteColumn")
	.on("touchstart",".favorite-item-link",function(e){
		var touch = e.originalEvent.targetTouches[0];
			startX = touch.clientX;
			startY = touch.clientY;
			$(".favorite-item-link").removeClass("cur");
	})
	.on("touchmove", ".favorite-item-link",function(e){
		var $this = $(this);
		
		var touch = e.originalEvent.targetTouches[0];
			endX =  touch.clientX;
			endY =  touch.clientY;
		var XNum = (endX-startX)<0 ? startX-endX : endX-startX;
		var YNum = (endY-startY)<0 ? startY-endY : endY-startY;
		if(XNum >= YNum){//水平滑动
			e.preventDefault();
			if((endX-startX) <= -10){//从右往左
				$this.addClass("cur");
	  		}else if((endX-startX) >= 10){//从左往右
	  			$this.removeClass("cur");
	  		}
		}
	});
	*/
	//点击星星
	$(".favorite-column").on("click", ".icon-star", function(){
		var $this = $(this);
		var $Item = $this.parents("li.favorite-inner-item");
		var $ItemLink = $Item.find(".favorite-item-link");
		var ItemOpenId = $Item.data("openid");
		
		if(!$this.hasClass("icon-star-active")){//加星标
			$.ajax({
				"type": "post",
				"url": "/app/bizcard/markStar.do",
				"data": {openId:ItemOpenId},
				"dataType": "json",
				"error": function(){
					$.flytip("好像有什么不对劲哦！");
				},
				"success": function(data){
					if(data && data.success){
						$this.addClass("icon-star-active");
						$ItemLink.removeClass("cur");
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
				"data": {openId:ItemOpenId},
				"dataType": "json",
				"error": function(){
					$.flytip("好像有什么不对劲哦！");
				},
				"success": function(data){
					if(data && data.success){
						$this.removeClass("icon-star-active");
						$ItemLink.removeClass("cur");
						$.flytip("取消成功");
					}else{
						$.flytip(data.desc);
					}
				}
			});
		}
	});
	//修改备注
	$(".favorite-column").on("click", ".remark-btn", function(){
		var $this = $(this);
		var $favoriteInnerItem = $this.parents("li.favorite-inner-item");
		var $ItemLink = $favoriteInnerItem.find(".favorite-item-link");
		var $spName = $favoriteInnerItem.find(".sp_Name");
		
		$.vConfirm({
			"tit": "修改备注",
			"msg": "<input type='text' class='remarkInput' id='remarkInput' value='"+$spName.text()+"' placeholder='备注名称' />",
			"ok":{
				"callback": function(){
					var remarkInputVal = $("#remarkInput").val();
					
					$.ajax({
						"type": "post",
						"url": "/app/bizcard/modifyName.do",
						"data": {openId:$favoriteInnerItem.data("openid"),name: remarkInputVal},
						"dataType": "json",
						"error": function(){
							$.flytip("好像有什么不对劲哦！");
						},
						"success": function(data){
							if(data.success){
								$spName.text(remarkInputVal);
								$ItemLink.removeClass("cur");
								$.flytip("修改成功");
							}else{
								$.flytip(data.desc);
							}
						}
					});
				},
				"isClose": true
			}
		});
	});
	
	//取消收藏
	$("#favoriteColumn").on("click",".remove-btn",function(e){
		var $this = $(this);
		var $Item = $this.parents("li.favorite-inner-item");
		var openId = $Item.data("openid");
		
		$.ajax({
			"type": "post",
			"url": "/app/bizcard/uncollect.do?openId="+openId,
			"dataType": "json",
			"error": function(){
				$.flytip("好像有什么不对劲哦！");
			},
			"success": function(data){
				if(data.success){
					$Item.remove();
				}else{
					$.flytip(data.desc);
				}
			}
		});
		
		$.flytip("移除成功");
	});

});