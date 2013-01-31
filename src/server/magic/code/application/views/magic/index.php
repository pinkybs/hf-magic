<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>HappyMagic</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	
	<script src="<?php echo media::static_url();?>flash/js/swfobject.js" type="text/javascript"></script>
	<script type="text/javascript" src="http://static.connect.renren.com/js/v1.0/FeatureLoader.jsp"></script>
	<style type="text/css">
		html, body { height:100%; overflow:hidden; }
		body { margin:0; }
	</style>
</head>
<body align="center">
	<div  align="center"> 
	<script type="text/javascript">
		var flashvars = {
			interfaceHost:"<?php echo url::base(true);?>",
			staticHost:"",
			createUrl: "<?php echo $api;?>",
			initInterface: "<?php echo $init_interface;?>",
			localWords: "<?php echo $local_words;?>",
			piantou: "<?php echo $piantou;?>",
			initUi: "<?php echo $initUi;?>",
			tipsStr: '<?php echo Kohana::lang("base.tips");?>',
			createModule: "<?php echo $module;?>"
		};
		var params = {
			menu: "false",
			scale: "noScale",
			allowFullscreen: "true",
			allowScriptAccess: "always",
			base: ".",
			bgcolor: "#FFFFFF",
			align: "center"
		};
		var attributes = {
			id:"HappyMagic"
		};
		swfobject.embedSWF("<?php echo media::flash_url();?>MagicLoader.swf", "altContent", "748", "600", "9.0.0", "<?php echo media::flash_url();?>expressInstall.swf", flashvars, params, attributes);
	</script>
	</div>
	<div id="altContent">
		<h1>HappyMagic</h1>
		<p>Alternative content</p>
		<p><a href="http://www.adobe.com/go/getflashplayer"><img 
			src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" 
			alt="Get Adobe Flash player" /></a></p>
	</div>
	<div>你的魔法教室是<?php echo $role_id;?>班, 此魔法屋属于<?php echo $name;?>,拆迁可耻</div>
  <script type="text/javascript">
  	function sendFeed(id){
  		var feedSettings = {
  			"template_bundle_id": id,
  			"template_data":{
                             "images":[{
                                 "src":"http://fmn042.xnimg.cn/fmn042/20090806/0905/p_large_oQSJ_2633m016062.jpg",
                                 "href":"http://app.renren.com/testmagic/"}],
                                 "title":"  <a href='http://app.renren.com/testmagic/'>在测试新鲜事title</a>",
                                 "feedtype":"feed类型,暂时不用",
                                 "content":"<a href='http://app.renren.com/testmagic/'>可以填写很多东西,有链接</a>",
                                 "action":""},
  			"body_general": "另外一些介绍",
  			"callback": function(ok){},
  			"user_message_prompt": "给用户提示下,问题不大",
  			"user_message": "默认信息,用处很大"
  		};
  		XN.Connect.showFeedDialog(feedSettings);
  	}
  </script>
  <a href="#" onclick="sendFeed(1);return false;">发送自定义新鲜事</a>

  <script type="text/javascript">
    XN_RequireFeatures(["Connect"], function()
    {
    	XN.Main.init("<?php echo Kohana::config('base.api_key')?>", "<?php echo url::base();?>xd_receiver.html");
    });
  </script>
</body>
</html>