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

        file_put_contents('test_data.txt', $result . "\r\n");
        file_put_contents('test_data.txt', json_encode(session('uuid')) . "\r\n");
		
		if ($result) {
			$tmpRes = json_decode($result, true);
			session('openid', $tmpRes['openid']);
			session('unionid', isset($tmpRes['unionid']) ? $tmpRes['unionid'] : '');
		}
		
		return (array)$result;
	}

    /* 新增用户 */
    public function addUser($openid, $unionid, $nickname, $avatarUrl, $country, $province, $city, $gender, $authSetting)
    {
        if (!trim($openid)) {
            $curtime = time();
            $userData = [
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
                $userData['openid'] = $openid;
                $userData['create_at'] = $curtime;

                return Db::table('lkl_wechat_user')->insert($userData);
            } else {
                Db::table('lkl_wechat_user')->where(['openid'=>$openid])->save($userData);

                return $uuid;
            }
        } else {
            return 0;
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
