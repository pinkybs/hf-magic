<?

require_once 'Taobao/Rest/Abstract.php';

class Taobao_Rest_Jianghu extends Taobao_Rest_Abstract
{
    /**
     * Returns the requested info fields for the requested set of users.
     *
     * @param array $uids    An array of user ids
     * @param array $fields  An array of info field names desired
     *
     * @return array  An array of user objects
     */
    public function user_getProfile($uid = null)
    {
        $params = array();
        if ($uid) {
            $params['query_uid'] = $uid;
        }
        return $this->call_method('taobao.jianghu.user.getProfile', $params);
    }
    
    public function users_getProfileList($uids)
    {
        $params = array(
            'uids' => $uids
        );
        return $this->call_method('taobao.jianghu.users.getProfileList', $params);
    }
    
    public function friends_getFriendList($page_no = 1, $page_size = 1000)
    {
        $params = array(
            'page_no' => $page_no,
            'page_size' => $page_size
        );
        return $this->call_method('taobao.jianghu.friends.getFriendList', $params);
    }
    
    public function friends_areFriends($uid1, $uid2)
    {
        $params = array(
            'uid1' => $uid1, 
            'uid2' => $uid2
        );
        return $this->call_method('taobao.jianghu.friends.areFriends', $params);
    }
    
    public function feed_publish($body, $params = array())
    {
        $params['body'] = $body;
        return $this->call_method('taobao.jianghu.feed.publish', $params);
    }
    
    public function msg_publish($to_uid, $content, $type)
    {
        $params = array(
            'type' => $type, 
            'content' => $content,
            'to_uid' => $to_uid
        );
        return $this->call_method('taobao.jianghu.msg.publish', $params);
    }   
    
    public function coins_consume($count)
    {
        $params = array(
            'coin_count' => $count
        );
        return $this->call_method('taobao.jianghu.coins.consume', $params);
    }
    
    public function coins_sum()
    {
        return $this->call_method('taobao.jianghu.coins.sum', array());
    }
    
    public function albums_getAlbumList($page_no = 1, $page_size = 20, $owner_uid = null)
    {
        $params = array(
            'page_no' => $page_no,
            'page_size' => $page_size
        );
        if ($owner_uid) {
            $params['owner_uid'] = $owner_uid;
        }
        
        return $this->call_method('taobao.jianghu.albums.getAlbumList', $params);
    }
    
    public function get_picture_uploadPicture($album_id, $picture_name)
    {
        $method = 'taobao.jianghu.picture.uploadPicture';
        $params = array(
            'album_id' => $album_id,
            'picture_name' => $picture_name
        );
        
        return $this->create_request_url($method, $params);
    }
    
	//
	public function get_vas_isv_url($params)
    {
        $method = 'taobao.vas.isv.url.get';
        return $this->call_method($method, $params);
    }
    
	//
	public function get_vas_isv_info($outer_order_id, $proxy_code, $buyer_time)
    {
        $method = 'taobao.vas.isv.info.get';
        $params = array(
            'outer_order_id' => $outer_order_id,
            'proxy_code' => $proxy_code,
            'buyer_time' => $buyer_time
        );
        return $this->call_method($method, $params);
    }
    
}