<?php defined('SYSPATH') or die('No direct access allowed.');

class Platform_Controller extends Controller {

    public function __construct(){
        parent::__construct();
    }
    
    public function invite(){
    	$view = new View('magic/invite');
        $view->render(true);
    }
    
    public function friends(){
    	$view = new View('magic/friends');
        $view->render(true);
    }

}