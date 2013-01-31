<?php

/** @see Zend_Controller_Router_Route_Interface */
require_once 'Zend/Controller/Router/Route/Interface.php';

/**
 * StaticRoute is used for managing static URIs.
 * It's a lot faster compared to the standard Route implementation.
 *
 * @package    MyLib_Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2008/07/18     Hulj
 */
class MyLib_Zend_Controller_Router_Route_Static implements Zend_Controller_Router_Route_Interface
{
    
    protected $_route = array();
    protected $_defaults = array();
    protected $_actionKey = 'action';
    const ACTION_DELIMITER = ';';

    /**
     * Instantiates route based on passed Zend_Config structure
     */
    public static function getInstance(Zend_Config $config)
    {
        $defs = ($config->defaults instanceof Zend_Config) ? $config->defaults->toArray() : array();
        return new self($config->route, $defs);
    }

    /**
     * Prepares the route for mapping.
     *
     * @param string Map used to match with later submitted URL path
     * @param array Defaults for map variables with keys as variable names
     */
    public function __construct($route, $defaults = array())
    {
        $this->_route = explode(self::ACTION_DELIMITER, trim($route, '/'));
        $this->_defaults = (array) $defaults;
    }

    /**
     * Matches a user submitted path with a previously defined route.
     * Assigns and returns an array of defaults on a successful match.
     *
     * @param string Path used to match against this routing map
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($path)
    {
        $path = trim($path, '/');
        foreach ($this->_route as $route) {
            if ($path == $route) {
                if (!isset($this->_defaults[$this->_actionKey])) {
                    $this->_defaults[$this->_actionKey] = $route;
                }
                return $this->_defaults;
            }
        }
        
        return false;
    }

    /**
     * Assembles a URL path defined by this route
     *
     * @param array An array of variable and value pairs used as parameters
     * @return string Route path with user submitted parameters
     */
    public function assemble($data = array())
    {
        return $this->_route;
    }

    /**
     * Return a single parameter of route's defaults
     *
     * @param name Array key of the parameter
     * @return string Previously set default
     */
    public function getDefault($name)
    {
        if (isset($this->_defaults[$name])) {
            return $this->_defaults[$name];
        }
        return null;
    }

    /**
     * Return an array of defaults
     *
     * @return array Route defaults
     */
    public function getDefaults()
    {
        return $this->_defaults;
    }

}
