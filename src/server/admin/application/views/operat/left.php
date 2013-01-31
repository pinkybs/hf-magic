<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>left</title>
        <link href="<?php echo url::imgpath(); ?>style.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" language="javascript" src="<?php echo url::jspath(); ?>jquery-1.4.2.min.js"></script>
        <script type="text/javascript" language="javascript">
            function logout(){
                if(confirm("是否确定退出？")){
                    window.parent.location.href="<?php echo url::site('default/logout'); ?>";
                }
            }
            function change(){
                var platlist = <?php echo $platlist; ?>;
                var str = "<select name=\"sel_plat\" id=\"sel_plat\">";
                for(var i=0;i<platlist.length;i++){
                    str += "<option value=\""+platlist[i]["id"]+"\">"+platlist[i]["title"]+"</option>";
                }
                str += "</select>";
                str += "<span style=\"color: #369; cursor: pointer;\" onclick=\"ensure();\">[确定]</span>";
                str += "<span style=\"color: #369; cursor: pointer;\" onclick=\"cacenl();\">[取消]</span>";
                $("#span_plant").hide();
                $("#li_plant").css({"display":"block"}).html(str);
            }
            function cacenl(){
                $("#span_plant").show();
                $("#li_plant").css({"display":"none"}).html("");
            }
            function ensure(){
                if(confirm("确定切换至 "+$("#sel_plat option:selected").text()+" 平台？")){
                    var obj = $("#sel_plat").val();
                    var ajaxurl1 = "<?php echo url::site('operat/changeplant'); ?>?date="+obj+"&r="+Math.random();
                    $.get(ajaxurl1, function(data){
                        if(data == "ok"){
                            alert("更改成功！");
                            $("#span_title").html($("#sel_plat option:selected").text());
                            cacenl();
                            window.parent.frames["BodyFrame"].location.href="<?php echo url::site('operat/body'); ?>";
                        }
                    });
                }else{
                    cacenl();
                }
            }
        </script>
    </head>

    <body style="background: url(<?php echo url::imgpath(); ?>left_bg.jpg) repeat-y;">
        <div id="left_logo"><img src="<?php echo url::imgpath(); ?>logo.gif" alt="快乐运营后台工具" /></div>
        <div id="left_main">
            <ul id="left_ul" class="left_ul">
                <li id="left_ul_li_main" style="background: url(<?php echo url::imgpath(); ?>ico_sys.gif) 0px 5px no-repeat;"><a href="<?php echo url::site('operat/body'); ?>" target="BodyFrame">首页面</a></li>
                <li style="background: url(<?php echo url::imgpath(); ?>ico_sys1.png) 6px 8px no-repeat;"><a href="<?php echo url::site('operat/notemanage'); ?>" target="BodyFrame">游戏内公告管理</a></li>
                <li style="background: url(<?php echo url::imgpath(); ?>ico_sys10.png) 6px 8px no-repeat; border-bottom: dotted 1px #999;"><a href="<?php echo url::site('operat/itemrelease'); ?>" target="BodyFrame">Item发放工具</a></li>
                <li style="background: url(<?php echo url::imgpath(); ?>ico_sys10.png) 6px 8px no-repeat; border-bottom: dotted 1px #999;"><a href="<?php echo url::site('operat/buildrelease'); ?>" target="BodyFrame">装饰物发放工具</a></li>
            </ul>
        </div>
        <div id="left_info" style="border-bottom: dotted 1px #999;">
            <ul id="user_ul" class="left_ul">
                <li id="user_ul_li_main" style="background: url(<?php echo url::imgpath(); ?>ico_user.png) 0px 3px no-repeat; height: 35px; line-height: 35px;">用户信息</li>
                <li>当前用户：<span><?php echo $userinfo['name']; ?></span>&nbsp;&nbsp;<span style="color: #369; cursor: pointer;" onclick="logout();">[退出]</span>&nbsp;&nbsp;</li>
                <li>用户权限：<span><?php echo $ulevel; ?></span></li>
                <li>登陆时间：<span><?php echo date("H:i:s", strtotime($userinfo['time'])); ?></span></li>
                <li>当前日期：<span><?php echo $nowday; ?></span></li>
                <li>当前平台：<span id="span_plant"><span id="span_title"><?php echo $platinfo['title']; ?></span>&nbsp;&nbsp;<span style="color: #369; cursor: pointer;" onclick="change();">[更改]</span>&nbsp;&nbsp;</span></li>
                <li id="li_plant" style="display:none;"></li>
            </ul>
        </div>
        <div style="background: url(<?php echo url::imgpath(); ?>ico_info.png) 3px 6px no-repeat; height: 38px; line-height: 38px;">版本信息：v201103.1</div>
    </body>
</html>
