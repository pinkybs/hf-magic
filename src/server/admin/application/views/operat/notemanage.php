<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>notemanage</title>
        <link href="<?php echo url::imgpath(); ?>style.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>jquery-1.5.2.min.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>public.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo url::sourcepath(); ?>lhgdialog/lhgdialog.min.js"></script>
        <script type="text/javascript" language="javascript">
            var types = new Array()
            types[0] = "推荐"
            types[1] = "最新"
            types[2] = "公告"
            types[3] = "活动"
            var dialogtitle;
            var dlg;
            $(document).ready(function(){
                $("#tab_repeat1 tr:last").find("td").css("border-bottom", "none");
                getdata();
            });
            function getdata(){
                $("#td_totals").html("<img src=\"<?php echo url::imgpath(); ?>loading.gif\" style=\"position: relative;top: 8px;\" />数据载入中...");
                var ajaxurl1 = "<?php echo url::site('operat/notemanage'); ?>?cmd=null&r="+Math.random();
                $.getJSON(ajaxurl1, function(data){
                    if(data.length>0){
                        var str = "<table id=\"tab_repeat2\" style=\"width: 100%; margin: 0;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
                        for(var i=0;i<data.length;i++){
                            str += "<tr class=\"tab_repeat_tr2\">";
                            str += "<td><input type=\"hidden\" name=\"hid_title\" id=\"hid_title_"+i+"\" value=\""+data[i]['title']+"\" />"+data[i]['title']+"</td>";
                            str += "<td style=\" width: 90px;\"><input type=\"hidden\" name=\"hid_type\" id=\"hid_type_"+i+"\" value=\""+data[i]['type']+"\" />"+types[data[i]['type']]+"</td>";
                            str += "<td style=\" width: 390px;\"><input type=\"hidden\" name=\"hid_link\" id=\"hid_link_"+i+"\" value=\""+data[i]['link']+"\" />"+data[i]['link']+"</td>";
                            str += "<td style=\" width: 160px;\">"+data[i]["date"]+"</td>";
                            str += "<td style=\" width: 200px;\">";
                            str += "<span style=\"cursor: pointer; color: #F60;\" onclick=\"creatpop("+i+", 'edit');\">编辑</span>&nbsp;&nbsp;&nbsp;";
                            str += "<span style=\"cursor: pointer; color: #F60;\" onclick=\"creatpop("+i+", 'del');\">删除</span>";
                            str += "</td>";
                            str += "</tr>";
                        }
                        str += "</table>";
                        $("#td_totals").html(str);
                        $("#tab_repeat2 tr:last").find("td").css("border-bottom", "none");
                        fortable("tab_repeat2");
                    }else{
                        $("#td_totals").html("暂无数据！");
                    }
                });
            }
            //准备创建弹出窗口
            function creatpop(obj, cmd){
                switch(cmd){
                    case "add":
                        dialogtitle = "公告管理--->添加";
                        showpop(obj, cmd);
                        break;
                    case "edit":
                        dialogtitle = "公告管理--->编辑";
                        showpop(obj, cmd);
                        break;
                    case "del":
                        if(confirm("是否确定删除该公告？")){
                            var params = $.param({"id":obj,"cmd":cmd});
                            isajax(params);
                        }
                        break;
                    default:
                        break;
                }
            }
            //弹出窗口
            function showpop(obj, cmd){
                dlg = new $.dialog({
                    id: "notepop_"+obj,
                    title: dialogtitle,
                    width: 500,
                    height: 230,
                    resize: false,
                    cover: true,
                    btnBar: false,
                    html: creathtml(obj, cmd),
                    rang: true,
                    dgOnLoad: function(){
                        if(cmd=="edit"){
                            $("#tbox_title_"+obj,dlg.topDoc).val($("#hid_title_"+obj).val());
                            $("#sel_type_"+obj,dlg.topDoc).val($("#hid_type_"+obj).val());
                            $("#tbox_link_"+obj,dlg.topDoc).val($("#hid_link_"+obj).val());
                        }
                        $("#dosubmit",dlg.topDoc).click(function(){
                            var stitle = $("#tbox_title_"+obj,dlg.topDoc).val();
                            var stype = $("#sel_type_"+obj,dlg.topDoc).val();
                            var slink = $("#tbox_link_"+obj,dlg.topDoc).val();
                            if(!val_empty(stitle) || !val_empty(slink)){
                                alert("有未填项！");
                                return false;
                            }
                            var params = $.param({"id":obj,"stitle":stitle,"stype":stype,"slink":slink,"cmd":cmd});
                            isajax(params);
                        });
                    }
                });
                dlg.ShowDialog();
            }
            function creathtml(obj, cmd){
                var str = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"margin: 10px auto 0 auto; width: 98%;\">";
                str += "<tr style=\"height: 42px; line-height: 42px;\">";
                str += "<td style=\"width: 100px; text-align: right;\">公告标题：</td>";
                str += "<td style=\"width: 350px; text-align: left;\"><input type=\"text\" name=\"tbox_title\" id=\"tbox_title_"+obj+"\" class=\"tbox\"  style=\"width: 300px;\" /></td>";
                str += "</tr>";
                str += "<tr style=\"height: 42px; line-height: 42px;\">";
                str += "<td style=\"width: 100px; text-align: right;\">公告类型：</td>";
                str += "<td style=\"width: 350px; text-align: left;\"><select name=\"sel_type\" id=\"sel_type_"+obj+"\">";
                str += "<option value=\"0\">"+types[0]+"</option><option value=\"1\">"+types[1]+"</option><option value=\"2\">"+types[2]+"</option><option value=\"3\">"+types[3]+"</option>";
                str += "</select>&nbsp;&nbsp;&nbsp;&nbsp;";
                str += "</td>";
                str += "</tr>";
                str += "<tr style=\"height: 42px; line-height: 42px;\">";
                str += "<td style=\"width: 100px; text-align: right;\">公告链接：</td>";
                str += "<td style=\"width: 350px; text-align: left;\"><input type=\"text\" name=\"tbox_link\" id=\"tbox_link_"+obj+"\" class=\"tbox\"  style=\"width: 300px;\" /></td>";
                str += "</tr>";
                str += "</tr>";
                str += "<tr style=\"height: 42px; line-height: 42px;\">";
                str += "<td style=\"width: 100px; text-align: center;\" colspan=\"2\"><input type=\"submit\" name=\"dosubmit\" id=\"dosubmit\" value=\"确 定\" class=\"but\" /></td>";
                str += "</tr>";
                str += "</table>";
                return str;
            }
            
            function isajax(params){
                var ajaxurl1 = "<?php echo url::site('operat/notemanage'); ?>?"+params+"&r="+Math.random();
                $.get(ajaxurl1, function(data){
                    if(data == "1"){
                        alert("操作成功！");
                        getdata();
                    }else{
                        alert("操作失败！");
                    }
                    dlg.cancel();
                });
            }
        </script>
    </head>

    <body>
        <div class="div_repeat">
            <table class="tab_repeat" id="tab_repeat1" border="0" cellspacing="0" cellpadding="0" style="margin: 10px auto 0 auto; width: 98%;">
                <tr class="tab_repeat_tr1">
                    <td style="text-align: left; font-size: 14px;" colspan="5">
                        <span style="margin: 0 10px 0 0;">游戏内公告管理</span>
                        <span><input type="submit" name="dosubmit" id="dosubmit" value="添加公告" class="but" onclick="creatpop(null,'add');" /></span> 
                    </td>
                </tr>
                <tr class="tab_repeat_tr1" style="background: none; text-indent: 0px;">
                    <td style="border-right: solid 1px #65dfe7;">公告标题</td>
                    <td style="border-right: solid 1px #65dfe7; width: 90px;">公告类型</td>
                    <td style="border-right: solid 1px #65dfe7; width: 390px;">公告链接</td>
                    <td style="border-right: solid 1px #65dfe7; width: 160px;">发布时间</td>
                    <td style="width: 200px;">操作</td>
                </tr>
                <tr>
                    <td colspan="5" id="td_totals" style="height: 38px; line-height: 38px;"></td>
                </tr>
            </table>
        </div>
    </body>
</html>
