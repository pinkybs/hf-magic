<?php

class Hapyfish2_Island_Stat_Bll_DaycLoadTm
{

    public static function calcDayData($dt, $dir)
    {
        ///home/admin/logs/weibo/stat-data/cLoadTm/
        //$strDate = date('Ymd', $dt);
        $strDate = $dt;
        $fileName = $dir . $strDate . '/all-cLoadTm-' . $strDate . '.log';
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'stat_DaycLoadTm');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'stat_DaycLoadTm');
                return false;
            }
            $lines = explode("\n", $content);
            $cntTotal = $cntBreak1_2 = $cntBreak2_3 = 0;
            $aryTm1_2 = $aryTm2_3 = $aryTm3_4 = array();
            $pie_tm1_2_fast = $pie_tm1_2_normal1 = $pie_tm1_2_normal2 = $pie_tm1_2_slow = $pie_tm1_2_bad = 0;

            $cntTotal_new = $cntBreak1_2_new = $cntBreak2_3_new = 0;
            $aryTm1_2_new = $aryTm2_3_new = $aryTm3_4_new = array();
            $pie_tm1_2_fast_new = $pie_tm1_2_normal1_new = $pie_tm1_2_normal2_new = $pie_tm1_2_slow_new = $pie_tm1_2_bad_new = 0;

            foreach ($lines as $line) {
                if (empty($line)) {
    				continue;
    			}

                $aryLine = explode("\t", $line);
                $tm1 = $aryLine[3];
                $tm2 = $aryLine[4];
                $tm3 = $aryLine[5];
                $tm4 = $aryLine[6];
                $isNew = $aryLine[7];

                $cntTotal += 1;
                if ($isNew == 1) {
    			    $cntTotal_new += 1;
    			}

                if ($tm2 == 0) {
                    $cntBreak1_2 += 1;
                    if ($isNew == 1) {
        			    $cntBreak1_2_new += 1;
        			}
                    continue;
                }

                //噪点数据
                if ($tm2<$tm1 || $tm2-$tm1 > 30000) {
                    info_log('1_2:'.$line, 'stat_DaycLoadTm_trash_'.$strDate);
                    continue;
                }

                $aryTm1_2[] = ($tm2 - $tm1)/1000;
                if ($isNew == 1) {
    			    $aryTm1_2_new[] = ($tm2 - $tm1)/1000;
    			}

                //数据分段细化
                if ($tm2-$tm1<=1000) {
                    $pie_tm1_2_fast += 1;
                    if ($isNew == 1) {
        			    $pie_tm1_2_fast_new += 1;
        			}
                }
                else if ($tm2-$tm1<=3000) {
                    $pie_tm1_2_normal1 += 1;
                    if ($isNew == 1) {
        			    $pie_tm1_2_normal1_new += 1;
        			}
                }
                else if ($tm2-$tm1<=6000) {
                    $pie_tm1_2_normal2 += 1;
                    if ($isNew == 1) {
        			    $pie_tm1_2_normal2_new += 1;
        			}
                }
                else if ($tm2-$tm1<=10000) {
                    $pie_tm1_2_slow += 1;
                    if ($isNew == 1) {
        			    $pie_tm1_2_slow_new += 1;
        			}
                }
                else {
                    $pie_tm1_2_bad += 1;
                    if ($isNew == 1) {
        			    $pie_tm1_2_bad_new += 1;
        			}
                }

                if ($tm3 == 0) {
                    $cntBreak2_3 += 1;
                    if ($isNew == 1) {
        			    $cntBreak2_3_new += 1;
        			}
                    continue;
                }

                //噪点数据
                if ($tm3<$tm2 || $tm3-$tm2 > 600000) {
                    info_log('2_3:'.$line, 'stat_DaycLoadTm_trash_'.$strDate);
                    continue;
                }
                $aryTm2_3[] = ($tm3 - $tm2)/1000;
                if ($isNew == 1) {
    			    $aryTm2_3_new[] = ($tm3 - $tm2)/1000;
    			}

                if ($tm4 == 0) {
                    continue;
                }

                //噪点数据
                if ($tm4<$tm3 || $tm4-$tm3 > 86400000) {
                    info_log('3_4:'.$line, 'stat_DaycLoadTm_trash_'.$strDate);
                    continue;
                }
                if ($tm3!=0) {
                    $aryTm3_4[] = ($tm4 - $tm3)/1000;
                    if ($isNew == 1) {
        			    $aryTm3_4_new[] = ($tm4 - $tm3)/1000;
        			}
                }
            }

            $sumTm1_2 = array_sum($aryTm1_2);
            $sumTm2_3 = array_sum($aryTm2_3);
            $sumTm3_4 = array_sum($aryTm3_4);

            $sumTm1_2_new = array_sum($aryTm1_2_new);
            $sumTm2_3_new = array_sum($aryTm2_3_new);
            $sumTm3_4_new = array_sum($aryTm3_4_new);

            //nocookie
            $noFlashData = self::_getNoFlash($dt);
            $noCookieData = self::_getNoCookie($dt);

            $dal = Hapyfish2_Island_Stat_Dal_DaycLoadTm::getDefaultInstance();
            $row = $dal->getRow($strDate);
            if (!empty($row)) {
                $dal->delete($strDate);
            }
            $info = array();
            $info['log_time'] = (int)$strDate;
            $info['count_total'] = $cntTotal;
            $info['count_tm1_2_break'] = $cntBreak1_2;
            $info['count_tm2_3_break'] = $cntBreak2_3;
            $info['tm1_2'] = $sumTm1_2.'|'.count($aryTm1_2);
            $info['tm2_3'] = $sumTm2_3.'|'.count($aryTm2_3);
            $info['tm3_4'] = $sumTm3_4.'|'.count($aryTm3_4);
            $info['pie_tm1_2_fast'] = $pie_tm1_2_fast;
            $info['pie_tm1_2_normal1'] = $pie_tm1_2_normal1;
            $info['pie_tm1_2_normal2'] = $pie_tm1_2_normal2;
            $info['pie_tm1_2_slow'] = $pie_tm1_2_slow;
            $info['pie_tm1_2_bad'] = $pie_tm1_2_bad;

            $info['count_total_new'] = $cntTotal_new;
            $info['count_tm1_2_break_new'] = $cntBreak1_2_new;
            $info['count_tm2_3_break_new'] = $cntBreak2_3_new;
            $info['tm1_2_new'] = $sumTm1_2_new.'|'.count($aryTm1_2_new);
            $info['tm2_3_new'] = $sumTm2_3_new.'|'.count($aryTm2_3_new);
            $info['tm3_4_new'] = $sumTm3_4_new.'|'.count($aryTm3_4_new);
            $info['pie_tm1_2_fast_new'] = $pie_tm1_2_fast_new;
            $info['pie_tm1_2_normal1_new'] = $pie_tm1_2_normal1_new;
            $info['pie_tm1_2_normal2_new'] = $pie_tm1_2_normal2_new;
            $info['pie_tm1_2_slow_new'] = $pie_tm1_2_slow_new;
            $info['pie_tm1_2_bad_new'] = $pie_tm1_2_bad_new;

            if ($noFlashData) {
                $info['noflash_browser'] = json_encode($noFlashData);
            }
            if ($noCookieData) {
                $info['nocookie_browser'] = json_encode($noCookieData);
            }

            $dal->insert($info);

            /*info_log($cntTotal.'|'.$cntBreak1_2.'|'.$cntBreak2_3
                .','.$sumTm1_2.'|'.count($aryTm1_2)
                .','.$sumTm2_3.'|'.count($aryTm2_3)
                .','.$sumTm3_4.'|'.count($aryTm3_4), 'stat_DaycLoadTm');*/

        }
        catch (Exception $e) {
            info_log($e->getMessage(), 'stat_DaycLoadTm');
            return false;
        }
        return true;
    }

    private static function _getNoCookie($dt)
    {
        //$strDate = date('Ymd', $dt);
        $retData = array();
        $strDate = $dt;
        $fileName = '/home/admin/logs/weibo/stat-data/nocookie/' . $strDate . '/all-nocookie-' . $strDate . '.log';
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'stat_DaycLoadTm');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'stat_DaycLoadTm');
                return false;
            }
            $lines = explode("\n", $content);

            $retData['IE6'] = 0;
            $retData['IE7'] = 0;
            $retData['IE8'] = 0;
            $retData['IE9'] = 0;
            $retData['FF'] = 0;
            $retData['Chrome'] = 0;
            $retData['Safari'] = 0;
            $retData['Opera'] = 0;
            $retData['Other'] = 0;
            $retData['total'] = 0;
            $retData['IE6_new'] = 0;
            $retData['IE7_new'] = 0;
            $retData['IE8_new'] = 0;
            $retData['IE9_new'] = 0;
            $retData['FF_new'] = 0;
            $retData['Chrome_new'] = 0;
            $retData['Safari_new'] = 0;
            $retData['Opera_new'] = 0;
            $retData['Other_new'] = 0;
            $retData['total_new'] = 0;
            foreach ($lines as $line) {
                if (empty($line)) {
    				continue;
    			}

                $aryLine = explode("\t", $line);
                $uid = $aryLine[2];
                $browser = $aryLine[3];
                $isNew = $aryLine[4];

                //噪点数据
                if ($uid) {
                    continue;
                }

                $retData['total'] += 1;
                if ($isNew == 1) {
                    $retData['total_new'] += 1;
                }
                if (strpos($browser, 'Internet Explorer 6') !== false) {
                    $retData['IE6'] += 1;
                    if ($isNew == 1) {
                        $retData['IE6_new'] += 1;
                    }
                }
                else if (strpos($browser, 'Internet Explorer 7') !== false) {
                    $retData['IE7'] += 1;
                    if ($isNew == 1) {
                        $retData['IE7_new'] += 1;
                    }
                }
                else if (strpos($browser, 'Internet Explorer 8') !== false) {
                    $retData['IE8'] += 1;
                    if ($isNew == 1) {
                        $retData['IE8_new'] += 1;
                    }
                }
                else if (strpos($browser, 'Internet Explorer 9') !== false) {
                    $retData['IE9'] += 1;
                    if ($isNew == 1) {
                        $retData['IE9_new'] += 1;
                    }
                }
                else if (strpos($browser, 'Firefox') !== false) {
                    $retData['FF'] += 1;
                    if ($isNew == 1) {
                        $retData['FF_new'] += 1;
                    }
                }
                else if (strpos($browser, 'Chrome') !== false) {
                    $retData['Chrome'] += 1;
                    if ($isNew == 1) {
                        $retData['Chrome_new'] += 1;
                    }
                }
                else if (strpos($browser, 'Opera') !== false) {
                    $retData['Opera'] += 1;
                    if ($isNew == 1) {
                        $retData['Opera_new'] += 1;
                    }
                }
                else if (strpos($browser, 'Safari') !== false) {
                    $retData['Safari'] += 1;
                    if ($isNew == 1) {
                        $retData['Safari_new'] += 1;
                    }
                }
                else {
                    $retData['Other'] += 1;
                    if ($isNew == 1) {
                        $retData['Other_new'] += 1;
                    }
                }
            }
        }
        catch (Exception $e) {
            info_log('_getNoCookie:'.$e->getMessage(), 'stat_DaycLoadTm');
            return false;
        }
        return $retData;
    }

    private static function _getNoFlash($dt)
    {
        //$strDate = date('Ymd', $dt);
        $retData = array();
        $strDate = $dt;
        $fileName = '/home/admin/logs/weibo/stat-data/noflash/' . $strDate . '/all-noflash-' . $strDate . '.log';
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'stat_DaycLoadTm');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'stat_DaycLoadTm');
                return false;
            }
            $lines = explode("\n", $content);

            $retData['IE6'] = 0;
            $retData['IE7'] = 0;
            $retData['IE8'] = 0;
            $retData['IE9'] = 0;
            $retData['FF'] = 0;
            $retData['Chrome'] = 0;
            $retData['Safari'] = 0;
            $retData['Opera'] = 0;
            $retData['Other'] = 0;
            $retData['total'] = 0;
            $retData['IE6_new'] = 0;
            $retData['IE7_new'] = 0;
            $retData['IE8_new'] = 0;
            $retData['IE9_new'] = 0;
            $retData['FF_new'] = 0;
            $retData['Chrome_new'] = 0;
            $retData['Safari_new'] = 0;
            $retData['Opera_new'] = 0;
            $retData['Other_new'] = 0;
            $retData['total_new'] = 0;
            foreach ($lines as $line) {
                if (empty($line)) {
    				continue;
    			}

                $aryLine = explode("\t", $line);
                $uid = $aryLine[2];
                $browser = $aryLine[3];
                $isNew = $aryLine[4];

                $retData['total'] += 1;
                if ($isNew == 1) {
                    $retData['total_new'] += 1;
                }

                if (strpos($browser, 'Internet Explorer 6') !== false) {
                    $retData['IE6'] += 1;
                    if ($isNew == 1) {
                        $retData['IE6_new'] += 1;
                    }
                }
                else if (strpos($browser, 'Internet Explorer 7') !== false) {
                    $retData['IE7'] += 1;
                    if ($isNew == 1) {
                        $retData['IE7_new'] += 1;
                    }
                }
                else if (strpos($browser, 'Internet Explorer 8') !== false) {
                    $retData['IE8'] += 1;
                    if ($isNew == 1) {
                        $retData['IE8_new'] += 1;
                    }
                }
                else if (strpos($browser, 'Internet Explorer 9') !== false) {
                    $retData['IE9'] += 1;
                    if ($isNew == 1) {
                        $retData['IE9_new'] += 1;
                    }
                }
                else if (strpos($browser, 'Firefox') !== false) {
                    $retData['FF'] += 1;
                    if ($isNew == 1) {
                        $retData['FF_new'] += 1;
                    }
                }
                else if (strpos($browser, 'Chrome') !== false) {
                    $retData['Chrome'] += 1;
                    if ($isNew == 1) {
                        $retData['Chrome_new'] += 1;
                    }
                }
                else if (strpos($browser, 'Opera') !== false) {
                    $retData['Opera'] += 1;
                    if ($isNew == 1) {
                        $retData['Opera_new'] += 1;
                    }
                }
                else if (strpos($browser, 'Safari') !== false) {
                    $retData['Safari'] += 1;
                    if ($isNew == 1) {
                        $retData['Safari_new'] += 1;
                    }
                }
                else {
                    $retData['Other'] += 1;
                    if ($isNew == 1) {
                        $retData['Other_new'] += 1;
                    }
                }
            }
        }
        catch (Exception $e) {
            info_log('_getNoFlash:'.$e->getMessage(), 'stat_DaycLoadTm');
            return false;
        }
        return $retData;
    }


    public static function getDay($day)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Island_Stat_Dal_DaycLoadTm::getDefaultInstance();
			$data = $dal->getRow($day);
		} catch (Exception $e) {

		}

		return $data;
	}

    public static function listData($day1, $day2)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Island_Stat_Dal_DaycLoadTm::getDefaultInstance();
			$data = $dal->listData($day1, $day2);
		} catch (Exception $e) {

		}

		return $data;
	}
}