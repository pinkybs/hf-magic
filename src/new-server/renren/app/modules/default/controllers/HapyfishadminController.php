<?php

class HapyfishadminController extends Zend_Controller_Action
{

    //Admin Username & Password
    private $_admins = array('admin'=>'happyfish@2011',
    						 'jianghao'=>'1014@13052097027',
    						 'daqiang'=>'1011@alex.leung'
                       );

    private $_curAdmin;

	public function init()
	{
	    $loginU = $_SERVER['PHP_AUTH_USER'];
	    $loginP = $_SERVER['PHP_AUTH_PW'];
		if (!isset($loginU) || !isset($loginP)
            || !array_key_exists($loginU, $this->_admins) || $this->_admins[$loginU] != $loginP) {
			Header("WWW-Authenticate: Basic realm=Happy magic admin, Please Login");
			Header("HTTP/1.0 401 Unauthorized");

			echo <<<EOB
				<html><body>
				<h1>Rejected!</h1>
				<big>Wrong Username or Password!</big>
				</body></html>
EOB;
			exit;
		}

		$this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
        $this->view->appId = APP_ID;
        $this->view->appKey = APP_KEY;
        $this->view->adminName = $loginU;
        $this->_curAdmin = $loginU;
	}

    protected function echoResult($data)
    {
        header("Cache-Control: no-store, no-cache, must-revalidate");
        echo json_encode($data);
        exit();
    }

	function vaild()
	{

	}

	function check()
	{

	}

	function indexAction()
	{
		$this->render();
	}

    function reloadbasicAction()
	{
	    $newVer = $this->_request->getParam('ver');
	    if ($newVer) {
	        $ok = Hapyfish2_Magic_Cache_BasicInfo::setBasicVersion($newVer);
	    }

        $list = Hapyfish2_Magic_Tool_Server::getWebList();
		if (!empty($list)) {
			$host = str_replace('http://', '', HOST);
			foreach ($list as $server) {
				$url = 'http://' . $server['local_ip'] . '/tools/loadinitdata';
				$result = Hapyfish2_Magic_Tool_Server::requestWeb($host, $url);
				echo $server['name']. '--' . $url . ':' . $result . '<br/>';
			}
		}
		echo "OK";
		exit;
	}

	function basicdataAction()
	{
	    $apiBasicVer = Hapyfish2_Magic_Cache_BasicInfo::getBasicVersion();
	    $this->view->ver = $apiBasicVer;
        $this->view->tblist = Hapyfish2_Admin_Bll_Basic::getBasicTbList();
		$this->render();
	}

	function detaildataAction()
	{
        $tbName = $this->_request->getParam('table');
        $tbInfo = Hapyfish2_Admin_Bll_Basic::getBasicTbByName($tbName);
        if (!$tbInfo) {
            echo 'table not found,please check.';
            exit;
        }

        $cols = $tbInfo['column'];
        $colModel = $colNames = array();
        $colNames[] = '操作';
        $colModel[] = array('name'=>'actopt', 'index'=>'actopt', 'width'=>'80', 'sortable'=>false);
        foreach ($cols as $key=>$col) {
            $colNames[] = $col;
            $colModel[] = array('name'=>$key, 'index'=>$key, 'width'=>'85', 'sortable'=>false, 'editable'=>true, 'edittype'=>'text');
        }
	    if (isset($tbInfo['keynum'])) {
            $colNames[] = '键';
            $colModel[] = array('name'=>'mulKey', 'index'=>'mulKey', 'width'=>'15', 'sortable'=>true, 'key'=>true, 'sorttype'=>'text');
        }

        $dal = Hapyfish2_Admin_Dal_Basic::getDefaultInstance();
        $lstData = $dal->getBasicList($tbName);
        if (isset($tbInfo['keynum'])) {
            foreach ($lstData as $key=>&$data) {
                $counter = 0;
                $mulKey = '';
                foreach ($data as $val) {
                    if ($counter < $tbInfo['keynum']) {
                        $mulKey = $val.'_'.$mulKey;
                        $counter ++;
                    }
                    else {
                        break;
                    }
                }
                $data['mulKey'] = substr($mulKey, 0, -1);
            }
        }
        $this->view->lstData = json_encode($lstData);

        $canDel = isset($tbInfo['candel']) ? $tbInfo['candel'] : 0;
        $selDelList = array();
        if ($canDel) {
            foreach ($lstData as $key=>$data) {
                $selId = $data[$tbInfo['candel']];
                if (!array_key_exists($selId, $selDelList)) {
                    $selDelList[$selId] = $selId;
                }
            }
        }

        $this->view->tbShowName = $tbInfo['name'];
        $this->view->tbName = $tbName;
        $this->view->colNames = json_encode($colNames);
        $this->view->colModel = json_encode($colModel);
        $this->view->candel = $canDel;
        $this->view->selDelList = $selDelList;

		$this->render();
	}

