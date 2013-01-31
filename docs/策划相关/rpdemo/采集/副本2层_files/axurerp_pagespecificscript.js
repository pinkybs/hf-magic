
var PageName = '副本2层';
var PageId = 'p60675752f03247b6b3636b112b844e35'
var PageUrl = '副本2层.html'
document.title = '副本2层';

if (top.location != self.location)
{
	if (parent.HandleMainFrameChanged) {
		parent.HandleMainFrameChanged();
	}
}

var $OnLoadVariable = '';

var $CSUM;

var hasQuery = false;
var query = window.location.hash.substring(1);
if (query.length > 0) hasQuery = true;
var vars = query.split("&");
for (var i = 0; i < vars.length; i++) {
    var pair = vars[i].split("=");
    if (pair[0].length > 0) eval("$" + pair[0] + " = decodeURIComponent(pair[1]);");
} 

if (hasQuery && $CSUM != 1) {
alert('Prototype Warning: The variable values were too long to pass to this page.\nIf you are using IE, using Firefox will support more data.');
}

function GetQuerystring() {
    return '#OnLoadVariable=' + encodeURIComponent($OnLoadVariable) + '&CSUM=1';
}

function PopulateVariables(value) {
  value = value.replace(/\[\[OnLoadVariable\]\]/g, $OnLoadVariable);
  value = value.replace(/\[\[PageName\]\]/g, PageName);
  return value;
}

function OnLoad(e) {

}

eval(GetDynamicPanelScript('u40', 1));

eval(GetDynamicPanelScript('u54', 1));

eval(GetDynamicPanelScript('u34', 1));

eval(GetDynamicPanelScript('u19', 1));

eval(GetDynamicPanelScript('u9', 1));

eval(GetDynamicPanelScript('u37', 1));

eval(GetDynamicPanelScript('u55', 1));

eval(GetDynamicPanelScript('u70', 1));

eval(GetDynamicPanelScript('u67', 1));

eval(GetDynamicPanelScript('u43', 1));

var u71 = document.getElementById('u71');

var u20 = document.getElementById('u20');

var u64 = document.getElementById('u64');
gv_vAlignTable['u64'] = 'center';
var u51 = document.getElementById('u51');
gv_vAlignTable['u51'] = 'center';
var u70 = document.getElementById('u70');

var u36 = document.getElementById('u36');
gv_vAlignTable['u36'] = 'center';
var u31 = document.getElementById('u31');
gv_vAlignTable['u31'] = 'center';
var u45 = document.getElementById('u45');
gv_vAlignTable['u45'] = 'center';
var u11 = document.getElementById('u11');
gv_vAlignTable['u11'] = 'center';
var u27 = document.getElementById('u27');
gv_vAlignTable['u27'] = 'center';
var u6 = document.getElementById('u6');
gv_vAlignTable['u6'] = 'center';
var u67 = document.getElementById('u67');

var u4 = document.getElementById('u4');

u4.style.cursor = 'pointer';
if (bIE) u4.attachEvent("onclick", Clicku4);
else u4.addEventListener("click", Clicku4, true);
function Clicku4(e)
{

if (true) {

	self.location.href="Home.html" + GetQuerystring();

}

}

var u73 = document.getElementById('u73');

var u2 = document.getElementById('u2');

var u10 = document.getElementById('u10');

var u0 = document.getElementById('u0');

var u69 = document.getElementById('u69');
gv_vAlignTable['u69'] = 'center';
var u26 = document.getElementById('u26');

var u49 = document.getElementById('u49');
gv_vAlignTable['u49'] = 'center';
var u63 = document.getElementById('u63');

var u35 = document.getElementById('u35');

u35.style.cursor = 'pointer';
if (bIE) u35.attachEvent("onclick", Clicku35);
else u35.addEventListener("click", Clicku35, true);
function Clicku35(e)
{

if (true) {

	SetPanelVisibilityu37("");

}

}

var u29 = document.getElementById('u29');
gv_vAlignTable['u29'] = 'center';
var u54 = document.getElementById('u54');

var u8 = document.getElementById('u8');
gv_vAlignTable['u8'] = 'center';
var u34 = document.getElementById('u34');

var u68 = document.getElementById('u68');

var u14 = document.getElementById('u14');
gv_vAlignTable['u14'] = 'center';
var u48 = document.getElementById('u48');

var u72 = document.getElementById('u72');
gv_vAlignTable['u72'] = 'center';
var u28 = document.getElementById('u28');

var u44 = document.getElementById('u44');

var u33 = document.getElementById('u33');
gv_vAlignTable['u33'] = 'center';
var u50 = document.getElementById('u50');

