<?php

class Happyfish_Magic_Dal_Abstract
{
    /**
     * db config
     * @var array
     */
    protected $_config;
    
    /**
     * db read adapter
     * @var Zend_Db_Abstract
     */
    protected $_rdb;

    /**
     * db write adapter
     * @var Zend_Db_Abstract
     */
    protected $_wdb;
    
    /**
     * init the user's variables
     *
     * @param array $config ( config info )
     */
    public function __construct($config = null)
    {
        if (is_null($config)) {
            $config = getDBConfig();
        }
        
        $this->_config = $config;
        $this->_rdb = $config['readDB'];
        $this->_wdb = $config['writeDB'];
    }
    
    public function getReader()
    {
        return $this->_rdb;
    }
    
    public function getWriter()
    {
        return $this->_wdb;
    }
    
    public function getConfig()
    {
        return $this->_config;
    }
}