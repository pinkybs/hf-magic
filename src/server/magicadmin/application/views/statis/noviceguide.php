<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>noviceguide</title>
        <link href="<?php echo url::imgpath(); ?>style.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>jquery-1.4.2.min.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>public.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo url::sourcepath(); ?>jsdate/WdatePicker.js"></script>
        <script type="text/javascript" language="javascript">
            $(document).ready(function(){
                $("#tab_repeat1 tr:last").find("td").css("border-bottom", "none");
                getnguide();
            });
            function getnguide(){
                var start1 = $("#tbox_date1").val();
                var start2 = $("#tbox_date2").val();
                var ajaxurl = "<?php echo url::site('statis/noviceguide'); ?>?sdate="+start1+"&edate="+start2+"&r="+Math.random();
                $("#td_totals").html("<img src=\"<?php echo url::imgpath(); ?>loading.gif\" style=\"position: relative;top: 8px;\" />数据载入中...");
                $.getJSON(ajaxurl, function(data){
                    if(data != "0"){
                        var str = "<table id=\"tab_repeat2\" style=\"width: 100%; margin: 0;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
                        for(var i=0;i<data.length;i++){
                            var newbia = eval(data[i]["newbie"]);
                            var ratio = eval(data[i]['ratio']);
                            str += "<tr class=\"tab_repeat_tr2\">";
                            str += "<td>"+data[i]["time"].substring(0,10)+"</td>";
                            str += "<td style=\" width: 70px;\">"+data[i]["num"]+"</td>";
                            str += "<td style=\" width: 145px;\">"+newbia[0]+"人 / <font color=\"#2aafb8\">"+ratio[0]+"</font></td>";
                            str += "<td style=\" width: 145px;\">"+newbia[1]+"人 / <font color=\"#2aafb8\">"+ratio[1]+"</font></td>";
                            str += "<td style=\" width: 145px;\">"+newbia[2]+"人 / <font color=\"#2aafb8\">"+ratio[2]+"</font></td>";
                            str += "<td style=\" width: 145px;\">"+newbia[3]+"人 / <font color=\"#2aafb8\">"+ratio[3]+"</font></td>";
                            str += "<td style=\" width: 145px;\">"+newbia[4]+"人 / <font color=\"#2aafb8\">"+ratio[4]+"</font></td>";
                            str += "<td style=\" width: 145px;\">"+newbia[5]+"人 / <font color=\"#2aafb8\">"+ratio[5]+"</font></td>";
                            str += "<td style=\" width: 145px;\">"+newbia[6]+"人 / <font color=\"#2aafb8\">"+ratio[6]+"</font></td>";
                            str += "</tr>";
                        }
                        str += "</table>";
                        $("#td_totals").html(str);
                        $("#tab_repeat2 tr:last").find("td").css("border-bottom", "none");
                        fortable("tab_repeat2");
                    }else{
                        $("#td_totals").html("<div style=\"color:red; text-align: left; margin: 0 auto 0 auto; width: 98%; font-size: 12px;\">暂无数据！</div>");
                    }
                });
            }
        </script>
    </head>

    <body>
        <div class="div_repeat">
            <table class="tab_repeat" id="tab_repeat1" border="0" cellspacing="0" cellpadding="0" style="margin: 10px auto 0 auto; width: 98%;">
                <tr class="tab_repeat_tr1">
                    <td style="text-align: left; font-size: 14px;" colspan="8">
                        <span>新手引导流失统计</span>
                        <span style="margin-left: 200px;">日期选择：
                            <input type="text" name="tbox_date1" id="tbox_date1" class="tbox" value="<?php echo date('Y-m-d', mktime(0, 0, 0, date('n'), 1, date('Y'))); ?>" onFocus="WdatePicker({isShowClear:false,readOnly:true,skin:'whyGreen'})" style="width: 175px;" />
                        	~
                            <input type="text" name="tbox_date2" id="tbox_date2" class="tbox" value="<?php echo date('Y-m-d', mktime(0, 0, 0, date('n'), date('t'), date('Y'))); ?>" onFocus="WdatePicker({isShowClear:false,readOnly:true,skin:'whyGreen'})" style="width: 175px;" />
                        </span>
                        <span><input type="submit" name="dosubmit" id="dosubmit" value="查 询" class="but" onclick="getnguide();" /></span> 
                    </td>
                </tr>
                <tr class="tab_repeat_tr1" style="background: none; text-indent: 0px;">
                    <td style="border-right: solid 1px #65dfe7;">分析时间</td>
                    <td style="border-right: solid 1px #65dfe7; width: 70px;">分析人数</td>
                    <td style="border-right: solid 1px #65dfe7; width: 145px;">1、点门放人 / 百分比</td>
                    <td style="border-right: solid 1px #65dfe7; width: 145px;">2、教授魔法 / 百分比</td>
                    <td style="border-right: solid 1px #65dfe7; width: 145px;">3、帮助学生 / 百分比</td>
                    <td style="border-right: solid 1px #65dfe7; width: 145px;">4、收水晶 / 百分比</td>
                    <td style="border-right: solid 1px #65dfe7; width: 145px;">5、变化咒 / 百分比</td>
                    <td style="border-right: solid 1px #65dfe7; width: 145px;">6、合成术 / 百分比</td>
                    <td style="width: 145px;">7、完成任务 / 百分比</td>
                </tr>
                <tr>
                    <td colspan="9" id="td_totals" style="height: 38px; line-height: 38px;"></td>
                </tr>
            </table>
        </div>
    </body>
</html>