	function getdataAction()
	{
	    $tbName = $this->_request->getParam('table');
	    $dal = Hapyfish2_Admin_Dal_Basic::getDefaultInstance();
        $lstData = $dal->getBasicList($tbName);
        echo json_encode($lstData);
        exit;
	}

    function savedataAction()
	{
	    $tbName = $this->_request->getParam('table');
	    $tbInfo = Hapyfish2_Admin_Bll_Basic::getBasicTbByName($tbName);
	    if (!$tbInfo) {
            echo 'table not found,please check.';
            exit;
        }

	    $cols = $tbInfo['column'];
	    $info = array();
        foreach ($cols as $key=>$col) {
            $info[$key] = $this->_request->getPost($key);
            if (isset($tbInfo['keynum']) && $key == 'id') {
                if (strpos($info[$key], '_') > 0) {
                    $aryTmp = explode('_', $info[$key]);
                    $info[$key] = $aryTmp[1];
                }
            }
        }

        $dal = Hapyfish2_Admin_Dal_Basic::getDefaultInstance();
        $rst = $dal->addInfo($tbName, $info);
        //echo json_encode($rst);
        echo 'complete';
        exit;
	}

	function deldataAction()
	{
	    $tbName = $this->_request->getParam('table');
	    $field = $this->_request->getParam('key');
	    $selVal = $this->_request->getParam('selVal');
	    if (empty($tbName) || empty($field) || empty($selVal)) {
	        echo '删除失败，参数不正';
	        exit;
	    }

	    $dal = Hapyfish2_Admin_Dal_Basic::getDefaultInstance();
        $rst = $dal->deleteInfo($tbName, $field, $selVal);
        echo '副本地图' . $selVal . '数据删除成功！<br />';
        echo '<a href="#" onclick="parent.showDetail(\''. $tbName .'\');">》返回《</a>';
		exit;
	}

	function exportdataAction()
	{
	    $tbName = $this->_request->getParam('table');
        $fileName = LOG_DIR . '/admin/export_'.$tbName.'.txt';
        $rst = Hapyfish2_Admin_Bll_Basic::generateBasicDataFile($tbName, $fileName);
        if ($rst) {
            echo $rst;
            exit;
        }

        if (!file_exists($fileName)) {
            echo "export failed: $fileName";
            exit;
        }

        header('Content-Description: File Transfer');
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename='.basename($fileName));
        //header('Content-Transfer-Encoding: binary');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        //header('Expires: 0');
        header('Content-Length: ' . filesize($fileName));
        ob_clean();
        flush();
        readfile($fileName);
        exit;
	}

