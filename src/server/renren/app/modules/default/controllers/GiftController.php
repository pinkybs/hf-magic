<?php

/**
 * island index controller
 *
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create      2010/01/20    Hulj
 */
class GiftController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = Zend_Registry::get('static');
        $this->view->version = Zend_Registry::get('version');
        $this->view->hostUrl = Zend_Registry::get('host');
    }

    public function topAction()
    {
        $application = Bll_Application_Renren::getInstance();
        $this->view->app_id = $application->getAppId();
        $this->view->domain = $application->xn_params['domain'];

        $uid = $application->getUserId();

        $dalUser = Dal_Island_User::getDefaultInstance();

        $this->view->giftList = array_chunk(Bll_Cache_Island::getGiftList(),12);
        $this->view->userInfo = $dalUser->getUser($uid);
        $this->render();
    }

    public function friendsAction()
    {
        $application = Bll_Application_Renren::getInstance();
        $this->view->app_id = $application->getAppId();
        $this->view->domain = $application->xn_params['domain'];

        $uid = $application->getUserId();
        $gid = $this->_request->getPost('gid');
        $gift = null;
        if ($gid) {
            $gift = Bll_Cache_Island::getGiftById($gid);
        }

        if (!$gift) {
            $top = 'http://apps.' . $application->xn_params['domain'] . '/rrisland/gift/top';
            echo '<xn:redirect url="' . $top . '" />';
            exit;
        }

        $friends = Bll_Cache_User::getRenrenFriends($uid, $_SESSION['session_key']);
        //$friends['224261478'] = array('uid' => '224261478', 'name' => 'fox', 'thumbnail' => 'http://hdn.xnimg.cn/photos/hdn221/20091116/1920/h_main_reDl_62f7000444152f74.jpg');

        foreach ($friends as $friend) {
        	$friends[$friend['uid']]['isAppUser'] = 0;
        }

        $infriends = Bll_Friend::getFriends($uid);
        if (!empty($infriends)) {
	        foreach($infriends as $fid) {
				if(isset($friends[$fid])) {
					$friends[$fid]['isAppUser'] = 1;
				}
	        }
        }

        $dalGift2 = Dal_Mongo_Gift::getDefaultInstance();
        $count = $dalGift2->getGiftStatus($uid);

        $this->view->gid = $gid;
        $this->view->gift = $gift;
        $this->view->friends = $friends;
        $this->view->count = $count;
        $this->render();
    }


    public function sendAction()
    {
        $application = Bll_Application_Renren::getInstance();

        $uid = $application->getUserId();

        $gid = $this->_request->getPost('gid');
        $ids = $this->_request->getPost('ids');

        $dalGift2 = Dal_Mongo_Gift::getDefaultInstance();
        $sendCount = $dalGift2->getGiftStatus($uid);

        if (!empty($gid) && !empty($ids)) {
            $in_fids = array();
            $out_fids = array();
            foreach ($ids as $fid) {
                if (Bll_User::isAppUser($fid)) {
                    $in_fids[] = $fid;
                } else {
                    $out_fids[] = $fid;
                }
            }
            $count = $sendCount - count($ids);
            if ($count >=0 ) {
                Bll_Island_Gift::sendGift($gid, $uid, $count, $in_fids, $out_fids);
            }
        }

        $top = 'http://apps.' . $application->xn_params['domain'] . '/rrisland/gift/top';

        echo '<xn:redirect url="' . $top . '" />';
        exit;
    }

    function preDispatch()
    {
        $application = Bll_Application_Renren::newInstance($this);

        $application->run();
    }


 }
