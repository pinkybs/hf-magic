<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
// Custom ArrayIterator (inherits from ArrayIterator)
class MyArrayIterator extends ArrayIterator {
    // custom implementation
    public function result_array()
    {
    	return $this->getArrayCopy();
    }
}