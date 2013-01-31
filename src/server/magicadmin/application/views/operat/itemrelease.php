<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>itemrelease</title>
        <link href="<?php echo url::imgpath(); ?>style.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>jquery-1.5.2.min.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>public.js"></script>
        <script type="text/javascript" language="javascript" src="<?php echo url::sourcepath(); ?>lhgdialog/lhgdialog.min.js"></script>
        <script type="text/javascript" language="javascript">
            $(document).ready(function(){
                $("#but_issuelease").attr("disabled", "disabled");
            });
            var json;
            var i=0;
            //向Item表添加一行
            function itemadd(obj){
                var item = $("#sel_"+obj);
                var itemnum = $("#tbox_"+obj+"_num").val();
                if(item.val() == null || item.val() == "" || item.selectedIndex == 0){
                    alert("尚未选择！");
                    return false;
                }
                if(!val_num(itemnum)){
                    alert("只能输入数字！");
                    $("#tbox_"+obj+"_num").val("1");
                    return false;
                }
                var str = "<tr class=\"tab_repeat2_tr2\" id=\"tab_repeat2_tr2_"+i+"\">";
                str += "<td style=\"width: 350px;\"><input type=\"hidden\" class=\"hid_itemid\" id=\"hid_itemid"+i+"\" value=\""+item.val()+"\" />"+item.find("option:selected").text()+"</td>";
                str += "<td style=\"width: 100px;\"><input type=\"hidden\" class=\"hid_num\" id=\"hid_num"+i+"\" value=\""+itemnum+"\" />"+itemnum+"</td>";
                str += "<td><input name=\"docommon\" type=\"button\" value=\"移除\" onclick=\"itemdel('tab_repeat2_tr2_"+i+"',"+i+");\" /></td>";
                str += "</tr>";
                $("#tab_repeat2_tr1").after(str);
                getjson();
                i++;
            }
            //判断当前按钮是不可以开启
            function judgmentbut(){
                if(json.length<=0){
                    $("#but_issuelease").attr("disabled", "disabled");
                }else{
                    $("#but_issuelease").attr("disabled", false);
                }
            }
            //删除Item一行
            function itemdel(obj,i){
                $("#"+obj).remove();
                getjson();
            }
            function getjson(){
                var ids = $(".hid_itemid");
                var num = $(".hid_num");
                var str = "[";
                for(var i=0;i<ids.length;i++){
                    if(i==ids.length-1){
                        str += "{\"itemid\":"+$("#"+ids[i].id).val()+",\"num\":"+$("#"+num[i].id).val()+"}";
                    }else{
                        str += "{\"itemid\":"+$("#"+ids[i].id).val()+",\"num\":"+$("#"+num[i].id).val()+"},";
                    }
                }
                str += "]";
                json = eval('('+str+')');
                $("#hid_item").val(str);
                judgmentbut();
            }
            var dlg;
            function creatpop(){
                dlg = new $.dialog({
                    id: "signrelease",
                    title: "单用户发放",
                    width: 500,
                    height: 230,
                    resize: false,
                    cover: true,
                    btnBar: false,
                    page: "<?php echo url::site('operat/itemsingle'); ?>",
                    rang: true
                });
                dlg.ShowDialog();
            }
        </script>
    </head>

    <body>
        <table class="tab_repeat" id="tab_repeat1" border="0" cellspacing="0" cellpadding="0" style="margin: 10px auto 10px auto; width: 98%; border-bottom: none;">
            <tr class="tab_repeat_tr1">
                <td style="text-align: left; font-size: 14px;" colspan="3">
                    <span style="margin: 0 10px 0 0;">Item发放工具</span>
                    <span><input type="submit" name="dosubmit" id="dosubmit" value="单条发放" class="but" onclick="creatpop();" /></span> 
                </td>
            </tr>
            <tr class="tab_repeat_tr1" style="background: none; text-indent: 0px;">
                <td style="width: 30%;" valign="top">
                    <div class="div_item_selete">
                        <div class="div_item_selete_title">Item选择</div>
                        <div class="div_item_selete_info">
                            <?php
                            $str_cl = "<div><select name=\"sel_cl\" size=\"15\" id=\"sel_cl\" style=\"border: solid 1px #65dfe7;\">";
                            $str_sp = "<div style=\"border-bottom: dotted 1px #65dfe7;\"><select name=\"sel_sp\" size=\"15\" id=\"sel_sp\" style=\"border: solid 1px #65dfe7;\">";
                            $str_ys = "<div style=\"border-bottom: dotted 1px #65dfe7;\"><select name=\"sel_ys\" size=\"15\" id=\"sel_ys\" style=\"border: solid 1px #65dfe7;\">";
                            $str_qt = "<div><select name=\"sel_qt\" size=\"15\" id=\"sel_qt\" style=\"border: solid 1px #65dfe7;\">";
                            foreach ($items as $item) {
                                switch ($item['type']) {
                                    case "1":
                                        $str_sp .= "<option value=\"" . $item['id'] . "\">" . $item['name'] . "【" . $item['id'] . "】" . "</option>";
                                        break;
                                    case "2":
                                        $str_ys .= "<option value=\"" . $item['id'] . "\">" . $item['name'] . "【" . $item['id'] . "】" . "</option>";
                                        break;
                                    case "3":
                                        $str_cl .= "<option value=\"" . $item['id'] . "\">" . $item['name'] . "【" . $item['id'] . "】" . "</option>";
                                        break;
                                    case "4":
                                        $str_qt .= "<option value=\"" . $item['id'] . "\">" . $item['name'] . "【" . $item['id'] . "】" . "</option>";
                                        break;
                                    default:
                                        break;
                                }
                            }
                            $str_cl .= "</select><span style=\"border-right: none;\"><input type=\"text\" name=\"tbox_cl_num\" id=\"tbox_cl_num\" maxlength=\"4\" style=\"width: 30px; border: none; text-align: center;\" value=\"1\" /></span><span onclick=\"itemadd('cl');\">添加材料>></span></div>";
                            $str_sp .= "</select><span style=\"border-right: none;\"><input type=\"text\" name=\"tbox_sp_num\" id=\"tbox_sp_num\" maxlength=\"4\" style=\"width: 30px; border: none; text-align: center;\" value=\"1\" /></span><span onclick=\"itemadd('sp');\">添加食品>></span></div>";
                            $str_ys .= "</select><span style=\"border-right: none;\"><input type=\"text\" name=\"tbox_ys_num\" id=\"tbox_ys_num\" maxlength=\"4\" style=\"width: 30px; border: none; text-align: center;\" value=\"1\" /></span><span onclick=\"itemadd('ys');\">添加药水>></span></div>";
                            $str_qt .= "</select><span style=\"border-right: none;\"><input type=\"text\" name=\"tbox_qt_num\" id=\"tbox_qt_num\" maxlength=\"4\" style=\"width: 30px; border: none; text-align: center;\" value=\"1\" /></span><span onclick=\"itemadd('qt');\">添加其它>></span></div>";
                            echo $str_sp, $str_ys, $str_cl, $str_qt;
                            ?>
                        </div>
                    </div>


                </td>
                <td valign="top">
                    <form id="form1" name="form1" enctype="multipart/form-data" method="post" action="">
                        <table class="tab_repeat2" id="tab_repeat2" border="0" cellspacing="0" cellpadding="0" style="margin: 10px auto 10px auto; width: 98%; border-bottom: none;">
                            <tr class="tab_repeat2_tr1" id="tab_repeat2_tr1">
                                <td style="width: 350px; border-right: solid 1px #65dfe7;">Item名称</td>
                                <td style="width: 100px; border-right: solid 1px #65dfe7;">数 量</td>
                                <td>删 除</td>
                            </tr>
                            <tr style="height: 38px; line-height: 38px; text-align: right;">
                                <td colspan="3">
                                    <input type="hidden" name="hid_item" id="hid_item" value="" />
                                    <span><input type="file" name="file_upload" id="file_upload" style="width: 350px; height: 25px;" /></span>
                                    <span style="padding: 0 5px;"><input type="submit" name="but_issuelease" id="but_issuelease" value="批量发放" class="but" /></span>
                                </td>
                            </tr>
                        </table>
                    </form>
                </td>
            </tr>
        </table>
    </body>
</html>
