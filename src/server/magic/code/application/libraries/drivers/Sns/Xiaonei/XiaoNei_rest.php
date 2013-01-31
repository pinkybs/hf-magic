<?php
class XiaoNei_Rest {

    public  $api_key = "";
    public  $api_secret = "";

    public  $session_key = "";

    public  $lastResponse = "";

    public  $format = "json";

    private $last_call_id = 1;

    private $server_url = "http://api.renren.com/restserver.do?";

    private $lastError = "";


    function __construct($api_key, $api_secret){
    	//$this->session_key = $_REQUEST['xn_sig_session_key'];
    	$this->last_call_id = microtime(true);
    	
        $this->api_key      = $api_key;
        $this->api_secret   = $api_secret;
    }

    public function adminGetAllocation($integration_point_name){
        return $this->send("admin.getAllocation", array(
            "integration_point_name" => $integration_point_name,
        ));
    }

    /*
    public function authCreateToken(){
        return $this->send("auth.createToken");
    }

    public function authGetSession($auth_token){
        return $this->send("auth.getSession", array(
            "auth_token" => $auth_token,
        ));
    }
    */

    public function blogsGet(){
        return $this->send("blogs.get");
    }

    public function friendsAreFriends($uid1, $uid2){
        return $this->send("friends.areFriends", array(
            "uids1"  => $uid1, 
            "uids2"  => $uid2,
        ));
    }

    public function batchRun($batch_queue){
        return $this->sendBatch("batch.run", array(
            "batch_queue"  => json_encode($batch_queue), 
        ));
    }

    public function friendsGet(){
        return $this->send("friends.get");
    }

    public function friendsGetFriends(){
        return $this->send("friends.getFriends");
    }

    public function friendsGetLists(){
        return $this->send("friends.getLists");
    }

    public function friendsGetAppUsers(){
        return $this->send("friends.getAppUsers");
    }
    
    public function friendsGetAppFriends(){
        return $this->send("friends.getAppFriends");
    }

    public function feedSend($template_id, $extra = array()){
        return $this->send("feed.publishTemplatizedAction", array_merge($extra, array(
            "template_id" => $template_id, 
        )));
    }

    public function groupsGet($gids = "", $uid = ""){
        return $this->send("groups.get", array(
            "gids" => $gids, 
            "uid"  => $uid, 
        ));
    }
    public function usersIsAppUser($uid = ""){
        return $this->send("users.isAppAdded", array(
            "uid" => $uid, 
        ));
    }

    public function notificationsSend($notification, $to_ids = "", $type = ""){
        return $this->send("notifications.send", array(
            "to_ids"        => $to_ids, 
            "notification"  => $notification, 
            "type"          => $type, 
        ));
    }

    /*
    public function notificationsSendEmail($uid, $title, $html = "", $text = ""){
        return $this->send("notifications.sendEmail", array(
            "uid"   => $uid, 
            "title" => $title, 
            "html"  => $html, 
            "text"  => $text, 
        ));
    }
    */

    public function payTestIsCompleted($order_id ){
        return $this->send("payTest.isCompleted", array(
            "order_id"   => $order_id, 
        ));
    }

    public function payTestRegOrder($order_id , $price, $title = "", $next_url = ""){
        return $this->send("payTest.regOrder", array(
            "order_id"   => $order_id, 
            "price"   => $price, 
            "title"   => $title, 
            "next_url"   => $next_url, 
        ));
    }

    public function payIsCompleted($order_id ){
        return $this->send("pay.isCompleted", array(
            "order_id"   => $order_id, 
        ));
    }

    public function payRegOrder($order_id , $price, $title = "", $next_url = ""){
        return $this->send("pay.regOrder", array(
            "order_id"   => $order_id, 
            "price"   => $price, 
            "title"   => $title, 
            "next_url"   => $next_url, 
        ));
    }

    public function photosGet($album_name = "", $pids = ""){
        return $this->send("photos.get", array(
            "album_name"   => $album_name, 
            "pids"   => $pids, 
        ));
    }

    public function photosGetAlbums(){
        return $this->send("photos.getAlbums");
    }

    public function profileSetQNML($uids, $profile){
        return $this->send("profile.setQNML", array(
            "uid"   => $uids, 
            "profile" => $profile, 
        ));
    }

    public function usersGetInfo($uids = '', $fields = ''){
    	if ($uids === '') {
	        return $this->send("users.getInfo", array());
    	}
        return $this->send("users.getInfo", array(
            "uids"      => $uids, 
            "fields"    => $fields,
        ));
    }

    public function usersAppInviter(){
        return $this->send("users.appInviter");
    }

    public function usersGetLoggedInUser(){
        return $this->send("users.getLoggedInUser");
    }

    public function videoGet(){
        return $this->send("video.get");
    }


    public function lastError(){
        return $this->lastError;
    }

    private function send($action, $params = array()){

        $queryString = $this->buildQuery($action, $params);

        if (function_exists("curl_init")) {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->server_url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_ENCODING, "");
            $result = curl_exec($ch);
            curl_close($ch);
        } else {

            $context = array(
                "http" => array(
                    "method" => "POST",
							'header' => 'Content-type: application/x-www-form-urlencoded'."\r\n".
										'User-Agent: XIAONEI API PHP5 Client 1.1 '."\r\n".
										'Content-length: ' . strlen($queryString),
                    "content" => $queryString,
                )
            );
            $contextid = stream_context_create($context);
            $sock=fopen($this->server_url, "r", false, $contextid);
            if ($sock) {
                $result='';
                while (!feof($sock))
                    $result.=fgets($sock, 4096);

                fclose($sock);
            }
        }

        $this->lastResponse = $result;

        $result =  json_decode($result, true);

        if (is_array($result) && isset($result['error_code'])) {
        	if ($action != 'users.isAppAdded') {
        		Kohana::log('error', $result['error_msg']);
        		Network::buffer_error('refresh_page');
        	}
			return false;
            // not throw exception now.
            //
            // throw new Client_360quan_Rest_Exception($result['error_msg'], $result['error_code']);
        }

        return $result; 
    }


    private function buildQuery($action, $params){
        $params["method"] = "xiaonei." . $action;
        $params["format"] = $this->format;
        $params["session_key"] = urlencode($this->session_key);
        $params["api_key"] = $this->api_key;
        $params["call_id"] = microtime(true);
        if ($params["call_id"] <= $this->last_call_id) {
            $params["call_id"] = $this->last_call_id + 0.001;
        }
        $this->last_call_id = $params["call_id"];
        if (!isset($params["v"])) {
            $params["v"] = "1.0";
        }

//        $params["sig"] = Sns_XiaoNei_Driver::generate_sig($params, $this->api_secret);
//
//        return http_build_query($params);
        
		$post_params = array();
		foreach ($params as $key => &$val) {
		  if (is_array($val)) $val = implode(',', $val);
		  $post_params[] = $key.'='.urlencode($val);
		}
		$secret = $this->api_secret;
		$post_params[] = 'sig='.Sns_Xiaonei_Rest_Driver::generate_sig($params, $secret);
		return implode('&', $post_params);

    }

}


