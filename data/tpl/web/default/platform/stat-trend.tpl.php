<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li class="active"><a href="javascript:;">关键指标详解</a></li>
</ul>
<div class="clearfix" id="clear">
	<div class="pull-left">
		<form action="" id="form1">
			<input type="hidden" name="c" value="platform">
			<input type="hidden" name="a" value="stat">
			<input type="hidden" name="do" value="trend">
			<input type="hidden" name="id" value=<?php  echo $id;?>>
			<input type="hidden" name="m" value=<?php  echo $_GPC['m'];?>>
			<?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d', $starttime),'endtime'=>date('Y-m-d', $endtime)));?>
		</form>
	</div>
	<div class="clearfix"></div>
	<br>
	<div class="panel panel-default" style="padding:1em">
		<nav role="navigation" class="navbar navbar-default navbar-static-top" style="margin: -1em -1em 1em -1em;">
			<div class="container-fluid">
				<div class="navbar-header">
					<a href="javascript:;" class="navbar-brand">规则使用趋势图</a>
				</div>
			</div>
		</nav>
		<div style="margin-top:20px;">
			<canvas id="myChart" height="80"></canvas>
		</div>
	</div>
	<?php  if(is_array($keywords)) { foreach($keywords as $id => $row) { ?>
	<div class="panel panel-default" style="padding:1em">
		<nav role="navigation" class="navbar navbar-default navbar-static-top" style="margin: -1em -1em 1em -1em;">
			<div class="container-fluid">
				<div class="navbar-header">
					<a href="javascript:;" class="navbar-brand">所属关键字使用趋势图&nbsp;&nbsp;&nbsp;<small>(关键字：<?php  echo $keywordnames[$id]['content'];?>)</small></a>
				</div>
			</div>
		</nav>
		<div style="margin-top:20px">
			<canvas id="trend_keyword_<?php  echo $id;?>" height="80"></canvas>
		</div>
	</div>
	<?php  } } ?>
</div>
	<script>
		require(['chart', 'jquery', 'daterangepicker'], function(c, $){
			$('.daterange').on('apply.daterangepicker', function(ev, picker) {
				$('#form1')[0].submit();
			});
			var label = <?php  echo json_encode($day)?>;
			var datasets =  <?php  echo json_encode($hit)?>;
			var lineChartData = {
				labels : label,
				datasets : [
					{
						fillColor : "rgba(36,165,222,0.1)",
						strokeColor : "rgba(36,165,222,1)",
						pointColor : "rgba(36,165,222,1)",
						pointStrokeColor : "#fff",
						pointHighlightFill : "#fff",
						pointHighlightStroke : "rgba(36,165,222,1)",
						data : datasets
					}
				]
			}

			var myLine = new Chart(document.getElementById("myChart").getContext("2d")).Line(lineChartData, {responsive : true});

			<?php  if(is_array($keywords)) { foreach($keywords as $id => $row) { ?>
				var label = <?php  echo json_encode($row['day'])?>;
				var datasets = <?php  echo json_encode($row['hit'])?>;
				var lineChartData = {
					labels : label,
					datasets : [
						{
							fillColor : "rgba(149,192,0,0.1)",
							strokeColor : "rgba(149,192,0,1)",
							pointColor : "rgba(149,192,0,1)",
							pointStrokeColor : "#fff",
							pointHighlightFill : "#fff",
							pointHighlightStroke : "rgba(149,192,0,1)",
							data : datasets
						}
					]
	
				}

				var myLine = new Chart(document.getElementById("trend_keyword_<?php  echo $id;?>").getContext("2d")).Line(lineChartData, {responsive : true});
			<?php  } } ?>
		});
	</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>