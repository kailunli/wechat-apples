<?php
namespace app\index\service;

class Base 
{
	protected $wechatAppId = 'wx9446d2169abf4697';
    protected $wechatAppSecret = '201dd04116c4297eb62de88868f82ab8';
	
	public function __construct()
	{
		
	}
	
	// 生成用户唯一标志
	public function generateIdentityNo()
	{
		$curtime = date('YmdHis');
		do {
			$identityNo =  mt_rand(100000, 999999) . $curtime;
		} while (\think\Db::table('lkl_third_user')->where(['identity_no'=>$identityNo])->find());
		
		return $identityNo;
	}
}