
jq(document).ready(function(){
	var status_loginnedd = jq("#status_loginnedd");
	var mn_mymsg_menu = jq("#mn_mymsg_menu");
	status_loginnedd.mouseenter(function(){
		t_delay= setTimeout(function(){
			mn_mymsg_menu.fadeIn("slow");
		},200);
	});
	status_loginnedd.mouseleave(function(){
		clearTimeout(t_delay);
		mn_mymsg_menu.fadeOut("slow");
	});


});

