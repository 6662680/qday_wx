<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<div class="main"> <form action="" method="post" class="form-horizontal form">
<div class="panel panel-default">
    <div class="panel-heading">
       <h4>借用高级认证设置<small>如果你的公众号没有oauth2接口权限，可以借用别人的接口权限来防止作弊。</small></h4>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">AppId</label>
            <div class="col-sm-9 col-xs-12">
               <input type="text" name="appid" class="form-control" value="<?php  echo $set['appid'];?>" />
        </div>
        </div>
 <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">AppSecret</label>
            <div class="col-sm-9 col-xs-12">
               	 <input type="text" name="appsecret" class="form-control" value="<?php  echo $set['appsecret'];?>" />
            </div>
        </div>
     
            
                  
         <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
            <div class="col-sm-9 col-xs-12">
               	借用说明：必需设置借用高级认证号的OAuth2.0网页授权的回调域名为你公众号第三方平台的全域名。
                如：你的系统域名为：www.xxxx.com ，你必需让借用高级认证号设置OAuth2.0网页授权的回调域名为:www.xxxx.com
                <br />
                <img src="../addons/wdl_scratch/style/appid.jpg">
                
            </div>
        </div>
            
            
    </div>
    
        
</div>
    
            
        <div class="panel panel-default">
    <div class="panel-heading">
       <h4>分享借用高级认证设置<small>如果你的公众号未认证，则需要借用其他认证订阅号，或认证服务号。</small></h4>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">AppId</label>
            <div class="col-sm-9 col-xs-12">
               <input type="text" name="appid_share" class="form-control" value="<?php  echo $set['appid_share'];?>" />
        </div>
        </div>
 <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">AppSecret</label>
            <div class="col-sm-9 col-xs-12">
               	 <input type="text" name="appsecret_share" class="form-control" value="<?php  echo $set['appsecret_share'];?>" />
            </div>
        </div>
     
            
                  
         <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
            <div class="col-sm-9 col-xs-12">
               借用说明：必需设置借用认证号的JS接口安全域名。在公众号设置-功能设置中，可以找到。
                <br />
                <img src="../addons/wdl_scratch/style/jssdk.png">
                
            </div>
        </div>
            
            
    </div>
    
        
</div>
        
        <div class="form-group col-sm-12">
			<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
        
    </form>
    </div>
 
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>