;(function($){
	/**
	 * 图标列表 选择
	 */
	$.fn.bottomFixed = function (options) {
		
		var winH = $(window).height();
		
		return this.each(function(){
			
			var $this = $(this);
			
			$(window).resize(function(){
				if($(this).height() < winH){
					$this.css("position","relative");
				}else{
					$this.css("position","fixed");
				}
			});
			
		});
		
	};
})(jQuery);