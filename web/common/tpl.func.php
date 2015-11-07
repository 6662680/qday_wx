<?php
/**
 * [SxxPro System] Copyright (c) 2014 012WZ.COM
 * SxxPro is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');


function tpl_form_field_audio($name, $value = '', $options = array()) {
	
	$val = $default;
	if(!empty($value)) {
		$val = tomedia($value);
	}
	if(!is_array($options)){
		$options = array();
	}
	if(empty($options['tabs'])){
		$options['tabs'] = array('browser'=>'active', 'upload'=>'');
	}
	$options = array_elements(array('extras','tabs','global','dest_dir'), $options);
	$options['direct'] = true;
	$options['multi'] = false;
	
	$s = '';
	if (!defined('TPL_INIT_AUDIO')) {
		$s = '
<script type="text/javascript">
	function showAudioDialog(elm, base64options, options) {
		require(["util"], function(util){
			var btn = $(elm);
			var ipt = btn.parent().prev();
			var val = ipt.val();
			util.audio(val, function(url){
				if(url && url.filename && url.url){
					btn.prev().show();
					
					ipt.val(url.filename);
					ipt.attr("filename",url.filename);
					ipt.attr("url",url.url);
					
					setAudioPlayer();
				}
				if(url && url.media_id){
					ipt.val(url.media_id);
				}
			}, "" , '.json_encode($options).');
		});
	}

	function setAudioPlayer(){
		require(["jquery", "util", "jquery.jplayer"], function($, u){
			$(function(){
				$(".audio-player").each(function(){
					$(this).prev().find("button").eq(0).click(function(){
						var src = $(this).parent().prev().val();
						if($(this).find("i").hasClass("fa-stop")) {
							$(this).parent().parent().next().jPlayer("stop");
						} else {
							if(src) {
								$(this).parent().parent().next().jPlayer("setMedia", {mp3: u.tomedia(src)}).jPlayer("play");
							}
						}
					});
				});

				$(".audio-player").jPlayer({
					playing: function() {
						$(this).prev().find("i").removeClass("fa-play").addClass("fa-stop");
					},
					pause: function (event) {
						$(this).prev().find("i").removeClass("fa-stop").addClass("fa-play");
					},
					swfPath: "resource/components/jplayer",
					supplied: "mp3"
				});
				$(".audio-player-media").each(function(){
					$(this).next().find(".audio-player-play").css("display", $(this).val() == "" ? "none" : "");
				});
			});
		});
	}

	setAudioPlayer();
</script>';
		echo $s;
		define('TPL_INIT_AUDIO', true);
	}

	$s .= '
	<div class="input-group">
		<input type="text" value="'.$value.'" name="'.$name.'" class="form-control audio-player-media" autocomplete="off" '.($options['extras']['text'] ? $options['extras']['text'] : '').'>
		<span class="input-group-btn">
			<button class="btn btn-default audio-player-play" type="button" style="display:none;"><i class="fa fa-play"></i></button>
			<button class="btn btn-default" type="button" onclick="showAudioDialog(this, \''.base64_encode(iserializer($options)).'\','.str_replace('"','\'', json_encode($options)).');">选择媒体文件</button>
		</span>
	</div>
	<div class="input-group audio-player">
	</div>';
	return $s;
}


function tpl_form_field_multi_audio($name, $value = array(), $options = array()) {
	global $_W;
	
	$s = '';
	
	if(empty($options['tabs'])){
		$options['tabs'] = array('browser'=>'active', 'upload'=>'');
	}
	$options['direct'] = false;
	$options['multi'] = true;
	
	if (!defined('TPL_INIT_MULTI_AUDIO')) {
		$s .= '
<script type="text/javascript">
	function showMultiAudioDialog(elm, name) {
		require(["util"], function(util){
			var btn = $(elm);
			var ipt = btn.parent().prev();
			var val = ipt.val();

			util.audio(val, function(urls){
				$.each(urls, function(idx, url){
					var obj = $(\'<div class="multi-audio-item" style="height: 40px; position:relative; float: left; margin-right: 18px;"><div class="multi-audio-player"></div><div class="input-group"><input type="text" class="form-control" readonly value="\' + url.filename + \'" /><div class="input-group-btn"><button class="btn btn-default" type="button"><i class="fa fa-play"></i></button><button class="btn btn-default" onclick="deleteMultiAudio(this)" type="button"><i class="fa fa-remove"></i></button></div></div><input type="hidden" name="\'+name+\'[]" value="\'+url.filename+\'"></div>\');
					$(elm).parent().parent().next().append(obj);
					setMultiAudioPlayer(obj);
				});
			}, "" , '.json_encode($options).');
		});
	}
	function deleteMultiAudio(elm){
		require([\'jquery\'], function($){
			$(elm).parent().parent().parent().remove();
		});
	}
	function setMultiAudioPlayer(elm){
		require(["jquery", "util", "jquery.jplayer"], function($, u){
			$(".multi-audio-player",$(elm)).next().find("button").eq(0).click(function(){
				var src = $(this).parent().prev().val();
				if($(this).find("i").hasClass("fa-stop")) {
					$(this).parent().parent().prev().jPlayer("stop");
				} else {
					if(src) {
						$(this).parent().parent().prev().jPlayer("setMedia", {mp3: u.tomedia(src)}).jPlayer("play");
					}
				}
			});
			$(".multi-audio-player",$(elm)).jPlayer({
				playing: function() {
					$(this).next().find("i").eq(0).removeClass("fa-play").addClass("fa-stop");
				},
				pause: function (event) {
					$(this).next().find("i").eq(0).removeClass("fa-stop").addClass("fa-play");
				},
				swfPath: "resource/components/jplayer",
				supplied: "mp3"
			});
		});
	}
</script>';
		define('TPL_INIT_MULTI_AUDIO', true);
	}

	$s .= '
<div class="input-group">
	<input type="text" class="form-control" readonly="readonly" value="" placeholder="批量上传音乐" autocomplete="off">
	<span class="input-group-btn">
		<button class="btn btn-default" type="button" onclick="showMultiAudioDialog(this,\''.$name.'\');">选择音乐</button>
	</span>
</div>
<div class="input-group multi-audio-details clear-fix" style="margin-top:.5em;">';
	if(!empty($value) && !is_array($value)){
		$value = array($value);
	}
	if (is_array($value) && count($value)>0) {
		$n = 0;
		foreach ($value as $row) {
			$m = random(8);
			$s .= '
	<div class="multi-audio-item multi-audio-item-'.$n.'-'.$m.'" style="height: 40px; position:relative; float: left; margin-right: 18px;">
		<div class="multi-audio-player"></div>
		<div class="input-group">
			<input type="text" class="form-control" value="'. $row .'" readonly/>
			<div class="input-group-btn">
				<button class="btn btn-default" type="button"><i class="fa fa-play"></i></button>
				<button class="btn btn-default" onclick="deleteMultiAudio(this)" type="button"><i class="fa fa-remove"></i></button>
			</div>
		</div>
		<input type="hidden" name="'.$name.'[]" value="'.$row.'">
	</div>
	<script language="javascript">setMultiAudioPlayer($(".multi-audio-item-'.$n.'-'.$m.'"));</script>';
			$n++;
		}
	}
	$s .= '
</div>';

	return $s;
}


function tpl_form_field_link($name, $value = '', $options = array()) {
	$s = '';
	if (!defined('TPL_INIT_LINK')) {
		$s = '
		<script type="text/javascript">
			function showLinkDialog(elm) {
				require(["util","jquery"], function(u, $){
					var ipt = $(elm).parent().prev();
					u.linkBrowser(function(href){
						ipt.val(href);
					});
				});
			}
		</script>';
		define('TPL_INIT_LINK', true);
	}
	$s .= '
	<div class="input-group">
		<input type="text" value="'.$value.'" name="'.$name.'" class="form-control ' . $options['css']['input'] . '" autocomplete="off">
		<span class="input-group-btn">
			<button class="btn btn-default ' . $options['css']['btn'] . '" type="button" onclick="showLinkDialog(this);">选择链接</button>
		</span>
	</div>
	';
	return $s;
}


function tpl_form_field_emoji($name, $value='') {
	$s = '';
	if (!defined('TPL_INIT_EMOJI')) {
		$s = '
		<script type="text/javascript">
			function showEmojiDialog(elm) {
				require(["util","jquery"], function(u, $){
					var btn = $(elm);
					var spview = btn.parent().prev();
					var ipt = spview.prev();
					if(!ipt.val()){
						spview.css("display","none");
					}
					u.emojiBrowser(function(emoji){
						ipt.val("\\\" + emoji.find("span").text().replace("+", "").toLowerCase());
						spview.show();
						spview.find("span").removeClass().addClass(emoji.find("span").attr("class"));
					});
				});
			}
		</script>';
		define('TPL_INIT_EMOJI', true);
	}
	$s .= '
	<div class="input-group" style="width: 500px;">
		<input type="text" value="'.$value.'" name="'.$name.'" class="form-control" autocomplete="off">
		<span class="input-group-addon" style="display:none"><span></span></span>
		<span class="input-group-btn">
			<button class="btn btn-default" type="button" onclick="showEmojiDialog(this);">选择表情</button>
		</span>
	</div>
	';
	return $s;
}


function tpl_form_field_color($name, $value = '') {
	$s = '';
	if (!defined('TPL_INIT_COLOR')) {
		$s = '
		<script type="text/javascript">
			require(["jquery", "util"], function($, util){
				$(function(){
					$(".colorpicker").each(function(){
						var elm = this;
						util.colorpicker(elm, function(color){
							$(elm).parent().prev().find(":text").val(color.toHexString());
						});
					});
					$(".colorclean").click(function(){
						$(this).parent().prev().val("");
						var $container = $(this).parent().parent().parent().next();
						$container.find(".colorpicker").val("");
						$container.find(".sp-preview-inner").css("background-color","#000000");
					});
				});
			});
		</script>';
		define('TPL_INIT_COLOR', true);
	}
	$s .= '
		<div class="row row-fix">
			<div class="col-xs-6 col-sm-4" style="padding-right:0;">
				<div class="input-group">
					<input class="form-control" type="text" placeholder="请选择颜色" value="'.$value.'">
					<span class="input-group-btn">
						<button class="btn btn-default colorclean" type="button">
							<span><i class="fa fa-remove"></i></span>
						</button>
					</span>
				</div>
			</div>
			<div class="col-xs-2" style="padding:2px 0;">
				<input class="colorpicker" type="text" name="'.$name.'" value="'.$value.'" placeholder="">
			</div>
		</div>
		';
	return $s;
}


function tpl_form_field_icon($name, $value='') {
	if(empty($value)){
		$value = 'fa fa-external-link';
	}
	$s = '';
	if (!defined('TPL_INIT_ICON')) {
		$s = '
		<script type="text/javascript">
			function showIconDialog(elm) {
				require(["util","jquery"], function(u, $){
					var btn = $(elm);
					var spview = btn.parent().prev();
					var ipt = spview.prev();
					if(!ipt.val()){
						spview.css("display","none");
					}
					u.iconBrowser(function(ico){
						ipt.val(ico);
						spview.show();
						spview.find("i").attr("class","");
						spview.find("i").addClass("fa").addClass(ico);
					});
				});
			}
		</script>';
		define('TPL_INIT_ICON', true);
	}
	$s .= '
	<div class="input-group" style="width: 300px;">
		<input type="text" value="'.$value.'" name="'.$name.'" class="form-control" autocomplete="off">
		<span class="input-group-addon"><i class="'.$value.' fa"></i></span>
		<span class="input-group-btn">
			<button class="btn btn-default" type="button" onclick="showIconDialog(this);">选择图标</button>
		</span>
	</div>
	';
	return $s;
}


function tpl_form_field_image($name, $value = '', $default = '', $options = array()) {
	global $_W;

	if(empty($default)) {
		$default = './resource/images/nopic.jpg';
	}
	$val = $default;
	if(!empty($value)) {
		$val = tomedia($value);
	}
	if(empty($options['tabs'])){
		$options['tabs'] = array('upload'=>'active', 'browser'=>'', 'crawler'=>'');
	}
	if(!empty($options['global'])){
		$options['global'] = true;
	} else {
		$options['global'] = false;
	}
	if(empty($options['class_extra'])) {
		$options['class_extra'] = '';
	}
	if (isset($options['dest_dir']) && !empty($options['dest_dir'])) {
		if (!preg_match('/^\w+([\/]\w+)?$/i', $options['dest_dir'])) {
			exit('图片上传目录错误,只能指定最多两级目录,如: "weizan_store","weizan_store/d1"');
		}
	}
	
	$options['direct'] = true;
	$options['multi'] = false;
	
	if(isset($options['thumb'])){
		$options['thumb'] = !empty($options['thumb']);
	}
	
	$s = '';
	if (!defined('TPL_INIT_IMAGE')) {
		$s = '
		<script type="text/javascript">
			function showImageDialog(elm, opts, options) {
				require(["util"], function(util){
					var btn = $(elm);
					var ipt = btn.parent().prev();
					var val = ipt.val();
					var img = ipt.parent().next().children();
				
					util.image(val, function(url){
						if(url.url){
							if(img.length > 0){
								img.get(0).src = url.url;
							}
							ipt.val(url.filename);
							ipt.attr("filename",url.filename);
							ipt.attr("url",url.url);
						}
						if(url.media_id){
							if(img.length > 0){
								img.get(0).src = "";
							}
							ipt.val(url.media_id);
						}
					}, opts, options);
				});
			}
			function deleteImage(elm){
				require(["jquery"], function($){
					$(elm).prev().attr("src", "./resource/images/nopic.jpg");
					$(elm).parent().prev().find("input").val("");
				});
			}
		</script>';
		define('TPL_INIT_IMAGE', true);
	}

	$s .= '
<div class="input-group '. $options['class_extra'] .'">
	<input type="text" name="'.$name.'" value="'.$value.'"'.($options['extras']['text'] ? $options['extras']['text'] : '').' class="form-control" autocomplete="off">
	<span class="input-group-btn">
		<button class="btn btn-default" type="button" onclick="showImageDialog(this, \'' . base64_encode(iserializer($options)) . '\', '. str_replace('"','\'', json_encode($options)).');">选择图片</button>
	</span>
</div>';
	if(!empty($options['tabs']['browser']) || !empty($options['tabs']['upload'])){
		$s .=
			'<div class="input-group '. $options['class_extra'] .'" style="margin-top:.5em;">
				<img src="' . $val . '" onerror="this.src=\''.$default.'\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail" '.($options['extras']['image'] ? $options['extras']['image'] : '').' width="150" />
				<em class="close" style="position:absolute; top: 0px; right: -14px;" title="删除这张图片" onclick="deleteImage(this)">×</em>
			</div>';
	}
	return $s;
}


function tpl_form_field_multi_image($name, $value = array(), $options = array()) {
	global $_W;
	
	if(empty($options['tabs'])){
		$options['tabs'] = array('upload'=>'active', 'browser'=>'', 'crawler'=>'');
	}
	$options['multi'] = true;
	$options['direct'] = false;
	
	$s = '';
	if (!defined('TPL_INIT_MULTI_IMAGE')) {
		$s = '
<script type="text/javascript">
	function uploadMultiImage(elm) {
		require(["jquery","util"], function($, util){
			var name = $(elm).next().val();
			util.image( "", function(urls){
				$.each(urls, function(idx, url){
					$(elm).parent().parent().next().append(\'<div class="multi-item"><img onerror="this.src=\\\'./resource/images/nopic.jpg\\\'; this.title=\\\'图片未找到.\\\'" src="\'+url.url+\'" class="img-responsive img-thumbnail"><input type="hidden" name="\'+name+\'[]" value="\'+url.filename+\'"><em class="close" title="删除这张图片" onclick="deleteMultiImage(this)">×</em></div>\');
				});
			}, "", '.json_encode($options).');
		});
	}
	function deleteMultiImage(elm){
		require(["jquery"], function($){
			$(elm).parent().remove();
		});
	}
</script>';
		define('TPL_INIT_MULTI_IMAGE', true);
	}

	$s .= <<<EOF
<div class="input-group">
	<input type="text" class="form-control" readonly="readonly" value="" placeholder="批量上传图片" autocomplete="off">
	<span class="input-group-btn">
		<button class="btn btn-default" type="button" onclick="uploadMultiImage(this);">选择图片</button>
		<input type="hidden" value="{$name}" />
	</span>
</div>
<div class="input-group multi-img-details">
EOF;
	if (is_array($value) && count($value)>0) {
		foreach ($value as $row) {
			$s .='
<div class="multi-item">
	<img src="'.tomedia($row).'" onerror="this.src=\'./resource/images/nopic.jpg\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail">
	<input type="hidden" name="'.$name.'[]" value="'.$row.'" >
	<em class="close" title="删除这张图片" onclick="deleteMultiImage(this)">×</em>
</div>';
		}
	}
	$s .= '</div>';

	return $s;
}

function tpl_form_field_wechat_image($name, $value = '', $default = '', $options = array()) {
	global $_W;
	$account = uni_accounts();
	$data = array();
	if(!empty($account)) {
		foreach($account as $li) {
			if($li['level'] < 3) continue;
			$data['item'][] = $li;
		}
		$data['total'] = count($data['item']);
		unset($account);
	}

	if(empty($options['acid']) && $data['total'] == 1) {
		$options['acid'] = $data['item'][0]['acid'];
	}
	if(empty($data['total'])) {
		$options['error'] = 1;
	}
	if(empty($default)) {
		$default = './resource/images/nopic.jpg';
	}
	$val = $default;
	if(!empty($value)) {
		$media_data = (array)media2local($value, true);
		$val = $media_data['attachment'];
	}
	if(empty($options['tabs'])){
		$options['tabs'] = array('upload'=>'active', 'browser'=>'');
	}
	if(empty($options['class_extra'])) {
		$options['class_extra'] = '';
	}

	$options['direct'] = true;
	$options['multi'] = false;
	$options['type'] = empty($options['type']) ? 'image' : $options['type'];
	$s = '';
	if (!defined('TPL_INIT_WECHAT_IMAGE')) {
		$s = '
		<script type="text/javascript">
			function showWechatImageDialog(elm, options) {
				require(["util"], function(util){
					var btn = $(elm);
					var ipt = btn.parent().prev();
					var val = ipt.val();
					var img = ipt.parent().next().children();
					util.wechat_image(val, function(url){
						if(url.media_id){
							if(img.length > 0){
								img.get(0).src = url.url;
							}
							ipt.val(url.media_id);
						}
					}, options);
				});
			}
			function deleteImage(elm){
				require(["jquery"], function($){
					$(elm).prev().attr("src", "./resource/images/nopic.jpg");
					$(elm).parent().prev().find("input").val("");
				});
			}
		</script>';
		define('TPL_INIT_WECHAT_IMAGE', true);
	}

	$s .= '
<div class="input-group '. $options['class_extra'] .'">
	<input type="text" name="'.$name.'" value="'.$value.'"'.($options['extras']['text'] ? $options['extras']['text'] : '').' class="form-control" autocomplete="off">
	<span class="input-group-btn">
		<button class="btn btn-default" type="button" onclick="showWechatImageDialog(this, '. str_replace('"','\'', json_encode($options)).');">选择图片</button>
	</span>
</div>';
	if(!empty($options['tabs']['browser']) || !empty($options['tabs']['upload'])){
		$s .=
			'<div class="input-group '. $options['class_extra'] .'" style="margin-top:.5em;">
				<img src="' . $val . '" onerror="this.src=\''.$default.'\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail" '.($options['extras']['image'] ? $options['extras']['image'] : '').' width="150" />
				<em class="close" style="position:absolute; top: 0px; right: -14px;" title="删除这张图片" onclick="deleteImage(this)">×</em>
			</div>';
	}
	if(!empty($media_data) && $media_data['model'] == 'temp' && (time() - $media_data['createtime'] > 259200)){
		$s .= '<span class="help-block"><b class="text-danger">该素材已过期 [有效期为3天]，请及时更新素材</b></span>';
	}
	return $s;
}

function tpl_form_field_wechat_multi_image($name, $value = '', $default = '', $options = array()) {
	global $_W;
	$account = uni_accounts();
	$data = array();
	if(!empty($account)) {
		foreach($account as $li) {
			if($li['level'] < 3) continue;
			$data['item'][] = $li;
		}
		$data['total'] = count($data['item']);
		unset($account);
	}
	if(empty($options['acid']) && $data['total'] == 1) {
		$options['acid'] = $data['item'][0]['acid'];
	}
	if(empty($data['total'])) {
		$options['error'] = 1;
	}

	if(empty($default)) {
		$default = './resource/images/nopic.jpg';
	}
	if(empty($options['tabs'])){
		$options['tabs'] = array('upload'=>'active', 'browser'=>'');
	}
	if(empty($options['class_extra'])) {
		$options['class_extra'] = '';
	}

	$options['direct'] = false;
	$options['multi'] = true;
	$options['type'] = empty($options['type']) ? 'image' : $options['type'];
	$s = '';
	if (!defined('TPL_INIT_WECHAT_MULTI_IMAGE')) {
		$s = '
<script type="text/javascript">
	function uploadWechatMultiImage(elm) {
		require(["jquery","util"], function($, util){
			var name = $(elm).next().val();
			util.wechat_image( "", function(urls){
				$.each(urls, function(idx, url){
					$(elm).parent().parent().next().append(\'<div class="multi-item"><img onerror="this.src=\\\'./resource/images/nopic.jpg\\\'; this.title=\\\'图片未找到.\\\'" src="\'+url.url+\'" class="img-responsive img-thumbnail"><input type="hidden" name="\'+name+\'[]" value="\'+url.media_id+\'"><em class="close" title="删除这张图片" onclick="deleteWechatMultiImage(this)">×</em></div>\');
				});
			}, '.json_encode($options).');
		});
	}
	function deleteWechatMultiImage(elm){
		require(["jquery"], function($){
			$(elm).parent().remove();
		});
	}
</script>';
		define('TPL_INIT_WECHAT_MULTI_IMAGE', true);
	}

	$s .= <<<EOF
<div class="input-group">
	<input type="text" class="form-control" readonly="readonly" value="" placeholder="批量上传图片" autocomplete="off">
	<span class="input-group-btn">
		<button class="btn btn-default" type="button" onclick="uploadWechatMultiImage(this);">选择图片</button>
		<input type="hidden" value="{$name}" />
	</span>
</div>
<div class="input-group multi-img-details">
EOF;
	if (is_array($value) && count($value)>0) {
		foreach ($value as $row) {
			$s .='
<div class="multi-item">
	<img src="'.media2local($row).'" onerror="this.src=\'./resource/images/nopic.jpg\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail">
	<input type="hidden" name="'.$name.'[]" value="'.$row.'" >
	<em class="close" title="删除这张图片" onclick="deleteWechatMultiImage(this)">×</em>
</div>';
		}
	}
	$s .= '</div>';
	return $s;
}

function tpl_form_field_wechat_voice($name, $value = '', $options = array()) {
	global $_W;
	$account = uni_accounts();
	$data = array();
	if(!empty($account)) {
		foreach($account as $li) {
			if($li['level'] < 3) continue;
			$data['item'][] = $li;
		}
		$data['total'] = count($data['item']);
		unset($account);
	}
	if(empty($options['acid']) && $data['total'] == 1) {
		$options['acid'] = $data['item'][0]['acid'];
	}
	if(empty($data['total'])) {
		$options['error'] = 1;
	}

	if(!empty($value)) {
		$media_data = (array)media2local($value, true);
		$val = $media_data['attachment'];
	}
	if(!is_array($options)){
		$options = array();
	}
	if(empty($options['tabs'])){
		$options['tabs'] = array('upload'=>'active', 'browser'=>'');
	}
	$options = array_elements(array('tabs','global','dest_dir','acid','error'), $options);
	$options['direct'] = true;
	$options['multi'] = false;
	$options['type'] = 'voice';

	$s = '';
	if (!defined('TPL_INIT_WECHAT_VOICE')) {
		$s = '
<script type="text/javascript">
	function showWechatVoiceDialog(elm, options) {
		require(["util"], function(util){
			var btn = $(elm);
			var ipt = btn.parent().prev();
			var val = ipt.val();
			util.wechat_audio(val, function(url){
				if(url && url.media_id && url.url){
					btn.prev().show();
					ipt.val(url.media_id);
					ipt.attr("media_id",url.media_id);
					ipt.attr("url",url.url);
					setWechatAudioPlayer();
				}
				if(url && url.media_id){
					ipt.val(url.media_id);
				}
			} , '.json_encode($options).');
		});
	}

	function setWechatAudioPlayer(){
		require(["jquery", "util", "jquery.jplayer"], function($, u){
			$(function(){
				$(".audio-player").each(function(){
					$(this).prev().find("button").eq(0).click(function(){
						var src = $(this).parent().prev().attr("url");
						if($(this).find("i").hasClass("fa-stop")) {
							$(this).parent().parent().next().jPlayer("stop");
						} else {
							if(src) {
								$(this).parent().parent().next().jPlayer("setMedia", {mp3: u.tomedia(src)}).jPlayer("play");
							}
						}
					});
				});

				$(".audio-player").jPlayer({
					playing: function() {
						$(this).prev().find("i").removeClass("fa-play").addClass("fa-stop");
					},
					pause: function (event) {
						$(this).prev().find("i").removeClass("fa-stop").addClass("fa-play");
					},
					swfPath: "resource/components/jplayer",
					supplied: "mp3"
				});
				$(".audio-player-media").each(function(){
					$(this).next().find(".audio-player-play").css("display", $(this).val() == "" ? "none" : "");
				});
			});
		});
	}

	setWechatAudioPlayer();
</script>';
		echo $s;
		define('TPL_INIT_WECHAT_VOICE', true);
	}

	$s .= '
	<div class="input-group">
		<input type="text" value="'.$value.'" name="'.$name.'" class="form-control audio-player-media" autocomplete="off" '.($options['extras']['text'] ? $options['extras']['text'] : '').'>
		<span class="input-group-btn">
			<button class="btn btn-default audio-player-play" type="button" style="display:none"><i class="fa fa-play"></i></button>
			<button class="btn btn-default" type="button" onclick="showWechatVoiceDialog(this,'.str_replace('"','\'', json_encode($options)).');">选择媒体文件</button>
		</span>
	</div>
	<div class="input-group audio-player">
	</div>';
	if(!empty($media_data) && $media_data['model'] == 'temp' && (time() - $media_data['createtime'] > 259200)){
		$s .= '<span class="help-block"><b class="text-danger">该素材已过期 [有效期为3天]，请及时更新素材</b></span>';
	}
	return $s;
}

function tpl_form_field_wechat_video($name, $value = '', $options = array()) {
	global $_W;
	$account = uni_accounts();
	$data = array();
	if(!empty($account)) {
		foreach($account as $li) {
			if($li['level'] < 3) continue;
			$data['item'][] = $li;
		}
		$data['total'] = count($data['item']);
		unset($account);
	}
	if(empty($options['acid']) && $data['total'] == 1) {
		$options['acid'] = $data['item'][0]['acid'];
	}
	if(empty($data['total'])) {
		$options['error'] = 1;
	}

	if(!empty($value)) {
		$media_data = (array)media2local($value, true);
		$val = $media_data['attachment'];
	}
	if(!is_array($options)){
		$options = array();
	}
	if(empty($options['tabs'])){
		$options['tabs'] = array('video'=>'active', 'browser'=>'');
	}
	$options = array_elements(array('tabs','global','dest_dir', 'acid', 'error'), $options);
	$options['direct'] = true;
	$options['multi'] = false;
	$options['type'] = 'video';
	$s = '';
	if (!defined('TPL_INIT_WECHAT_VIDEO')) {
		$s = '
<script type="text/javascript">
	function showWechatVideoDialog(elm, options) {
		require(["util"], function(util){
			var btn = $(elm);
			var ipt = btn.parent().prev();
			var val = ipt.val();
			util.wechat_audio(val, function(url){
				if(url && url.media_id && url.url){
					btn.prev().show();
					ipt.val(url.media_id);
					ipt.attr("media_id",url.media_id);
					ipt.attr("url",url.url);
				}
				if(url && url.media_id){
					ipt.val(url.media_id);
				}
			}, '.json_encode($options).');
		});
	}

</script>';
		echo $s;
		define('TPL_INIT_WECHAT_VIDEO', true);
	}

	$s .= '
	<div class="input-group">
		<input type="text" value="'.$value.'" name="'.$name.'" class="form-control" autocomplete="off" '.($options['extras']['text'] ? $options['extras']['text'] : '').'>
		<span class="input-group-btn">
			<button class="btn btn-default" type="button" onclick="showWechatVideoDialog(this,'.str_replace('"','\'', json_encode($options)).');">选择媒体文件</button>
		</span>
	</div>
	<div class="input-group audio-player">
	</div>';
	if(!empty($media_data) && $media_data['model'] == 'temp' && (time() - $media_data['createtime'] > 259200)){
		$s .= '<span class="help-block"><b class="text-danger">该素材已过期 [有效期为3天]，请及时更新素材</b></span>';
	}
	return $s;
}


function tpl_form_field_location_category($name, $values = array(), $del = false) {
	$html = '';
	if (!defined('TPL_INIT_LOCATION_CATEGORY')) {
		$html .= '
		<script type="text/javascript">
			require(["jquery", "location"], function($, loc){
				$(".tpl-location-container").each(function(){

					var elms = {};
					elms.cate = $(this).find(".tpl-cate")[0];
					elms.sub = $(this).find(".tpl-sub")[0];
					elms.clas = $(this).find(".tpl-clas")[0];
					var vals = {};
					vals.cate = $(elms.cate).attr("data-value");
					vals.sub = $(elms.sub).attr("data-value");
					vals.clas = $(elms.clas).attr("data-value");
					loc.render(elms, vals, {withTitle: true});
				});
			});
		</script>';
		define('TPL_INIT_LOCATION_CATEGORY', true);
	}
	if (empty($values) || !is_array($values)) {
		$values = array('cate'=>'','sub'=>'','clas'=>'');
	}
	if(empty($values['cate'])) {
		$values['cate'] = '';
	}
	if(empty($values['sub'])) {
		$values['sub'] = '';
	}
	if(empty($values['clas'])) {
		$values['clas'] = '';
	}
	$html .= '
		<div class="row row-fix tpl-location-container">
			<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
				<select name="' . $name . '[cate]" data-value="' . $values['cate'] . '" class="form-control tpl-cate">
				</select>
			</div>
			<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
				<select name="' . $name . '[sub]" data-value="' . $values['sub'] . '" class="form-control tpl-sub">
				</select>
			</div>
			<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
				<select name="' . $name . '[clas]" data-value="' . $values['clas'] . '" class="form-control tpl-clas">
				</select>
			</div>';
	if($del) {
		$html .='
			<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3" style="padding-top:5px">
				<a title="删除" onclick="$(this).parents(\'.tpl-location-container\').remove();return false;"><i class="fa fa-times-circle"></i></a>
			</div>
		</div>';
	} else {
		$html .= '</div>';
	}

	return $html;
}


function tpl_ueditor($id, $value = '') {
	$s = '';
	if (!defined('TPL_INIT_UEDITOR')) {
		$s .= '<script type="text/javascript" src="./resource/components/ueditor/ueditor.config.js"></script>
			<script type="text/javascript" src="./resource/components/ueditor/ueditor.all.min.js"></script>
			<script type="text/javascript" src="./resource/components/ueditor/lang/zh-cn/zh-cn.js"></script>';
	}
	$s .= !empty($id) ? "<textarea id=\"{$id}\" name=\"{$id}\" type=\"text/plain\" style=\"height:300px;\">{$value}</textarea>" : '';
	$s .= "
	<script type=\"text/javascript\">
			var ueditoroption = {
				'autoClearinitialContent' : false,
				'toolbars' : [['fullscreen', 'source', 'preview', '|', 'bold', 'italic', 'underline', 'strikethrough', 'forecolor', 'backcolor', '|',
					'justifyleft', 'justifycenter', 'justifyright', '|', 'insertorderedlist', 'insertunorderedlist', 'blockquote', 'emotion', 'insertvideo',
					'link', 'removeformat', '|', 'rowspacingtop', 'rowspacingbottom', 'lineheight','indent', 'paragraph', 'fontsize', '|',
					'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol',
					'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', '|', 'anchor', 'map', 'print', 'drafts']],
				'elementPathEnabled' : false,
				'initialFrameHeight': 500,
				'focus' : false,
				'maximumWords' : 9999999999999
			};
			var opts = {
				type :'image',
				direct : false,
				multi : true,
				tabs : {
					'upload' : 'active',
					'browser' : '',
					'crawler' : ''
				},
				path : '',
				dest_dir : '',
				global : false,
				thumb : false,
				width : 0
			};
			UE.registerUI('myinsertimage',function(editor,uiName){
				editor.registerCommand(uiName, {
					execCommand:function(){
						require(['fileUploader'], function(uploader){
							uploader.show(function(imgs){
								if (imgs.length == 0) {
									return;
								} else if (imgs.length == 1) {
									editor.execCommand('insertimage', {
										'src' : imgs[0]['url'],
										'_src' : imgs[0]['attachment'],
										'width' : '100%',
										'alt' : imgs[0].filename
									});
								} else {
									var imglist = [];
									for (i in imgs) {
										imglist.push({
											'src' : imgs[i]['url'],
											'_src' : imgs[i]['attachment'],
											'width' : '100%',
											'alt' : imgs[i].filename
										});
									}
									editor.execCommand('insertimage', imglist);
								}
							}, opts);
						});
					}
				});
				var btn = new UE.ui.Button({
					name: '插入图片',
					title: '插入图片',
					cssRules :'background-position: -726px -77px',
					onclick:function () {
						editor.execCommand(uiName);
					}
				});
				editor.addListener('selectionchange', function () {
					var state = editor.queryCommandState(uiName);
					if (state == -1) {
						btn.setDisabled(true);
						btn.setChecked(false);
					} else {
						btn.setDisabled(false);
						btn.setChecked(state);
					}
				});
				return btn;
			}, 19);
			".(!empty($id) ? "
				$(function(){
					var ue = UE.getEditor('{$id}', ueditoroption);
					$('#{$id}').data('editor', ue);
					$('#{$id}').parents('form').submit(function() {
						if (ue.queryCommandState('source')) {
							ue.execCommand('source');
						}
					});
				});" : '')."
	</script>";
	return $s;
}
