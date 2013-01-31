<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>快乐魔法</title>
        <link href="<?php echo url::imgpath(); ?>style.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <form id="form1" name="form1" method="post">
            <table class="tab_repeat" id="tab_repeat" border="0" cellspacing="0" cellpadding="0" style="margin: 50px auto 0 auto;">
                <tr class="tab_repeat_tr1">
                    <td colspan="3" style="text-align: left; font-size: 14px;"><span>登陆</span></td>
                </tr>
                <tr class="tab_repeat_tr2">
                    <td class="tab_repeat_td3" style="border-right: solid 1px #65dfe7;">用户名：</td>
                    <td class="tab_repeat_td1" style="text-align: left; padding-left: 5px;">
                        <input type="text" name="tbox_uname" id="tbox_uname" class="tbox" />
                    </td>
                    <td class="tab_repeat_td2" style="text-align: left; padding-left: 5px;"></td>
                </tr>
                <tr class="tab_repeat_tr2">
                    <td class="tab_repeat_td3" style="border-right: solid 1px #65dfe7;">密码：</td>
                    <td class="tab_repeat_td1" style="text-align: left; padding-left: 5px;">
                        <input type="password" name="tbox_upass" id="tbox_upass" class="tbox" />
                    </td>
                    <td class="tab_repeat_td2" style="text-align: left; padding-left: 5px;"></td>
                </tr>
                <tr class="tab_repeat_tr2">
                    <td class="tab_repeat_td3" style="border-right: solid 1px #65dfe7;">类型：</td>
                    <td class="tab_repeat_td1" style="text-align: left; padding-left: 5px;">
                        <select name="sel_type" id="sel_type">
                            <option value="0">运营统计</option>
                            <option value="1">运营工具</option>
                        </select>
                    </td>
                    <td class="tab_repeat_td2" style="text-align: left; padding-left: 5px;"></td>
                </tr>
                <tr class="tab_repeat_tr2">
                    <td colspan="3" style="height: 44px;">
                        <span><input type="submit" name="dosubmit" id="dosubmit" value="登 陆" class="but" /></span> 
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
