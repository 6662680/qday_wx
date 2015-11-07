// JavaScript Document



( function ( L ) {
	L.fx.animationClass = {
		1 : { up : {out : 'pt-page-moveToTop' , in : 'pt-page-moveFromBottom'} , down : {out : 'pt-page-moveToBottom' , in : 'pt-page-moveFromTop'} ,
			left : {out : 'pt-page-moveToLeft' , in : 'pt-page-moveFromRight'} ,right : { out : 'pt-page-moveToRight' , in : 'pt-page-moveFromLeft'}
			},
		2 :{ up : {out : 'pt-page-rotatePushTop' , in : 'pt-page-moveFromBottom'} , down : {out : 'pt-page-rotatePushBottom' , in : 'pt-page-moveFromTop'},
			left : {out : 'pt-page-rotatePushLeft' , in : 'pt-page-moveFromRight'} ,right : { out : 'pt-page-rotatePushRight' , in : 'pt-page-moveFromLeft'}
			},
		3 : { up : {out : 'pt-page-rotatePushTop' , in : 'pt-page-rotatePullBottom pt-page-delay180'} , down : {out : 'pt-page-rotatePushBottom' , in : 'pt-page-rotatePullTop pt-page-delay180'},
			left : {out : 'pt-page-rotatePushLeft' , in : 'pt-page-rotatePullRight pt-page-delay180'} ,right : { out : 'pt-page-rotatePushRight' , in : 'pt-page-rotatePullLeft pt-page-delay180'}
			},	
		4 : { up : {out : 'pt-page-rotateBottomSideFirst' , in : 'pt-page-moveFromBottom pt-page-delay200 pt-page-ontop'} , 
			down : {out : 'pt-page-rotateTopSideFirst' , in : 'pt-page-moveFromTop pt-page-delay200 pt-page-ontop'} ,
			left : {out : 'pt-page-rotateRightSideFirst' , in : 'pt-page-moveFromRight pt-page-delay200 pt-page-ontop'} ,
			right : { out : 'pt-page-rotateLeftSideFirst' , in : 'pt-page-moveFromLeft pt-page-delay200 pt-page-ontop'}
			},	
		5 : { up : {out : 'pt-page-flipOutTop' , in : 'pt-page-flipInBottom pt-page-delay500' } , down : {out : 'pt-page-flipOutBottom' , in : 'pt-page-flipInTop pt-page-delay500' } ,
			left : {out : 'pt-page-flipOutRight' , in : 'pt-page-flipInLeft pt-page-delay500' } ,right : { out : 'pt-page-flipOutLeft' , in : 'pt-page-flipInRight pt-page-delay500' }
			},
		6 : { up : {out : 'pt-page-rotateFall pt-page-ontop' , in : 'pt-page-scaleUp' } , down : {out : 'pt-page-rotateFall pt-page-ontop' , in : 'pt-page-scaleUp' } ,
			left : {out : 'pt-page-rotateFall pt-page-ontop' , in : 'pt-page-scaleUp' } ,right : { out : 'pt-page-rotateFall pt-page-ontop' , in : 'pt-page-scaleUp' }
			},
		7 : { up : {out : 'pt-page-rotateFoldTop' , in : 'pt-page-moveFromBottomFade' } , down : {out : 'pt-page-rotateFoldBottom' , in : 'pt-page-moveFromTopFade' } ,
			left : {out : 'pt-page-rotateFoldLeft' , in : 'pt-page-moveFromRightFade' } ,right : { out : 'pt-page-rotateFoldRight' , in : 'pt-page-moveFromLeftFade' }
			},	
		8 : { up : {out : 'pt-page-moveToTopFade' , in : 'pt-page-rotateUnfoldBottom' } , down : {out : 'pt-page-moveToBottomFade' , in : 'pt-page-rotateUnfoldTop0' } ,
			left : {out : 'pt-page-moveToLeftFade' , in : 'pt-page-rotateUnfoldRight' } ,right : { out : 'pt-page-moveToRightFade' , in : 'pt-page-rotateUnfoldLeft' }
			},
		9 : { up : {out : 'pt-page-rotateCubeTopOut pt-page-ontop' , in : 'pt-page-rotateCubeTopIn' } ,
		 	down : {out : 'pt-page-rotateCubeBottomOut pt-page-ontop' , in : 'pt-page-rotateCubeBottomIn' } ,
			left : {out : 'pt-page-rotateCubeLeftOut pt-page-ontop' , in : 'pt-page-rotateCubeLeftIn' } ,
			right : { out : 'pt-page-rotateCubeRightOut pt-page-ontop' , in : 'pt-page-rotateCubeRightIn' }
			},
		10 : { up : {out :'pt-page-rotateCarouselTopOut pt-page-ontop' , in : 'pt-page-rotateCarouselTopIn' } , 
			down : {out : 'pt-page-rotateCarouselBottomOut pt-page-ontop' , in : 'pt-page-rotateCarouselBottomIn' } ,
			left : {out : 'pt-page-rotateCarouselLeftOut pt-page-ontop' , in : 'pt-page-rotateCarouselLeftIn' } ,
			right :{out : 'pt-page-rotateCarouselRightOut pt-page-ontop' , in : 'pt-page-rotateCarouselRightIn' }
			},
		11 : { up : {out : 'pt-page-fad' , in : 'pt-page-moveFromBottom pt-page-ontop' } , down : {out : 'pt-page-fade' , in : 'pt-page-moveFromTop pt-page-ontop' } ,
			left : {out : 'pt-page-fade' , in : 'pt-page-moveFromRight pt-page-ontop' } ,right : { out : 'pt-page-fade' , in : 'pt-page-moveFromLeft pt-page-ontop' }
			},
		12 : { up : {out : 'pt-page-moveToTopFade' , in : 'pt-page-moveFromBottomFade' } , down : {out : 'pt-page-moveToBottomFade' , in : 'pt-page-moveFromTopFade' } ,
			left : {out : 'pt-page-moveToLeftFade' , in : 'pt-page-moveFromRightFade' } ,right : { out : 'pt-page-moveToRightFade' , in : 'pt-page-moveFromLeftFade' }
			},
		13 : { up : {out : 'pt-page-moveToTopEasing pt-page-ontop' , in : 'pt-page-moveFromBottom' } ,
			 down : {out : 'pt-page-moveToBottomEasing pt-page-ontop' , in : 'pt-page-moveFromTop' } ,
			left : {out : 'pt-page-moveToLeftEasing pt-page-ontop' , in : 'pt-page-moveFromRight' } ,
			right : { out : 'pt-page-moveToRightEasing pt-page-ontop' , in : 'pt-page-moveFromLeft' }
			},
		14 : { up : {out : 'pt-page-scaleDown' , in : 'pt-page-moveFromBottom pt-page-ontop' } , 
			down : {out : 'pt-page-scaleDown' , in : 'pt-page-moveFromTop pt-page-ontop' } ,
			left : {out : 'pt-page-scaleDown' , in : 'pt-page-moveFromRight pt-page-ontop' } ,
			right : { out : 'pt-page-scaleDown' , in : 'pt-page-moveFromLeft pt-page-ontop' }
			},
		15 : { up : {out : 'pt-page-scaleDownUp' , in : 'pt-page-scaleUp pt-page-delay300' } , down : {out : 'pt-page-scaleDown' , in : 'pt-page-scaleUpDown pt-page-delay300' } ,
			left : {out : 'pt-page-scaleDownUp' , in : 'pt-page-scaleUp pt-page-delay300' } ,right : { out : 'pt-page-scaleDown' , in : 'pt-page-scaleUpDown pt-page-delay300' }
			},
		16 : { up : {out : 'pt-page-moveToTop pt-page-ontop' , in : 'pt-page-scaleUp' } , down : {out : 'pt-page-moveToBottom pt-page-ontop' , in : 'pt-page-scaleUp' } ,
			left : {out : 'pt-page-moveToLeft pt-page-ontop' , in : 'pt-page-scaleUp' } ,right : { out : 'pt-page-moveToRight pt-page-ontop' , in : 'pt-page-scaleUp' }
			},	
		17 : { up : {out : 'pt-page-rotateSlideOut' , in : 'pt-page-rotateSlideIn' } , down : {out : 'pt-page-rotateSlideOut' , in : 'pt-page-rotateSlideIn' } ,
			left : {out : 'pt-page-rotateSlideOut' , in : 'pt-page-rotateSlideIn' } ,right : { out : 'pt-page-rotateSlideOut' , in : 'pt-page-rotateSlideIn' }
			}	
};
} ) ( _liuLiang );
