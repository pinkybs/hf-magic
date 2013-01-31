<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="校内网,社区,游戏,网游,webgame,豆瓣,小镇,小镇少年,回忆,童年,80后">
<!--  BEGIN Browser History required section -->
<link rel="stylesheet" type="text/css" href="<?=media::flash_url()?>history/history.css" />
<!--  END Browser History required section -->

<title>小镇少年技术性封测版本</title>
<script src="<?=media::flash_url()?>AC_OETags.js" language="javascript"></script>
<script href="<?=media::flash_url()?>history/history.js" language="javascript"></script>

<style>
body { margin:0px auto; padding:0;  }
#ad{ width:800px; height:12px; font-size:11px;text-align:center;color:#66C; font-family: "宋体"}
#menu{ width:800px; height:80px;  font-size:14px; color:#F00; font-weight:bold; padding-top:3px; _padding-bottom:4px;*padding-bottom:1px;}
#adsmall{padding-left:3px; overflow:hidden;width:453; padding-top:11px;}
#menu #text{clear:both;position:relative;width:337px; *width:343px; _width:341px; vertical-align:middle; text-indent:0.1em;border:1px solid #dbdbdb;background:#ffffcc; height:28px; margin-top:2px;*margin-top:4px;!important;*margin-top:4px; overflow:hidden; float:left; font-family:Arial, Helvetica, sans-serif;}
#menu #text li{overflow:hidden; list-style-type:none;}
#menu #buy{background-image: url(<?=media::img_url()?>icon/buy.gif) ;background-repeat:no-repeat; width:127px; height:38px;float:left; cursor:pointer;}
#menu #bbs{background-image: url(<?=media::img_url()?>icon/fans_bbs.gif) ;background-repeat:no-repeat; width:100px; height:38px;float:left;margin-left:5px;*margin-left:7px;!important;*margin-left:7px;}
#menu #buy a{width:127px; height:38px; display:block}
#menu #bbs a,#menu #friend a{width:100px; height:38px; display:block}
#menu #friend{background-image: url(<?=media::img_url()?>icon/fans_friend.gif); background-repeat:no-repeat; width:100px; height:38px;float:left; margin-left:5px;*margin-left:7px;!important;*margin-left:7px;}
#featured{
     position:relative;
     margin:0;
     padding:0;
     line-height:30px;
}
.float{
}
.float li{
     position:absolute;
}

#apDiv1 {
	width:800px;
	height:17px;
	position:absolute;
	font-size:12px;
	text-align: right;
	color:#66C;
	font-family: "宋体";
	top: -1px;*top: 0px;!important;*top: 0px;;
}
a:link { text-decoration: none;color: #66C}
a:active { text-decoration:none}
a:hover { text-decoration:underline;color: #66C}
a:visited { text-decoration: none;color: #66C}
.addfish {width: 100%; text-align:center; height:60px; margin-top:98px;  float:left;display:block;}
.tianjia{ width:330px;background-image:url(<?=media::img_url()?>add_happyfish1.gif) ;background-repeat:no-repeat; overflow:hidden;float:left;display:block; text-indent:-999px; line-height:60px;}
.add{ width:468px; height:60px;text-align:center; background:#fff; height:60px; float:left;display:block; overflow:hidden;}
</style>
<script language="JavaScript" type="text/javascript">
<!--
// -----------------------------------------------------------------------------
// Globals
// Major version of Flash required
var requiredMajorVersion = 9;
// Minor version of Flash required
var requiredMinorVersion = 0;
// Minor version of Flash required
var requiredRevision = 124;
// -----------------------------------------------------------------------------
// -->
</script>
</head>

<body align="center">
<div align="center">欢迎光临豆瓣社区游戏《小镇少年》<a target="_blank" href="http://www.douban.com/group/townboy/">加入豆瓣小组</a> qq群:104912994</div>
<div align="center">

<script language="JavaScript" type="text/javascript">
<!--
// Version check for the Flash Player that has the ability to start Player Product Install (6.0r65)
var hasProductInstall = DetectFlashVer(6, 0, 65);

// Version check based upon the values defined in globals
var hasRequestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);

if ( hasProductInstall && !hasRequestedVersion ) {
	// DO NOT MODIFY THE FOLLOWING FOUR LINES
	// Location visited after installation is complete if installation is required
	var MMPlayerType = (isIE == true) ? "ActiveX" : "PlugIn";
	var MMredirectURL = window.location;
    document.title = document.title.slice(0, 47) + " - Flash Player Installation";
    var MMdoctitle = document.title;

	AC_FL_RunContent(
		"src", "playerProductInstall",
		"FlashVars", "MMredirectURL="+MMredirectURL+'&MMplayerType='+MMPlayerType+'&MMdoctitle='+MMdoctitle+"",
		"width", "800",
		"height", "600",
		"align", "middle",
		"id", "main",
		"quality", "high",
		"bgcolor", "#869ca7",
		"name", "main",
		"allowScriptAccess","sameDomain",
		"type", "application/x-shockwave-flash",
		"pluginspage", "http://www.adobe.com/go/getflashplayer"
	);
} else if (hasRequestedVersion) {
	// if we've detected an acceptable version
	// embed the Flash Content SWF when all tests are passed
	AC_FL_RunContent(
			"src", "<?=media::flash_url()?>main?version=douban",
			"width", "800",
			"height", "600",
			"align", "middle",
			"id", "main",
			"quality", "high",
			"bgcolor", "#869ca7",
			"name", "main",
			"allowScriptAccess","sameDomain",
			"type", "application/x-shockwave-flash",
			"pluginspage", "http://www.adobe.com/go/getflashplayer",
			"FlashVars", "param1=session_"+"<?=session_id()?>"
	);
  } else {  // flash is too old or we can't detect the plugin
    var alternateContent = 'Alternate HTML content should be placed here. '
  	+ 'This content requires the Adobe Flash Player. '
   	+ '<a href=http://www.adobe.com/go/getflash/>Get Flash</a>';
    document.write(alternateContent);  // insert non-flash content
  }
// -->
</script>
<noscript>
  	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
			id="main" width="800" height="600"
			codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
			<param name="movie" value="<?=media::flash_url()?>main.swf?version=bailu_mooncake" />
			<param name="quality" value="high" />
			<param name="bgcolor" value="#869ca7" />
			<param name="allowScriptAccess" value="sameDomain" />
			<embed src="main.swf" quality="high" bgcolor="#869ca7"
				width="800" height="600" name="main" align="middle"
				play="true"
				loop="false"
				quality="high"
				allowScriptAccess="sameDomain"
				type="application/x-shockwave-flash"
				pluginspage="http://www.adobe.com/go/getflashplayer">
			</embed>
	</object>
</noscript>
</div>
<?php if (true) {?>
<div align="center">
<script type="text/javascript"><!--
google_ad_client = "pub-1003796506501163";
/* 728x90, 创建于 09-9-30 */
google_ad_slot = "1483580867";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>
<?php }?>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-9298473-3");
pageTracker._trackPageview();
} catch(err) {}</script>

<!--  BEGIN Browser History required section -->
<script src="<?=media::flash_url()?>history/history.js" language="javascript"></script>
<!--  END Browser History required section -->
</html>