<?php
namespace app\index\controller;

use \think\Controller;

class Base extends Controller
{
    protected $wechatAppId = 'wx9446d2169abf4697';
    protected $wechatAppSecret = '201dd04116c4297eb62de88868f82ab8';
    protected $uuid = 0;
    protected $noLoginController = [];
    protected $noLoginActions = [
        'Index' => [
            'index',
            'login'
        ]
    ];

    public function initialize()
    {
        parent::initialize();

        $noLoginActions = $this->noLoginActions;
        $controller = request()->controller();
        $action = request()->action();

        dump($controller);
        dump($action);

        if (!(isset($noLoginActions[$controller]) && isset($noLoginActions[$controller][$action]))) {
            $this->access();
        }
    }

    protected function access()
    {
        if ($user = session('user')) {
            $uuid = strval($user['uuid']);
            $this->uuid = $uuid;
            if ($uuid <= 0) {
                echo json_encode(['return_code' => -900, 'msg' => '请登录！']);
                exit();
            }
        } else {
            echo json_encode(['return_code'=>-900, 'msg'=>'请登录！']);
            exit();
        }
    }


}