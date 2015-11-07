!
function(i) {
    var n = $(".j-picture"),
    t = $(".j-x-picture");
    n.on("click",
    function() {
        t.show()
    }),
    t.on("click",
    function() {
        t.hide()
    }),
    i.guess = {
        config: function(i) {
            return this.qid = i,
            this
        },
        run: function() {
            this.listenChoices()
        },
        listenChoices: function() {
            var i = this;
            return $(".j-choice").on("click",
            function() {
                var n = $(this).data("id");
                i.submit(n)
            }),
            this
        },
        submit: function(i) {
            var n = this.qid;
            $("body").addClass("ajax-loading"),
            $.post("/wxapp/draw/a", {
                qid: n,
                cid: i
            },
            function(i) {
                location.href = i.map.url
            },
            "json")
        }
    }
} (window);
