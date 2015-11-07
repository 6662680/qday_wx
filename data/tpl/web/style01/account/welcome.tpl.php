<?php defined('IN_IA') or exit('Access Denied');?>
<!--<script>window.location.href="/www/index.php"</script>-->
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-base', TEMPLATE_INCLUDEPATH)) : (include template('common/header-base', TEMPLATE_INCLUDEPATH));?>

<div class="home">

	<div class="head clearfix">

		<div class="container">

			<div class="head_logo" <?php  if(!empty($_W['setting']['copyright']['flogo'])) { ?>style="background:url('<?php  echo tomedia($_W['setting']['copyright']['flogo']);?>') no-repeat;"<?php  } ?>></div>

			<div class="pull-right btns">

				<a href="<?php  echo url('user/login');?>" class="btn btn-default">登录</a>&nbsp;

				<?php  if(!empty($settings['register']['open'])) { ?>

					<a href="<?php  echo url('user/register');?>" class="btn btn-default">注册</a>

				<?php  } ?>

			</div>

		</div>

	</div>

	<div class="content clearfix">

		<div class="banner"></div>

		<div class="con container">

			<div class="panel panel-default">

				<div class="panel-body">

					<h4>系统功能介绍</h4>

					<div class="row">

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><i class="fa fa-tablet"></i></div>

							<h6>微3G网站</h6>

							<p>将企业微网站植入微信公众平台,关注公众平台即可访问网站.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><i class="fa fa-bullseye"></i></div>

							<h6>微信互动营销</h6>

							<p>大转盘,刮刮卡,会员卡,客户互动很轻松.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><i class="fa fa-cogs"></i></div>

							<h6>一站式管理</h6>

							<p>一个账号管理多个微信公众号,轻松方便,快捷.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><i class="fa fa-life-ring"></i></div>

							<h6>微信账号集中一站管理</h6>

							<p>支持多个微信账号的添加与管理,节约运营成本.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><i class="fa fa-file-text-o"></i></div>

							<h6>无限自定义图文回复</h6>

							<p>突破微信公众平台内容限制,快速响应预设回复.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><i class="fa fa-desktop"></i></div>

							<h6>众多边界工具免费享用</h6>

							<p>我有多款查询小工具提高微信公众账号活跃度.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><i class="fa fa-send"></i></div>

							<h6>淘宝店铺商品快速导入</h6>

							<p>倡导手机淘宝,引领微支付前沿,一键快速添加.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><i class="fa fa-sitemap"></i></div>

							<h6>强大的商家运营管理</h6>

							<p>实现本地商家与微信用户直接的消费营销互动.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><i class="fa fa-share-alt"></i></div>

							<h6>网站完美兼容其他接口</h6>

							<p>整合第三方应用接口,实现网站与平台数据互通.</p>

						</div>

					</div>

				</div>

			</div>

			<div class="panel panel-default">

				<div class="panel-body">

					<h4>功能模块介绍</h4>

					<div class="row">

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/01.png"></div>

							<h6>微现场</h6>

							<p>微信上墙,现场抽奖,摇一摇抽奖活动.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/02.png"></div>

							<h6>微旅游</h6>

							<p>旅游产品也可以在微信上实现预订了.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/03.png"></div>

							<h6>微外卖</h6>

							<p>微外卖结合GPRS无线打印机,订单实时打印,真正做到了省时,省心,省力.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/04.png"></div>

							<h6>微商城</h6>

							<p>每个公众号都可以建立品牌微信商城,小微信也有大商城,电商轻松走入微信.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/05.png"></div>

							<h6>微汽车</h6>

							<p>汽车销售管理、预约保养、预约试驾、违章查询、360度全景看车、车主关怀等功能.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/06.png"></div>

							<h6>微房产</h6>

							<p>楼盘介绍、子楼盘管理、户型介绍及户型图、楼盘相册、房友印象以及专家点评等功能.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/07.png"></div>

							<h6>微团购</h6>

							<p>微团购适合商家搞团购活动及秒杀活动，能快速提升销售业绩.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/08.png"></div>

							<h6>微点菜</h6>

							<p>微点菜简化了传统行业的点菜流程，配套GPRS无线打印机，订单实时打印，省时、省心、省力.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/09.png"></div>

							<h6>微喜帖</h6>

							<p>微喜帖是针对结婚庆典而推出的一款行业产品，传播自己即将结婚的动态等等.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/10.png"></div>

							<h6>音乐盒</h6>

							<p>爱，好的音乐盒能提升粉丝的粘性，让粉丝沉浸在音乐的世界里，舍不得离开.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/11.png"></div>

							<h6>微网站</h6>

							<p>快速建立一个精美的企业手机网站，让公众号的信息展示更加丰富更加完善.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/12.png"></div>

							<h6>微投票</h6>

							<p>获得用户心声，丰富营销思路，对微信公众号的内容改善都是有极大的帮助.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/13.png"></div>

							<h6>微信自定义菜单</h6>

							<p>用户无需再通过输入关键词触发回复，直接点击菜单就可以看相关的内容.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/14.png"></div>

							<h6>微信会员卡</h6>

							<p>帮助企业建立集品牌推广、会员管理、营销活动、统计报表于一体的微信会员管理平台.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/15.png"></div>

							<h6>微信大转盘</h6>

							<p>用户可以反复抽奖，并可带动周边朋友一起分享，从而形成极强的口碑营销效果.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/16.png"></div>

							<h6>微信刮刮卡活动</h6>

							<p>开展在线刮奖活动，吸引顾客线下消费，并可带动周边朋友一起分享，吸引更多的粉丝关注.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/17.png"></div>

							<h6>微信优惠券活动</h6>

							<p>电子优惠券利益客户，又节约营销预算，商家的销售额也会获得爆炸式的增长效果.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/18.png"></div>

							<h6>微信LBS位置回复</h6>

							<p>适用于多门店、多服务点，让客户更方便的找到商家的详细，地址及电话等等.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/19.png"></div>

							<h6>微相册</h6>

							<p>提供精美的相册系统，完美的图片展示效果，婚纱摄影，产品展示最佳选择.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/20.png"></div>

							<h6>微订单</h6>

							<p>灵活的自定义订单系统，可以做在线报名，预约，订房，试驾等等各种预定.</p>

						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">

							<div class="icon"><img src="resource/images/module/21.png"></div>

							<h6>微统计</h6>

							<p>实时统计粉丝关注情况及微网站浏览数，根据统计对相关活动及市场行为作出相应调整.</p>

						</div>

					</div>



				</div>

			</div>

			<div class="contact panel panel-default">

				<div class="panel-body">

					<h4>联系我们</h4>

					<div class="map col-lg-5 col-md-6 col-sm-12 col-xs-12" style="height:150px;">

						<div class="img" style="width:413px; height:150px; border:#ccc solid 1px;" id="baidumap"></div>

					</div>

					<div class="info col-lg-7 col-md-6 col-sm-12 col-xs-12">

						<p>联系人：<?php  echo $copyright['person'];?></p>

						<p>Q&nbsp;&nbsp;&nbsp;&nbsp; Q：<?php  echo $copyright['qq'];?></p>

						<p>手&nbsp;&nbsp;&nbsp;机：<?php  echo $copyright['phone'];?></p>

						<p>公&nbsp;&nbsp;&nbsp;司：<?php  echo $copyright['company'];?></p>

						<p>地&nbsp;&nbsp;&nbsp;址：<?php  echo $copyright['address'];?></p>

					</div>

				</div>

			</div>

		</div>

	</div>

	<div class="center-block footer" role="footer">

		<div class="text-center">

			<?php  if(empty($_W['setting']['copyright']['footerright'])) { ?><a href="http://www.qdaygroup.com">关于情天</a>&nbsp;&nbsp;<a href="http://www.qdaygroup.com">情天帮助</a><?php  } else { ?><?php  echo $_W['setting']['copyright']['footerright'];?><?php  } ?> &nbsp; &nbsp; <?php  if(!empty($_W['setting']['copyright']['statcode'])) { ?><?php  echo $_W['setting']['copyright']['statcode'];?><?php  } ?>

		</div>

		<div class="text-center">

			<?php  if(empty($_W['setting']['copyright']['footerleft'])) { ?>Powered by <a href="http://www.qdaygroup.com"><b>情天</b></a> v<?php echo IMS_VERSION;?> &copy; 2014 <a href="http://www.qdaygroup.com">www.qdaygroup.com</a><?php  } else { ?><?php  echo $_W['setting']['copyright']['footerleft'];?><?php  } ?>
		</div>
	</div>
