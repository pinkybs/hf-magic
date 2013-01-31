<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Insert title here</title>
</head>
<body>
        <form name="ssform" method="get" action="<?php echo url::base(true);?>sns?rnd=<?=mt_rand();?>"> 
            <div class="inp"> 
                <span>请输入邀请码:<input name="invite_code" type="text" title="请输入邀请码" size="22" maxlength="60" value=""/></span> 
                <span><input class="bn-srh" type="submit" value="确认"/></span> 
            </div> 
        </form> 
</body>
</html>