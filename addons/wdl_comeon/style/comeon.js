var Comeon = {};
Comeon.init = function(options){
    $(function(){

        
        //参加
        $(".btn-join").click(function(){
          
             showloading();
            $.post(options.joinurl , {
                rid:options.rid,
                mobile:$('input[name=mobile]').val(),openid: options.openid
            },function(data){
                data =eval("(" + data + ")");
                hideloading();
                if(data.result==0){
                  
                    swal({   title: "",  
                       text:data.msg,
                        type: "warning",  
                    
                        confirmButtonColor: "#DD6B55",  
                        confirmButtonText: "确定" }, 
                         function(){ });
                }
                else{
                     swal({   title: "",  
                        text:  data.msg,  
                        type: "success",  
                      
                        confirmButtonColor: "#DD6B55",  
                        confirmButtonText: "好的，这就邀请去!" }, 
                         function(){ location.reload() 
                     });
                }
            });
        });
        
        //助力
        $(".btn-help").click(function(){
            
             showloading();
            $.post(options.helpurl , {
                rid:options.rid,
                fansid:options.fansid,openid: options.openid
            },function(data){
                data =eval("(" + data + ")");
                hideloading();
                if(data.result==0){
                  
                    swal({   title: "",  
                       text:data.msg,
                        type: "warning",  
                        confirmButtonColor: "#DD6B55",  
                        confirmButtonText: "确定" }, 
                         function(){ });
                }
                else{
                     swal({   title: "",  
                        text:  data.msg,  
                        type: "success",  
                      
                        confirmButtonColor: "#DD6B55",  
                        confirmButtonText: "确定" }, 
                         function(){ //location.reload() 
                     });
                }
            });
            
        });
        
        
        $(".btn-search").click(function(){
                  showloading();
            $.post(options.searchurl , {
                rid:options.rid,
                mobile:$('input[name=mobile]').val()
            },function(data){
                data =eval("(" + data + ")");
                hideloading();
                if(data.result==0){
                     
                    swal({   title: "",  
                       text:data.msg,
                        type: "warning",  
                    
                        confirmButtonColor: "#DD6B55",  
                        confirmButtonText: "确定" }, 
                         function(){ });
                    
                }
                else{
                     swal({   title: "",  
                        text:  data.msg,  
                        type: "success",  
                      
                        confirmButtonColor: "#DD6B55",  
                        confirmButtonText: "确定" }, 
                         function(){ //location.reload() 
                     });
                }
            });
        });
        
        
        //分享
        $(".btn-invite").click(function(){
            $("#mcover").show();
        })
        
     })
}
