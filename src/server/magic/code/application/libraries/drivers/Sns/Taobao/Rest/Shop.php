<?

require_once 'Taobao/Rest/Abstract.php';

class Taobao_Rest_Shop extends Taobao_Rest_Abstract
{    
    public function shop_get($fields, $nick)
    {
        $params = array(
            'fields' => $fields,
            'nick' => $nick
        );
        return $this->call_method('taobao.shop.get', $params);
    }
    
}