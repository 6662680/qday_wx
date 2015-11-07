var utilSOYA = function () {

    return {
		replaceToBr: function(str){
            return str.replace(/\r\n/g, "<br/>").replace(/\r/g, "<br/>").replace(/\n/g, "<br/>");
        },
		replaceBr: function(str){
			return str.replace(/<br>/g, "\n").replace(/<BR>/g, "\n").replace(/<br\/>/g, "\n").replace(/<BR\/>/g, "\n");
		},
		htmlEncode : function(value, quot) {
            return !value ? value : String(value).replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, quot === true ? '\\&quot;' : "&quot;").replace(/'/g, quot === true ? '\\&apos;' : "&apos;");
        },
		htmlDecode : function(value) {
            return !value ? value : String(value).replace(/&gt;/g, ">").replace(/&lt;/g, "<").replace(/&quot;/g, '"').replace(/&amp;/g, "&").replace(/&apos;/g, "'");
        },
		isUrl: function(url){
			return /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/.test(url || '');
		},
		isNumber: function(s){
    		var regu = "^[0-9]+$";
    		var re = new RegExp(regu);
    		if (s.search(re) != - 1) {
        		return true;
    		}
    		else {
        		return false;
    		}
		},
		getStrLength: function(str){
            ///<summary>获得字符串实际长度，中文2，英文1</summary>
            ///<param name="str">要获得长度的字符串</param>
            var realLength = 0, len = str.length, charCode = -1;
            for (var i = 0; i < len; i++) {
                charCode = str.charCodeAt(i);
                if (charCode >= 0 && charCode <= 128) 
                    realLength += 1;
                else 
                    realLength += 2;
            }
            return realLength;
        },
        convertTime: function(time){
            time = parseInt(time, 10);
            if (isNaN(time)) {
                return '未知';
            }
            var second = parseInt(time / 1000, 10);
            var minute = parseInt(second / 60, 10);
            var hour = parseInt(minute / 60, 10);
            
            if (hour) {
                minute = parseInt(minute % 60, 10);
                second = parseInt(second % 60, 10);
                return hour + '小时' + minute + '分' + second + '秒';
            }
            if (minute) {
                second = parseInt(second % 60, 10);
                return minute + '分' + second + '秒';
            }
            return second + '秒';
        },
		toArray: function(){
			var ua = navigator.userAgent.toLowerCase();
			var isIE = /msie/.test(ua);
            return isIE ? function(a, i, j, res){
                res = [];
                for (var x = 0, len = a.length; x < len; x++) {
                    res.push(a[x]);
                }
                return res.slice(i || 0, j || res.length);
            }
         : function(a, i, j){
                return Array.prototype.slice.call(a, i || 0, j || a.length);
            };
        }(),
        convertUrl: function (url) {
            var path = window.path;
//            if (!path) {
//                throw new Error('请引入公共文件commonCss.jsp');
//            }
            if (path !== "") {
                path = path + "/";
            }
            else {
                path = "/";
            }
            if (url.substring(0, 1) === "/") {
                url = url.substring(1);
            }
            url = path + url;
            return url;
        },
        /**
         * 是否为空
         * @param str
         *
         */
        isNull: function (str) {
            if (str == undefined) return true;
            if (str == "") return true;
            var regu = "^[ ]+$";
            var re = new RegExp(regu);
            return re.test(str);
        },
        isMsie: function () {
            return /msie/.test(navigator.userAgent.toLowerCase());
        },
        mail: function (val) {
            var ver = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,4}$/;
            return ver.test(val);
        },
        contactNumber: function (val) {
            var ver = /^[0-9]{5,11}$/;
            return ver.test(val);
        },
        phone: function (val) {
            var ver = /^[0-9]{4}-[0-9]{7}$/;
            return ver.test(val);
        },
        /**
         * 5-15位的qq号码
         */
        qq: function (val) {
            var ver = /^[1-9][0-9]{4,14}$/;
            return ver.test(val);
        },
        /**
         * 字母、数字、-_组成
         */
        letterDigitLine: function (val) {
            var ver = /^[0-9a-zA-Z_\-]{1,}$/;
            return ver.test(val);

        },
        /**
         * 钱 最多2位小数点money
         */
        isMoney: function (val) {
            var ver = /^([1-9][\d]{0,}|0)(\.[\d]{1,2})?$/;
            return ver.test(val);
        },
        isMoneyNoLenth: function (val) {
            var ver = /^(([1-9]\d{0,9})|0)(\.\d)?$/;
            return ver.test(val);
        },
        /**
         * 依赖jquery-1.4.2
         * 依赖jquery.json-2.2，参考http://code.google.com/p/jquery-json/
         * 用于将form序列化成json串，并且可以反序列化添充回来
         */
        serializeObjectToJson: function (serializeArray) {
            /**
             * 此方法代码参考：http://css-tricks.com/snippets/jquery/serialize-form-to-json/
             */
            var o = {};
            var a = serializeArray;
            $.each(a, function () {

                var _ns = this.name.split(".");
                if (_ns.length == 1) {
                    if (o[this.name]) {
                        if (!o[this.name].push) {
                            o[this.name] = [o[this.name]];
                        }
                        o[this.name].push(this.value || '');
                    } else {
                        o[this.name] = this.value || '';
                    }
                } else {
                    var cur = o[_ns[0]];
                    if (cur === undefined) cur = o[_ns[0]] = {};
                    var len = _ns.length;
                    for (var i = 1; i < len - 1; i++) {
                        cur = cur[_ns[i]] = cur[_ns[i]] || {};
                    }
                    if (cur[_ns[len - 1]]) {
                        if (!cur[_ns[len - 1]].push) {
                            cur[_ns[len - 1]] = [cur[_ns[len - 1]]];
                        }
                        cur[_ns[len - 1]].push(this.value || '');
                    } else {
                        cur[_ns[len - 1]] = this.value || '';
                    }

                }
            });
            return o;
            // return $.toJSON(o);
        },
        /**
         * 阻止冒泡事件
         * @param e
         */
        stopPropagation: function (e) {
            var event = $.event.fix(e);
            event.stopPropagation();
        }
    };
}();
var JsonUtiSOYA = {
    //定义换行符
    n: "\n",
    //定义制表符
    t: "\t",
    //转换String
    convertToString: function(obj) {
        return JsonUtiSOYA.__writeObj(obj, 1);
    },
    //写对象
    __writeObj: function(obj    //对象
        , level             //层次（基数为1）
        , isInArray) {       //此对象是否在一个集合内
        //如果为空，直接输出null
        if (obj == null) {
            return "null";
        }
        //为普通类型，直接输出值
        if (obj.constructor == Number || obj.constructor == Date || obj.constructor == String || obj.constructor == Boolean) {
            var v = obj.toString();
            var tab = isInArray ? JsonUtiSOYA.__repeatStr(JsonUtiSOYA.t, level - 1) : "";
            if (obj.constructor == String || obj.constructor == Date) {
                //时间格式化只是单纯输出字符串，而不是Date对象
                return tab + ("\"" + v + "\"");
            }
            else if (obj.constructor == Boolean) {
                return tab + v.toLowerCase();
            }
            else {
                return tab + (v);
            }
        }

        //写Json对象，缓存字符串
        var currentObjStrings = [];
        //遍历属性
        for (var name in obj) {
            var temp = [];
            //格式化Tab
            var paddingTab = JsonUtiSOYA.__repeatStr(JsonUtiSOYA.t, level);
            temp.push(paddingTab);
            //写出属性名
            temp.push(name + " : ");

            var val = obj[name];
            if (val == null) {
                temp.push("null");
            }
            else {
                var c = val.constructor;

                if (c == Array) { //如果为集合，循环内部对象
                    temp.push(JsonUtiSOYA.n + paddingTab + "[" + JsonUtiSOYA.n);
                    var levelUp = level + 2;    //层级+2

                    var tempArrValue = [];      //集合元素相关字符串缓存片段
                    for (var i = 0; i < val.length; i++) {
                        //递归写对象
                        tempArrValue.push(JsonUtiSOYA.__writeObj(val[i], levelUp, true));
                    }

                    temp.push(tempArrValue.join("," + JsonUtiSOYA.n));
                    temp.push(JsonUtiSOYA.n + paddingTab + "]");
                }
                else if (c == Function) {
                    temp.push("[Function]");
                }
                else {
                    //递归写对象
                    temp.push(JsonUtiSOYA.__writeObj(val, level + 1));
                }
            }
            //加入当前对象“属性”字符串
            currentObjStrings.push(temp.join(""));
        }
        return (level > 1 && !isInArray ? JsonUtiSOYA.n : "")                       //如果Json对象是内部，就要换行格式化
            + JsonUtiSOYA.__repeatStr(JsonUtiSOYA.t, level - 1) + "{" + JsonUtiSOYA.n     //加层次Tab格式化
            + currentObjStrings.join("," + JsonUtiSOYA.n)                       //串联所有属性值
            + JsonUtiSOYA.n + JsonUtiSOYA.__repeatStr(JsonUtiSOYA.t, level - 1) + "}";   //封闭对象
    },
    __isArray: function(obj) {
        if (obj) {
            return obj.constructor == Array;
        }
        return false;
    },
    __repeatStr: function(str, times) {
        var newStr = [];
        if (times > 0) {
            for (var i = 0; i < times; i++) {
                newStr.push(str);
            }
        }
        return newStr.join("");
    },
    evalJSON:function(str){
        return eval('(' + str + ')');
    }
};
/** trim() method for String */
String.prototype.trim = function () {
    return this.replace(/(^\s*)|(\s*$)/g, '');
};
String.format = function(format){
    var args = utilSOYA.toArray(arguments, 1);
    return format.replace(/\{(\d+)\}/g, function(m, i){
        return args[i];
    });
}
$.extend({
    getUrlVars: function () {
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for (var i = 0; i < hashes.length; i++) {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    },
    getUrlVar: function (name) {
        return $.getUrlVars()[name];
    }
});