</div>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.4"></script>
<script>
	require(['jquery'],function($){

		var bmap = {

			'option' : {

				'lock' : false,

				'container' : 'baidumap',

				'infoWindow' : {'width' : 250, 'height' : 100, 'title' : ''},

				'point' : {'lng' : <?php  echo $copyright['baidumap']['lng'];?>, 'lat' : <?php  echo $copyright['baidumap']['lat'];?>}

			},

			'init' : function(option) {

				var $this = this;

				$this.option = $.extend({},$this.option,option);

				$this.option.defaultPoint = new BMap.Point($this.option.point.lng, $this.option.point.lat);

				$this.bgeo = new BMap.Geocoder();

				$this.bmap = new BMap.Map($this.option.container);

				$this.bmap.centerAndZoom($this.option.defaultPoint, 15);

				$this.bmap.enableScrollWheelZoom();

				$this.bmap.enableDragging();

				$this.bmap.enableContinuousZoom();

				$this.bmap.addControl(new BMap.NavigationControl());

				$this.bmap.addControl(new BMap.OverviewMapControl());

				//添加标注

				$this.marker = new BMap.Marker($this.option.defaultPoint);

				$this.marker.enableDragging();

				$this.bmap.addOverlay($this.marker);

				$this.marker.setAnimation(BMAP_ANIMATION_BOUNCE);

			},

			'setMarkerCenter' : function() {

				var $this = this;

				var center = $this.bmap.getCenter();

				$this.marker.setPosition(new BMap.Point(center.lng, center.lat));

				$this.showPointValue();

				$this.showAddress();

			}

		};

		$(function(){

			var option = {};

			option = {'point' : {'lng' : '<?php  echo $copyright['baidumap']['lng'];?>', 'lat' : '<?php  echo $copyright['baidumap']['lat'];?>'}}

			bmap.init(option);

		});

	});

</script>
</body>
</html>
