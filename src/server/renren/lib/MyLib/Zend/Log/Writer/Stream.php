<?php

/** @see Zend_Log_Writer_Abstract */
require_once 'Zend/Log/Writer/Abstract.php';

/** @see Zend_Log_Formatter_Simple */
require_once 'Zend/Log/Formatter/Simple.php';

/**
 * Implement log writer stream
 * 
 * @package    MyLib_Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2008/07/18     Hulj
 */
class MyLib_Zend_Log_Writer_Stream extends Zend_Log_Writer_Abstract
{
    /**
     * Holds the PHP stream to log to.
     * @var null|stream
     */
    protected $_stream = null;
    
    /**
     * array of log events
     */
    protected $_events = array();
    
    protected $_length = 10;

    /**
     * Class Constructor
     *
     * @param  streamOrUrl     Stream or URL to open as a stream
     * @param  mode            Mode, only applicable if a URL is given
     * @param  length Length, max count of events
     */
    public function __construct($streamOrUrl, $mode = 'a', $length = 10)
    {
        if (is_resource($streamOrUrl)) {
            if (get_resource_type($streamOrUrl) != 'stream') {
                throw new Zend_Log_Exception('Resource is not a stream');
            }
            
            if ($mode != 'a') {
                throw new Zend_Log_Exception('Mode cannot be changed on existing streams');
            }
            
            $this->_stream = $streamOrUrl;
        }
        else {
            if (!$this->_stream = @fopen($streamOrUrl, $mode, false)) {
                $msg = "\"$streamOrUrl\" cannot be opened with mode \"$mode\"";
                throw new Zend_Log_Exception($msg);
            }
        }
        
        $this->_formatter = new Zend_Log_Formatter_Simple();
        
        if ($length >= 1) {
            $this->_length = $length;
        }
    }

    /**
     * Close the stream resource.
     *
     * @return void
     */
    public function shutdown()
    {
        $this->_output();
        
        if (is_resource($this->_stream)) {
            fclose($this->_stream);
        }
    }

    /**
     * Write a message to the log, can cached
     *
     * @param  array  $event  event data
     * @return void
     */
    protected function _write($event)
    {
        $line = $this->_formatter->format($event);
        
        if (count($this->_events) === $this->_length) {
            if (false === $this->_output()) {
                throw new Zend_Log_Exception('Unable to write to stream');
            }
            
            $this->_events = array();
        }
        
        $this->_events[] = $line;
    }

    /**
     * Output messages to the log
     *
     * @return boolean
     */
    protected function _output()
    {
        if (empty($this->_events)) {
            return true;
        }
        
        $result = false;
        
        if (@flock($this->_stream, LOCK_EX)) {
            $result = @fwrite($this->_stream, implode('', $this->_events));
            @flock($this->_stream, LOCK_UN);
        }
        
        return $result;
    }

}
