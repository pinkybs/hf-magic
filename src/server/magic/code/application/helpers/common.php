<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class common {
	public static function redirect_json($msg, $params = array())
	{
		$re_array = array('message' => $msg) + $params;
		
		echo json_encode($re_array);
		die();
	}
	
	public static function redirect_error_json($msg, $params = array())
	{
		$re_array = array('message' => $msg, 'error' => 1) + $params;
		
		echo json_encode($re_array);
		die();
	}
	
	public static function setVar($key, $vl)
	{
		$var = &PEAR::getStaticProperty('_APP', $key);
		$var = $vl;
	}
	
	public static function getVar($key)
	{
		$var = PEAR::getStaticProperty('_APP', $key);
		return $var;
	}
	
	/**
	 * 完美兼容一维数组和多维迭代器
	 * @param String $name
	 * @param Array $data
	 */
	public static function transform($name, $data)
	{
		$trans = Kohana::config('transform.'.$name);
		
		$output = array();

		$i = 0;
		foreach ($data as $k => $v) {
			//一维数组
			if (!is_array($v)) {
				foreach ($trans as $key => $vl) {
					if (isset($data[$key])) {
						if (is_array($vl)) {
							$output[$vl[0]] = eval('return '.$vl[1].'("'.$data[$key].'");');
						} else {
							if (is_numeric($data[$key])) {
								$output[$vl] = (int)$data[$key];
							} else {
								$output[$vl] = $data[$key];
							}
						}
					} else {
						$output[$vl] = '';
					}
				}
				break;
			} else {
			//二维数组
				foreach ($trans as $key => $vl) {
					//TODO 低效率
					$key_new = explode('|', $key, 2);
					if (is_numeric($key_new[0])) {
						$key = $key_new[1];
					}
					if (is_array($v[0])) {
						if (isset($v[0][$key])) {
							if (is_array($vl)) {
								$output[$i][$vl[0]] = eval('return '.$vl[1].'("'.$v[0][$key].'");');
							} else {
								$output[$i][$vl] = $v[0][$key];
							}
						} else {
							if (is_array($vl)) {
								$output[$i][$vl[0]] = '';
							} else {
								$output[$i][$vl] = '';
							}
						}
					} else {
						if (isset($v[$key])) {
							if (is_array($vl)) {
								$output[$i][$vl[0]] = eval('return '.$vl[1].'("'.$v[$key].'");');
							} else {
								$output[$i][$vl] = $v[$key];
							}
						} else {
							if (is_array($vl)) {
								$output[$i][$vl[0]] = '';
							} else {
								$output[$i][$vl] = '';
							}
						}
					}
				}
				$i++;
			}
		}
		
		return array($name => $output);
	}
	
	/** 
	 * 获取分表后的真实表名
	 */
	public static function getTableName($table_pre, $id)
	{
		$table_cut = Kohana::config('table.cut');
		
		if ($table_cut[$table_pre] === 0) {
			$table_name = $table_pre;
		} else {
			$table_name = $table_pre.'_'.floor($id/Kohana::config('base.cut_database_num')) % $table_cut[$table_pre];
		}

		return $table_name;
	}
	
	/**
	 * 返回给客户端的数据格式 key=>1,num=>2,name=>中文
	 * @param unknown_type $name
	 * @param unknown_type $data
	 */
	public static function prize($name, $data = array())
	{
		$prize = self::getVar('prize');
		
		if ($name == 'item') {
			//取出
			$item_data = Item::getItemData($data['key']);
			$data['name'] = $item_data['name'];
		}
		
		if (!isset($prize[$name])) {
			$prize[$name] = array();
			$prize[$name][$data['key']] = $data;
		} else {
			if (isset($prize[$name][$data['key']])) {
				$data['num'] += $prize[$name][$data['key']]['num'];
				$prize[$name][$data['key']] = $data;
			}
		}
		self::setVar('prize', $prize);
	}
	
	public static function retPrize()
	{
		$ret = self::getVar('prize_msg');
		$prize = self::getVar('prize');
		foreach ($prize as $type => $type_data) {
			$ret .= "  ".Kohana::lang('base.'.$type).":";
			foreach ($type_data as $vl) {
				if (!isset($vl['name'])) {
					//语言包
					$vl['name'] = Kohana::lang('base.'.$vl['key']);
				}
				
				$ret .= $vl['name'].' '.$vl['num'].', ';
			}
		}
		
		if ($ret != '') {
			$ret = Kohana::lang('base.get').$ret;
		}
		
		self::setVar('prize_msg', $ret);
		return $ret;
	}
	
	public static function pay_log($msg)
	{
		error_log(date("Y-m-d H:i:s").":\n  $msg\n", 3, APPPATH.'paylogs/'.date("Y-m-d").".log");
	}
	
	public static function adpay_log($msg)
	{
		error_log(date("Y-m-d H:i:s").":\n  $msg\n", 3, APPPATH.'adpaylogs/'.date("Y-m-d").".log");
	}
	
	/**
	 * 生成随机
	 * @param unknown_type $rand_data
	 * @param unknown_type $rand_key
	 */
	public static function random($rand_data, $rand_key = 'rand_pct')
	{
		$random_data = array();
		
		if (empty($rand_data)) {
			return array();
		}
		$sum = 0;
		foreach ($rand_data as $data) {
			$sum += $data[$rand_key];
		}
	    foreach ( $rand_data as $data ){
	        $rand_num = mt_rand(1, $sum);
	        if( $rand_num <=  $data[$rand_key]){
	            $result = $data;
	            break;
	        }else{
	            $sum -= $data[$rand_key];
	        }
    	}
		return $result;
	}

	/**
	 * 生成随机
	 * @param unknown_type $rand_data
	 * @param unknown_type $rand_key
	 */
	public static function random_prize($rand_data)
	{
		$random_data = array();
		
		if (empty($rand_data)) {
			return array();
		}
	
		$sum = array_sum($rand_data);
	    foreach ( $rand_data as $key => $data ){
	        $rand_num = mt_rand(1, $sum);
	        if( $rand_num <=  $data){
	            $result = $key;
	            break;
	        }else{
	            $sum -= $data;
	        }
    	}
		return $result;
	}
	
	/**
	 * 生成随机旧
	 * @param unknown_type $rand_data
	 * @param unknown_type $rand_key
	 */
	public static function random_old($rand_data, $rand_key = 'rand_pct')
	{
		$random_data = array();
		
		if (empty($rand_data)) {
			return array();
		}
		
		foreach ($rand_data as $data) {
			for ($j = 1; $j <= $data[$rand_key]; $j++) {
				$random_data[] = $data;
			}
		}
		//var_dump($random_data);
		return arr::_array_rand($random_data);
	}
	
	/**
	 * 取出字符串中的数字
	 */
	public static function getNums($str)
	{
		$len = strlen($str);
		$num_str = '';
		for ( $i = 0; $i < $len; $i++ ) {
			if (is_numeric($str{$i})) {
				$num_str .= $str{$i};
			}
		}
		return $num_str;
	}
	
	/**
	 * 生成随机
	 * @param unknown_type $rand_data
	 * @param unknown_type $rand_key
	 */
	public static function random_prize_old($rand_data)
	{
		$random_data = array();
		
		if (empty($rand_data)) {
			return array();
		}
		
		foreach ($rand_data as $key => $vl) {
			for ($j = 1; $j <= $vl; $j++) {
				$random_data[] = $key;
			}
		}
		//var_dump($random_data);
		return arr::_array_rand($random_data);
	}
	
	public static function result($level_up = false, $status = 1, $content = '')
	{
		//取出此用户信息
		$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		
		$data = $role->getOriginData();
		
		$level_up = false;
		if ($data['level'] != $role->get('level')) {
			$level_up = true;
		}
		
		$house_level_up = false;
		if ($data['house_level'] != $role->get('house_level')) {
			$house_level_up = 1;
		}
		
		$result = array(
			'status' => $status,
			'content' => $content,
			'levelUP' => $level_up,
			'coin' => $role->get('coin') - $data['coin'],
			'gem' => $role->get('gmoney') - $data['gmoney'],
			'exp' => $role->get('exp') - $data['exp'],
			'mp' => $role->get('mp') - $data['mp'],
			'roomLevelUp' => $house_level_up,
		);
		
		return $result;
	}
	
	public static function result_error($content)
	{
		$result = array(
			'status' => -1,
			'content' => $content,
		);
		
		return $result;
	}
	
	/**
	 * 获取基础表数据
	 * @param unknown_type $key
	 */
	public static function basic($key)
	{
		$basic_model = new Basic_Model();
		
		$basic_result = $basic_model->getBasicBaseList();
		
		$value = '';
		foreach ($basic_result as $vl) {
			$vl = current($vl);
			if ($vl['key'] == $key) {
				$value = $vl['value'];
			}
		}
		return $value;
	}
	
	/**
	 * 哈希
	 * @param unknown_type $id
	 * @param unknown_type $num
	 * @param unknown_type $level
	 */
    public static function locate($id, $num, $level = 0)
    {
        $val = md5($id);
        $val = hexdec($val[0].$val[1].$val[2].$val[3]);
        return (floor($val / 65536 * $num) + $level) % $num;
    }
    
	public static function endtime()
	{
		Kohana::log('error', microtime(true)." end ".Router::$current_uri);
	}
}
?>