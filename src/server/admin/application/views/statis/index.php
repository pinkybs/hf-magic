<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>快乐魔法数据统计</title>
        <style type="text/css">
            *{
                width: 100%;
                height: 100%;
            }
            body {
                margin: 0;
                padding: 0;
                text-align: center;
                font-size: 12px;
                color: #333;
            }
        </style>
    </head>
    <frameset cols="187,*" frameborder="no" border="0" framespacing="0">
        <frame src="<?php echo url::site('statis/left'); ?>" name="LeftFrame" scrolling="No" noresize="noresize" id="LeftFrame" title="LeftFrame" />
        <frame src="<?php echo url::site('statis/body'); ?>" name="BodyFrame" id="BodyFrame" title="BodyFrame" />
    </frameset>
    <noframes><body>
        </body></noframes>
</html>
