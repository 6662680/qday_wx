jQuery(function(){
	var submitNowYear;
	var default_sub = $("#form_submit_btn").html();
	$("#look-house-input input").focus(function(){
		$("#form_submit_btn").removeAttr("disabled");
	});
	$("#form_submit_btn").on('swipeclick', function(){
			if(submitNowYear == '' || submitNowYear ==undefined){
				var _now = new Date();
				submitNowYear = _now.getFullYear();
			}
			var _month=parseInt($('select[name="month"]').val(),10);
			var _day=parseInt($('select[name="day"]').val(),10);
 			var lookTime=_month+'月'+_day+'日';
			var success_msg = $.trim($('#look-house-input').find('input[name="success_msg"]').val());
			var activity_id = parseInt($('#activity_id').html(),10);
			if (success_msg == '') {
				success_msg = '您的信息已提交成功！';
			} 
			var obj = $(this);
			
			var name = $.trim(obj.parents('#look-house-input').find('input[name="str1"]').val());
			var name_text = $.trim(obj.parents('#look-house-input').find('input[name="str1"]').prev().html());
			var tel = $.trim(obj.parents('#look-house-input').find('input[name="str2"]').val());
			var tel_text = $.trim(obj.parents('#look-house-input').find('input[name="str2"]').prev().html());
			//验证名称
			if(name==''){
				dialogShow('','请输入'+name_text);
				return;
			}
			if(tel == ''){
				dialogShow('','请输入'+tel_text);
				return;
			}
			/*
			if(tel.length > 15 || !tel.match(/^\d+$/)){
				dialogShow('','请输入正确格式的'+tel_text);
				return;
			}*/
			
			if(lookTime == ''){
				dialogShow('','请选择您想预约的时间！');
				return;
			}

			if (tel!=''||name!=''||lookTime!='') {
				$.ajax({
					   type: "POST",
					   url: sumbit_url,
					   data: "str1="+name+"&str2="+tel+"&str3="+lookTime,
					   dataType: "json",
					   success: function(result){
						   if(result.success){
								obj.attr('disabled','disabled');
								obj.removeClass('btn-warning');
								obj.addClass('btn-success');
								if (result.data==1) {
									obj.html('重复提交');
									dialogShow('','您的信息已提交，无需重复提交！');
								} else {//提示语修改
									dialogShow('',success_msg);
									obj.html('提交成功');
								}
							}else{
								alert(result.message);
							}
					   }
					});
			}
			
			
			
	});
	
	$('select[name="month"]').change(function(e){
		var dayHtml='';
		var month = parseInt($(this).val(),10)-1;
		var today = new Date();
		
		if(month >= (today.getMonth()) )
		{
			submitNowYear = today.getFullYear();
		}else{
			submitNowYear = today.getFullYear()+1;
		}
		
		var _day = new Date(submitNowYear,month+1,0);
		for(var j=1;j<=_day.getDate();j++){
			dayHtml+='<option value="'+j+'" >'+j+'日</option>';
		}
		$('select[name="day"]').empty().html(dayHtml);
	});
	
	$('select[name="day"]').change(function(e){
		 
	});
	
	$('div[data-status]').on('click',function(){
		var top = $(this).css('top');
		if($(this).hasClass('swipe-click')){
			$(this).find('img').toggle();			
		}
	});
	
});