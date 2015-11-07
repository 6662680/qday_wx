/**
 * author Carloss.hl
 * 2014-05-11
 * version 0.1
 */
window.HL = window.HL || {};
var HL = window.HL, pageLoad, bodyHeight;



HL.COMMON = {
    pageLoad: function(type){
        //统计
        window.mobileStatisticsRequest && window.mobileStatisticsRequest(false);
		
        //userAgent
        var userAgent = window.navigator.userAgent.toLowerCase();
        if (userAgent.indexOf('android') != -1) {
            $('.index-body-bg').remove();
        }
		if (userAgent.indexOf('iphone') != -1) {
            $('.power-num-container').css('position', 'fixed');
        }
		
		//显示主体
		$('.page-load-container').css('opacity', 1);

        switch (type) {
            case 'introducePage':
                HL.COMMON.initIntroduceSwipe();
				//HL.COMMON.wxOptionMenu(true);
				//HL.COMMON.wxToolbar(false);
                break;
			case 'winningFormPage':
				HL.COMMON.bindSwitchSex();
				//HL.COMMON.wxOptionMenu(false);
				//HL.COMMON.wxToolbar(false);
                break;
			case 'weekWinningFormPage':
				HL.COMMON.bindSwitchSex();
				//HL.COMMON.wxOptionMenu(false);
				//HL.COMMON.wxToolbar(false);
                break;
			case 'winningNotice':
				//HL.COMMON.wxOptionMenu(true);
				//HL.COMMON.wxToolbar(false);
				break;
            case 'helpPage':
            	if ($('div[data-id="helpPage"]').attr('data-msg')) {
                	HL.COMMON.alert($('div[data-id="helpPage"]').attr('data-msg'), $('div[data-id="helpPage"]'));
            	}
				//HL.COMMON.wxOptionMenu(true);
				//HL.COMMON.wxToolbar(false);
                break;
            case 'friendPage':
                HL.COMMON.bindTurnOnOff();
				
				//初始化体力值
				HL.COMMON.getPlayer();
				
				//HL.COMMON.wxOptionMenu(false);
				//HL.COMMON.wxToolbar(false);
                break;
            case 'rankingPage':
				//HL.COMMON.wxOptionMenu(true);
				//HL.COMMON.wxToolbar(false);
				HL.COMMON.initRankingTab();
				//HL.COMMON.bindWeekRankingMenu();
                break;
            case 'indexPage':
				//初始声音开关
				HL.COMMON.initAudioSwitch();
				//初始化体力值
				HL.COMMON.getPlayer()
				//菜单
				//HL.COMMON.wxOptionMenu(!window.isSubscribe);
				//HL.COMMON.wxToolbar(false);
                break;
                
        }
		//网络状态
		HL.COMMON.setPlayAudio();
        HL.COMMON.getNetworkType(function(e){
            if ($('div[data-id="introducePage"]')[0] || $('div[data-id="indexPage"]')[0]) 
                HL.COMMON.setPlayAudio();
        });
    },
    getPlayer: function(){
		var _this = this;
        $.ajax({
            url: GETPLAYERURL,
            method: 'POST',
            data: {
                //wxUserId: window.wxUserId
            },
            success: function(data){
                console.log(data);
				var data = data.message;
				$('#gatherNum').html(data.power);
				$('#zzNum').html(parseInt(data.power / 2000));
				_this.setEngery(parseInt(data.energy, 10), 1, true);
            },
            error: function(){

            }
        })
    },
    wxOptionMenu: function(show){
        show = show ? 'showOptionMenu' : 'hideOptionMenu';
        setTimeout(function(){
            window.WeixinJSBridge && window.WeixinJSBridge.call(show);
        })
        setTimeout(function(){
            window.WeixinJSBridge && window.WeixinJSBridge.call(show);
        }, 500)
        setTimeout(function(){
            window.WeixinJSBridge && window.WeixinJSBridge.call(show);
        }, 1000)
    },
    getNetworkType: function(callback, failure){
		var _this = this;
        window.WeixinJSBridge && window.WeixinJSBridge.invoke('getNetworkType', {}, function(e){
            if (e.err_msg == 'network_type:wifi') {
                if (typeof callback == 'function') {
                    callback(e);
                }
                return;
            }
            else {
                if (typeof failure === 'function') {
                    failure(e);
                }
                //非wifi执行
            }
        });
    },
	wxToolbar: function(show){
//		show = show ? 'showToolbar' : 'hideToolbar';
//        setTimeout(function(){
//            window.WeixinJSBridge && window.WeixinJSBridge.call(show);
//        })
//        setTimeout(function(){
//            window.WeixinJSBridge && window.WeixinJSBridge.call(show);
//        }, 500)
//        setTimeout(function(){
//            window.WeixinJSBridge && window.WeixinJSBridge.call(show);
//        }, 1000)
	},
	hasAudio: !($.cookie('index-swich-audio') == 'false'),
    initAudioSwitch: function(){
        if (this.hasAudio) {
            $('.switch-audio').addClass('on').removeClass('off');
            return;
        }
        $('.switch-audio').addClass('off').removeClass('on');
    },
    switchAudio: function(el){
        if (this.hasAudio) {
			this.hasAudio = false;
			$(el).addClass('off').removeClass('on');
			$.cookie('index-swich-audio', false);
			this.pauseAudio();
            return;
        }
		this.hasAudio = true;
		$(el).addClass('on').removeClass('off');
		$.cookie('index-swich-audio', true);
    },
    pauseAudio: function(){
        var normalAudios = $('.audio-item-normal');
        var criticalAudios = $('.audio-item-critical');
        var openAudios = $('.audio-item-open');
		var winningAudios = $('.audio-item-winning');
        try {
            normalAudios.each(function(){
                //this.currentTime = 0;
                this.pause();
            })
            
        } 
        catch (e) {
        
        }
        try {
            criticalAudios.each(function(){
                //this.currentTime = 0;
                this.pause();
            })
            
        } 
        catch (e) {
        
        }
        try {
            openAudios.each(function(){
                //this.currentTime = 0;
                this.pause();
            })
            
        } 
        catch (e) {
        
        }
		try {
            winningAudios.each(function(){
                //this.currentTime = 0;
                this.pause();
            })
            
        } 
        catch (e) {
        
        }
    },
    playAudio: function(type){
        if (!this.hasAudio) {
            return;
        }
		var audios;
        var normalAudios = $('.audio-item-normal');
		var criticalAudios = $('.audio-item-critical');
		var openAudios = $('.audio-item-open');
		var winningAudios = $('.audio-item-winning');
        switch (type) {
            case 'normal':
                audios = normalAudios;
                break;
            case 'critical':
                audios = criticalAudios;
                break;
            case 'open':
                audios = openAudios;
                break;
			case 'winning':
                audios = winningAudios;
                break;
        }
        if (audios.length <= 0) {
            return;
        }
        var rd = parseInt(Math.random() * ((audios.length - 1) - 0 + 1) + 0);
        try {
			
            //audios.eq(rd)[0].currentTime = 0;
            audios.eq(rd)[0].play();
        } 
        catch (e) {
        
        }
        
    },
	setPlayAudio: function(){
		var target = $('body');
		$('.switch-audio').show();
        if (target.attr('data-rendered') == 'true') {
            return;
        }
		var normalAudios = ['../addons/hl_zzz/template/audio/bu.mp3', '../addons/hl_zzz/template/audio/bloom.mp3', '../addons/hl_zzz/template/audio/fight.mp3', '../addons/hl_zzz/template/audio/bu.mp3', '../addons/hl_zzz/template/audio/XX.mp3', '../addons/hl_zzz/template/audio/kwdlh.mp3', '../addons/hl_zzz/template/audio/afs.mp3'];
		var criticalAudios = ['../addons/hl_zzz/template/audio/critical.mp3'];
		var winningAudios = ['../addons/hl_zzz/template/audio/winning.mp3'];
		var openAudios = ['../addons/hl_zzz/template/audio/open.mp3'];
        $(normalAudios).each(function(index){
			var audio = $('<audio style="display:none; position:absolute; left:0px; top:0px; font-size:0px; line-height:0px; width:0px; height:0px; overflow:hidden; visibility:hidden" class="audio-item-normal" preload controls>' +
           
            '<source src="' + window.path + normalAudios[index] + '" type="audio/mpeg">' +
            'Your browser does not support the audio tag.' +
            '</audio>');
            target.append(audio);
        })
		$(criticalAudios).each(function(index){
			var audio = $('<audio style="display:none; position:absolute; left:0px; top:0px; font-size:0px; line-height:0px; width:0px; height:0px; overflow:hidden; visibility:hidden" class="audio-item-critical" preload controls>' +
           
            '<source src="' + window.path + criticalAudios[index] + '" type="audio/mpeg">' +
            'Your browser does not support the audio tag.' +
            '</audio>');
            target.append(audio);
        })
		$(openAudios).each(function(index){
			var audio = $('<audio style="display:none; position:absolute; left:0px; top:0px; font-size:0px; line-height:0px; width:0px; height:0px; overflow:hidden; visibility:hidden" class="audio-item-open" preload controls>' +
           
            '<source src="' + window.path + openAudios[index] + '" type="audio/mpeg">' +
            'Your browser does not support the audio tag.' +
            '</audio>');
            target.append(audio);
        })
		$(winningAudios).each(function(index){
			var audio = $('<audio style="display:none; position:absolute; left:0px; top:0px; font-size:0px; line-height:0px; width:0px; height:0px; overflow:hidden; visibility:hidden" class="audio-item-winning" preload controls>' +
           
            '<source src="' + window.path + winningAudios[index] + '" type="audio/mpeg">' +
            'Your browser does not support the audio tag.' +
            '</audio>');
            target.append(audio);
        })
		target.attr('data-rendered', true);
	},
	/**
	 * 设置体力值
	 */
	setEngery: function(engery, duration, now){
		var _this = this;
		var maxNum = $('div[data-id="indexPage"]')[0] ? parseInt($('div[data-id="indexPage"]').attr('data-energylimit'), 10) : parseInt($('div[data-id="friendPage"]').attr('data-energylimit'), 10);
		var powerNum = $('#powerNum');
		var endNum = engery;
        if (endNum < 0) {
            return;
        }
        if (endNum > maxNum) {
            endNum = maxNum;
        }
		powerNum.attr('data-power', engery);
        _this.countUp(powerNum, engery, now);
		var percent = 100 - (endNum / maxNum) * 100;
		$('.power-bar-percent').css('transition', 'all '+(duration ? duration : 0.3)+'s ease').css('transform', 'translateX(-' + percent + '%) translateZ(0)');
	},
	/**
	 * 计数
	 */
	countUp: function(target, value, speed){
		var _this = this;
		var countUp = target.data('countUp');
		speed = speed ? speed : 1;
		if(speed === true){
			target.html(value);
			return;
		}
        if (countUp) {
            clearInterval(countUp);
        }
        target.data('countUp', setInterval(function(){
			var start = parseInt(target.html());
			var to = value < start ? start - speed : start + speed;
            if (value < start) {
                if (to < value) {
                	to = value;
					target.html(to);
					clearInterval(target.data('countUp'));
					return;
                }
            }
            else {
                if (to > value) {
                	to = value;
					target.html(to);
					clearInterval(target.data('countUp'));
					return;
                }
            }
			if (start == value) {
				clearInterval(target.data('countUp'));
				return;
            }
            target.html(to);
        }))
	},
	/**
	 * 点击送米
	 */
	powerClick: function(el){
		var _this = this;
		var powerNum = parseInt($('#powerNum').attr('data-power'), 10);
		var energyCost = parseInt($('div[data-id="indexPage"]').attr('data-energycost'), 10);
        if (powerNum < energyCost) {
            _this.alert(TILI);
            return;
        }
        if ($(el).hasClass('disabled')) {
            return;
        }
		$(el).addClass('disabled');
		_this.powerClickTimeout = setTimeout(function(){
			pageLoad.showLoadMask(true, '正在努力传送中...');
		}, 500);
		_this.playAudio('normal');
		$.ajax({
			url: POWERUPURL,
            method: 'POST',
			success: function(data){
                console.log(data);
                if (data.message.success) {
                    _this.powerAnimate(data.message.result);
					_this.setEnergyCost();
                }
                if (_this.powerClickTimeout) {
                    clearTimeout(_this.powerClickTimeout);
                    _this.powerClickTimeout = null;
                }
				pageLoad.showLoadMask(false);
				$(el).removeClass('disabled');
			},
			error: function(){
				if (_this.powerClickTimeout) {
                    clearTimeout(_this.powerClickTimeout);
                    _this.powerClickTimeout = null;
                }
				pageLoad.showLoadMask(false);
				$(el).removeClass('disabled');
			}
		})
	},
	/**
	 * 送米回调动画
	 */
	powerAnimate: function(result){
		var _this = this;
		var powerUpResult = result.powerUpResult;
		var doublePowerBuff = $('div[data-id="indexPage"]').attr('data-double') == 'true';
		//体力不足
        if (powerUpResult.type == 0) {
            _this.alert(TILI);
        }
        //一般送米
        if (powerUpResult.type == 1) {
        	_this.showBall(powerUpResult.value, result.power, result.weekPower, false, doublePowerBuff);
        }
        //大把送米
        if (powerUpResult.type == 2) {
        	_this.showBall(powerUpResult.value, result.power, result.weekPower, true, doublePowerBuff);
        }
        //送粽子
        if (powerUpResult.type == 3) {
        	_this.winningAlert();
        }
		
	},
	/**
	 * 显示分数球
	 * @param {Object} value
	 * @param {Object} critical
	 * @param {Object} isDouble
	 * @param {Object} callback
	 */
    showBall: function(value, power, weekPower, critical, isDouble){
        var _this = this;
        var target = $('#powerButton').parent();
        //var powerBall = $('#powerBall');
		//var peopleImage = $('.people-image');
		var soyaImage = $('.soya-image');
		var indexBodyBg = $('.index-body-bg');
        var normalStrike = '<div class="normal-strike">{0}</div>';
        var doubleStrike = '<div class="double-strike">{0}</div>';
        var criticalStrike = '<div class="critical-strike">{0}</div>';
        var ball = $(String.format(normalStrike, '+' + value));
		 //userAgent
        var userAgent = window.navigator.userAgent.toLowerCase();
        
        if (isDouble) {
            ball = $(String.format(normalStrike, '<span style="font-size:1.4em;">2</span>X' + parseInt(value / 2, 10)));
        }
//        if (isDouble) {
//            ball = $(String.format(doubleStrike, '+' + value));
//        }
        if (critical) {
			ball = $(String.format(criticalStrike, '+' + value));
            if (isDouble) {
                ball = $(String.format(criticalStrike, '<span style="font-size:1.4em;">2</span>X' + parseInt( value / 2, 10)));
            }
			_this.playAudio('critical');
        }
        target.append(ball);
        setTimeout(function(){
            ball.attr('data-top', ball.offset().top).attr('data-left', ball.offset().left).css('transition', 'all .6s ease-in').css('transform', 'translateY(-30px) scale(2, 2) translateZ(0)').css('opacity', 1);
			//powerBall.css('transform', 'translateY(-60%) scale(1.5) translateZ(0)');
			//peopleImage.css('transform', 'translateY(50px) scale(1) translateZ(0)');
        });
        if (_this.cacheCriticalBall) {
            _this.cacheCriticalBall && _this.cacheCriticalBall.click() && (_this.cacheCriticalBall = null);
        }
        if (critical) {
            setTimeout(function(){
//                if (userAgent.indexOf('android') != -1) {
//                    soyaImage.css('transition', 'all 0s ease')
//                }
				soyaImage.css('opacity', 1).css('transform', 'scale(1) translateY(0) translateZ(0)');
				indexBodyBg.removeClass('none');
			});
			_this.cacheCriticalBall = ball;
            setTimeout(function(){
                ball.click(function(){
                    ball.css('transition', 'all ' + 1 + 's ease-out').css('transform', 'translateY(-' + (parseFloat(ball.offset().top) + 50) + 'px) translateX(-' + (parseFloat(ball.offset().left) / 6) + 'px) scale(1, 1) translateZ(0)').css('opacity', 0.8);
                    //powerBall.css('transform', 'translateY(-50%) scale(1) translateZ(0)');
                    //peopleImage.css('transform', ' translateY(0px) scale(0.9) translateZ(0)');
                    soyaImage.css('opacity', 0).css('transform', 'scale(10) translateY(-100px) translateZ(0)');
                    indexBodyBg.addClass('none');
                    setTimeout(function(){
                        ball.remove();
                    }, 1000)
                })
            }, 500)
			_this.setGatherNum(power, weekPower);
            return;
        }
        setTimeout(function(){
            ball.css('transition', 'all ' + 1 + 's ease-out').css('transform', 'translateY(-' + (parseFloat(ball.offset().top) + 50) + 'px) translateX(-' + (parseFloat(ball.offset().left) / 6) + 'px) scale(1, 1) translateZ(0)').css('opacity', 0.8);
            //powerBall.css('transform', 'translateY(-50%) scale(1) translateZ(0)');
            //peopleImage.css('transform', ' translateY(0px) scale(0.9) translateZ(0)');
            setTimeout(function(){
                ball.remove();
                _this.setGatherNum(power, weekPower);
            }, 1000)
        }, 600)
        
        
    },
	/**
	 * 设置送米值
	 */
	setGatherNum: function(power, weekPower){
		var _this = this;
		var gatherNum = $('#gatherNum');
		$('#zzNum').html(parseInt(power/2000));
		//var gatherNumWeek = $('#gatherNumWeek');
		_this.countUp(gatherNum, power, 10);
		//_this.countUp(gatherNumWeek, weekPower, 10);
	},
	/**
	 * 消耗体力
	 */
    setEnergyCost: function(){
        var energyCost = parseInt($('div[data-id="indexPage"]').attr('data-energycost'), 10);
        var powerNum = $('#powerNum');
        var startNum = parseInt(powerNum.attr('data-power'), 10);
        this.setEngery(startNum - energyCost);
    },
	/**
	 * 秘籍弹窗
	 */
	showDoubleDialog: function(){
		this.alert(MIJI, '', '', '', 'padding-top:12%; font-size:1.5em;line-height:1.5em;');
	},
    initIntroduceSwipe: function(){
        var slider = $('.introduce-slide');
        var startSlider = 0;
		var sliderBar = $('.introduce-slide-tabs');
        var delay = 10000;
        if (!slider[0]) {
            return;
        }
        if (slider.attr('data-rendered') == 'true') {
            return;
        }
        slider.css('opacity', 1);
        //轮播初始化
        var swipeSlider = slider.Swipe({
            startSlide: startSlider,
            auto: false,
            continuous: false,
            disableScroll: false,
            stopPropagation: true,
            callback: function(index, element){
                if (index === (slider.find('.introduce-slide-item').length - 1)) {
                    $('.introduce-slide-tabs').css('bottom', 65);
					$('.introducce-button').show();
                }
                else {
                    $('.introduce-slide-tabs').css('bottom', 10);
					$('.introducce-button').hide();
                }
            },
            transitionEnd: function(index, element){
                sliderBar.find('.introduce-slide-tab').eq(index).addClass('active').siblings().removeClass('active');
            }
        }).data('Swipe');
		slider.find('.introduce-slide-item').each(function(index){
			$('.introduce-slide-tabs').append('<div class="introduce-slide-tab' + (index == 0 ? ' active' : '') + '"></div>');
		})
		
    },
	
	/**
	 * 消息弹窗
	 * @param {Object} msg
	 * @param {Object} close
	 */
	alert: function(msg, target, close, type, style){
        
        if (!(close === false || typeof close === 'function')) {
            close = true;
        }
		var dialogType = '<div class="help-mask"><div class="help-mask-bubble"><img src="../addons/hl_zzz/template/images/dialog-bg-1.png" border="0" alt=""><span class="help-mask-text" style="{0}">{1}</span></div></div>';
		if(type == 'winning'){
			dialogType = '<div class="help-mask"><div class="help-mask-bubble"><img src="../addons/hl_zzz/template/images/dialog-bg-2.png" border="0" alt=""><span class="help-mask-text" style="{0}">{1}</span></div></div>';
		}
		var dialog = $(String.format(dialogType, style || '', msg));
		!target && $('div[data-id="indexPage"]').append(dialog);
		target && target.append(dialog);
		dialog.fadeIn();
		$('.page').css('overflow', 'hidden');
		setTimeout(function(){
            if (typeof close === 'function') {
                dialog.click(function(){
                    close();
                })
                return;
            }
			close && dialog.click(function(){
				$(this).remove();
				$('.page').css('overflow', 'auto');
			})
		}, 200)
	},
	/**
	 * 中奖弹窗
	 */
    winningAlert: function(){
        this.alert('', '', '', 'winning');
        html2canvas(document.body, {
            onrendered: function(canvas){
				if(!canvas){
					return;
				}
                if (!canvas.toDataURL) {
                    return;
                }
                
            }
        });
    },
	
	

	
	
	
}

$(document).ready(function(){

    //初始化页面切换
    pageLoad = $(document).pageLoad({
        changeHash: $('div[data-id="introducePage"]')[0] ? false : true,
        popstate: false,
        load: HL.COMMON.pageLoad,
        beforeload: function(){
            //统计
            window.mobileStatisticsRequest && window.mobileStatisticsRequest(true);
        }
    });
    //消除移动端点击延迟
    $(function(){
        FastClick.attach(document.body);
    });
	//轮询关注
    if (window.isSubscribe === false) {
    	HL.COMMON.isSubscribe();
    }
})
//微信分享
//_WXShare(HL.COMMON.getWxShareImg(), 100, 100, $('title').html(), HL.COMMON.getWxShareDes(), window.location.href, window.appId || '');
