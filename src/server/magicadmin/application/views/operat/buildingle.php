<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>buildingle</title>
        <link href="<?php echo url::imgpath(); ?>style.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>jquery-1.5.2.min.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>public.js"></script>
        <script type="text/javascript" language="javascript">
            function sendajax(){
                var uid = $("#tbox_uid").val();
                var item = $("#tbox_item").val();
                var num = $("#tbox_num").val();
                if(!val_num(uid) || !val_num(item) || !val_num(num)){
                    alert("只能输入数字！");
                    resetv();
                    return false;
                }
                var params = $.param({"uid":uid,"item":item,"num":num});
                isajax(params);
                resetv();
            }
            function isajax(params){
                var ajaxurl1 = "<?php echo url::site('operat/buildingle'); ?>?"+params+"&r="+Math.random();
                $.get(ajaxurl1, function(data){
                    if(data == "1"){
                        alert("操作成功！");
                    }else{
                        alert("操作失败！");
                    }
                });
            }
            function resetv(){
                $("#tbox_uid").val("");
                $("#tbox_item").val("");
                $("#tbox_num").val("");
            }
        </script>
    </head>

    <body>
        <table border="0" cellspacing="0" cellpadding="0" style="margin: 10px auto 0 auto; width: 98%;">
            <tr style="height: 42px; line-height: 42px;">
                <td style="width: 100px; text-align: right;">用户UID：</td>
                <td style="width: 350px; text-align: left;"><input type="text" name="tbox_uid" id="tbox_uid" class="tbox"  style="width: 300px;" /></td>
            </tr>
            <tr style="height: 42px; line-height: 42px;">
                <td style="width: 100px; text-align: right;">装饰物ID：</td>
                <td style="width: 350px; text-align: left;"><input type="text" name="tbox_item" id="tbox_item" class="tbox"  style="width: 300px;" /></td>
            </tr>
            <tr style="height: 42px; line-height: 42px;">
                <td style="width: 100px; text-align: right;">数量：</td>
                <td style="width: 350px; text-align: left;"><input type="text" name="tbox_num" id="tbox_num" class="tbox"  style="width: 300px;" /></td>
            </tr>
            <tr style="height: 42px; line-height: 42px;">
                <td colspan="2"><input type="button" name="dosubmit" id="dosubmit" value="确 定" class="but" onclick="sendajax();" /></td>
            </tr>
        </table>
    </body>
</html>
