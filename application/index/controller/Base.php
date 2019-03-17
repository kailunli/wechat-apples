<?php
namespace app\index\controller;

use \think\Controller;

class Base extends Controller
{
    protected $wechatAppId = 'wx9446d2169abf4697';
    protected $wechatAppSecret = '201dd04116c4297eb62de88868f82ab8';
    protected $openid = '';
    public function initialize()
    {
        parent::initialize();


        if ($wcuser = session('wcuser')) {
            $openid = strval($wcuser['openid']);
            $this->openid = $openid;
        }

        file_put_contents('test_data.txt', '$this->openid = ' . $this->openid, FILE_APPEND);
    }


}