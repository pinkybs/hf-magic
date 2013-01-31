<?php

class Hapyfish2_Magic_Event_EvtCollect extends Hapyfish2_Magic_Event_Abstract
{

    protected $_exchangeCondition;

    public function setExchangeCondition($data)
	{
	    $exchangeId = $data['id'];
	    if (isset($this->_exchangeCondition[$exchangeId])) {
			$this->_exchangeCondition[$exchangeId] = $data;
		} else {
		    $this->_exchangeCondition = array();
			$this->_exchangeCondition[$exchangeId] = $data;
		}
	}

    public function getExchangeCondition()
	{
	    return $this->_exchangeCondition;
	}

	public function getExchangeConditionById($exchangeId)
	{
	    $list = $this->_exchangeCondition;
	    if (isset($list[$exchangeId])) {
	        return $list[$exchangeId];
	    }
	    return null;
	}


	public function checkAvailLife()
	{
	    $avail = false;
	    $tm = time();
	    $start = $this->getEventStartTime();
	    $end = $this->getEventEndTime();
	    if (!$end) {
	        return true;
	    }

	    if ($tm>=$start && $tm<=$end) {
            $avail = true;
	    }
	    return $avail;
	}


	//1-success 0-event time not right -1/-2...other failed
	public function exchange($uid, $exchangeId)
	{
	    if (!$this->checkAvailLife()) {
	        return 0;
	    }
        $rowCondition = $this->getExchangeConditionById($exchangeId);
        if (!$rowCondition) {
            return -1;
        }

        $need = $rowCondition['need'];
        $exFor = $rowCondition['for'];

        //check need item enough
        foreach ($need as $k => $v) {
            $curCnt = 0;
            //道具
			if ($v['type'] == 1) {
                $curCnt = Hapyfish2_Magic_HFC_Item::getUserItemCount($uid, $v['id']);
			}
			if ($curCnt<$v['num']) {
                return -2;//item not enough
			}
			$ok = Hapyfish2_Magic_HFC_Item::useUserItem($uid, $v['id'], $v['num']);
			if (!$ok) {
			    return -3;//remove item failed
			}
        }

        //exchange for collection
        $items = array();
        $decors = array();
	    foreach ($exFor as $k => $v) {
		    //道具
			if ($v['type'] == 1) {
				$items[] = array($v['id'], $v['num']);
			}
			//装饰物
			else if ($v['type'] == 2) {
                $decors[] = array($v['id'], $v['num']);
			}
		}//end for

		if ($items || $decors) {
            $awardRot = new Hapyfish2_Magic_Bll_Award();
    	    if ($items) {
    			$awardRot->setItemList($items);
    		}
            if ($decors) {
    			$awardRot->setDecorList($decors);
    		}
    		$awardRot->sendOne($uid);
		}

        return 1;
	}

    //1-success 0-event time not right -1/-2...other failed
	public function randDrop($uid, $dropItems)
	{
	    if (!$this->checkAvailLife()) {
	        return 0;
	    }

        $items = array();
        $decors = array();
        $numPar = 100;
	    foreach ($dropItems as $k => $v) {
	        //check if in random percent
		    $bingo = true;
		    if (isset($v['per'])) {
                if ($v['per'] < 1) {
                    $aryKeys['hit'] = $v['per']*$numPar;
                    $aryKeys['nohit'] = 100*$numPar - $v['per']*$numPar;
                }
                else if ($v['per'] <= 100) {
                    $aryKeys['hit'] = $v['per'];
                    $aryKeys['nohit'] = 100 - $v['per'];
                }
                else {
                    $aryKeys['hit'] = 100;
                }
                $hit = self::_randomKeyForOdds($aryKeys);
                if ($hit == 'nohit') {
                    $bingo = false;
                }
		    }
		    if (!$bingo) {
		        continue;
		    }
		    //道具
			if ($v['type'] == 1) {
				$items[] = array($v['id'], $v['num']);
			}
			//装饰物
			else if ($v['type'] == 2) {
                $decors[] = array($v['id'], $v['num']);
			}
		}//end for

		if ($items || $decors) {
            $awardRot = new Hapyfish2_Magic_Bll_Award();
    	    if ($items) {
    			$awardRot->setItemList($items);
    		}
            if ($decors) {
    			$awardRot->setDecorList($decors);
    		}
    		$awardRot->sendOne($uid);
		}

        return 1;
	}

	/**
	 * generate random by key=>odds
	 *
	 * @param array $aryKeys
	 * @return integer
	 */
	private static function _randomKeyForOdds($aryKeys)
	{
		$tot = 0;
		$aryTmp = array();
		foreach ($aryKeys as $key => $odd) {
			$tot += $odd;
			$aryTmp[$key] = $tot;
		}
		$rnd = mt_rand(1,$tot);

		foreach ($aryTmp as $key=>$value) {
			if ($rnd <= $value) {
				return $key;
			}
		}
	}
}