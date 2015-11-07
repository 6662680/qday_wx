;(function($) {
    /**
     * 读取cookie的值
     */
    $.readCookie = function(name) {
        var cookieValue = "";
        var search = name + "=";

        if (document.cookie.length > 0) {
            offset = document.cookie.indexOf(search);
            if (offset != -1) {
                offset += search.length;
                end = document.cookie.indexOf(";", offset);
                if (end == -1)
                    end = document.cookie.length;
                cookieValue = decodeURIComponent(document.cookie.substring(offset, end));
            }
        }
        return cookieValue;
    };
})(jQuery);

;(function($) {
    /**
     * 写入cookie
     */
    $.writeCookie = function(name, value, time) {
        var expire = "";
        var conversion = 0;

        if (time) {
            var timeArr = time.split(""),
                timeArrLast = timeArr.length - 1,
                timeDype = timeArr[timeArrLast],
                trueTime = Number(timeArr.slice(0, timeArrLast).join(""));

            switch (timeDype) {
                case "h":
                    conversion = 3600000;
                    break;
                case "m":
                    conversion = 60000;
                    break;
                case "s":
                    conversion = 1000;
                    break;
                default:
                    conversion = 3600000;
            }

            expire = new Date((new Date()).getTime() + trueTime * conversion);
            expire = "; expires=" + expire.toGMTString();
        }

        document.cookie = name + "=" + encodeURIComponent(value) + "; path=/"+ expire;
    };
})(jQuery);