var u22 = document.getElementById('u22');

var u52 = document.getElementById('u52');

var u66 = document.getElementById('u66');
gv_vAlignTable['u66'] = 'center';
var u13 = document.getElementById('u13');

var u47 = document.getElementById('u47');

u47.style.cursor = 'pointer';
if (bIE) u47.attachEvent("onclick", Clicku47);
else u47.addEventListener("click", Clicku47, true);
function Clicku47(e)
{

if (true) {

	SetPanelVisibilityu43("hidden");

}

}

var u12 = document.getElementById('u12');
gv_vAlignTable['u12'] = 'top';
var u41 = document.getElementById('u41');

var u53 = document.getElementById('u53');
gv_vAlignTable['u53'] = 'center';
var u57 = document.getElementById('u57');
gv_vAlignTable['u57'] = 'center';
var u21 = document.getElementById('u21');
gv_vAlignTable['u21'] = 'center';
var u37 = document.getElementById('u37');

var u7 = document.getElementById('u7');

u7.style.cursor = 'pointer';
if (bIE) u7.attachEvent("onclick", u7Click);
else u7.addEventListener("click", u7Click, true);
InsertAfterBegin(document.body, "<DIV class='intcases' id='u7LinksClick'></DIV>")
var u7LinksClick = document.getElementById('u7LinksClick');
function u7Click(e) 
{

	ToggleLinks(e, 'u7LinksClick');
}

InsertBeforeEnd(u7LinksClick, "<div class='intcaselink' onmouseout='SuppressBubble(event)' onclick='u7Clicku1d368b0ebb094fe7a9cd9bf3f49eac5d(event)'>工具足够</div>");
function u7Clicku1d368b0ebb094fe7a9cd9bf3f49eac5d(e)
{

	SetPanelVisibilityu34("");

	SetPanelVisibilityu40("");

	ToggleLinks(e, 'u7LinksClick');
}

InsertBeforeEnd(u7LinksClick, "<div class='intcaselink' onmouseout='SuppressBubble(event)' onclick='u7Clicku2578e556d81445ce84f97b814c0f99f2(event)'>工具不足</div>");
function u7Clicku2578e556d81445ce84f97b814c0f99f2(e)
{

	SetPanelVisibilityu43("");

	ToggleLinks(e, 'u7LinksClick');
}

if (bIE) u7.attachEvent("onmouseover", MouseOveru7);
else u7.addEventListener("mouseover", MouseOveru7, true);
function MouseOveru7(e)
{
if (!IsTrueMouseOver('u7',e)) return;
if (true) {

	SetPanelVisibilityu9("");

}

}

if (bIE) u7.attachEvent("onmouseout", MouseOutu7);
else u7.addEventListener("mouseout", MouseOutu7, true);
function MouseOutu7(e)
{
if (!IsTrueMouseOut('u7',e)) return;
if (true) {

	SetPanelVisibilityu9("hidden");

}

}

var u40 = document.getElementById('u40');

var u17 = document.getElementById('u17');

u17.style.cursor = 'pointer';
if (bIE) u17.attachEvent("onclick", u17Click);
else u17.addEventListener("click", u17Click, true);
InsertAfterBegin(document.body, "<DIV class='intcases' id='u17LinksClick'></DIV>")
var u17LinksClick = document.getElementById('u17LinksClick');
function u17Click(e) 
{

	ToggleLinks(e, 'u17LinksClick');
}

InsertBeforeEnd(u17LinksClick, "<div class='intcaselink' onmouseout='SuppressBubble(event)' onclick='u17Clicku2a9f1457c5084610aa8a4945f833cdbd(event)'>魔法足够</div>");
function u17Clicku2a9f1457c5084610aa8a4945f833cdbd(e)
{

	SetPanelVisibilityu54("");

	ToggleLinks(e, 'u17LinksClick');
}

InsertBeforeEnd(u17LinksClick, "<div class='intcaselink' onmouseout='SuppressBubble(event)' onclick='u17Clicku968bf5cde2864a4fb2696e4fa8a3593d(event)'>魔法不足</div>");
function u17Clicku968bf5cde2864a4fb2696e4fa8a3593d(e)
{

	SetPanelVisibilityu70("");

	ToggleLinks(e, 'u17LinksClick');
}

if (bIE) u17.attachEvent("onmouseover", MouseOveru17);
else u17.addEventListener("mouseover", MouseOveru17, true);
function MouseOveru17(e)
{
if (!IsTrueMouseOver('u17',e)) return;
if (true) {

	SetPanelVisibilityu19("");

}

}

