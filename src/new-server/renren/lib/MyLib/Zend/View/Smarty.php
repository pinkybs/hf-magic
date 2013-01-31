<?php

/** Zend_View_Interface */
require_once 'Zend/View/Interface.php';

/** Smarty */
require_once 'Smarty/Smarty.class.php';

/**
 * Implement view order by smarty
 *
 * @package    MyLib_Zend_View
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2008/07/18     Hulj
 */
class MyLib_Zend_View_Smarty implements Zend_View_Interface
{
    /**
     * Smarty object
     * @var Smarty
     */
    protected $_smarty;
    
    /**
     * Constructor.
     *
     * @param array $extraParams
     * @return void
     */
    public function __construct($extraParams = array())
    {
        $this->_smarty = new Smarty;
        
        foreach ($extraParams as $key => $value) {
            $this->_smarty->$key = $value;
        }
    }
    
    /**
     * Return the template engine object.
     *
     * @return Smarty
     */
    public function getEngine()
    {
        return $this->_smarty;
    }
    
    /**
     * Sets the base directory path to templates.
     *
     * @param   string  $path
     * @return  MyLib_Zend_View_Smarty
     */
    public function setBasePath($path, $classPrefix = 'Zend_View')
    {
        $path = rtrim($path, '\\/');

        $this->setTemplateDir($path . DIRECTORY_SEPARATOR . 'scripts');

        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        if ($module == null) {
            $module = $front->getDefaultModule();
        }
        
        $this->setCompileDir(SMARTY_TEMPLATES_C . DIRECTORY_SEPARATOR . $module);
        
        return $this;
    }
    
    /**
     * Alias of setBasePath() method.
     *
     * @param   string  $path
     * @return  MyLib_Zend_View_Smarty
     */
    public function addBasePath($path, $classPrefix = 'Zend_View')
    {
        $this->setBasePath($path);
        return $this;
    }
    
    /**
     * Sets the directory path to templates.
     *
     * @param   string  $path
     * @return  MyLib_Zend_View_Smarty
     */
    public function setTemplateDir($path)
    {
        if (is_dir($path) && is_readable($path)) {
            $this->_smarty->template_dir = $path;
            return $this;
        }
        
        throw new Exception('Invalid path provided');
    }
    
    /**
     * Sets the directory path to compiled templates.
     *
     * @param   string  $path
     * @return  MyLib_Zend_View_Smarty
     */
    public function setCompileDir($path)
    {
        if (is_dir($path) && is_writable($path)) {
            $this->_smarty->compile_dir = $path;
            return $this;
        }
        
        throw new Exception('Invalid path provided');
    }
    
    /**
     * Alias of setTemplateDir() method.
     *
     * @param   string  $path
     * @return  MyLib_Zend_View_Smarty
     */
    public function setScriptPath($path)
    {
        $this->setTemplateDir($path);
        return $this;
    }
    
    /**
     * Returns an array of the directory path to templates.
     *
     * @return  array
     */
    public function getScriptPaths()
    {
        return array($this->_smarty->template_dir);
    }
    
    /**
     * Assign a variable to the template
     *
     * @param string $key The variable name.
     * @param mixed $val The variable value.
     * @return void
     */
    public function __set($key, $val)
    {
        $this->_smarty->assign($key, $val);
    }
    
    /**
     * Retrieve an assigned variable
     *
     * @param string $key The variable name.
     * @return mixed The variable value.
     */
    public function __get($key)
    {
        return $this->_smarty->get_template_vars($key);
    }
    
    /**
     * Allows testing with empty() and isset() to work
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return (null !== $this->_smarty->get_template_vars($key));
    }
    
    /**
     * Allows unset() on object properties to work
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        $this->_smarty->clear_assign($key);
    }
    
    /**
     * Assign variables to the template
     *
     * Allows setting a specific key to the specified value, OR passing an array
     * of key => value pairs to set en masse.
     *
     * @see __set()
     * @param string|array $spec The assignment strategy to use (key or array of key
     * => value pairs)
     * @param mixed $value (Optional) If assigning a named variable, use this
     * as the value.
     * @return void
     */
    public function assign($spec, $value = null)
    {
        if (is_array($spec)) {
            $this->_smarty->assign($spec);
            return;
        }
    
        $this->_smarty->assign($spec, $value);
    }
    
    /**
     * Clear all assigned variables
     *
     * Clears all variables assigned to Zend_View either via {@link assign()} or
     * property overloading ({@link __get()}/{@link __set()}).
     *
     * @return void
     */
    public function clearVars()
    {
        $this->_smarty->clear_all_assign();
    }
    
    /**
     * Processes a template and returns the output.
     *
     * @param string $name The template to process.
     * @return string The output.
     */
    public function render($name)
    {
        $dir = $this->_smarty->template_dir;
        
        if (!is_readable($dir . DIRECTORY_SEPARATOR . $name)) {
            require_once 'Zend/View/Exception.php';
            $message = "script '$name' not found in path (" . $dir . ")";
            throw new Zend_View_Exception($message, $this);
        }
        
        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        if ($module == null) {
            $module = $front->getDefaultModule();
        }
        
        if (Zend_Registry::isRegistered('ua')) {
            $ua = Zend_Registry::get('ua');
            
            //docomo
            if ($ua == 1) {
                header('Content-type: application/xhtml+xml');
                $content = $this->_smarty->fetch($name);
                return mb_convert_encoding($content, 'SJIS-win', 'UTF-8');
            }
        }
 
        return $this->_smarty->fetch($name);
    }
}