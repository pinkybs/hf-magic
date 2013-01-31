<?php

class Hapyfish2_Magic_Event_Abstract
{

    /**
     * event id
     * @var int
     */
    protected $_eventId;

    /**
     * event name
     * @var string
     */
    protected $_eventName;

    /**
     * start time
     * @var int
     */
    protected $_startTime;

    /**
     * end time
     * @var int
     */
    protected $_endTime;

    /**
     * __construct() -
     *
     * @param $eid, $eName, $start, $end
     * @return void
     */
    public function __construct($eid, $eName, $start, $end)
    {
        $this->_eventId = $eid;
        $this->_eventName = $eName;
        $this->_startTime = $start;
        $this->_endTime = $end;
        $this->_init();
    }

    /**
     * get event id
     *
     * @return int
     */
    public function getEventId()
    {
        return $this->_eventId;
    }

    public function setEventId($id)
    {
        $this->_eventId = $id;
    }

    /**
     * get event name
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->_eventName;
    }

    public function setEventName($name)
    {
        $this->_eventName = $name;
    }

    /**
     * get event start time
     *
     * @return int
     */
    public function getEventStartTime()
    {
        return $this->_startTime;
    }

    public function setEventStartTime($start)
    {
        $this->_startTime = $start;
    }

	/**
     * get event end time
     *
     * @return int
     */
    public function getEventEndTime()
    {
        return $this->_endTime;
    }

    public function setEventEndTime($end)
    {
        $this->_endTime = $end;
    }

    protected function _init()
    {

    }

}