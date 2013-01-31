if (typeof APP_DOMAIN=="undefined") {
	APP_DOMAIN = null;
} 

function getDomain()
{
	if (!APP_DOMAIN) {
		var search = window.location.search;
		var domain = 'renren.com';
		if (search && search.length > 1) {
			var ret = {},seg = search.replace(/^\?/,'').split('&'),len = seg.length, i = 0, s;
	        for (;i<len;i++) {
	            if (!seg[i]) { continue; }
	            s = seg[i].split('=');
	            ret[s[0]] = s[1];
	        }
	        
	        if(ret.domain) {
	        	domain = ret.domain;
	        }
		}
		
		APP_DOMAIN = domain;
	}
	
	return APP_DOMAIN;
}

function goInvite()
{
	var domain = getDomain();
	
	var inviteURL = 'http://apps.' + domain + '/rrisland/invite/top';
	window.top.location = inviteURL;
}

function goPay()
{
	var domain = getDomain();
	
	var inviteURL = 'http://apps.' + domain + '/rrisland/pay/top';
	window.top.location = inviteURL;
}

function sendFeed(feedSettings)
{
	 try {
	 	if (feedSettings) {
	 		feedSettings = gadgets.json.parse(feedSettings);
	 		if (feedSettings) {
	 			XN.Connect.showFeedDialog(feedSettings);
	 		}
	 	}
	 }catch(e){}
}

function sendUserLevelUpFeed(level)
{
	var title = '在【<a href="http://apps.renren.com/rrisland">快乐岛主</a>】中升到了' + level + '级，赶快去看看吧~';
	var body = '升级了，奖励真不少啊！大家不要落后，一起努力吧！';
	var feedSettings = {
		'template_bundle_id': 1,
        'template_data' : {
        	'images': [{'src': 'http://static.hapyfish.com/renren/apps/island/images/feed/user_level_up.gif', 'href': 'http://apps.renren.com/rrisland'}],
            'title': title,
            'body' : body
        },
        'body_general' : '',
        'user_message_prompt' : '',
        'user_message' : '只要好友多就能快升级，大家快点加我吧~'
	};
	
	XN.Connect.showFeedDialog(feedSettings);
}