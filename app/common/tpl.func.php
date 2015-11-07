<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');


function tpl_form_field_image($name, $value = ''){
	
	$thumb = empty($value) ? 'images/global/nopic.jpg' : $value;
	$thumb = tomedia($thumb);
	
	$html = <<<EOF

<div class="input-group">
	<input type="text" name="$name" value="$value" class="form-control" autocomplete="off" readonly="readonly">
	<span class="input-group-btn">
		<button class="btn btn-default" onclick="appupload(this)" type="button">上传图片</button>
	</span>
</div>
<span class="help-block">
	<img style="max-height:100px;" src="$thumb" >
</span>

<script>
window.appupload = window.appupload || function(obj){
	require(['jquery', 'util'], function($, u){
		u.image(obj, function(src){
			$(obj).parent().prev().val(src);
			$(obj).parent().parent().next().find('img').attr('src',u.tomedia(src));
		});
	});
}
</script>

EOF;
	return $html;
}