<?

require_once 'Qzone/Rest/Abstract.php';

class Qzone_Rest_Xiaoyou extends Qzone_Rest_Abstract
{
    public function user_getProfile()
    {
        return $this->call_method('xyoapp/xyoapp_get_userinfo', array());
    }
    
    public function user_isAppUser()
    {
        return $this->call_method('xyoapp/xyoapp_get_issetuped', array());
    }
    
    public function user_getAppFriendIds()
    {
        $params = array(
            'infoed' => '0',
        	'apped'	 => '1',
        	'page'   => '0'
        );
        
    	return $this->call_method('xyoapp/xyoapp_get_relationinfo', $params);
    }
    
    public function friend_isFriend($fopenid)
    {
        $params = array(
            'fopenid' => $fopenid
        );
        
    	return $this->call_method('xyoapp/xyoapp_get_isrelation', $params);    	
    }
    
    public function pay_isvip()
    {
    	return $this->call_method('xyoapp/xyoapp_pay_showvip', array());
    }
    
    public function pay_getBalance($needvip = false)
    {
        $params = array();
        if ($needvip) {
        	$params ['needvip'] = '1';
        }
        
    	return $this->call_method('xyoapp/xyoapp_pay_get', $params);
    }
    
    /**
     * pay, user can confirm or cancel
     *
     * @param array $items (array('xxx' => 3, 'yyy' => 5))
     * @param int $amt
     */
    public function pay_pay($items, $amt)
    {
    	$payitem = array();
    	foreach ($items as $key => $val) {
            $payitem[] = $key.'*'.$val;
        }
        
        $params = array(
        	'payitem' => implode('&amp;', $payitem),
        	'amt' => $amt
        );
		
        return $this->call_method('xyoapp/xyoapp_pay_pay', $params);
    }
    
    public function pay_confirm($billno)
    {
    	$params = array(
    		'billno' => $billno
    	);
    	
    	return $this->call_method('xyoapp/xyoapp_pay_confirm', $params);
    }
    
    public function pay_cancel($billno)
    {
    	$params = array(
    		'billno' => $billno
    	);
    	
    	return $this->call_method('xyoapp/xyoapp_pay_cancel', $params);    	
    }

}