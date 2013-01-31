<?php

class Hapyfish2_Island_Stat_Bll_Campaign
{

    public static $aryCampaignInfo = array(
    			'10000' => array('sdate'=>'', 'edata'=>'', 'des'=>'官方微博链接'),
    			'10001' => array('sdate'=>'7.25 10:00', 'edata'=>'7.29 10:00', 'des'=>'新浪微博大转盘第一季'),
    			'10002' => array('sdate'=>'8.4 14:00', 'edata'=>'', 'des'=>'微群宣传链接'),
    			'10003' => array('sdate'=>'8.23 00:00', 'edata'=>'', 'des'=>'微币页面广告数据')
                );

	public static function fromCampaign($campaignId, $uid)
	{

	    $campaignId = base64_decode($campaignId);
	    if (!array_key_exists($campaignId, self::$aryCampaignInfo)) {
            return false;
        }

		try {
            $log = Hapyfish2_Util_Log::getInstance();
            $log->report('campJoinStat', array($campaignId, $uid));

            setcookie('hf_fromcamp', '', 0, '/', str_replace('http://', '.', HOST));
		}
		catch (Exception $e) {
		}
		return true;
	}

    public static function fromCampaignPv($campaignId, $clientIp)
	{

	    if (!array_key_exists($campaignId, self::$aryCampaignInfo)) {
            return false;
        }

		try {
            $log = Hapyfish2_Util_Log::getInstance();
            $log->report('campPvStat', array($campaignId, $clientIp));

            setcookie('hf_fromcamp', base64_encode($campaignId), 0, '/', str_replace('http://', '.', HOST));
		}
		catch (Exception $e) {
		}
		return true;
	}

}