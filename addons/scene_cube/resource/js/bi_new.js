(function () {
	//BI工具
	var util = (function () {
		//BI工具类
		function BIUtil (){
			//数据缓存
			this.__dataCache__ = {
				ip : '',
				province : '未知地区',
				city : '未知地区',
				startTime : Date.now(),
				loadTime : 0
			};
			this.__readyArr__ = [];
			this.__isReady__ = false;
			//初始化
			BIUtil._init.apply(this);
		}

		//通过jsonp方式访问脚本
		BIUtil._init = function () {
			var that = this;
			var count = 0;
			//获取IP地址、物理地址
			this.getScript('http://pv.sohu.com/cityjson', function (e) {
				//保存ip地址
				that.__dataCache__.ip = returnCitySN.cip;
				//保存地区
				if(returnCitySN.cname.indexOf('未能识别') >= 0){
					that.__dataCache__.province = that.__dataCache__.city = '未知地区';
				}else if(returnCitySN.cname.indexOf('省') >= 0){
					var regResult = (/(^.*[省])(.*$)/ig).exec(returnCitySN.cname);
					that.__dataCache__.province = regResult[2] ? regResult[1] : regResult[0];
					that.__dataCache__.city = regResult[2] ? regResult[2] : regResult[1];
				}else{
					that.__dataCache__.province = that.__dataCache__.city = returnCitySN.cname;
				}
				//执行ready
				if(++count >= 2){
					that.__isReady__ = true;
					that.ready();
				}
			});

			//获取加载时间
			this.bindEvent(window, 'load', function (e) {
				//加载时间 = (当前时间 - 开始时间) + 显示误差值
				that.__dataCache__.loadTime = (Date.now() - that.__dataCache__.startTime) + 600;
				//执行ready
				if(++count >= 2){
					that.__isReady__ = true;
					that.ready();
				}
			});
		};

		//绑定事件
		BIUtil.prototype.bindEvent = function (element, eventName, handler) {
			if(element.attachEvent){
				element.attachEvent('on' + eventName, handler);
			}else{
				element.addEventListener(eventName, handler, false);
			}
		}

		//移除事件
		BIUtil.prototype.unbindEvent = function (element, eventName, handler) {
			if(element.detachEvent){
				element.detachEvent('on' + eventName, handler);
			}else{
				element.removeEventListener(eventName, handler, false);
			}
		}

		//通过jsonp方式访问脚本
		BIUtil.prototype.ready = function (fn) {
			if(fn){
				this.__readyArr__.push(fn);
				if(this.__isReady__){
					fn(this);
				}
			}else if(this.__isReady__){
				for(var i = 0; i < this.__readyArr__.length; i++){
					this.__readyArr__[i](this);
				}
			}
		};

		//通过jsonp方式访问脚本
		BIUtil.prototype.getScript = function (url, callback, isJsonp) {
			//创建script标签
			var script = document.createElement('script');
			script.type = 'text/javascript';
			//回调事件
			if(callback){
				if(isJsonp){
					window.jsonp_callbacks = window.jsonp_callbacks || {};
					var callbackKey = ['fn', Math.random().toString(16).replace('0.','')].join('_');
					window.jsonp_callbacks[callbackKey] = callback;
					url = this.addQueryString(url, {callback : 'jsonp_callbacks.' + callbackKey});
				}else{
					script.onload = callback;
				}
			}
			//设置src
			script.src = url;
			//添加到head
			document.head.appendChild(script);
		};

		//格式化Url参数
		BIUtil.prototype.formatUrlParams = function(data){
			var arr = [];
			for (var name in data) {
				arr.push(encodeURIComponent(name) + '=' + encodeURIComponent(data[name]));
			}
			return arr.join('&');
		};

		//添加queryString
		BIUtil.prototype.addQueryString = function(url, queryString){
			if(typeof(queryString) == 'object'){
				queryString = this.formatUrlParams(queryString);
			}else{
				queryString = queryString.replace(/^\s+|\s+$/ig, '');
			}
			if(queryString){
				url = [url, (url.indexOf('?') >= 0 ? '&' : '?'), queryString].join('');
			}
			return url;
		};

		//获取网络类型
		BIUtil.prototype.ajax = function (options) {

		};

		//设置Cookie
		BIUtil.prototype.getCookie = function (key) {
			var reg = new RegExp('(^|\\s+)' + key +'=([^;]*)(;|$)');
			var regResult = document.cookie.match(reg);
			if(regResult){
				return unescape(regResult[2]);
			}else{
				return '';
			}
		};

		//获取Cookie
		BIUtil.prototype.setCookie = function (key, value, expires) {
			var cookieItem = key + '=' + escape(value);
			if(expires){
				if(typeof(expires) == 'number'){
					expires = new Date(expires);
				}
				cookieItem += ';expires=' + expires.toGMTString();
			}
			document.cookie = cookieItem;
		};

		//创建一个GUID
		BIUtil.prototype.createGuid = function () {
			//定义guid
			var guid = '';
			//创建guid
			do{
				guid += Math.random().toString(16).replace('0.','');
			}while(guid.length < 32)
			guid = [guid.substr(0, 8), guid.substr(8, 4), guid.substr(12, 4), guid.substr(16, 4), guid.substr(20, 12)].join('-');
			//返回guid
			return guid.toUpperCase();
		};

		//获取网络类型
		BIUtil.prototype.getNetworkType = function () {
			var networkType = 'UNKNOWN';
			var result = (/NetType\/([^\s]*)/ig).exec(navigator.userAgent);
			if(result){
				networkType = result[1];
			}else if(navigator.connection){
				var connection = navigator.connection;
				var type = connection['type'];
				for(var key in connection){
					if( key != 'type' && connection[key] == type){
						networkType = key;
					}
				}
			}
			return networkType;
		};

		//获取今天日期的结束时间
		BIUtil.prototype.getToDayEndTime = function () {
			var endTime = new Date();
			endTime.setHours(23);
			endTime.setMinutes(59);
			endTime.setSeconds(59);
			endTime.setMilliseconds(999);
			return endTime.getTime();
		};

		//获取UID
		BIUtil.prototype.getUID = function () {
			//从Cookie获取uid
			var uid = this.getCookie('BI_UID');
			//如没有则创建uid
			if(!uid){
				uid = this.createGuid();
				var expires = new Date();
				expires.setFullYear(expires.getFullYear() + 60);
				this.setCookie('BI_UID', uid, expires);
			}
			//返回uid
			return uid;
		};

		//获取UUID（UV统计）
		BIUtil.prototype.getUUID = function () {
			//从Cookie获取uid
			var uuid = this.getCookie('BI_UUID');
			//如没有则创建uid
			if(!uuid){
				uuid = this.createGuid();
				var expires = this.getToDayEndTime();
				this.setCookie('BI_UUID', uuid, expires);
			}
			//返回uid
			return uuid;
		};

		//获取操作系统名称
		BIUtil.prototype.getOS = function () {
			//定义结果变量
			var name = 'Other';
			var version = '';
			//获取userAgent
			var ua = navigator.userAgent;
			//移动平台iOS探测
			var reg = /like Mac OS X|Android|Windows Phone|Symbian/ig;
			var regResult = reg.exec(ua);
			if(!regResult){
				reg = /Mac OS X|Windows NT|Linux/ig;
				regResult = reg.exec(ua);
			}
			if(!regResult){
				//返回UNKNOWN
				return name;
			}else{
				//操作系统检测
				switch(regResult[0]){
					case 'like Mac OS X':
						name = 'iOS';
						reg = /(iPhone|iPod|iPad).*?OS\s*(\d*[\_|\.\d]*)/ig;
					break;
					case 'Android':
						name = 'Android';
						reg = /(Android)\s*(\d*[\.\d]*)/ig;
					break;
					case 'Windows Phone':
						name = 'Windows Phone';
						reg = /(Windows Phone)\s*[OS]*\s*(\d*[\.\d]*)/ig;
					break;
					case 'Symbian':
						name = 'Symbian';
						reg = /(Symbian)\s*[OS]*\/*\s*(\d[\.\d]*)/ig;
					break;
					case 'Mac OS X':
						name = 'OS X';
						reg = /(Mac OS X)\s*(\d*[\_|\.\d]*)/ig;
					break;
					case 'Windows NT':
						name = 'Windows NT';
						reg = /(Windows NT)\s*(\d*[\_|\.\d]*)/ig;
					break;
					case 'Linux':
						name = 'Linux';
						reg = /(Linux)\s*(i*\d*)/ig;
					break;
				}
				//获取版本号
				regResult = reg.exec(ua);
				if(regResult && regResult.length >= 3){
					version = regResult[2].replace(/\_+/ig, '.');
					reg = /^\d+\.*\d*/ig;
					regResult = reg.exec(version);
					if(regResult){
						version = regResult[0];
					}
				}
			}

			//返回操作系统名称+版本号
			return [name, version].join(' ');
		};

		//获取操作系统名称
		BIUtil.prototype.getBrowser = function () {
			//定义结果变量
			var name = 'Other';
			var version = '';
			//获取userAgent
			var ua = navigator.userAgent;
			//移动平台iOS探测
			var reg = /MSIE|Chrome|Firefox|Opera|UCBrowser|UCWEB|Safari/ig;
			var regResult = reg.exec(ua);
			if(!regResult){
				//返回UNKNOWN
				return name;
			}else{
				//浏览器检测
				switch(regResult[0]){
					case 'MSIE':
						name = 'IE';
						reg = /MS(IE)[\/|\s]+(\d*[\.\d]*)/ig;
					break;
					case 'Chrome':
						name = 'Chrome';
						reg = /(Chrome)[\/|\s]+(\d*[\.\d]*)/ig;
					break;
					case 'Firefox':
						name = 'Firefox';
						reg = /(Firefox)[\/|\s]+(\d*[\.\d]*)/ig;
					break;
					case 'Safari':
						name = 'Safari';
						reg = /(Safari)[\/|\s]*(\d*[\.\d]*)/ig;
					break;
					case 'Opera':
						name = 'Opera';
						reg = /(Opera)[\/|\s]+(\d*[\.\d]*)/ig;
					break;
					case 'UCBrowser':
						name = 'UC';
						reg = /(UCBrowser)[\/|\s]+(\d*[\.\d]*)/ig;
					break;
					case 'UCWEB':
						name = 'UC';
						reg = /(UCWEB)[\/|\s]*(\d*[\.\d]*)/ig;
					break;
				}
				//获取版本号
				regResult = reg.exec(ua);
				if(regResult && regResult.length >= 3){
					version = regResult[2].replace(/\_+/ig, '.');
					reg = /^\d+\.*\d*/ig;
					regResult = reg.exec(version);
					if(regResult){
						version = regResult[0];
					}
				}
			}

			//返回操作系统名称+版本号
			return [name, version].join(' ');
		};

		//获取IP
		BIUtil.prototype.getIP = function () {
			return this.__dataCache__.ip;
		};

		//获取省
		BIUtil.prototype.getProvince = function () {
			return this.__dataCache__.province;
		};

		//获取城市
		BIUtil.prototype.getCity = function () {
			return this.__dataCache__.city;
		};

		//获取屏幕分辨率
		BIUtil.prototype.getDpi = function (e) {
			return [window.screen.width, window.screen.height].join('*');
		};

		//获取距离此刻的访问时长
		BIUtil.prototype.getRemainTime = function () {
			return Date.now() - this.__dataCache__.startTime;
		};

		//获取统计的时间点
		BIUtil.prototype.getSTime = function () {
			return this.__dataCache__.startTime;
		};

		//获取页面加载时间（第一屏显示时间）
		BIUtil.prototype.getLoadTime = function () {
			return this.__dataCache__.loadTime;
		};

		//实例化工具类
		return new BIUtil();
	})();


	//BI接口
	var bi = (function (util) {
		//BI接口类
		function BI() {
			this.__postData__ = {};
			//初始化
			BI._init.apply(this);
		}

		//获取基础数据
		BI._init = function() {
			var that = this;
			//post基础数据
			var onloadCallback = function (e) {
				util.ready(function (e) {
					//获取基础数据和行为数据
					BI._getBasicData.apply(that);
					BI._getBehaviorData.apply(that);
					//提交基础数据和行为数据
					BI._postBasicData.apply(that);
					BI._postBehaviorData.apply(that);
					//判断是否为云购页面
					if(document.location.href.indexOf('.cn/wx/pay') > 0){
						//提交云购数据
						BI._getYunGoData.apply(that);
						BI._postYunGoData.apply(that);
					}
				});
			};
			util.bindEvent(window, 'load', onloadCallback);

			//post行为数据
			/*var onunloadCallback = function (e) {
				BI._getBehaviorData.apply(that);
				BI._postBehaviorData.apply(that);
			};
			util.bindEvent(window, 'load', onunloadCallback);*/
		};

		//BI数据push接口
		BI.prototype.push = function (serviceType, key, value) {
			var serviceKey = 'service_' + serviceType;
			var biItem = this.__postData__[serviceKey];
			if(!biItem){
				biItem = {
					opt : {
						service_type : serviceType
					},
					data : {}
				};
				this.__postData__[serviceKey] = biItem;
			}
			biItem.data[key] = value;
		}

		//BI数据push接口
		BI.prototype.getDataByServiceType = function (serviceType) {
			var serviceKey = 'service_' + serviceType;
			return this.__postData__[serviceKey];
		}

		//获取基础数据
		BI._getBasicData = function () {
			//自动获取基础数据
			//this.push(1, 'device_type', util.getDeviceType());	//设备类型（三星/苹果/小米...）
			this.push(1, 'ua', navigator.userAgent);				//userAgent
			this.push(1, 'browser', util.getBrowser());				//浏览器名称
			this.push(1, 'os', util.getOS());						//操作系统
			this.push(1, 'lang', navigator.language);				//语言类型
			this.push(1, 'ip', util.getIP());						//IP地址
			this.push(1, 'stime', util.getSTime());					//统计时间
			//this.push(1, 'device', util.getDeviceType());			//设备名
			//this.push(1, 'activity_id', util.getActivityID());	//LiveApp ID
			this.push(1, 'link', document.location.href);			//url
			this.push(1, 'refer', document.referrer);				//refer
			this.push(1, 'dpi', util.getDpi());						//分辨率
			this.push(1, 'uuid', util.getUUID());					//UV统计ID
			this.push(1, 'uid', util.getUID());						//用户唯一标识ID
			this.push(1, 'nettype', util.getNetworkType());			//网络类型
			//this.push(1, 'site_id', util.getSiteID());			//LiveApp 所属用户 ID
			this.push(1, 'province', util.getProvince());			//省份
			this.push(1, 'city', util.getCity());					//城市

			//返回基础数据
			return this.getDataByServiceType(1);
		};

		//获取行为数据
		BI._getBehaviorData = function() {
			//自动获取基础数据
			this.push(2, 'link', document.location.href);			//url
			this.push(2, 'ref', document.referrer);					//refer
			this.push(2, 'lt', util.getLoadTime());					//加载时间
			//this.push(2, 'isShare', Date.now());					//是否分享
			//this.push(2, 'sf', util.getNetworkType());			//谁分享的（PV分享）
			//this.push(2, 'rt', util.getRemainTime());				//停留时间
			//this.push(2, 'vch', util.getIP());					//访问渠道（微信/二维码）
			//this.push(2, 'vd', util.getProvince());				//访问深度
			this.push(2, 'uid', util.getUID());						//用户唯一标识ID
			this.push(2, 'stime', util.getSTime());					//统计时间

			//返回基础数据
			return this.getDataByServiceType(2);
		};

		//获取基础数据
		BI._getYunGoData = function() {
			//自动获取基础数据
			//this.push(3, 'sid', document.location.href);			//商铺ID
			//this.push(3, 'shopId', document.referrer);			//商品ID
			this.push(3, 'link', document.location.href);			//url
			this.push(3, 'ref', document.referrer);					//refer
			this.push(3, 'uid', util.getUID());						//IP地址
			//this.push(3, 'rt', util.getRemainTime());				//停留时长
			this.push(3, 'ip', util.getIP());						//IP地址
			this.push(3, 'province', util.getProvince());			//省份
			this.push(3, 'city', util.getCity());					//城市
			this.push(3, 'stime', util.getSTime());					//统计时间

			//返回基础数据
			return this.getDataByServiceType(3);
		};

		//获取基础数据
		BI._postDataByServiceType = function(serviceType, callback) {
			//获取bi数据
			var biData = this.getDataByServiceType(serviceType);
			//bi数据中的url转义
			var temp;
			for(var key in biData.data){
				temp = biData.data[key];
				if(typeof(temp) == 'string' && temp.indexOf('http') == 0){
					biData.data[key] = escape(temp);
				}
			}
			//数据提交url
			var url = ['http://121.40.184.62?p=', JSON.stringify(biData)].join('');
			//通过脚本提交BI数据
			util.getScript(url, callback);
		};

		//提交基础数据
		BI._postBasicData = function(callback) {
			BI._postDataByServiceType.apply(this, [1, callback]);
		};

		//提交行为数据
		BI._postBehaviorData = function(callback) {
			BI._postDataByServiceType.apply(this, [2, callback]);
		};

		//提交云购数据
		BI._postYunGoData = function(callback) {
			BI._postDataByServiceType.apply(this, [3, callback]);
		};

		//返回BI类的实例
		return new BI();
	})(util);


	//将保存到window命名空间下
	window.bi = bi;
	window.util = util;
})();