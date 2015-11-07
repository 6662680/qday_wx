!
function(n) {
    var e, t = "#000",
    o = 0,
    i = -80;
    n.newCanvas = {
        config: function(n) {
            return e = n[0].getContext("2d"),
            e.strokeStyle = t,
            e.lineWidth = 5,
            e.lineJoin = e.lineCap = "round",
            this.$canvas = n,
            this
        },
        run: function() {
            return this.reset(),
            this.$canvas.handleTouch(),
            this.$canvas.handlePointer(),
            this.$canvas.handleMouse(),
            this
        },
        reset: function() {
            var n = this.$canvas[0];
            e.clearRect(0, 0, n.width, n.height),
            e.fillStyle = "#fff",
            e.fillRect(0, 0, n.width, n.height)
        },
        getImg: function() {
            return this.$canvas[0].toDataURL()
        }
    };
    var a = function(n, t) {
        o = 1,
        e.beginPath(),
        e.moveTo(n, t)
    },
    h = function(n, t) {
        o && (e.lineTo(n, t), e.stroke())
    },
    c = function() {
        o = 0
    };
    $.fn.handleTouch = function() {
        $(this).on("touchstart",
        function(n) {
            a(n.changedTouches[0].pageX, n.changedTouches[0].pageY - 44 + i)
        }),
        $(this).on("touchmove",
        function(n) {
            n.preventDefault(),
            h(n.changedTouches[0].pageX, n.changedTouches[0].pageY - 44 + i)
        }),
        $(window).on("touchend", c)
    },
    $.fn.handlePointer = function() {
        $(this).on("MSPointerDown",
        function(n) {
            a(n.pageX, n.pageY - 44 + i)
        }),
        $(this).on("MSPointerMove",
        function(n) {
            n.preventDefault(),
            h(n.pageX, n.pageY - 44 + i)
        }),
        $(window).on("MSPointerUp", c)
    },
    $.fn.handleMouse = function() {
        $(this).on("mousedown",
        function(n) {
            a(n.pageX, n.pageY - 44 + i)
        }),
        $(this).on("mousemove",
        function(n) {
            h(n.pageX, n.pageY - 44 + i)
        }),
        $(window).on("mouseup", c)
    }
} (window);