
jq(document).ready(function(){
	var status_loginned = jq("#status_loginned");
	var mn_myaccount_menu = jq("#mn_myaccount_menu");
	status_loginned.mouseenter(function(){
		t_delay= setTimeout(function(){
			mn_myaccount_menu.fadeIn("slow");
		},200);
	});
	status_loginned.mouseleave(function(){
		clearTimeout(t_delay);
		mn_myaccount_menu.fadeOut("slow");
	});


});

