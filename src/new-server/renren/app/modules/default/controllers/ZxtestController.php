<?php

class ZxtestController extends Zend_Controller_Action
{
	function vaild()
	{

	}

	function check()
	{
		$uid = $this->_request->getParam('uid');
		if (empty($uid)) {
			echo 'uid can not empty';
			exit;
		}

		$isAppUser = Hapyfish2_Magic_Cache_User::isAppUser($uid);
		if (!$isAppUser) {
			echo 'uid error, not app user';
			exit;
		}

		return $uid;
	}

	public function basicgiftlistrefreshAction()
	{
	    $key = 'magic:giftlist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->delete($key);

		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);

        echo json_encode(Hapyfish2_Magic_Cache_Gift::loadBasicGiftList());
        exit;
	}

    public function basicdailyawardlistrefreshAction()
	{
	    $key = 'magic:dailyaward';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->delete($key);

		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);

        echo json_encode(Hapyfish2_Magic_Cache_BasicInfo::getDailyAwardList());
        exit;
	}

    public function gifttodaywishAction()
	{
	    $uid = $this->check();
        $mkey = 'm:u:gift:wish:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        echo $uid.' wish:'.json_encode($cache->get($mkey));
        exit;
	}

    public function cleargifttodaywishAction()
	{
	    $uid = $this->check();
        $dalGift = Hapyfish2_Magic_Dal_Gift::getDefaultInstance();
        $dalGift->deleteWish($uid);
        $mkey = 'm:u:gift:wish:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->delete($mkey);
        echo 'ok';
        exit;
	}

    public function cleargifttodaysentAction()
	{
        $uid = $this->check();
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $mkey = 'm:u:gift:sent:g:uids:' . $uid;
        $mkey2 = 'm:u:gift:sent:w:uids:' . $uid;
        echo $uid.'<br/>gift sent:'.json_encode($cache->get($mkey));
        echo '<br/>wish sent:'.json_encode($cache->get($mkey2));
        $cache->delete($mkey);
        $cache->delete($mkey2);
        echo 'clear ok';
        exit;
	}

    public function clearreceivegiftAction()
	{
	    $uid = $this->check();
        $dalGift = Hapyfish2_Magic_Dal_Gift::getDefaultInstance();
        $dalGift->deleteBag($uid);
        echo 'ok';
        exit;
	}

	public function cleardailyawardAction()
	{
	    $uid = $this->check();
	    $today = date('Ymd');
    	$mckey = Hapyfish2_Magic_Bll_DailyAward::$_mcKeyPrex . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        echo json_encode($cache->get($mckey));
        $cache->delete($mckey);
        echo '<br/>clear ok.';
        exit;
	}

	public function clearlogintimeAction()
	{
	    $uid = $this->check();
	    $mckey = 'm:u:login:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		echo json_encode($cache->get($mckey));
        $cache->delete($mckey);
        echo '<br/>clear ok.';
        exit;
	}

    public function cleareventcollAction()
	{
	    $uid = $this->check();
	    $dal = Hapyfish2_Magic_Event_Dal_Collection::getDefaultInstance();
	    $rst = $dal->clear($uid);
	    Hapyfish2_Magic_Event_Bll_Collection::reloadUserCollect($uid);
	    echo json_encode($rst);
        echo '<br/>clear ok.';
        exit;
	}

	public function mapdataAction()
	{
	    $dal = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
	    $list = $dal->getMapCopyDecorList(3);
	    echo json_encode($list);
	    exit;
	}



    public function mapscenegetAction()
	{
        $uid = $this->check();
        $mapId = 101;
        $userVo = Hapyfish2_Magic_Bll_User::getUserInit($uid);
        $list = Hapyfish2_Magic_Bll_MapCopy::getMapCopyScene($uid, $mapId, $userVo);
        echo json_encode($list);
        exit;
	}

    public function mapgetAction()
	{
        $uid = $this->check();
        $mapId = 101;
        $list = Hapyfish2_Magic_HFC_MapCopy::getAll($uid);
        echo json_encode($list);
        exit;
	}

	public function mapinitAction()
	{
        $uid = $this->check();
        $mapId = 101;
        $basicMapInfo = Hapyfish2_Magic_Cache_BasicInfo::getMapCopyInfo($mapId);
        $list = Hapyfish2_Magic_Bll_MapCopy::initMapCopy($uid, $basicMapInfo);
        echo json_encode($list);
        exit;
	}

    public function mapclearAction()
	{
        $uid = $this->check();
        $mapId = 101;
        echo Hapyfish2_Magic_Bll_MapCopy::clearMapCopy($uid);
        echo '<br/>ok';
        exit;
	}

    public function maptaskclearAction()
	{
        $uid = $this->check();
        $pMapId = $this->_request->getParam('pmapid');
        echo Hapyfish2_Magic_Bll_Task::clearMapTask($uid, $pMapId);
        echo '<br/>ok';
        exit;
	}

    public function maptaskgetAction()
	{
        $uid = $this->check();
        $list = Hapyfish2_Magic_HFC_TaskMap::getAllInMap($uid, 1);
        echo json_encode($list);
        exit;
	}

    public function maptaskinitAction()
	{
        $uid = $this->check();
        $tasks = Hapyfish2_Magic_Bll_Task::getUserMapTask($uid, 1);
        echo json_encode($tasks);
        exit;
	}

	public function testAction()
	{
	    $per = (int)$this->_request->getParam('percent');
	    $rnd = mt_rand(1,$per);
	    echo $rnd;
        exit;
	    $aa = null;
	    echo $aa ? 'true' : 'false';
	    exit;
	}

	public function testwyxAction()
	{
		$url = 'http://game.weibo.com/home/widget/ajaxGet';

		$appId = (int)$this->_request->getParam('appId');
		$id = (int)$this->_request->getParam('id');//achievement id
		$type = (int)$this->_request->getParam('type');//0- achievement 1- score
		$value = (int)$this->_request->getParam('value');//score value

		$aryParam = array(
				'appId' => $appId,
				'id' => $id,
				'type' => $type,
				'value' => $value
		);
		$post_string = http_build_query($aryParam);

		try {
			$ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_HEADER, true);
	        curl_setopt($ch, CURLOPT_REFERER, 'http://game.weibo.com/ninja_cat?origin=1003');
	        curl_setopt($ch, CURLOPT_COOKIE, 'UOR=www.spider.com.cn,widget.weibo.com,; ULV=1343884614093:6:2:3:9445880979366.596.1343884614010:1343786160583; _s_tentry=-; Apache=9445880979366.596.1343884614010; SINAGLOBAL=9445880979366.596.1343884614010; SUS=SID-2137675743-1343884626-XD-qew35-4cc5d6714c46ec06ebaef1593e43b53c; SUE=es%3D0fff6192e00963069c6173db442b4f2a%26ev%3Dv1%26es2%3D1543b3c10ced8872d0a2b8ea89605cdd%26rs0%3DT4VSrEOqtLwbN6Qm%252BSKUCuAfETVz7hSmL6qZni5KMjRpFZxLdgU2aiLYEXA4NnRqHwQJwfM2tJqzVR0CZFfBu6zkWYrJzBOkIi1ur1Znrz8CmBA2RfaO9fZmdI0EP0a18SY8ucXPu8ga7DLx6QGhSDtLVCfiZKnHslyHADqQ5Vk%253D%26rv%3D0; SUP=cv%3D1%26bt%3D1343884627%26et%3D1343971027%26d%3Dc909%26i%3Dfe98%26us%3D1%26vf%3D0%26vt%3D0%26ac%3D0%26uid%3D2137675743%26user%3Dzhangxin_sk%2540qq.com%26ag%3D4%26name%3Dzhangxin_sk%2540qq.com%26nick%3Dzhangxin_sk%26fmp%3D%26lcp%3D2011-12-23%252014%253A22%253A43; SSOLoginState=1343884626; un=zhangxin_sk@qq.com; wvr=3.6; saeut=27.115.15.14.1343884650170883; USRGAME=inst_2611; WBTGVersion=39725cb39fd512fd; wyx_nav_tip=15555:1.15556:1.15557:1.; __utma=97321481.1424996815.1343884664.1343884664.1343884664.1; __utmb=97321481.2.10.1343884664; __utmc=97321481; __utmz=97321481.1343884664.1.1.utmcsr=weibo.com|utmccn=(referral)|utmcmd=referral|utmcct=/zhangxinsk; wyx_ad=1343884889689');
	        //max connect time
	        //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIME_OUT);
	        //max curl execute time
	        //curl_setopt($ch, CURLOPT_TIMEOUT, self::TIME_OUT);
	        //cache dns 1 hour
	        //curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 3600);
	        //renren can get and send data encoding by gzip
	        //curl_setopt($ch, CURLOPT_ENCODING, 'gzip');

	        $cURLVersion = curl_version();
	        $ua = 'PHP-cURL/' . $cURLVersion['version'] . ' Rest/1.0';
	        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

			$result = curl_exec($ch);

	        curl_close($ch);
		}
		catch (Exception $e) {
			echo 'curl-err'.$e->getMessage();
		}

        $msg = substr($result, strpos($result, '{"errorCode"'));
        if ($msg) {
        	echo $msg;
        	$ary = json_decode($msg, true);
        	if ($ary['msg']) {
        		//header('WWW-Autha:'.$ary['msg']);
        		echo urldecode($ary['msg']);
        	}
        }
        else {
        	echo '<br />***';
        	echo ($result);
        }

		exit;
	}
}