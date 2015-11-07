KindEditor.ready(function (K) {
    
    var editor = K.editor({
 		allowFileManager : true,
		uploadJson : "./index.php?act=attachment&do=upload",
		fileManagerJson : "./index.php?act=attachment&do=manager",

    }); 
    $('button.select_img').live("click",function (e) {
        editor.loadPlugin('images', function () {
            editor.plugin.imageDialog({
                imageUrl: $(e.target).parent().prevAll("input[type=text]").val(),
                clickFn: function (url, title, width, height, border, align) {
                    var $input = $(e.target).parent().prevAll("input[type=text]")
                    $input.val(url)
                    $input.hide();
                    var t_img = $(e.target).parent().prevAll(".thumb_img:first");
                    if (t_img.length == 0) {
                        var tmp = '<img class="thumb_img" src="{0}" style="max-height: 100px;">';
                        $input.before(tmp.format(url))
                    } else {
                        t_img.attr("src", url);
                    }

                    editor.hideDialog();
                }
            });
        });
    });
});
