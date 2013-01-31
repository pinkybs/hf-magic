<?php

/**
 * island index controller
 *
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create      2010/01/20    Hulj
 */
class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = Zend_Registry::get('static');
        $this->view->version = Zend_Registry::get('version');
        $this->view->hostUrl = Zend_Registry::get('host');
    }

    /**
     * index Action
     *
     */
    public function indexAction()
    {
    	echo 'hello magic!! <br />';
    	
        $this->render();
    }
    

    public function flashAction()
    {
        if(isset($_GET['hf_dev']) && isset($_GET['hf_uid'])) {
            $uid = $_GET['hf_uid'];
            //if is new user
	        if (!Happyfish_Magic_Bll_Cache_User::getAppUser($uid)) {
	    	    Happyfish_Magic_Bll_User::join($uid);
	        }
	        
             // start session
            $session_id = md5('RENREN' . APP_KEY . APP_SECRET . $uid);
            session_id($session_id);
            $auth = Zend_Auth::getInstance();
            $auth->getStorage()->write($uid);
            $_SESSION['user'] = $uid;
            $scode = time();
            $_SESSION['scode'] = $scode;
            $_SESSION['authid'] = md5($scode . $uid);
            $_SESSION['app_id'] = APP_ID;
            $_SESSION['session_key'] = '';
            $_SESSION['expires'] = 3600;
            $this->view->app_id = APP_ID;
            $this->view->uid = $uid;
            $this->view->scode = $scode;
            $this->view->domain = 'renren.com';
//info_log($auth->getIdentity(), 'aa');
            //$this->render();
            $this->_redirect($this->view->hostUrl . '/index');
            return;
        }

    	$application = Happyfish_Magic_Bll_Application_Renren::newInstance($this);
    	$application->setLoadUser(false);
        $application->run();

        $scode = time();
        $uid = $application->getUserId();
        $_SESSION['scode'] = $scode;
        $_SESSION['authid'] = md5($scode . $uid);

        $this->view->app_id = $application->getAppId();
        $this->view->uid = $uid;
        $this->view->scode = $scode;
        $this->view->domain = $application->xn_params['domain'];
        $this->render();
    }
    
    

    public function testAction()
    {
    	echo 'magic hello<br />';

/* tests nb data
    	Happyfish_Magic_Bll_Cache_NbBasicInfo::clearNbBuilding();
    	$aa = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbBuilding(191005);
	    var_dump($aa);
	    echo '<br /><strong>NbBuilding</strong><br />';

	    Happyfish_Magic_Bll_Cache_NbBasicInfo::clearNbCard();
    	$aa = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbCard(11);
	    var_dump($aa);
	    echo '<br /><strong>NbCard</strong><br />';

	    Happyfish_Magic_Bll_Cache_NbBasicInfo::clearNbGuest();
    	$aa = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbGuest(2);
	    var_dump($aa);
	    echo '<br /><strong>NbGuest</strong><br />';

	    Happyfish_Magic_Bll_Cache_NbBasicInfo::clearNbItem();
	    $aa = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbItem(8306);
	    var_dump($aa);
	    echo '<br /><strong>NbItem</strong><br />';

	    Happyfish_Magic_Bll_Cache_NbBasicInfo::clearNbLevel();
	    $aa = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbLevel(2);
	    var_dump($aa);
	    echo '<br /><strong>NbLevel</strong><br />';

    	Happyfish_Magic_Bll_Cache_NbBasicInfo::clearNbMagicA();
    	$aa = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbMagicA(1002);
	    var_dump($aa);
	    echo '<br /><strong>NbMagicA</strong><br />';

	    Happyfish_Magic_Bll_Cache_NbBasicInfo::clearNbMagicB();
    	$aa = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbMagicB(8009);
	    var_dump($aa);
	    echo '<br /><strong>NbMagicB</strong><br />';

	    Happyfish_Magic_Bll_Cache_NbBasicInfo::clearNbMagicC();
    	$aa = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbMagicC(91001);
	    var_dump($aa);
	    echo '<br /><strong>NbMagicC</strong><br />';

    	Happyfish_Magic_Bll_Cache_NbBasicInfo::clearNbMagicLevel();
    	$aa = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbMagicLevel(2, 1);
	    var_dump($aa);
	    echo '<br /><strong>NbMagicLevel</strong><br />';
	    
	    Happyfish_Magic_Bll_Cache_NbBasicInfo::clearNbMarketLevel();
    	$aa = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbMarketLevel(3);
	    var_dump($aa);
	    echo '<br /><strong>NbMarketLevel</strong><br />';



	    Happyfish_Magic_Bll_Cache_NbBasicInfo::clearNbSymbol();
    	$aa = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbSymbol(2);
	    var_dump($aa);
	    echo '<br /><strong>NbSymbol</strong><br />';

	    Happyfish_Magic_Bll_Cache_NbBasicInfo::clearNbTaskDaily();
    	$aa = Happyfish_Magic_Bll_Cache_NbBasicInfo::listNbTaskDaily();
	    var_dump($aa);
	    echo '<br /><strong>NbTaskDaily</strong><br />';
	    exit;


    	Happyfish_Magic_Bll_Cache_NbBasicInfo::clearNbMessage();
    	$aa = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbMessage(9);
	    var_dump($aa);
	    echo '<br /><strong>NbMessage</strong><br />';

    	$dalMongo = Happyfish_Magic_Dal_Mongo_SnsUser::getDefaultInstance();
    	$dalMongo->addPerson(array('uid'=>22112313, 'name' =>'zhang xin', 'sex' => 1, 
    				               'headurl' => 'http://profile.img.mixi.jp/photo/member/23/13/22112313_2483545385.jpg',
    							   'tinyurl' => 'http://profile.img.mixi.jp/photo/member/23/13/22112313_2483545385.jpg'));
    	$dalMongo1 = Happyfish_Magic_Dal_Mongo_SnsFriend::getDefaultInstance();
    	$dalMongo1->insertFriend(22112313, array(111,567,999999));

    	$dalMongo->addPerson(array('uid'=>999999, 'name' =>'tester', 'sex' => 0, 
    				               'headurl' => 'http://ava-a.yahoo-mbga.jp/img_ava/profile/1994968/116/large-upper-normal.gif',
    							   'tinyurl' => 'http://ava-a.yahoo-mbga.jp/img_ava/profile/1994968/116/large-upper-normal.gif'));
    	$dalMongo1 = Happyfish_Magic_Dal_Mongo_SnsFriend::getDefaultInstance();
    	$dalMongo1->insertFriend(999999, array(22112313));
  */  	
    	

/* test mongo user*/
    	
    	
    	//$rst = Happyfish_Magic_Bll_User::join(22112313);
    	//echo "<br> $rst <br>";
/* test mongo message
    $aryMsg = array();
    	$aryMsg['actor_uid'] = 111;
    	$aryMsg['target_uid'] = 22112313;
    	$aryMsg['type'] = 1;
    	$aryMsg['template_id'] = 2;
    	$aryMsg['properties'] = array('actor' => 111, 'bad_status_name' => '冰冻');
    	$aryMsg['link'] = '';
    	$aryMsg['create_time'] = time();
    	echo Happyfish_Magic_Bll_Message::addUserMessage($aryMsg);
    	
    	$aryMsg = array();
    	$aryMsg['actor_uid'] = 111;
    	$aryMsg['target_uid'] = 22112313;
    	$aryMsg['type'] = 1;
    	$aryMsg['template_id'] = 1;
    	$aryMsg['properties'] = array('actor' => 111, 'num1' => 10, 'num2' =>2, 'item_name' => '红水晶');
    	$aryMsg['link'] = '';
    	$aryMsg['create_time'] = time();
    	echo Happyfish_Magic_Bll_Message::addUserMessage($aryMsg);	

		var_dump(Happyfish_Magic_Bll_Message::getUserMessage(22112313)); 

    	
    	Happyfish_Magic_Bll_Cache_User::clearUserMagic(22112313);
    	var_dump(Happyfish_Magic_Bll_Cache_User::lstUserMagic(22112313));
*/
    	
    	
    	exit();
    }

    public function clearcAction()
    {
    	$controller = $this->getFrontController();
        $controller->setParam('noViewRenderer', true);
    	Happyfish_Magic_Bll_Cache_NbBasicInfo::clearAll();
    	echo 'clear cache done';
    }
    
}