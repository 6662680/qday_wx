<?php defined('IN_IA') or exit('Access Denied');?>			</div>
		</div>
	</div>
	<script>
		function subscribe(){
			$.post("<?php  echo url('utility/subscribe');?>", function(){
				setTimeout(subscribe, 5000);
			});
		}
		function sync() {
			$.post("<?php  echo url('utility/sync');?>", function(){
				setTimeout(sync, 60000);
			});
		}
		$(function(){
			subscribe();
			sync();
		});
	</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer-base', TEMPLATE_INCLUDEPATH)) : (include template('common/footer-base', TEMPLATE_INCLUDEPATH));?>
