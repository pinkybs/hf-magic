<?php

defined('SYSPATH') or die('No direct access allowed.');

class Default_Controller extends Controller {

    public function __construct() {
        parent::__construct();
        $this->session = Session::instance();
    }

    public function index() {
        $this->userinfo = $this->session->get('userinfo');
        if ($this->userinfo) {
            if ($this->userinfo['type'] == "0") {
                url::redirect(url::site("statis"));
            } elseif ($this->userinfo['type'] == "1") {
                url::redirect(url::site("operat"));
            }
        }

        if ($this->input->post('dosubmit')) {
            if (!$this->input->post('tbox_uname') || !$this->input->post('tbox_upass')) {
                common::alertinfo("用户名或者密码不能为空!");
            }
            $uname = $this->input->post('tbox_uname');
            $upass = $this->input->post('tbox_upass');
            $utype = $this->input->post('sel_type');
            $isauth = user::isauth($uname, $upass, $utype);
            if (!$isauth) {
                common::alertinfo("用户名或者密码不正确!");
            }
            $isauth['time'] = date("Y-m-d H:i:s", time());
            $isauth['type'] = $utype;
            $this->session->set('userinfo', $isauth);
            if ($utype == "0") {
                url::redirect(url::site("statis"));
            } elseif ($utype == "1") {
                url::redirect(url::site("operat"));
            }
        } else {
            $view = new View('default');
            $view->render(TRUE);
        }
    }

    //退出登陆
    public function logout() {
        $this->session->destroy();
        url::redirect(url::site("default"));
    }

}
