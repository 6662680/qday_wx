;(function($){
	/**
	 * html5 上传
	 * v
	 */
	$.fn.h5upload = function (options) {

		var defaults = {
			"progress": ".progress", //进度显示
			"number":1,
			"urls":".urls",
			"picUrl": ".picUrl", //显示图片
			"picPath": ".picPath", //图片path，用于保存
			"uploadFile": ".uploadFile", //上传input
			"picWidth": "640", //图片等比压缩的宽度
			"uploadUrl": "/upload/ajaxUploadifyUploadFile.do?fileType=bizcard",
			"callback":null
		};
		var settings = $.extend({},defaults,options);
		var number = 1;
		return this.each(function(){
			var $this = $(this);
		
			//进度
			function onProgress(file, loaded, total) {
				var progerssNum = (loaded / total * 100);
				var eleProgress = $this.find(settings.progress);
				var percent = 0;
				if(typeof progerssNum == "number") {
					percent = progerssNum.toFixed(2) + '%';
					eleProgress.show().text(percent);
				} else {
					$.flytip("上传失败，请重新选择图片");
				}
			}
			//成功
			function onSuccess(file, response) {
				
				var data = eval('('+response+')');
				$this.find(settings.picUrl).html("<img src='"+data.url+"'>");
				$this.find(settings.picPath).val(data.path);
				if(settings.number > 1) {
					var urlsObj = $this.find(settings.urls);
					var urlInputObj = urlsObj.find("input");
					var inputValue = "<input type='hidden' value='"+data.url+"' data-path='"+data.path+"'/>";
					if(urlInputObj.length < settings.number) {
						urlsObj.append(inputValue);
					} else {
						if(number - (settings.number+1) >= settings.number) {
							number = number - settings.number;
						}
						urlInputObj.eq(number - (settings.number+1)).replaceWith(inputValue);
					}
				}
				$this.find(settings.progress).hide();
				if(data.state == "SUCCESS") {
					$.flytip("上传成功!");
					settings.callback && settings.callback();
					number ++;
				} else {
					$.flytip("上传失败，请重新选择图片");
				}
			}
			//失败
			function onFailure(file, response) {
				$.flytip("上传失败，请重新选择图片");
				$this.find(settings.progress).hide();
			}
			//文件上传
			function funUploadFile(files) {
				
				var url = settings.uploadUrl;//上传URl
				var file = files[0];
				var xhr = new XMLHttpRequest();
				
				if (xhr.upload) {
					// 上传中
					xhr.upload.addEventListener("progress", function(e) {
						onProgress(file, e.loaded, e.total);
					}, false);
	
					// 文件上传成功或是失败
					xhr.onreadystatechange = function(e) {
						if (xhr.readyState == 4) {
							if (xhr.status == 200) {
								onSuccess(file, xhr.responseText);
							} else {
								onFailure(file, xhr.responseText);		
							}
						}
					};
	
					// 开始上传
					xhr.open("POST", url, true);
					
					var fd = new FormData();
			        fd.append('files', file);
			        xhr.send(fd);
				}
			}
			
			$this.find(settings.uploadFile).on("change", function(e) {
				var files = e.target.files || e.dataTransfer.files;
				if($(this).val()){
					funUploadFile(files);
				}
			});
		
		});
	};
		
})(jQuery);