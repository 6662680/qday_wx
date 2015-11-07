<?php defined('IN_IA') or exit('Access Denied');?>			</div>
		</div>
		<script>require(['bootstrap']);</script>
		<div class="center-block footer" role="footer">
			<div class="text-center">
				<?php  if(empty($_W['setting']['copyright']['footerright'])) { ?><a href="http://www.qdaygroup.com">关于情天</a>&nbsp;&nbsp;<a href="http://www.qdaygroup.com">情天帮助</a><?php  } else { ?><?php  echo $_W['setting']['copyright']['footerright'];?><?php  } ?><?php  if(!empty($_W['setting']['copyright']['statcode'])) { ?>&nbsp; &nbsp; <?php  echo $_W['setting']['copyright']['statcode'];?><?php  } ?>
			</div>
			<div class="text-center">
				<?php  if(empty($_W['setting']['copyright']['footerleft'])) { ?>Powered by <a href="http://www.qdaygroup.com"><b>情天</b></a> v<?php echo IMS_VERSION;?> &copy; 2014 <a href="http://www.qdaygroup.com">www.qdaygroup.com</a><?php  } else { ?><?php  echo $_W['setting']['copyright']['footerleft'];?><?php  } ?>
			</div>
		</div>
	</div>
</body>
</html>