	function importdataAction()
	{
		$tbName = $this->_request->getParam('table');
	    $tbInfo = Hapyfish2_Admin_Bll_Basic::getBasicTbByName($tbName);
	    if (!$tbInfo) {
            echo 'table not found,please check.';
            exit;
        }

		if (!isset($_POST['btnImport'])){
            echo 'not post please retry.';
            exit;
		}

		$file = $_FILES['filename'];
        $file_type = substr(strstr($file['name'],'.'), 1);

        // 检查文件格式
        if ($file_type != 'txt'){
            echo '文件格式不对,请重新上传!';
            exit;
        }

        //先备份原有数据
        $bakFile = LOG_DIR . '/admin/bak/'.$tbName.'_'.date('Ymd-His').'.txt';
        Hapyfish2_Admin_Bll_Basic::generateBasicDataFile($tbName, $bakFile);

        //再导入新数据
        $aryFailed = array();
        $cntSuccess = Hapyfish2_Admin_Bll_Basic::importBasicDataFromFile($tbName, $file['tmp_name'], $aryFailed);

		echo "<br />";
		if ($cntSuccess) {
		    echo $cntSuccess . ' 行数据导入成功！<br />';
		}
		else {
		    echo '数据导入失败！<br />';
		}
		if ($aryFailed) {
		    echo '第'. implode(',', $aryFailed) . '行数据有问题，请检查后重新导入！<br />';
		}
		echo '<a href="#" onclick="parent.showDetail(\''. $tbName .'\');">》返回《</a>';
		exit;
	}

    function grantitemAction()
	{
	    $this->view->decors = Hapyfish2_Magic_Bll_BasicInfo::getBuildingList();
	    $this->view->items = Hapyfish2_Magic_Bll_BasicInfo::getItemList();
		$this->render();
	}

    function ajaxgrantitemAction()
	{
	    $coin = (int)$this->_request->getParam('coin');
	    $gold = (int)$this->_request->getParam('gold');
	    $item = (int)$this->_request->getParam('item');
	    $decor = (int)$this->_request->getParam('decor');
	    $cntItem = (int)$this->_request->getParam('cntItem');
	    $cntDecor = (int)$this->_request->getParam('cntDecor');
	    $uids = $this->_request->getParam('uids');

	    if (empty($uids)) {
            $this->echoResult(array('status'=>0,'msg'=>'uid is empty!'));
	    }
	    if (empty($coin) && empty($gold) && empty($item) && empty($decor) && empty($cntItem) && empty($cntDecor) && empty($uids)) {
            $this->echoResult(array('status'=>0,'msg'=>'nothing to send!'));
	    }

	    $aryUid = explode(',', $uids);
	    $msg = '';
	    $sendUids = array();
	    foreach ($aryUid as $uid) {
	        $uid = (int)$uid;
	        $rowUser = Hapyfish2_Platform_Bll_User::getUser($uid);
	        if (!$rowUser) {
	            continue;
	        }

            $robot = new Hapyfish2_Magic_Bll_Award();
            $strGain = '';
            if ($coin) {
                $robot->setCoin($coin);
                $strGain .= '--金币+' . $coin;
            }
	        if ($gold) {
                $robot->setGold($gold, 98);
                $strGain .= '--宝石+' . $gold;
            }
	        if ($item && $cntItem) {
                $robot->setItem($item, $cntItem);
                $strGain .= '--物品'.$item.'*' . $cntItem;
            }
	        if ($decor && $cntDecor) {
                $robot->setDecor($decor, $cntDecor);
                $strGain .= '--装饰'.$decor.'*' . $cntDecor;
            }
            $robot->sendOne($uid);

	        $msg .= "\n" .$uid . ' -> ' . $strGain;
	        $sendUids[]= $uid;
	    }

	    //$log = Hapyfish2_Util_Log::getInstance();
	    $sendItem = 'coin:'.$coin.'  gold:'.$gold.'  item:'.$item.'*'.$cntItem.'  decor:'.$decor.'*'.$cntDecor;
	    //$log->report('adminGrantitem', array($this->_curAdmin, implode(',', $sendUids), $sendItem));
	    info_log($this->_curAdmin.':'.implode(',', $sendUids).'->'.$sendItem, 'adminGrantitem');

	    $rst = array('status'=>1,'msg'=>$msg,'tm'=>date('Y/m/d H:i:s'));
		$this->echoResult($rst);
	}


}