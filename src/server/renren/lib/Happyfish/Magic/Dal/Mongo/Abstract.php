<?php

class Happyfish_Magic_Dal_Mongo_Abstract
{
    protected $_mg;

    public function __construct($mongo = null)
    {
        if (is_null($mongo)) {
            $mongo = getMongo();
        }

        $dbName = MONGODB_NAME;
        $this->_mg = $mongo->$dbName;        
    }
    
    public function getMongo()
    {
        return $this->_mg;
    }
}