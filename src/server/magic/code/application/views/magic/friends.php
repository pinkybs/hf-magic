<?php defined('SYSPATH') or die('No direct access allowed.');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>HappyMagic</title>
<script type="text/javascript">
	function display(){
	  var friends = document.getElementById('friends');//根据id获取div
	  var friendframe = document.getElementById('friendframe');//根据id获取iframe
	  friends.style.display = "block";//显示弹层
	  friendframe.src="http://www.hp.com/index.php/platform/invite";//将iframe的src属性置为好友选择器的页面
	}

	function closediv(){
	  var friends = document.getElementById('friends');//根据id获取div
	  friends.style.display = "none";//将弹层置为隐藏
	}
</script>
</head>

<body>
<div>
<span onclick="display();" style="cursor: pointer; margin: 0 5px;">邀请好友</span>
<span onclick="closediv();" style="cursor: pointer;">关闭</span>
</div>

<div id="friends"><iframe style="width: 810px; height: 620px" frameborder="0" id="friendframe" scrolling="no" /></div>
</body>
</html>
