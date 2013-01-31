<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>activegrowth</title>
        <link href="<?php echo url::imgpath(); ?>style.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>jquery-1.4.2.min.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>FusionCharts.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>public.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo url::sourcepath(); ?>jsdate/WdatePicker.js"></script>
        <script type="text/javascript" language="javascript">
            $(document).ready(function(){
                $("#tab_repeat1 tr:last").find("td").css("border-bottom", "none");
                getactive();
            });
            function getactive(){
                var start1 = $("#tbox_date1").val();
                var start2 = $("#tbox_date2").val();
                var ajaxurl1 = "<?php echo url::site('statis/activegrowth'); ?>?sdate="+start1+"&edate="+start2+"&type=hour&r="+Math.random();
                $("#div_fcharts").html("<img src=\"<?php echo url::imgpath(); ?>loading.gif\" style=\"position: relative;top: 8px;\" />数据载入中...");
                $.get(ajaxurl1, function(data){
                    if(data != "0"){
                        $("#div_fcharts").html(data);
                    }else{
                        $("#div_fcharts").html("暂无数据！");
                    }
                });
            }
        </script>
    </head>

    <body>
        <div class="div_repeat">
            <table class="tab_repeat" id="tab_repeat1" border="0" cellspacing="0" cellpadding="0" style="margin: 10px auto 0 auto; width: 98%;">
                <tr class="tab_repeat_tr1">
                    <td style="text-align: left; font-size: 14px;" colspan="3">
                        <span>日活跃、日增长用户分布图</span>
                        <span style="margin-left: 200px;">日期选择：
                            <input type="text" name="tbox_date1" id="tbox_date1" class="tbox" value="<?php echo date('Y-m-d', mktime(0, 0, 0, date('n'), 1, date('Y'))); ?>" onFocus="WdatePicker({isShowClear:false,readOnly:true,skin:'whyGreen'})" style="width: 175px;" />
                        	~
                            <input type="text" name="tbox_date2" id="tbox_date2" class="tbox" value="<?php echo date('Y-m-d', mktime(0, 0, 0, date('n'), date('t'), date('Y'))); ?>" onFocus="WdatePicker({isShowClear:false,readOnly:true,skin:'whyGreen'})" style="width: 175px;" />
                        </span>
                        <span><input type="submit" name="dosubmit" id="dosubmit" value="查 询" class="but" onclick="getactive();" /></span> 
                    </td>
                </tr>
                <tr class="tab_repeat_tr2">
                    <td colspan="3" style="text-align: left; font-size: 14px;">
                        <div id="div_fcharts" style="color:red; margin: 0 auto 0 auto; width: 98%; font-size: 12px;"></div>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
