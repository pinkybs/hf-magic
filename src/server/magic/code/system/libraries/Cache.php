<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Provides a driver-based interface for finding, creating, and deleting cached
 * resources. Caches are identified by a unique string. Tagging of caches is
 * also supported, and caches can be found and deleted by id or tag.
 *
 * $Id: Cache.php 3769 2008-12-15 00:48:56Z zombor $
 *
 * @package    Cache
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Cache_Core {

	// For garbage collection
	protected static $loaded;

	// Configuration
	protected $config;

	// Driver object
	protected $driver;
	
	public static $instances = array();
	
	CONST RES_SUCCESS = 0;
	CONST RES_FAILURE = 1;
	CONST RES_HOST_LOOKUP_FAILURE = 2;
	CONST RES_UNKNOWN_READ_FAILURE = 7;
	CONST RES_PROTOCOL_ERROR = 8;
	CONST RES_CLIENT_ERROR = 9;
	CONST RES_SERVER_ERROR = 10;
	CONST RES_WRITE_FAILURE = 5;
	CONST RES_DATA_EXISTS = 12;
	CONST RES_NOTSTORED = 14;
	CONST RES_NOTFOUND = 16;
	CONST RES_PARTIAL_READ = 18;
	CONST RES_SOME_ERRORS = 19;
	CONST RES_NO_SERVERS = 20;
	CONST RES_END = 21;
	CONST RES_ERRNO = 25;
	CONST RES_BUFFERED = 31;
	CONST RES_TIMEOUT = 30;
	CONST RES_BAD_KEY_PROVIDED = 32;
	CONST RES_CONNECTION_SOCKET_CREATE_FAILURE = 11;
	CONST RES_PAYLOAD_FAILURE = -1001;

	/**
	 * Returns a singleton instance of Cache.
	 *
	 * @param   array  configuration
	 * @return  Cache_Core
	 */
	public static function instance($config = '')
	{
		if ( ! isset(Cache::$instances[$config]))
		{
			// Create a new instance
			Cache::$instances[$config] = new Cache($config);
		}

		return Cache::$instances[$config];
	}

	/**
	 * Loads the configured driver and validates it.
	 *
	 * @param   array|string  custom configuration or config group name
	 * @return  void
	 */
	public function __construct($config = FALSE)
	{
		if (is_string($config))
		{
			$name = $config;

			// Test the config group name
			if (($config = Kohana::config('cache.'.$config)) === NULL)
				throw new Kohana_Exception('cache.undefined_group', $name);
		}

		if (is_array($config))
		{
			// Append the default configuration options
			$config += Kohana::config('cache.default');
		}
		else
		{
			// Load the default group
			$config = Kohana::config('cache.default');
		}

		// Cache the config in the object
		$this->config = $config;

		// Set driver name
		$driver = 'Cache_'.ucfirst($this->config['driver']).'_Driver';

		// Load the driver
		if ( ! Kohana::auto_load($driver))
			throw new Kohana_Exception('core.driver_not_found', $this->config['driver'], get_class($this));

		// Initialize the driver
		$this->driver = new $driver($this->config['params'], $this->config);

		// Validate the driver
		if ( ! ($this->driver instanceof Cache_Driver))
			throw new Kohana_Exception('core.driver_implements', $this->config['driver'], get_class($this), 'Cache_Driver');

		Kohana::log('debug', 'Cache Library initialized');

		if (self::$loaded !== TRUE)
		{
			$this->config['requests'] = (int) $this->config['requests'];

			if ($this->config['requests'] > 0 AND mt_rand(1, $this->config['requests']) === 1)
			{
				// Do garbage collection
				$this->driver->delete_expired();

				Kohana::log('debug', 'Cache: Expired caches deleted.');
			}

			// Cache has been loaded once
			self::$loaded = TRUE;
		}
	}

	/**
	 * Fetches a cache by id. Non-string cache items are automatically
	 * unserialized before the cache is returned. NULL is returned when
	 * a cache item is not found.
	 *
	 * @param   string  cache id
	 * @return  mixed   cached data or NULL
	 */
	public function get($id)
	{
		// Change slashes to colons
		$id = str_replace(array('/', '\\'), '=', $id);

		if ($data = $this->driver->get($id))
		{
			if (substr($data, 0, 14) === '<{serialized}>')
			{
				// Data has been serialized, unserialize now
				$data = unserialize(substr($data, 14));
			}
		}

		return $data;
	}
	
	public function getCas($id, &$token)
	{
		// Change slashes to colons
		$id = str_replace(array('/', '\\'), '=', $id);

		if ($data = $this->driver->getCas($id, $token))
		{
			if (substr($data, 0, 14) === '<{serialized}>')
			{
				// Data has been serialized, unserialize now
				$data = unserialize(substr($data, 14));
			}
		}

		return $data;
	}
	
	public function getMulti($keys)
	{
		if ($this->config['driver'] == 'memcached') {
			if ($keys === array()) {
				return array();
			}
			$datas =  $this->driver->getMulti($keys);
			foreach ($datas as $key => $vl) {
				if ($datas[$key])
				{
					if (substr($datas[$key], 0, 14) === '<{serialized}>')
					{
						// Data has been serialized, unserialize now
						$datas[$key] = unserialize(substr($datas[$key], 14));
					}
				}
			}
			
			return $datas;
		}
		
		// Change slashes to colons
		$data = array();
		foreach ($keys as $key) {
			$data[$key] = $this->get($key);
		}
		
		return $data;
	}

	/**
	 * Fetches all of the caches for a given tag. An empty array will be
	 * returned when no matching caches are found.
	 *
	 * @param   string  cache tag
	 * @return  array   all cache items matching the tag
	 */
	public function find($tag)
	{
		if ($ids = $this->driver->find($tag))
		{
			$data = array();
			foreach ($ids as $id)
			{
				// Load each cache item and add it to the array
				if (($cache = $this->get($id)) !== NULL)
				{
					$data[$id] = $cache;
				}
			}

			return $data;
		}

		return array();
	}
	
	function setMulti($datas, $tags = NULL, $lifetime = NULL)
	{
		if ($this->config['driver'] == 'memcached') {
			foreach ($datas as $key => $data) {
				if (is_resource($data))
					throw new Kohana_Exception('cache.resources');
		
				// Change slashes to colons
				$key = str_replace(array('/', '\\'), '=', $key);
		
				if ( ! is_string($data))
				{
					// Serialize all non-string data, so that types can be preserved
					$data = '<{serialized}>'.serialize($data);
				}
		
				// Make sure that tags is an array
				$tags = empty($tags) ? array() : (array) $tags;
		
				if ($lifetime === NULL)
				{
					// Get the default lifetime
					$lifetime = $this->config['lifetime'];
				}
				
				$datas[$key] = $data;
			}
			return $this->driver->setMulti($datas, $tags, $lifetime);
		}
		
		foreach ($datas as $key => $vl) {
			//var_dump($key, $vl);
			$this->set($key, $vl);
		}
		
		return true;
	}
	
	/**
	 * Set a cache item by id. Tags may also be added and a custom lifetime
	 * can be set. Non-string data is automatically serialized.
	 *
	 * @param   string   unique cache id
	 * @param   mixed    data to cache
	 * @param   array    tags for this item
	 * @param   integer  number of seconds until the cache expires
	 * @return  boolean
	 */
	function cas($cas, $id, $data, $tags = NULL, $lifetime = NULL)
	{
		if (is_resource($data))
			throw new Kohana_Exception('cache.resources');

		// Change slashes to colons
		$id = str_replace(array('/', '\\'), '=', $id);

		if ( ! is_string($data))
		{
			// Serialize all non-string data, so that types can be preserved
			$data = '<{serialized}>'.serialize($data);
		}

		// Make sure that tags is an array
		$tags = empty($tags) ? array() : (array) $tags;

		if ($lifetime === NULL)
		{
			// Get the default lifetime
			$lifetime = $this->config['lifetime'];
		}

		return $this->driver->cas($cas, $id, $data, $tags, $lifetime);
	}

	/**
	 * Set a cache item by id. Tags may also be added and a custom lifetime
	 * can be set. Non-string data is automatically serialized.
	 *
	 * @param   string   unique cache id
	 * @param   mixed    data to cache
	 * @param   array    tags for this item
	 * @param   integer  number of seconds until the cache expires
	 * @return  boolean
	 */
	function set($id, $data, $tags = NULL, $lifetime = NULL)
	{
		if (is_resource($data))
			throw new Kohana_Exception('cache.resources');

		// Change slashes to colons
		$id = str_replace(array('/', '\\'), '=', $id);

		if ( ! is_string($data))
		{
			// Serialize all non-string data, so that types can be preserved
			$data = '<{serialized}>'.serialize($data);
		}

		// Make sure that tags is an array
		$tags = empty($tags) ? array() : (array) $tags;

		if ($lifetime === NULL)
		{
			// Get the default lifetime
			$lifetime = $this->config['lifetime'];
		}

		return $this->driver->set($id, $data, $tags, $lifetime);
	}

	/**
	 * Delete a cache item by id.
	 *
	 * @param   string   cache id
	 * @return  boolean
	 */
	public function delete($id)
	{
		// Change slashes to colons
		$id = str_replace(array('/', '\\'), '=', $id);

		return $this->driver->delete($id);
	}

	/**
	 * Delete all cache items with a given tag.
	 *
	 * @param   string   cache tag name
	 * @return  boolean
	 */
	public function delete_tag($tag)
	{
		return $this->driver->delete(FALSE, $tag);
	}

	/**
	 * Delete ALL cache items items.
	 *
	 * @return  boolean
	 */
	public function delete_all()
	{
		return $this->driver->delete(TRUE);
	}
	
	public function getResultCode()
	{
		return $this->driver->getResultCode();
	}

} // End Cache
