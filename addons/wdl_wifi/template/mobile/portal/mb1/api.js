var rptk_apis = new
(function () {
    var self = this;

    this.go_away = function () {
        var url = "http://service.rippletek.com/ext/portal/rptknotice.html";
        window.location.href = url;
    }

    function ajax_req(xmlhttp, method, url, cb) {
        xmlhttp.onreadystatechange = function() {
            cb(xmlhttp);
        }
        try {
            xmlhttp.open(method, url, true);
            xmlhttp.send();
        } catch(ex) {
            in_ajax = false;
            self.where_am_i_4s();
        }
    }

    var in_ajax = false;
    function done(xmlhttp) {
        if (xmlhttp.readyState == 4) {
            in_ajax = false;
            var status = xmlhttp.status;
            if(status == 200) {
                var res = JSON.parse(xmlhttp.responseText);
                if (res.sn) {
                    self.set_key(res.sn);
                } else {
                    self.go_away();
                }
            } else if (status == 0) {
                self.where_am_i_4s();
            }
        }
    }

    var dev_url = "http://rippletek.lan";
    var cnt = 0;
    this.where_am_i = function () {
        if (window.XMLHttpRequest) {
            var xmlhttp = new XMLHttpRequest();
            in_ajax = true;
            where_am_i_aux(xmlhttp);
        } else {
            self.where_am_i_4s();
        }
    }

    function where_am_i_aux(xmlhttp) {
        ajax_req(xmlhttp, "GET", dev_url + "/whereami?rand=" + Math.random(), done);
        setTimeout(function() {
            if (in_ajax) {
                if (cnt < 1) {
                    cnt ++;
                    where_am_i_aux(xmlhttp);
                }
            }
        }, 2500);
    }

    this.is_4s = false;
    this.where_am_i_4s = function () {
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = dev_url + "/4swhereami?rand=" + Math.random();
        var f = function () {
            if (document.body) {
                document.body.appendChild(script);
                self.is_4s = true;
            } else {
                setTimeout(f, 100);
            }
        };
        f();
    }

    this.ripple_inside = function () {
        var k = self.key();
        return (k != undefined);
    }

    this.m_key = undefined;
    this.key = function () {
        if (self.is_4s) {
            if (typeof sn == "string") {
                return sn;
            }
            return undefined;
        }
        return self.m_key;
    }

    this.set_key = function (k) {
        self.m_key = k;
    }
});
