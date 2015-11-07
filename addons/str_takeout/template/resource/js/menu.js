
$(function(){
	$('#icoMenu').click(function(){
		$('#sideNav').toggle();
	});

	$('#menuWrap .add').each(function(){
		$(this).amount(0, $.amountCb());
		for(var i = 0, num = parseInt($(this).data('num')); i < num; i++){
			$(this).click();
		}
	});
	
	var _wraper = $('#menuDetail');

	var dialogTarget;
	$(document).on('click', '.menu_list li', function(e){
		var _this = $(this),
			F = function(str){return _this.find(str);},
			title = F('h3').text(),
			imgUrl = F('img').attr('src'),
			price = F('.unit_price').text(),
			sales = F('.sales strong').attr('class'),
			saleNum = F('.sale_num').text(),
			info = F('.info').text(),
			_detailImg = _wraper.find('img');

		_wraper.find('.price').text(price).end()
			.find('.sales strong').attr('class', sales).end()
			.find('.sale_num').text(saleNum).end()
			.find('.info').text(info);

		_wraper.parents('.dialog').find('.dialog_tt').text(title);

		if(F('.add').length){
			$('#detailBtn').removeClass('disabled').text('来一份');
		}else{
			$('#detailBtn').addClass('disabled').text('已售完');
		}

		if(imgUrl){
			_detailImg.attr('src', imgUrl).show().next().hide();
		}else{
			_detailImg.hide().next().show();
		}

		dialogTarget = _this;
		_wraper.dialog({title: title, closeBtn: true});

	});
	$(document).on('click', '#menuWrap .price_wrap', function(e){
		e.stopPropagation();
	});


	$('#detailBtn').click(function(){
		if(!$(this).hasClass('detail')){
			dialogTarget.find('.add ').click();
		}
	});
});