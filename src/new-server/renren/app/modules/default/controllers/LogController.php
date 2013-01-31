<?php

class LogController extends Zend_Controller_Action
{
    
    /**
     * initialize basic data
     * @return void
     */
    public function init()
    {
    	$controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
    }
    
    public function getsigAction()
    {
    	$t = time();
    	echo '1001_' . $t . '_' . md5('1001' . $t . APP_KEY);
    	echo '<br/>';
    	echo '1002_' . $t . '_' . md5('1002' . $t . APP_KEY);
    	exit;
    }
    
    protected function vailid($name)
    {
    	$skey = $_COOKIE[$name];
		if (!$skey) {
    		return false;
    	}
    	
        $tmp = split('_', $skey);
    	if (empty($tmp)) {
    		return false;
    	}
    	$count = count($tmp);
    	if ($count != 3) {
    		return false;
    	}
    	
        $id= $tmp[0];
        $t = $tmp[1];
        $sig = $tmp[2];
        
        $vsig = md5($id . $t . APP_KEY);
		if ($sig != $vsig) {
			return false;
		}
		
        //max long time one day
        /*
        if (time() > $t + 31104000) {
        	return false;
        }*/
		
		return array('id' => $id, 't' => $t);
    }
    
	public function payflowAction()
	{
		$date = $this->_request->getParam('date');
		$logDir = '/data/log/happyfish/payflow/';
		if (!empty($date)) {
			$info = $this->vailid('hf_payflow_date');
	        if (!$info) {
				exit;
	        }

			if ($info['id'] == 1001) {
				$listname = $logDir . $date . '/file_list.txt';
				if (is_file($listname)) {
					echo file_get_contents($listname);
				}
			}
			exit;
		}
		
		$file = $this->_request->getParam('file');
		if (!empty($file)) {
			$info = $this->vailid('hf_payflow_file');
	        if (!$info) {
				exit;
	        }

			if ($info['id'] == 1002) {
				$tmp = explode('_', $file);
				if (count($tmp) == 0) {
					$filename = $logDir . $file . '/' . $file;
				} else if(count($tmp) == 2) {
					$filename = $logDir . $tmp[0] . '/' . $file;
				} else {
					exit;
				}
				
				
				if (is_file($filename)) {
					echo file_get_contents($filename);
				}
			}
			exit;
		}
		
		exit;
	}

 }