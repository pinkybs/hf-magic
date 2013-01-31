<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
/*
 * Created on 2009-2-26
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
/**
 * Media URL
 *
 * Returns the "media_url" item from your config file
 *
 * @access	public
 * @return	string
 * @since 2007/12/12
 * @author xupeng
 */	
class config_Core {
	/**
	 * Get a config item or group.
	 *
	 * @param   string   item name
	 * @param   boolean  force a forward slash (/) at the end of the item
	 * @param   boolean  is the item required?
	 * @return  mixed
	 */
	public static function config($key, $slash = FALSE, $required = TRUE)
	{
		if (kohana::$configuration === NULL)
		{
			// Load core configuration
			kohana::$configuration['core'] = kohana::config_load('core');

			// Re-parse the include paths
			kohana::include_paths(TRUE);
		}

		// Get the group name from the key
		$group = explode('.', $key, 2);
		$group = $group[0];

		if ( ! isset(kohana::$configuration[$group]))
		{
			// Load the configuration group
			kohana::$configuration[$group] = kohana::config_load($group, $required);
		}

		// Get the value of the key string
		$value = kohana::key_string(kohana::$configuration, $key);

		if ($slash === TRUE AND is_string($value) AND $value !== '')
		{
			// Force the value to end with "/"
			$value = rtrim($value, '/').'/';
		}

		return $value;
	}

	/**
	 * Sets a configuration item, if allowed.
	 *
	 * @param   string   config key string
	 * @param   string   config value
	 * @return  boolean
	 */
	public static function config_set($key, $value)
	{
		// Do this to make sure that the config array is already loaded
		kohana::config($key);

		if (substr($key, 0, 7) === 'routes.')
		{
			// Routes cannot contain sub keys due to possible dots in regex
			$keys = explode('.', $key, 2);
		}
		else
		{
			// Convert dot-noted key string to an array
			$keys = explode('.', $key);
		}

		// Used for recursion
		$conf =& kohana::$configuration;
		$last = count($keys) - 1;

		foreach ($keys as $i => $k)
		{
			if ($i === $last)
			{
				$conf[$k] = $value;
			}
			else
			{
				$conf =& $conf[$k];
			}
		}

		if ($key === 'core.modules')
		{
			// Reprocess the include paths
			kohana::include_paths(TRUE);
		}

		return TRUE;
	}

	/**
	 * Load a config file.
	 *
	 * @param   string   config filename, without extension
	 * @param   boolean  is the file required?
	 * @return  array
	 */
	public static function config_load($name, $required = TRUE, $path = 'config')
	{
		if ($name === 'core')
		{
			// Load the application configuration file
			require APPPATH.'config/config'.EXT;

			if ( ! isset($config['site_domain']))
			{
				// Invalid config file
				die('Your Kohana application configuration file is not valid.');
			}

			return $config;
		}

		if (isset(kohana::$internal_cache['configuration'][$name]))
			return kohana::$internal_cache['configuration'][$name];

		// Load matching configs
		$configuration = array();

		if ($files = kohana::find_file($path, $name, $required))
		{
			foreach ($files as $file)
			{
				require $file;

				if (isset($config) AND is_array($config))
				{
					// Merge in configuration
					$configuration = array_merge($configuration, $config);
				}
			}
		}

		if ( ! isset(kohana::$write_cache['configuration']))
		{
			// Cache has changed
			kohana::$write_cache['configuration'] = TRUE;
		}

		return kohana::$internal_cache['configuration'][$name] = $configuration;
	}

	/**
	 * Clears a config group from the cached configuration.
	 *
	 * @param   string  config group
	 * @return  void
	 */
	public static function config_clear($group)
	{
		// Remove the group from config
		unset(kohana::$configuration[$group], kohana::$internal_cache['configuration'][$group]);

		if ( ! isset(kohana::$write_cache['configuration']))
		{
			// Cache has changed
			kohana::$write_cache['configuration'] = TRUE;
		}
	}
}
?>
