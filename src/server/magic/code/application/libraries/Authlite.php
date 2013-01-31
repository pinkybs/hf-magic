<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
/**
 * Authlite library
 * 
 * Based on Kohana's Auth library.
 *
 * @package		Layerful
 * @subpackage	Modules
 * @author		Layerful Team <http://layerful.org/>
 * @author		Fred Wu <fred@beyondcoding.com>
 * @copyright	BeyondCoding
 * @license		http://layerful.org/license MIT
 * @since		0.3.0
 */
class Authlite_Core {

	protected $session;
	protected $config;
	protected $username_column;
	protected $password_column;

	/**
	 * Create an instance of Auth.
	 *
	 * @return object
	 */
	public static function factory()
	{
		return new Authlite();
	}

	/**
	 * Return a static instance of Auth.
	 *
	 * @return object
	 */
	public static function instance()
	{
		static $instance;

		// Load the Authlite instance
		empty($instance) and $instance = new Authlite();

		return $instance;
	}

	public function __construct()
	{
		$this->session = Session::instance();
		$this->config  = Kohana::config('authlite');
		
		$this->username_column = $this->config['username'];
		$this->password_column = $this->config['password'];
		
		Kohana::log('debug', 'Authlite Library loaded');
	}

	/**
	 * Check if there is an active session.
	 *
	 * @return boolean
	 */
	public function logged_in()
	{
		// Get the user from the session
		$user = $this->session->get($this->config['session_key']);
		
		$status = is_object($user) ? true : false;
		
		// Get the user from the cookie
		if ($status == false)
		{
			$token = cookie::get('authautologin');
			
			if (is_string($token) && $token === $this->hash($user->{$this->username_column}.$user->{$this->password_column}))
			{
				$status = true;
				$this->login($user->{$this->username_column}, $user->{$this->password_column});
			}
		}

		return $status;
	}

	/**
	 * Returns the currently logged in user, or FALSE.
	 *
	 * @return object|false
	 */
	public function get_user()
	{
		if ($this->logged_in())
		{
			return $_SESSION[$this->config['session_key']];
		}

		return false;
	}

	/**
	 * Attempt to log in a user by using an ORM object and plain-text password.
	 *
	 * @param string username to log in
	 * @param string password to check against
	 * @param boolean enable auto-login
	 * @return boolean
	 */
	public function login($username, $password, $remember = false)
	{
		if (empty($password))
		{
			return false;
		}
		
		$user = ORM::factory($this->config['user_model'])->where($this->username_column, $username)->find();
		
		if ($user->{$this->password_column} === $this->hash($password))
		{
			$this->session->set($this->config['session_key'], $user);
			
			if ($remember == true)
			{
				$token = $this->hash($user->{$this->username_column}.$user->{$this->password_column});
				cookie::set('authlite_autologin', $token, $this->config['lifetime']);
			}
			
			return $user;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Log out a user by removing the related session variables.
	 *
	 * @return boolean
	 */
	public function logout()
	{
		if (cookie::get('authlite_autologin'))
		{
			cookie::delete('authlite_autologin');
		}
		
		// Remove the user from the session
		$this->session->delete($this->config['session_key']);

		// Regenerate session_id
		$this->session->regenerate();

		return ! $this->logged_in();
	}
	
	protected function hash($str)
	{
		return hash($this->config['hash_method'], $str);
	}

} // End Authlite