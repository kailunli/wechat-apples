<?php
namespace app\index\controller;

use think\Db;
use think\Request;
use app\index\service\Wechat;

class Index extends Base
{

    public function index(Request $req)
    {
        $page = $req->param('page', 1);
        $size = $req->param('size', 10);
        
        $page = ($page>1) ? $page-1 : 0;

        $userlistRedisKey = "lkl_user__list_{$page}_{$size}";
        try {
            if ($users = cache($userlistRedisKey)) {
                return $users;
            } else {
	       $users = Db::table('lkl_user')->field('id userid,username,portrait')->order(['sort'=>'desc', 'create_at'=>'desc'])->limit(($page>1?$page-1:0)*$size, $size)->select();
               cache($userlistRedisKey, json_encode($users), 7200);
               return json($users);	
            }
    	} catch(\Exception $e) {
            return $e->getMessage(); 
        }
    }

    // 微信小程序登录
    public function login(Request $req)
    {
       $code = $req->param('code', '', 'string');
	   
	   if (trim($code)) {
           $wcServ = new Wechat();
	       $session = $wcServ->getWCCode2Session($code);
	       if ($session) {
               if (isset($session['openid'])) {
                   $uuid = $wcServ->saveSession($session['openid'], isset($session['unionid'])?$session['unionid']:'', $session['session_key']);
                   session('user.uuid', $uuid);
                   dump(session('user'));

                   return json(['return_code'=>0, 'msg'=>'session', 'data'=>['uuid'=>$uuid]]);
               }
           }
	   }
	   return json(['return_code'=>-1, 'msg'=>'无效code']);
    }
    
    /*新增用户*/
    public function adduser(Request $req)
    {
        $uuid = $this->uuid;
        $nickname = $req->param('nickname', '', 'string');
        $avatarUrl = $req->param('avatar_url', '', 'string');
        $country = $req->param('country', '', 'string');
        $province = $req->param('province', '', 'string');
        $city = $req->param('city', '', 'string');
        $gender = $req->param('gender', 0, 'int');
        $authSetting = $req->param('auth_setting', '', 'string');

        $wxServ = new Wechat();
        $uuid = $wxServ->addUser($uuid, $nickname, $avatarUrl, $country, $province, $city, $gender, $authSetting);
        if ($uuid) {
	    return json(['return_code'=>0, 'msg'=>'授权成功', 'data'=>['uuid'=>$uuid]]);
	}
        return json(['return_code'=>-600, 'msg'=>'网络异常']);
    }

}
