# 微信小店助手

###安装方法
##### 1. 正常安装模块  
将压缩包解压后上传至 /source/modules/ 访问后台安装模块


##### 2. 修改系统文件
由于微擎系统还没有支持最新的微小店所需要的接口, 需要改造一下系统文件  
编辑文件 /source/modules/engine.php  
在代码
```
private function matcherEvent() {
```
**之前**加上以下内容  
(编辑代码之前先搜索文件, 如果已经存在, 就不需要再处理了.)  
(**注意: 不要使用windows自带的记事本处理代码, 会出问题. 请使用editplus或者Dreamweaver等专业编辑器**)  
```
//需要附加的代码
	private function matcherEventMerchant_order() {
		$params = array();
		if(in_array('microb_store', $this->modules)) {
			$params[] = array('module' => 'microb_store', 'rule' => '-1');
		}
		return $params;
	}
//附加代码结束

```


##### 3. 如果要支持微擎官方商城, 需要修改商城模块文件
由于官方商城模块还没有支持此功能, 需要改造一下模块的代码  
编辑文件 /source/modules/shopping/site.php  
在代码
```
if ($params['from'] == 'return') {
```
**之后**加上以下内容  
(编辑代码之前先搜索文件, 如果已经存在, 就不需要再处理了.)  
(**注意: 不要使用windows自带的记事本处理代码, 会出问题. 请使用editplus或者Dreamweaver等专业编辑器**)  
```
//需要附加的代码
            $file = IA_ROOT . '/source/modules/microb_store/inc/ShoppingHook.class.php';
            if(is_file($file)) {
                require $file;
                (new ShoppingHook())->submitNotify($params['tid']);
            }
//附加代码结束

```
