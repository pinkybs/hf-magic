<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Cache driver interface.
 *
 * $Id: Cache.php 3769 2008-12-15 00:48:56Z zombor $
 *
 * @package    Cache
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
abstract Class Cache_Driver {

	/**
	 * Set a cache item.
	 */
	public function set($id, $data, $tags, $lifetime){}

	/**
	 * Find all of the cache ids for a given tag.
	 */
	public function find($tag){}

	/**
	 * Get a cache item.
	 * Return NULL if the cache item is not found.
	 */
	public function get($id){}
	
	public function getMulti($keys){}
	public function setMulti($data, $tags, $lifetime){}

	/**
	 * Delete cache items by id or tag.
	 */
	public function delete($id, $tag = FALSE){}

	/**
	 * Deletes all expired cache items.
	 */
	public function delete_expired(){}
	
	public function getCas($id, &$token)
	{
		return $this->get($id);
	}
	
	public function cas($cas, $id, $data, $tags, $lifetime)
	{
		return $this->set($id, $data, $tags, $lifetime);
	}
	
	public function getResultCode()
	{
		return 0;
	}

} // End Cache Driver