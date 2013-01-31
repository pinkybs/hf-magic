<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>consumstatis</title>
        <link href="<?php echo url::imgpath(); ?>style.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>jquery-1.4.2.min.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>public.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo url::sourcepath(); ?>jsdate/WdatePicker.js"></script>
        <script type="text/javascript" language="javascript">
            $(document).ready(function(){
                getconsumstatis();
                $("#tab_repeat1 tr:last").find("td").css("border-bottom", "none");
            });
            function getconsumstatis(){
                var start1 = $("#tbox_date1").val();
                var start2 = $("#tbox_date2").val();
                var ajaxurl = "<?php echo url::site('statis/consumstatis'); ?>?sdate="+start1+"&edate="+start2+"&r="+Math.random();
                $("#td_totals").html("<img src=\"<?php echo url::imgpath(); ?>loading.gif\" style=\"position: relative;top: 8px;\" />数据载入中...");
                $.getJSON(ajaxurl, function(data){
                    if(data != "0"){
                        var str = "<table id=\"tab_repeat2\" style=\"width: 100%; margin: 0;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
                        for(var i=0;i<data.length;i++){
                            str += "<tr class=\"tab_repeat_tr2\">";
                            str += "<td>"+data[i]["sumtime"]+"</td>";
                            str += "<td style=\" width: 90px;\">"+data[i]["rid"]+"</td>";
                            str += "<td style=\" width: 160px;\">"+data[i]["rlevel"]+"</td>";
                            str += "<td style=\" width: 160px;\">"+data[i]["itemid"]+"</td>";
                            str += "<td style=\" width: 160px;\">"+data[i]["num"]+"</td>";
                            str += "<td style=\" width: 160px;\">"+data[i]["price"]+"</td>";
                            str += "<td style=\" width: 160px;\">"+data[i]["total"]+"</td>";
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
                    <td style="text-align: left; font-size: 14px;" colspan="6">
                        <span>用户消费统计</span>
                        <span style="margin-left: 200px;">日期选择：
                            <input type="text" name="tbox_date1" id="tbox_date1" class="tbox" value="<?php echo date('Y-m-d', mktime(0, 0, 0, date('n'), 1, date('Y'))); ?>" onFocus="WdatePicker({isShowClear:false,readOnly:true,skin:'whyGreen'})" style="width: 175px;" />
                        	~
                            <input type="text" name="tbox_date2" id="tbox_date2" class="tbox" value="<?php echo date('Y-m-d', mktime(0, 0, 0, date('n'), date('t'), date('Y'))); ?>" onFocus="WdatePicker({isShowClear:false,readOnly:true,skin:'whyGreen'})" style="width: 175px;" />
                        </span>
                        <span><input type="submit" name="dosubmit" id="dosubmit" value="查 询" class="but" onclick="getconsumstatis();" /></span> 
                    </td>
                </tr>
                <tr class="tab_repeat_tr1" style="background: none; text-indent: 0px;">
                    <td style="border-right: solid 1px #65dfe7;">时间</td>
                    <td style="border-right: solid 1px #65dfe7; width: 90px;">用户ID</td>
                    <td style="border-right: solid 1px #65dfe7; width: 160px;">用户等级</td>
                    <td style="border-right: solid 1px #65dfe7; width: 160px;">物品ID</td>
                    <td style="border-right: solid 1px #65dfe7; width: 160px;">物品数量</td>
                    <td style="border-right: solid 1px #65dfe7; width: 160px;">物品单价</td>
                    <td style="width: 160px;">物品总价</td>
                </tr>
                <tr>
                    <td colspan="9" id="td_totals" style="height: 38px; line-height: 38px;"></td>
                </tr>
            </table>
        </div>
    </body>
</html>
