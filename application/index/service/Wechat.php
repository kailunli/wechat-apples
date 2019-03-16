<?php
namespace app\index\service;

use think\Db;

class Wechat extends Base
{
	public function __construct()
	{
		
	}
	
	// 微信登录获取session
	public function getWCCode2Session($code)
	{
		$appId  = $this->wechatAppId;
		$secret = $this->wechatAppSecret;
		$url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appId}&secret={$secret}&js_code={$code}&grant_type=authorization_code";
		$result = curl_request($url);
		
		if ($result) {
			$tmpRes = json_decode($result, true);
			$this->saveWCOpenId($tmpRes['openid'], isset($tmpRes['unionid'])?$tmpRes['unionid']:'');
		}
		
		return $result;
	}

        /* 新增用户 */
        public function addUser($uuid, $openid, $unionid, $nickname, $avatarUrl, $country, $province, $city, $gender, $authSetting)
        {
            $curtime = time();
            $userData = [
                'openid' => $openid,
                'unionid' => $unionid,
                'nickname' => $nickname,
                'avatar_url' => $avatarUrl,
                'country' => $country,
                'province' => $province,
                'city' => $city,
                'gender' => $gender,
                'auth_setting' => $authSetting,
                'update_at' => $curtime
            ];

            if ($uuid <= 0) {
                $uuid = $this->uuid();
                $userData['create_at'] = $curtime;
                $userData['uuid'] = $uuid;
                
                return Db::table('lkl_wechat_user')->insert($userData);
            } else {
                Db::table('lkl_wechat_user')->where(['uuid'=>$uuid])->save($userData);
                
                return $uuid;   
            }
        }

        protected function uuid()
        {
            do {
                $uuid = intval(strval(microtime(true) * 10000) . mt_rand(10, 99));
            } while (Db::table('lkl_wechat_user')->where(['uuid'=>$uuid])->find());
 
            return $uuid;
        }


}
