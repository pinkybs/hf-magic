<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>itemport</title>
        <link href="<?php echo url::imgpath(); ?>style.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>jquery-1.5.2.min.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo url::sourcepath(); ?>lhgdialog/lhgdialog.min.js"></script>
        <script type="text/javascript" language="javascript">
            var dg = frameElement.lhgDG;
            $(function(){
                alert($("#divdata", dg.curWin).html());
            });


            function selectfile(){
                $("#file").click();
                if($("#file").val()){
                    $("#span_file").html($("#file").val());
                }
                return false;
            }
        </script>
    </head>

    <body>
        <form id="form1" name="form1" enctype="multipart/form-data" method="post" action="">
            <table class="tab_repeat2" id="tab_repeat2" border="0" cellspacing="0" cellpadding="0" style="margin: 10px auto 10px auto; width: 100%; height: 382px; border: none;">
                <tr style="height: 45px; line-height: 45px;">
                    <td>
                        <span>文件名：<span id="span_file"><font color="red">暂无</font></span><input type="file" name="file" id="file" style="width: 300px; height: 26px; line-height: 26px; display: none;" /></span>
                        <span style="margin: 0 10px;"><input type="submit" name="dosumbit" id="dosumbit" onclick="return selectfile();" value="导入文件" class="but" /></span>
                        <span style="margin: 0 10px;"><input type="button" name="cmdsumbit" id="cmdsumbit" value="开始发放" class="but" /></span>
                    </td>
                </tr>
                <tr style="text-align: left; text-indent: 24px;" valign="top">
                    <td>
                        <table class="tab_repeat" id="tab_repeat1" border="0" cellspacing="0" cellpadding="0" style="margin: 10px auto 10px auto; width: 98%; border-bottom: none;">
                            <tr class="tab_repeat_tr1" style="background: none; text-align: center;">
                                <td style="width: 50%;">Item</td>
                                <td style="width: 50%;">Uid</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr style="height: 30px; line-height: 25px; text-align: left; text-indent: 24px;">
                    <td style="border-bottom: none;">
                        注：选择要导入的文件，UID一行一个。
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
