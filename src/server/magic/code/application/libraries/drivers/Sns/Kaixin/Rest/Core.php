<?

require_once 'Kaixin/Rest/Abstract.php';

class Kaixin_Rest_Core extends Kaixin_Rest_Abstract
{
    /**
     * Returns the requested info fields for the requested set of users.
     *
     * @param array $uids    An array of user ids
     *
     * @return array  An array of user objects
     */
    public function users_getInfo($uids)
    {
        $params = array('uids' => $uids);

        return $this->call_method('users.getInfo', $params);
    }

    /**
     * Returns the user corresponding to the current session object.
     *
     * @return integer  User id
     */
    public function users_getLoggedInUser()
    {
        return $this->call_method('users.getLoggedInUser', array());
    }

    /**
     * Returns whether or not the user corresponding to the current
     * session object has the give the app basic authorization.
     *
     * @return boolean  true if the user has authorized the app
     */
    public function users_isAppUser($uids)
    {
        return $this->call_method('users.isAppUser', $params);
    }
    
    public function users_getEncodeSessionKey()
    {
    	return $this->call_method('users.getEncodeSessionKey', array());
    }
    
    public function users_getInvitationSucList($uid)
    {
    	$params = array('uid' => $uid);
    	return $this->call_method('users.getInvitationSucList', $params);
    }
    
    public function users_isFan($starUid, $uids)
    {
    	$params = array('staruid' => $starUid, 'uids' => join(',', $uids));
    	return $this->call_method('users.isFan', $params);
    }
    

    /**
     * Returns the friends id of the current session user.
     *
     * @param int $page  (Optional).
     * @param int $count   (Optional)
     *
     * @return array  An array of friends id
     */
    public function friends_get()
    {
        $params = array();
        return $this->call_method('friends.get', $params);
    }

    /**
     * Returns the friends of the current session user.
     *
     * @return array  An array of friends
     */
    public function friends_getFriends()
    {
        $params = array();
        return $this->call_method('friends.getFriends', $params);
    }
    
    public function friends_getAppFriends()
    {
        $params = array();
        return $this->call_method('friends.getAppFriends', $params);
    }

}