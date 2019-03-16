<?php
namespace app\index\controller;

use think\Db;
use think\Request;
use app\index\service\Wechat;

class Index
{
    protected $wechatAppId = 'wx9446d2169abf4697';
    protected $wechatAppSecret = '201dd04116c4297eb62de88868f82ab8';
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
	       $wc = new Wechat();
	       $session = $wc->getWCCode2Session($code);
	   
	       return json(['return_code'=>0, 'msg'=>'wc_session', 'data'=>['session'=>$session]]);
	   }
	   return json(['return_code'=>-1, 'msg'=>'无效code']);
    }
    
    public function getWechatSessionKey(Request $req)
    {
        $code = $req->param('code');
        $appid = $this->wechatAppId;
        $secret = $this->wechatAppSecret;

        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$code}&grant_type=authorization_code";
        $result = curl_request($url);
        return json($result);        
    }
    
    /*新增用户*/
    public function adduser(Request $req)
    {
        $openid = $req->param('openid', '', 'string');
        $unionid = $req->param('unionid', '', 'string');
        $nickname = $req->param('nickname', '', 'string');
        $avatarUrl = $req->param('avatar_url', '', 'string');
        $country = $req->param('country', '', 'string');
        $province = $req->param('province', '', 'string');
        $city = $req->param('city', '', 'string');
        $gender = $req->param('gender', 0, 'int');
        $authSetting = $req->param('auth_setting', '', 'string');

        $wxServ = new Wechat();
        $uuid = $wxServ->addUser($uuid, $openid, $unionid, $nickname, $avatarUrl, $country, $province, $city, $gender, $authSetting);
        if ($uuid) {
	    return json(['return_code'=>0, 'msg'=>'授权成功', 'data'=>['uuid'=>$uuid]]);
	}
        return json(['return_code'=>-600, 'msg'=>'网络异常']);
    }

}
