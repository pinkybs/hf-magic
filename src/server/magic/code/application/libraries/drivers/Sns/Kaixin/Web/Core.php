<?

require_once 'Kaixin/Web/Abstract.php';

class Kaixin_Web_Core extends Kaixin_Web_Abstract
{
    public function balance($vuid)
    {
        $params = array('uid' => $vuid);
    	return $this->call_method('balance', $params);
    }
    
    public function spend($params)
    {
    	return $this->call_method('spend', $params);
    }
}