define("http://core.h5.lietou-static.com/v1/public/cookie.js", [], function(require, exports) {
    var cookie = {
        get: function(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(";");
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == " ") c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) {
                    var ret;
                    try {
                        ret = decodeURIComponent(c.substring(nameEQ.length, c.length));
                    } catch (e) {
                        ret = unescape(c.substring(nameEQ.length, c.length));
                    }
                    return ret;
                }
            }
            return null;
        },
        set: function(name, value, days, path, domain, secure) {
            var expires;
            if (typeof days == "number") {
                var date = new Date();
                date.setTime(date.getTime() + days * 24 * 60 * 60 * 1e3);
                expires = date.toGMTString();
            } else if (typeof days == "string") {
                expires = days;
            } else {
                expires = false;
            }
            document.cookie = name + "=" + encodeURIComponent(value) + (expires ? ";expires=" + expires : "") + (path ? ";path=" + path : "") + (domain ? ";domain=" + domain : "") + (secure ? ";secure" : "");
        },
        del: function(name, path, domain, secure) {
            this.set(name, "", -1, path, domain, secure);
        },
        isLogin: function() {
            return this.get("user_id") != null;
        }
    };
    return cookie;
});