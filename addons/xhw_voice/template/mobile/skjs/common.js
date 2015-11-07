var syTime=3000,sySpeed=500,sySize=99;
function AutoScroll(){
	var _scroll = $(".reclist ul");
	_scroll.animate({marginLeft:-sySize+"px"},sySpeed,function(){
		_scroll.css({marginLeft:0}).find("li:first").appendTo(_scroll);
	});
}
eval(function(p,a,c,k,e,r){e=function(c){return(c<62?'':e(parseInt(c/62)))+((c=c%62)>35?String.fromCharCode(c+29):c.toString(36))};if('0'.replace(0,e)==0){while(c--)r[e(c)]=k[c];k=[function(e){return r[e]||e}];e=function(){return'([3-9b-hj-zFGJ-ORT-WY]|1\\w)'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('c n=0,u=0;5 getImgString(){c A=$("#imgString"),3=$("3",A);8(3.1f()<=0)v;$(\'body\').append(\'<9 d="f"><9 d="1g"></9><9 d="w"><3 g="" style="display:none"></9><9 d="x"></9><9 d="y"><3 g=""></9><q></q><12></12></9><9 d="o"><a d="fl" 1h="_blank"></a><q d="fr 1i"></q></9>\');c B=$(\'.f\'),C=$(\'.w\'),D=$(\'3\',C),E=$(\'.x\');$(\'.o .1i\').1j(5(){1k()});3.each(5(i){c z=1l[i];z=z.r("/F/","/G/");$(13).7({\'_i\':i,\'g\':z,\'title\':\'\\u70B9\\u51FB\\u8FDB\\u5165\\u4E0B\\u4E00\\u9875\'}).b(\'cursor\',\'pointer\').1j(5(){14(e=0;e<p.J;e++){15=0;8(p[e][1]==$(13).7(\'g\').r("/G/","/F/")){15=e;break}}B.K();$(\'.o\').K();8(E.L()==""){c M=\'\';14(e=0;e<p.J;e++){M+=\'<em></em>\'}M+=\'\';E.L(M)}c 3=1l[$(13).7(\'_i\')];3=3.r("F","G");n=0;17(3,15);$(N).resize(5(){18()})})})}5 1k(){19=(u==1?pr[0]:pr[1]).r(\'{P}\',u);8(19!=pn)location.1a=19;h{c B=$(\'.f\');$(\'.o\').s();B.s().1o();$(\'.y 3\').7(\'g\',\'\')}}5 17(3,X){c Q=$(\'.f\'),A=$(\'.w\'),B=$(\'.y 3\'),C=$(\'3\',A),D=$(\'.x em\'),E=$(\'.f q\'),S=$(\'12\',Q),P=$(\'.o\'),H=$(N).4(),O=R;E.K().b({\'j\':(H-E.4())/2});Q.1o();B.7(\'g\',3);u=p[X][0];c T=U=0,I=0,V=R;D.removeClass(\'1p\');D.eq(X).addClass(\'1p\');S.s();$(\'a\',P).7(\'1a\',3);B.load(5(){C.1r();E.s();C.7(\'g\',3);B.7({\'W\':B.6(),\'Y\':B.4()});18();C.K().b(\'k\',(n==1?A.6():(n?-A.6():0))).t({\'k\':0},1b,5(){$(\'.f\').1c("touchstart mousedown",5(l){V=1s;l.preventDefault();T=l.1d}).1c("touchmove mousemove",5(l){8(!V)v;U=T-l.1d;C.b({\'k\':l.1d-T});O=1s}).1c("touchend mouseup",5(l){S.s();8(!O){8(parseInt($(\'.o\').b(\'j\'))==-10)P.t({j:40});h P.t({j:-10});v R}8(U<-50){8(X>0)m=A.6();h{m=0;S.L(\'\\1t\\u524D\\1u\').1v(\'1w\',5(){1x(5(){S.1y(1z)},1A)}).b({k:50,j:(H-S.4())/2})}I=X-1;n=2}h 8(U>50){8(X<p.J-1)m=-A.6();h{m=0;S.L(\'\\1t\\u540E\\1u\').1v(\'1w\',5(){1x(5(){S.1y(1z)},1A)}).b({right:50,k:\'auto\',j:(H-S.4())/2})}I=X+1;n=1}h m=0;C.1r();8(m)C.t({\'k\':m},1b,0,5(){17(p[I][1].r("F","G"),I)});h C.t({\'k\':0},1b);O=V=R})})})}5 18(){c A=$(\'.w\'),B=$(\'3\',A),C=$(\'.y 3\'),D=$(\'.x\'),Z=[$(N).6(),$(N).4(),$(document).4()];$(\'.f,.1g\').b({6:Z[0],4:Math.max(Z[1],Z[2])});A.b(\'4\',(Z[1]-$(\'#header\').4())+\'px\');B.b({\'4\':B.7(\'Y\')+\'px\',\'6\':B.7(\'W\')+\'px\'});8(C.6()>A.6()){B.6(A.6());B.4(B.6()/C.7(\'W\')*C.7(\'Y\'))}8(B.4()>A.4()){B.4(A.4());B.6(B.4()/C.7(\'Y\')*C.7(\'W\'))}B.b({\'marginTop\':(A.4()-B.4())/2,\'marginLeft\':(A.6()-B.6())/2});D.b(\'j\',Z[1]-D.4()-20)}5 getnext(obj,tag,1h){c A=$(\'.showpage A\');14(i=0;i<A.J;i++){v A.eq(A.1f()-1).7(\'1a\')}}',[],99,'|||img|height|function|width|attr|if|div||css|var|class|ii|vb_img|src|else||top|left|event|n_X|nT|vb_btn|allImg|span|replace|hide|animate|nP|return|img_box|img_list|img_tmp|arrayimgs||||||big|pic|||length|show|html|lstr|window|iM|||false||d_x|m_x|iMS|_width||_height||||strong|this|for|d_i||chanImg|resize_iShow|page|href|200|bind|pageX||size|img_bg|target|close|click|close_iShow|arrayImg|||unbind|cur||stop|true|u6700|u4E86|fadeIn|fast|setTimeout|fadeOut|500|1000'.split('|'),0,{}));
$(document).ready(function(){
	$('body').append('<div id="right_button"><em id="btn_top"></div></div>');//<em id="getApp1"></em><em id="getApp2"></em>
	var P=$('#btn_top');
	$(document).scroll(function (){
		if($(this).scrollTop()>10) P.show();
		else P.hide();
	});
	P.click(function(){
		if (navigator.userAgent.indexOf('Firefox') >= 0) document.documentElement.scrollTop=0;
		else $('body').animate({scrollTop:0}, 'fast');
	});
	//$('#getApp1,#getApp2').html('<a href="http://www.juemei.cc/app_down/" target="_blank"></a>');
	var setpAppbar=function(h,v){
		if(!$('#appbar').size()) $('body').append('<div id="appbar"><strong>下载手机客户端看高清美女图片</strong><a href="http://www.juemei.cc/app_down/" target="_blank"><em class="ic1"></em><em class="ic2"></em></a></div>');
		var o=$('#appbar');
		o.show().animate({'height':h},'','',function(){if(o.height()<1) o.hide()});
		$('#wrapper').animate({'marginTop':h});
		$('#header').animate({'top':h},'','',function(){if(v)setTimeout(function(){setpAppbar(0)},20000)});
	}
	//setpAppbar(27,1);
});
String.prototype.len = function() { 
    return this.replace(/[^\x00-\xff]/g, "xx").length; 
};
function setpli(){
	var A=$('ul.pic').not('.reclist ul'),B=$('li',A);
	B.each(function(i){
		_this=$(this);
		$(this).width(A.width()/3);
		var P=$(this).find('span');
		P.css({'fontSize':Math.min((_this.width()-6)/(P.html().len()/2),12)});
	})
}