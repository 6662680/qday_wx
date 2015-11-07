_.templateSettings = {
    interpolate: /\{\{(.+?)\}\}/g
};
var APP = window.APP || {};
APP.CoverView = Backbone.View.extend({
    template: _.template($("#coverTemplate").html()),
    initialize: function() {
        $("#container").html(this.render().el)
    },
    render: function() {
        return this.$el.html(this.template()),
        this
    }
}),
APP.DescriptionView = Backbone.View.extend({
    template: _.template($("#descriptionsTemplate").html()),
    initialize: function() {
        $("#container").html(this.render().el),
        this.listenRandom()
    },
    render: function() {
        return this.$el.html(this.template()),
        this
    },
    listenRandom: function() {
        var t = this.$el;
        t.on("click", ".j-random",
        function() {
            var e = t.find(".main li").clone(),
            n = _.sample(Array.prototype.slice.call(e), e.length);
            t.find(".main ul").html(n)
        })
    }
}),
APP.DrawView = Backbone.View.extend({
    template: _.template($("#drawTemplate").html()),
    initialize: function(t) {
        this.options = t,
        $("#container").html(this.render().el);
        var e = this.$el.find(".inner").width();
        this.$el.find("canvas").attr({
            width: e,
            height: e
        }),
        this.listenDraw()
    },
    render: function() {
        var t = this.options;
        return this.$el.html(this.template({
            content: t.content
        })),
        this
    },
    listenDraw: function() {
        var t = this.$el.find("canvas"),
        e = this,
        n = window.newCanvas.config(t).run();
        this.$el.on("click", ".j-clear",
        function() {
            n.reset()
        }).on("click", ".j-done",
        function() {
            $("body").addClass("ajax-loading"),
			//console.log(n.getImg());
            $.post(getUrl(), {
                qid: e.options.obId,
                image: n.getImg()
            },
            function(imgid) {
                location.href = myImage(imgid);
            },
            "json")
        })
    }
}),
APP.DescriptionRouter = Backbone.Router.extend({
    routes: {
        "": "cover",
        cover: "cover",
        descriptions: "descriptions",
        "description/:obId/:content": "draw"
    },
    initialize: function() {
        this.cover()
    },
    cover: function() {
        new APP.CoverView
    },
    descriptions: function() {
        new APP.DescriptionView
    },
    draw: function(t, e) {
        new APP.DrawView({
            obId: t,
            content: e
        })
    }
}),
new APP.DescriptionRouter,
Backbone.history.start();