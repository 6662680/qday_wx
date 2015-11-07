//活动倒计时
function plus_time(time){
   var timestamp3 = new Date().getTime();
   var plus=time*1000-timestamp3;
   var days=Math.floor(plus/(24*3600*1000));
   var leave1=plus%(24*3600*1000);   
   var hours=Math.floor(leave1/(3600*1000));
   var leave2=leave1%(3600*1000);
   var minutes=Math.floor(leave2/(60*1000));
   var leave3=leave2%(60*1000);
   var seconds=Math.round(leave3/1000);
   if(days>0)
   {
    return "<span style='display:block;background:#0088cc;color:#fff;width:100%;padding:0 3px;'>还有"+days+"天 </span>";
   }
   else if(days==0)
   {
   return "<span style='display:block;background:#0088cc;color:#fff;width:100%;padding:0 3px;'>"+hours+"小时 "+minutes+" 分钟"+seconds+" 秒</span>";
   }
   else(plus<=0)
   {
   return "<span style='display:block;background:#1a0f2d;color:#fff;width:100%;padding:0 3px;'>活动已结束</span>";
   }

}

function plus_time_char(){
   for(var i=0;i<$('.brandtime').length;i++)
    {
     var comt=$('.brandtime').eq(i).attr("data-l");
     $('.brandtime').eq(i).html((plus_time(comt)));
    }
}

$(function(){
   $('.btn-share').click(function(){
   $('.mask_bg').show();
   });
   $('.mask_bg').click(function(){
   $(this).hide();
   });
});
