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

        $tmpRes = [];
		if ($result) {
			$tmpRes = json_decode($result, true);
		}

		return (array)$tmpRes;
	}

	/* 保存会话秘钥 */
	public function saveSession($openid, $unionid, $sessionKey)
    {
        $curtime = time();
        if ($user = Db::table('lkl_wechat_user')->where(['openid'=>$openid])->find()) { // 更新
            if (Db::table('lkl_wechat_user')->where(['uuid'=>$user['uuid']])->update([
                'session_key' => $sessionKey,
                'update_at' => $curtime
            ])) {
               return $user['uuid'];
            }
        } else {
            $uuid = $this->uuid();
            Db::table('lkl_wechat_user')->insert([
                'openid' => $openid,
                'unionid' => $unionid,
                'session_key' => $sessionKey,
                'create_at' => $curtime,
                'update_at' => $curtime
            ]);
            return $uuid;
        }
    }

    /* 新增用户 */
    public function addUser($uuid, $nickname, $avatarUrl, $country, $province, $city, $gender, $authSetting)
    {
        if (!trim($openid)) {
            $curtime = time();
            $userData = [
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
                $userData['uuid'] = $uuid;
                $userData['create_at'] = $curtime;

                if (Db::table('lkl_wechat_user')->insert($userData)) {
                    return $uuid;
                }
            } else {
                if (Db::table('lkl_wechat_user')->where(['uuid'=>$uuid])->save($userData)) {
                    return $uuid;
                }
            }
        }
        return 0;
    }

    protected function uuid()
    {
        do {
            $uuid = intval(strval(microtime(true) * 10000) . mt_rand(10, 99));
        } while (Db::table('lkl_wechat_user')->where(['uuid'=>$uuid])->find());

        return $uuid;
    }


}
