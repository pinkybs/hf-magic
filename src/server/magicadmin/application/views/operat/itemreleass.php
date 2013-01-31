<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>itemreleass</title>
        <link href="<?php echo url::imgpath(); ?>style.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>jquery-1.5.2.min.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>public.js"></script>
        <script type="text/javascript" language="javascript">
            $(document).ready(function(){
                $("#tab_repeat1").css("border-bottom", "none");
            });
            function goitemreleas(){
                window.location.href="<?php echo url::site('operat/itemrelease'); ?>";
            }
        </script>
    </head>

    <body>
        <div class="div_repeat">
            <table class="tab_repeat" id="tab_repeat1" border="0" cellspacing="0" cellpadding="0" style="margin: 10px auto 0 auto; width: 85%;">
                <tr class="tab_repeat_tr1">
                    <td style="text-align: left; font-size: 14px;" colspan="2">
                        <span style="margin: 0 10px 0 0;">Item物品发放结果</span>
                        <span><input type="button" name="dosubmit" id="dosubmit" value="返回发放" class="but" onclick="goitemreleas();" /></span> 
                    </td>
                </tr>
                <tr class="tab_repeat_tr1" style="background: none; text-indent: 0px;">
                    <td style="border-right: solid 1px #65dfe7; width: 50%;">应发列表</td>
                    <td style="width: 50%;">实际发放</td>
                </tr>
                <tr>
                    <td style="border-right: solid 1px #65dfe7;" valign="top">
                        <table id="tab_repeat2" style="width: 100%; margin: 0;" border="0" cellspacing="0" cellpadding="0">
                            <tr class="tab_repeat_tr2">
                                <td style="border-right: solid 1px #65dfe7;">发放Uid</td>
                                <td style="border-right: solid 1px #65dfe7; width: 30%;">物品id</td>
                                <td style="width: 30%;">发放数量</td>
                            </tr>
                            <?php
                            if (count($rs) > 0) {
                                $str = "";
                                for ($i = 0; $i < count($rs); $i++) {
                                    $str .= "<tr class=\"tab_repeat_tr2\">";
                                    $str .= "<td>" . $rs[$i]['uid'] . "</td>";
                                    $str .= "<td style=\"width: 30%;\">" . $rs[$i]['itemid'] . "</td>";
                                    $str .= "<td style=\"width: 30%;\">" . $rs[$i]['num'] . "</td>";
                                    $str .= "</tr>";
                                }
                                echo $str;
                            }
                            ?>
                        </table>
                    </td>
                    <td valign="top">
                        <table id="tab_repeat3" style="width: 100%; margin: 0;" border="0" cellspacing="0" cellpadding="0">
                            <tr class="tab_repeat_tr2">
                                <td style="border-right: solid 1px #65dfe7;">发放Uid</td>
                                <td style="border-right: solid 1px #65dfe7; width: 30%;">物品id</td>
                                <td style="width: 30%;">发放数量</td>
                            </tr>
                            <?php
                            if (count($info) > 0) {
                                $str = "";
                                for ($i = 0; $i < count($info); $i++) {
                                    $str .= "<tr class=\"tab_repeat_tr2\">";
                                    $str .= "<td>" . $info[$i]['uid'] . "</td>";
                                    $str .= "<td style=\"width: 30%;\">" . $info[$i]['itemid'] . "</td>";
                                    $str .= "<td style=\"width: 30%;\">" . $info[$i]['num'] . "</td>";
                                    $str .= "</tr>";
                                }
                                echo $str;
                            }
                            ?>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
