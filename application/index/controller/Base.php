<?php
namespace app\index\controller;

use \think\Controller;

class Base extends Controller
{
    protected $openid = '';
    public function initialize()
    {
        parent::initialize();

        if ($openid = session('openid') > 0) {
            $this->openid = $openid;
        }
    }


}