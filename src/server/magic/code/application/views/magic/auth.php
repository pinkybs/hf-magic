<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:xn="http://www.renren.com/2009/xnml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script type="text/javascript" src="http://static.connect.renren.com/js/v1.0/FeatureLoader.jsp"></script>
<title>游戏认证</title>
</head>
<body>
<img src="<?=media::img_url()?>xiaonei/haibao.jpg" border='0' width='800' />
<!-- 人人js -->
  <script type="text/javascript">
 	
    XN_RequireFeatures(["Connect"], function()
    {
		XN.Main.init("<?=Kohana::config('base.api_key')?>", "<?php echo url::base();?>xd_receiver.html");
		var callback = function(){ top.location.href = 'http://apps.renren.com/<?=Kohana::config('base.app_name')?>?<?=mt_rand();?>'; }
		var cancel = function(){ top.location.href = 'http://page.renren.com/<?=Kohana::config('base.app_name')?>'; }
		XN.Connect.showAuthorizeAccessDialog(callback,cancel);
    });
   </script>
</body>
</html>