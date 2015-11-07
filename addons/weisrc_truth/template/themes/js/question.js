!function(t) {
    function n() {
        var t = Math.floor(Math.random() * o.length);
        return o[t]
    }
    var i = '<input type="text" class="input custom-question j-question-string" placeholder="想与朋友交换的真心话题..."/>', s = '<div class="another j-change"><label>换个问题</label></div>', e = '<div class="ajax-loading-layer"><img src="/wxapp/images/knowme/ajax_loading.gif"/></div>', o = [], r = ($("#theme"), $("#questions")), a = $("#mytruth");
    t.question = {
        setInit: function(t) {
            return o = t, this
        },
        run: function() {
            this.onPopState().evQuestionList().evBtnCustom().evForm()
        },
        runWithQuestionAnswer: function(t, n) {
            this.onPopState().evQuestionList().evBtnCustom().evForm(), "pushState"in history && history.pushState({
                q: t,
                a: n
            }, null, location.href), this.showFormWithQuestionAnswer(t, n)
        },
        onPopState: function() {
            var t = this;
            return $(window).on("popstate", function() {
                "state"in history && null !== history.state ? "a"in history.state ? t.showFormWithQuestionAnswer(history.state.q, history.state.a) : t.showForm(history.state.q) : t.showList()
            }), this
        },
        evQuestionList: function() {
            var t = this;
            return r.on("click", ".j-question", function() {
                var n = $(this).data("q");
                t.showForm(n), "pushState"in history && history.pushState({
                    q: n
                }, null, "/wxapp/truth/2")
            }), this
        },
        evBtnCustom: function() {
            var t = this;
            return r.on("click", ".j-custom", function() {
                t.showForm(), "pushState"in history && history.pushState({
                    q: void 0
                }, null, "/wxapp/truth/2")
            }), this
        },
        evForm: function() {
            return a.on("click", ".j-change", function() {
                for (var t = a.find(".j-question-string"), i = n(); i == t.text().replace(/^ +| +$/g, "");)
                    i = n();
                t.text(i), a.find(".input.answer").val("")
            }), a.find("form").on("submit", function(t) {
                t.preventDefault();
                var n = $(this).find(".j-question-string"), i = n.val() || n.text(), s = $(this).find(".input[name=a]").val();
                i = i.replace(/^ +| +$/g, ""), s = s.replace(/^ +| +$/g, ""), i.length <= 1 || i.length > 256 ? n.addClass("error-border") : s.length <= 1 ? alert("这也太简洁了……要不再多说两句？") : s.length > 256 ? alert("太长了哦（已超出" + (s.length-256) + "字）") : ($(this).find("input[name=q]").val(i), $(this)[0].submit(), $("body").append(e), "replaceState"in history && history.replaceState(null, null, "/wxapp/truth"))
            }).on("input", ".j-question-string", function() {
                $(this).removeClass("error-border")
            }), this
        },
        showList: function() {
            a.hide(), r.show()
        },
        showForm: function(t) {
            var n = a.find(".j-question-content");
            n.html(t ? '<p class="question-string j-question-string">' + t + "</p>" + s : i), a.find("[name=a]").val(""), r.hide(), a.show()
        },
        showFormWithQuestionAnswer: function(t, n) {
            var e = a.find(".j-question-content");
            o.indexOf(t)>-1 ? e.html('<p class="question-string j-question-string">' + t + "</p>" + s) : (e.html(i), a.find(".j-question-string").val(t)), a.find("[name=a]").val(n), r.hide(), a.show()
        }
    }
}(window);
