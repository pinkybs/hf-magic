<?php

class Buyitem_Log_Model extends Model_Core {

    protected $table_pre = 'role_buyitem_log';

    //插入一条用户消费记录,三个参数$itemid=物品参数,$itemnum=物品数量,$itemprice=物品单价
    public function insertBuyItem($itemid, $itemnum, $itemprice) {
        if ($itemprice) {
            $arr = array();
            $arr ['rid'] = Role::getOwnRoleId();
            $role = Role::create($arr ['rid']);
            $arr ['rlevel'] = $role->get('level');
            $arr ['itemid'] = $itemid;
            $arr ['num'] = $itemnum;
            $arr ['price'] = $itemprice;
            $arr ['total'] = $itemnum * $itemprice;
            return $this->insert($arr);
        }
        return false;
    }

}