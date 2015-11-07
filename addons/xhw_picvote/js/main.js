//分享
function button2(){
$("#mcover").css("display","block")  // 分享给好友圈按钮触动函数
$('.lapiao em').removeClass('r').addClass('change_color')
}

function weChat(){

$("#mcover").css("display","none");  // 点击弹出层，弹出层消失
$('.lapiao em').removeClass('change_color').addClass('r')
}
//搜索
function button3(){

$(".cover").css("display","block") 
$(".search").css("display","block") 
}

function weChat1(){

$(".cover").css("display","none");  // 点击弹出层，弹出层消失
$(".search").css("display","none") 

}
$('.search').click(function(){
	return false;	
})


$('.butn').live('click',function(){
   if ($(this).find("i").hasClass("hide")) {
                $(this).find("i").removeClass("hide")
  				$('.caidan').slideDown()
            } else {
                $(this).find("i").addClass("hide")
                $('.caidan').slideUp()
			}
});
$('.caidan i').live('click',function(){
	//alert('ok')
  $('.txte').val($(this).html()).css('color','#333')
});