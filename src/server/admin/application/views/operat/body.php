<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>body</title>
        <link href="<?php echo url::imgpath(); ?>style.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>jquery-1.4.2.min.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>public.js"></script>
        <script type="text/javascript" language="javascript">
            $(document).ready(function(){
                $("#tab_repeat tr:last").find("td").css("border-bottom", "none");
            });
        </script>
    </head>

    <body>
        <div class="div_repeat">
            <table class="tab_repeat" id="tab_repeat" border="0" cellspacing="0" cellpadding="0" style="margin: 10px auto 0 auto; width: 98%;">
                <tr class="tab_repeat_tr1">
                    <td colspan="6" style="text-align: left; font-size: 14px;">
                        <span>首页面</span>
                    </td>
                </tr>
                <tr class="tab_repeat_tr2">
                    <td style="border-right: solid 1px #65dfe7;">平台名称</td>
                    <td style="border-right: solid 1px #65dfe7; width: 150px;">新增用户</td>
                    <td style="border-right: solid 1px #65dfe7; width: 150px;">活跃数</td>
                    <td style="border-right: solid 1px #65dfe7; width: 150px;">活跃比</td>
                    <td style="border-right: solid 1px #65dfe7; width: 150px;">收入</td>
                    <td style="width: 150px;">RMB收入</td>
                </tr>
                <?php
                if ($platformlist) {
                    foreach ($platformlist as $item) {
                        echo "<tr class=\"tab_repeat_tr2\">";
                        echo "<td style=\"border-right: solid 1px #65dfe7;\" id=\"td_title_" . $item['id'] . "\">" . $item['title'] . "</td>";
                        echo "<td id=\"td_new_" . $item['id'] . "\"></td>";
                        echo "<td id=\"td_hys_" . $item['id'] . "\"></td>";
                        echo "<td id=\"td_hyb_" . $item['id'] . "\"></td>";
                        echo "<td id=\"td_sru_" . $item['id'] . "\"></td>";
                        echo "<td id=\"td_rmb_" . $item['id'] . "\"></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr class=\"tab_repeat_tr2\"><td colspan=\"6\">暂无数据</td></tr>";
                }
                ?>
            </table>
        </div>
    </body>
</html>
