<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------


if (!function_exists('curl_request')) {
	function curl_request($url, $method='POST', array $postData=[], array $options=[]) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, strval($url));
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		if (strtolower($method) == 'post') {
			$postData = http_build_query($postData);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		}

		if ($options) {
			if (isset($options['https']) && $options['https'] === true) {
				curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
				curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,true);
			}
			if (isset($options['binarytransfer'])) { // true || false
				// 在启用CURLOPT_RETURNTRANSFER的时候，返回原生的（Raw）输出。
				curl_setopt($ch,CURLOPT_BINARYTRANSFER, (bool)$options['binarytransfer']);
			}
			if (isset($options['httpheader']) && is_array($options['httpheader'])) {
				curl_setopt($ch, CURLOPT_HTTPHEADER, $options['httpheader']);
			}
			if (isset($options['useragent'])) {
				curl_setopt($ch, CURLOPT_USERAGENT, strval($options['useragent']));
			}
			if (isset($options['followlocation'])) {
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $options['followlocation']);
			}
		}

		$result= curl_exec($ch);
		curl_close($ch);

		return $result;
	}
}
    
if (!function_exists('generate_random_ymd')) {
    function generate_random_ymd($startYear=1900, $endYear=null) {
        if (!is_numeric($startYear)) {
            $startYear = intval(date('Y')) - 100;
        }
        if ($endYear < $startYear) {
            $endYear = $startYear + 100;
        }
        if ($endYear === null) {
            $endYear = intval(date('Y')) - 1;
        }

        $randomYear = mt_rand($startYear, $endYear);
        $randomMonth = str_pad(mt_rand(1, 12), 2, '0', STR_PAD_LEFT);

        if (intval($randomMonth)) {
            if ($randomYear % 4 == 0 && $randomYear % 100 !=0 || $randomYear % 400 == 0) {
                $randomDay = mt_rand(1, 29);
            } else {
                $randomDay = mt_rand(1, 28);
            }
        } else if (in_array(intval($randomMonth), [1,3,5,7,8,10,12])) {
            $randomDay = mt_rand(1, 31);
        } else {
            $randomDay = mt_rand(1, 30);
        }
        $randomDay = str_pad($randomDay, 2, '0', STR_PAD_LEFT);

        return [
            'year' => $randomYear,
            'month' => $randomMonth,
            'day' => $randomDay
        ];
    }
}

