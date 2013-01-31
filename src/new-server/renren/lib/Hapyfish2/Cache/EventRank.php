<?php

class Hapyfish2_Cache_EventRank
{
    protected static $_instance;

    /**
     * Memcached object
     *
     * @var mixed memcached object
     */
    protected $_memcached = null;

    public function __construct($mc)
    {
        $this->_memcached = $mc;
    }

    public function delete($key)
    {
        return $this->_memcached->delete($key);
    }

    public function get($key)
    {
    	return $this->_memcached->get($key);
    }

    public function set($key, $data, $time = 0)
    {
    	return $this->_memcached->set($key, $data, $time);
    }

    public function insert($key, $info, $time = 0)
    {
    	$try = 5;
    	$null = null;
    	$maxLen = 100;
    	$ok = false;
    	$first = false;

    	while($try > 0) {
    	    $data = $this->_memcached->get($key, $null, $token);

    	    if ($data === false) {
    	        if ($this->_memcached->getResultCode() == Memcached::RES_NOTFOUND) {
    				$data = array();
    				$first = true;
    			} else {
    				break;
    			}
    	    }

    		if ($first) {
    			$data[] = $info;
    			$this->_memcached->add($key, $data, $time);
    		} else {
    			$arySorted = array();
    			if (empty($data)) {
    				$arySorted[] = $info;
    			}
    			else {
	    			$roseNum = $info[1];
	    			$cnt = count($data);
	    			if ($cnt == $maxLen) {
	    				$minRankCnt = $data[$cnt - 1][1];
	    			}
	    			else {
						$minRankCnt = 0;
	    			}

	    			if ($roseNum<=$minRankCnt) {
						return false;
	    			}

	    			//rank need update
	    			$rmKey = -1;
	    			foreach ($data as $dkey => $row) {
	    				if ($info[2] == $row[2]) {
	    					$rmKey = $dkey;
	    					break;
	    				}
	    			}
	    			//remove myself rank data
	    			if ($rmKey>=0) {
						unset($data[$rmKey]);
	    			}
	    			$arySorted = $data;
	    			$arySorted[] = $info;
					foreach ($arySorted as $dkey => $row) {
					    $aryNum[$dkey]  = $row[1];
					}
					array_multisort($aryNum, SORT_DESC, $arySorted);

	    			//max 100 rank
	    			if ($cnt >= $maxLen) {
	    				array_pop($arySorted);
	    			}
    			}

    			$this->_memcached->cas($token, $key, $arySorted, $time);
    		}

			if ($this->_memcached->getResultCode() == Memcached::RES_SUCCESS) {
				$ok = true;
				break;
			}
    	}

    	return $ok;
    }


}