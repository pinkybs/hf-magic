<?php
function smarty_function_html_stats($params, &$smarty)
{
	$output = '';
	
    if(defined('ENABLE_STATS') && ENABLE_STATS) {
        $id = $params['id'];
    	$uid = $params['uid'];
    	$output = '<!-- CF Stats -->'
    	        . '<script type="text/javascript" src="http://static.mixi.communityfactory.net/cmn/js/stats.js"></script>'
    	        . '<script type="text/javascript" language="javascript">'
    	        . 'try{var _CF_Tracker = CfStats.getTracker(' . $id . ');var user={"userId":' . $uid .'};_CF_Tracker.setCustomData(user);_CF_Tracker.trackPageView();_CF_Tracker.enableLinkTracking();}catch(err){}'
    	        . '</script>'
    	        . '<!-- End CF Stats Tag -->';
    }
              
    return $output;
}
