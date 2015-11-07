var myscroll= function(arguments){
   //var ds = arguments['ds'],
    var toolbar1 = '<div class="toolBar showit"> <div class="direction dleft"></div> <div class="funBar"> <a class="aupload" href="">向左滑动</a> <a class="adelete" href=""></a> </div> <div class="direction dright"></div> </div> <a href="javascript:;" class="closebtn showit">鍏抽棴</a>';
   var toolbar = '<div class="toolBar showit"> <div class="direction dleft"></div><div class="direction dright"></div></div>';
  var slideshow =  arguments['slideshow'],
      photolist = arguments['list'], 
      toolheight = arguments['toolheight'],
      gh = arguments['gh'],
      gw = arguments['gw'],
	  
      liEle = photolist.find('li'),
      imgEle = photolist.find('img'),
	  
      len = liEle.length;
  slideshow.append(toolbar);
  
  
  var prevEle = $('.toolBar .dleft'),
      nextEle = $('.toolBar .dright');
      /*deleteEle = $('.toolBar .adelete');*/

  

  var func = {
    init:function(){
      slideshow.width(gw).height(gh);
      liEle.width(gw).height(gh);          
      photolist.width(len*gw).height(gh);
      setTimeout(function(){
        $('.toolBar').fadeOut();
        $('.closebtn').removeClass('showit').fadeOut();
      },2500);
      imgEle.each(function(){
        $(this).load(function(){
          var _this = $(this),
              iw = _this.width(),
              ih = _this.height(),        
              tw = 0,
              th = 0,
              temp = 0;
          if(iw>gw){
            _this.width(gw);
            th = gw*ih/iw;
            if(th<gh){
              temp = (gh-toolheight-th)/2;              
              _this.height(th).css("margin-top",temp);
            }else{
              tw = iw*gh/ih;
              _this.height(gh).width(tw);              
            }
          }else{            
            if(ih>gh){ 
              tw = iw*gh/ih;            
              _this.height(gh).width(tw);
            }else{
                temp = (gh-toolheight-ih)/2;
                _this.css("margin-top",temp);
            }
          }
        });
      });
	  setTimeout(function(){
	  $('.toolBar').fadeOut();
	  $('.closebtn').removeClass('showit').fadeOut();
	},2500);
    },
    next:function(index){
      index++;
      var sleft = photolist.offset().left;
      if(index<len){
        photolist.animate({left: -index*gw },100);            
      }          
    },
    prev:function(index){
      var sleft = photolist.offset().left;
      if(sleft <0){
        photolist.animate({left: sleft + gw },100); 
      }
    },
    deleteit:function(){
      photolist.find('.current').find('img').attr("src",'images/noimg.png').width(110).height(100);
    },
    extendit:function(){
      $('.toolBar,.closebtn').removeClass('showit').hide();
      var imgsrc = photolist.find('.current').find('img').attr("src");
      console.log(imgsrc);
      if(imgsrc == 'images/noimg.png'){
        $('.toolBar .adelete').css("opacity",.5);
      }else{
        $('.toolBar .adelete').css("opacity",1); 
      }          
    }
  }

  func.init();
  var i = photolist.find('.current').index();
  prevEle.on('click',function(){         
    if(i== 0 || i>0){
      func.prev(i);
      photolist.find('li').eq(i).removeClass('current');
      i--;
      if(i==-1){i=0};
      photolist.find('li').eq(i).addClass('current');
      func.extendit();
    }
  });
  nextEle.on('click',function(){        
    if(i>-1 && i<len){
      func.next(i);
      photolist.find('li').eq(i).removeClass('current');
      i++;console.log(i);
      if(i==len){i=len-1};
      photolist.find('li').eq(i).addClass('current');

    }else{return false;}
    func.extendit();
  });
  /*deleteEle.on('click',function(){
    func.deleteit();
    func.extendit();
  });*/
  $("body").on('swiperight', function() {
    prevEle.click();        
  });
  $("body").on('swipeleft', function() {
    nextEle.click();
  });                 
}