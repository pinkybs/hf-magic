<?php defined('SYSPATH') or die('No direct access allowed.');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:xn="http://www.renren.com/2009/xnml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>HappyMagic</title>
<style type="text/css">
	html, body { height:100%; }
	body { margin:0; }
</style>
<script type="text/javascript" src="http://static.connect.renren.com/js/v1.0/FeatureLoader.jsp"></script>
</head>

<body>
<div style="border: solid 2px #360; width: 750px; padding: 5px;">
<xn:serverxnml style="width:750px; margin:0 auto;">
	<script type="text/xnml">
        <xn:request-form content="和 TA 一起去玩快乐魔法吧！ &lt;xn:req-choice url=&quot;<?php echo url::base();?> &quot; &quot;&gt;&lt;
			xn:req-choice url=&quot;<?php echo url::base();?> &quot;label=&quot;接受邀请&quot;&gt;"
			action="<?php echo url::base();?>/platform/send" iframecallback="closediv" channel_url="<?php echo url::base();?>xd_receiver.html">
            <xn:multi-friend-selector-x actiontext="邀请标题" mode="naf" height="450"/>
        </xn:request-form>
    </script>
</xn:serverxnml>
<script type="text/javascript">
    XN_RequireFeatures(["Connect"], function()
    {
      //这里要自己填两个参数：api_key和跨域文件xd_receiver.html的路径
      XN.Main.init("<?php echo Kohana::config('base.api_key')?>", "<?php echo url::base();?>xd_receiver.html");
    });

    function closediv(){
		if(parent.closediv){
			parent.closediv();
		}
	}
</script>
</div>
</body>
</html>
