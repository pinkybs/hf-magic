<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Memcache-based Cache driver.
 *
 * $Id: Memcache.php 3769 2008-12-15 00:48:56Z zombor $
 *
 * @package    Cache
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Cache_Memcached_Driver extends Cache_Driver {

	// Cache backend object and flags
	protected $backend;
	protected $flags;

	public function __construct($tmp, $config)
	{
		if ( ! extension_loaded('memcached'))
			throw new Kohana_Exception('cache.extension_not_loaded', 'memcached');

		$this->backend = new Memcached;
		$this->flags =  0;

		// Add the server to the pool
		$this->backend->addServer($config['host'], $config['port'])
			or Kohana::log('error', 'Cache: Connection failed: '.$config['host']);
	}

	public function find($tag)
	{
		return FALSE;
	}

	public function get($id)
	{
		return (($return = $this->backend->get($id)) === FALSE) ? NULL : $return;
	}
	
	public function getCas($id, &$token)
	{
		$null = null;
		return (($return = $this->backend->get($id, $null, $token)) === FALSE) ? NULL : $return;
	}
	
	public function getMulti($keys)
	{
		// Change slashes to colons
		$null = null;
		return (($return = $this->backend->getMulti($keys, $null, Memcached::GET_PRESERVE_ORDER)) === FALSE) ? NULL : $return;
	}
	
	public function cas($cas, $id, $data, $tags, $lifetime)
	{
		count($tags) and Kohana::log('error', 'Cache: Tags are unsupported by the memcache driver');

		// Memcache driver expects unix timestamp
		if ($lifetime !== 0)
		{
			$lifetime += time();
		}

		return $this->backend->cas($cas, $id, $data, $lifetime);
	}

	public function set($id, $data, $tags, $lifetime)
	{
		count($tags) and Kohana::log('error', 'Cache: Tags are unsupported by the memcache driver');

		// Memcache driver expects unix timestamp
		if ($lifetime !== 0)
		{
			$lifetime += time();
		}

		return $this->backend->set($id, $data, $lifetime);
	}
	
	public function setMulti($data, $tags, $lifetime)
	{
		count($tags) and Kohana::log('error', 'Cache: Tags are unsupported by the memcache driver');

		// Memcache driver expects unix timestamp
		if ($lifetime !== 0)
		{
			$lifetime += time();
		}

		return $this->backend->setMulti($data, $lifetime);
	}

	public function delete($id, $tag = FALSE)
	{
		if ($id === TRUE)
			return $this->backend->flush();

		if ($tag == FALSE)
			return $this->backend->delete($id);

		return TRUE;
	}

	public function delete_expired()
	{
		return TRUE;
	}
	
	public function getResultCode()
	{
		return $this->backend->getResultCode();
	}

} // End Cache Memcache Driver