/*
 根据html数据创建一个ITEM节点
 */
function buildAddForm(id, targetwrap) {
    var sourceobj = $('#' + id);
    var html = $('<div class="item">');
    id = id.split('-')[0];
    var size = $('.item').size();
    var htmlid = id + '-item-' + size;
    while (targetwrap.find('#' + htmlid).size() >= 1) {
        var htmlid = id + '-item-' + size++;
    }
    html.html(sourceobj.html().replace(/\(itemid\)/gm, htmlid));
    html.attr('id', htmlid);
    targetwrap.append(html);
    return html;
}
/*
 切换一个节点的编辑状态和显示状态
 */
function doEditItem(itemid) {
    $('#append-list .item').each(function(){
        $('#form', $(this)).css('display', 'none');
        $('#show', $(this)).css('display', 'block');
    });
    var parent = $('#' + itemid);
    $('#form', parent).css('display', 'block');
    $('#show', parent).css('display', 'none');
}

function doDeleteItem(itemid, deleteurl) {
    if (confirm('删除操作不可恢复，确认删除吗？')){
        if (deleteurl) {
            ajaxopen(deleteurl, function(){
                $('#' + itemid).remove();
            });
        } else {
            $('#' + itemid).remove();
        }
    }
    return false;
}

/*
 请求远程地址
 */
function ajaxopen(url, callback) {
    $.getJSON(url+'&time='+new Date().getTime(), function(data){
        if (data.type == 'error') {
            message(data.message, data.redirect, data.type);
        } else {
            if (typeof callback == 'function') {
                callback(data.message, data.redirect, data.type);
            } else if(data.redirect) {
                location.href = data.redirect;
            }
        }
    });
    return false;
}