if (bIE) u17.attachEvent("onmouseout", MouseOutu17);
else u17.addEventListener("mouseout", MouseOutu17, true);
function MouseOutu17(e)
{
if (!IsTrueMouseOut('u17',e)) return;
if (true) {

	SetPanelVisibilityu19("hidden");

}

}

var u5 = document.getElementById('u5');

u5.style.cursor = 'pointer';
if (bIE) u5.attachEvent("onclick", Clicku5);
else u5.addEventListener("click", Clicku5, true);
function Clicku5(e)
{

if (true) {

	self.location.href="副本1层.html" + GetQuerystring();

}

}

var u15 = document.getElementById('u15');

u15.style.cursor = 'pointer';
if (bIE) u15.attachEvent("onclick", Clicku15);
else u15.addEventListener("click", Clicku15, true);
function Clicku15(e)
{

if (true) {

	self.location.href="副本1层.html" + GetQuerystring();

}

}

var u56 = document.getElementById('u56');

var u3 = document.getElementById('u3');

var u65 = document.getElementById('u65');

var u1 = document.getElementById('u1');
gv_vAlignTable['u1'] = 'center';
var u25 = document.getElementById('u25');
gv_vAlignTable['u25'] = 'center';
var u59 = document.getElementById('u59');

u59.style.cursor = 'pointer';
if (bIE) u59.attachEvent("onclick", u59Click);
else u59.addEventListener("click", u59Click, true);
InsertAfterBegin(document.body, "<DIV class='intcases' id='u59LinksClick'></DIV>")
var u59LinksClick = document.getElementById('u59LinksClick');
function u59Click(e) 
{

	ToggleLinks(e, 'u59LinksClick');
}

InsertBeforeEnd(u59LinksClick, "<div class='intcaselink' onmouseout='SuppressBubble(event)' onclick='u59Clicku7af111ec0c0943c6b1041b055d2785cf(event)'>关闭</div>");
function u59Clicku7af111ec0c0943c6b1041b055d2785cf(e)
{

	SetPanelVisibilityu54("hidden");

	SetPanelVisibilityu55("hidden");

	SetPanelVisibilityu67("hidden");

	ToggleLinks(e, 'u59LinksClick');
}

InsertBeforeEnd(u59LinksClick, "<div class='intcaselink' onmouseout='SuppressBubble(event)' onclick='u59Clicku6440abfbdc1a4d41ae501b08265aa951(event)'>怪被魔法击中 后退</div>");
function u59Clicku6440abfbdc1a4d41ae501b08265aa951(e)
{

	SetPanelVisibilityu54("hidden");

	ToggleLinks(e, 'u59LinksClick');
}

InsertBeforeEnd(u59LinksClick, "<div class='intcaselink' onmouseout='SuppressBubble(event)' onclick='u59Clickua203d93df7bf4ec68b10ac992d764937(event)'>怪掉血 玩家扣魔</div>");
function u59Clickua203d93df7bf4ec68b10ac992d764937(e)
{

	SetPanelVisibilityu67("");

	SetPanelVisibilityu55("hidden");

	ToggleLinks(e, 'u59LinksClick');
}

var u43 = document.getElementById('u43');

var u16 = document.getElementById('u16');
gv_vAlignTable['u16'] = 'center';
var u39 = document.getElementById('u39');
gv_vAlignTable['u39'] = 'center';
var u19 = document.getElementById('u19');

var u9 = document.getElementById('u9');

var u30 = document.getElementById('u30');

var u74 = document.getElementById('u74');

var u60 = document.getElementById('u60');
gv_vAlignTable['u60'] = 'center';
var u24 = document.getElementById('u24');

var u46 = document.getElementById('u46');

var u55 = document.getElementById('u55');

var u38 = document.getElementById('u38');

u38.style.cursor = 'pointer';
if (bIE) u38.attachEvent("onclick", Clicku38);
else u38.addEventListener("click", Clicku38, true);
function Clicku38(e)
{

if (true) {

	SetPanelVisibilityu34("hidden");

	SetPanelVisibilityu37("hidden");

	SetPanelVisibilityu40("hidden");

}

}

var u61 = document.getElementById('u61');

var u18 = document.getElementById('u18');
gv_vAlignTable['u18'] = 'center';
var u62 = document.getElementById('u62');
gv_vAlignTable['u62'] = 'center';
var u32 = document.getElementById('u32');

var u42 = document.getElementById('u42');
gv_vAlignTable['u42'] = 'center';
var u23 = document.getElementById('u23');
gv_vAlignTable['u23'] = 'center';
var u58 = document.getElementById('u58');
gv_vAlignTable['u58'] = 'top';
if (window.OnLoad) OnLoad();
