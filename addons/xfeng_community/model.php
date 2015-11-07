<?php
  function site_slide_search_new($params = array()) {
			global $_GPC, $_W;
			extract($params);
		 	$sql = "SELECT * FROM ".tablename('xcommunity_slide'). " WHERE weid = '{$_W['weid']}' ORDER BY id DESC";
		 	$list = pdo_fetchall($sql);
	 		if (!empty($list)) {
				foreach ($list as &$row) {
					$row['url'] = strexists($row['url'], 'http') ? $row['url'] : '';
		 		}
		 	}
		 	return $list;
		}
	function tpl_form_field_calendar_new($name, $values = array()) {
	$html = '';
	if (!defined('TPL_INIT_CALENDAR')) {
		$html .= '
		<script type="text/javascript">
			function handlerCalendar(elm) {
				require(["jquery","moment"], function($, moment){
					var tpl = $(elm).parent().parent();
					var year = tpl.find("select.tpl-year").val();
					var month = tpl.find("select.tpl-month").val();
					// var day = tpl.find("select.tpl-day");
					day[0].options.length = 1;
					if(year && month) {
						var date = moment(year + "-" + month, "YYYY-M");
						var days = date.daysInMonth();
						for(var i = 1; i <= days; i++) {
							var opt = new Option(i, i);
							day[0].options.add(opt);
						}
						if(day.attr("data-value")!=""){
							day.val(day.attr("data-value"));
						} else {
							day[0].options[0].selected = "selected";
						}
					}
				});
			}
			require(["jquery"], function($){
				$(".tpl-calendar").each(function(){
					handlerCalendar($(this).find("select.tpl-year")[0]);
				});
			});
		</script>';
		define('TPL_INIT_CALENDAR', true);
	}

	if (empty($values) || !is_array($values)) {
		$values = array(0,0,0);
	}
	$values['year'] = intval($values['year']);
	$values['month'] = intval($values['month']);
	
	$year = array(date('Y'), '1914');
	$html .= '<div class="row row-fix tpl-calendar">
			<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
				<select name="' . $name . '[year]" onchange="handlerCalendar(this)" class="form-control tpl-year">
					<option value="">年</option>';
	for($i = $year[1]; $i <= $year[0]; $i++) {
		$html .= '<option value="' . $i . '"'.($i == $values['year'] ? ' selected="selected"' : '').'>' . $i . '</option>';
	}
	$html .= '	</select>
			</div>
			<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
				<select name="' . $name . '[month]" onchange="handlerCalendar(this)" class="form-control tpl-month">
					<option value="">月</option>';
	for($i = 1; $i <= 12; $i++) {
		$html .= '<option value="' . $i . '"'.($i == $values['month'] ? ' selected="selected"' : '').'>' . $i . '</option>';
	}
	$html .= '	</select>
			</div>
		</div>';
	return $html;
}
?>