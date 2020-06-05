<?php

namespace common\models\traits;

use Yii;

/**
 * randomString($length, $params = []);
 * createSingleRandomStr()
 * authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) 
 * getUniqidStr()
 * getCapitalMoney($num)
 */
trait Tool
{
	public function getUniqidStr()
	{
        $endtime = 1356019200; // 2012-12-21时间戳
        $curtime = time();
        $newtime = $curtime - $endtime;
        $rand = rand(0, 99); //两位随机
        $all = $rand . $newtime;
        $onlyid = base_convert($all, 10, 36); // 把10进制转为36进制的唯一ID
        return $onlyid;
    }

	public function getUniqidId()
	{
		$valid = false;
		do {
		    $id = rand(1000000, 9999999);
			$this->getInfo($id, 'name');
			$valid = empty($info) ? true : false;
		} while (!$valid);
		return $id;
	}

	public function randomString($length, $params = [])
	{
		$prefix = isset($params['prefix']) ? $params['prefix'] : '';
		$suffix = isset($params['suffix']) ? $params['suffix'] : '';
		$length = $length - strlen($prefix) - strlen($suffix);
		$lowerUpper = isset($params['lowerUpper']) ? $params['lowerUpper'] : 'strtolower';
		$onlyLetterNum = isset($params['onlyLetterNum']) ? $params['onlyLetterNum'] : true;
		$string = $lowerUpper(Yii::$app->getSecurity()->generateRandomString($length));
		if ($onlyLetterNum) {
			$string = str_replace(['-', '_'], ['a', '1'], $string);
		}
		return $prefix . $string . $suffix;
	}

    public function createSingleRandomStr()
    {   
        mt_srand((double) microtime() * 1000000);
        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

	public function createSingleOrder()
	{
		$yCode = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
		$yStr = $yCode[intval(date('Y')) - 2019];
		$date = strtoupper(dechex(date('m'))) . date('d') . substr(time(), -2);
		$micro = substr(microtime(), 2, 3);
		$rand = sprintf('%02d', rand(10000, 99999));
		//echo $yStr . '-' . $date . '-' . $micro . '-' . $rand;
		$orderSn = $yStr . $date . $micro . $rand;
		return $orderSn;
	}

    public function getCapitalMoney($num)
    {  
        $c1 = "零壹贰叁肆伍陆柒捌玖";  
        $c2 = "分角元拾佰仟万拾佰仟亿";  
        $num = round($num, 2);  
        $num = $num * 100;  
        if (strlen($num) > 10) {  
            return "数据太长，没有这么大的钱吧，检查下";  
        }   
        $i = 0;  
        $c = "";  
        while (1) {  
            if ($i == 0) {  
                $n = substr($num, strlen($num)-1, 1);  
            } else {  
                $n = $num % 10;  
            }   
            $p1 = substr($c1, 3 * $n, 3);  
            $p2 = substr($c2, 3 * $i, 3);  
            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {  
                $c = $p1 . $p2 . $c;  
            } else {  
                $c = $p1 . $c;  
            }   
            $i = $i + 1;  
            $num = $num / 10;  
            $num = (int)$num;  
            if ($num == 0) {  
                break;  
            }   
        }  
        $j = 0;  
        $slen = strlen($c);  
        while ($j < $slen) {  
            $m = substr($c, $j, 6);  
            if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {  
                $left = substr($c, 0, $j);  
                $right = substr($c, $j + 3);  
                $c = $left . $right;  
                $j = $j-3;  
                $slen = $slen-3;  
            }   
            $j = $j + 3;  
        }   
  
        if (substr($c, strlen($c)-3, 3) == '零') {  
            $c = substr($c, 0, strlen($c)-3);  
        }  
        if (empty($c)) {  
            return "零元整";  
        }else{  
            return $c . "整";  
        }  
    }

    protected function _writePointLog($sort, $subSort, $datas, $startTime = null)
    {
        $endTime = microtime(true);
        $startTime = is_null($startTime) ? Yii::$app->params['currentTime'] : $startTime;
        $timeUsed = number_format($endTime - $startTime, 3);
        $currentDate = date('Y-m-d H:i:s');
        $keyInfo = isset($datas['key']) ? $datas['key'] : '';
        $message = implode('---', $datas);
        $content = "==={$keyInfo}==={$timeUsed}==={$currentDate}===\r\n{$message}\r\n\r\n";

        $app = Yii::$app->id;
        $logFile = \Yii::getAlias('@backend/runtime') . "/record-log/{$app}/{$sort}/{$subSort}_" . date('Y-m-d') . '.log';
        $path = dirname($logFile);
        if (!is_dir($path)) {
            \yii\helpers\FileHelper::createDirectory($path);
        }
        file_put_contents($logFile, $content, FILE_APPEND);

        return true;
    }

    public function getOwnerMobileInfos($number)
    {
        $names = ['王', '李', '孟', '石', '吕', '张', '赵', '刘', '黄', '胡', '王', '李', '张'];
        $nameSuffixs = ['先生', '女士', '小姐'];
        $mobiles = ['3', '4', '5', '8'];
        $owners = [];
        for ($i = 1; $i < $number; $i++) {
            $name = $names[array_rand($names)];
            $nameSuffix = $nameSuffixs[array_rand($nameSuffixs)];
            $mobile = '1' . $mobiles[array_rand($mobiles)] . '*****' . rand(1000, 9999);
            $owner = [
                'name' => $name . $nameSuffix,
                'area' => rand(80, 300),
                'minute' => rand(2, 30),
                'mobile' => $mobile,
            ];
            $owners[] = $owner;
        }
        return $owners;
    }

    public function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) 
    {
        // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙   
        $ckey_length = 4;   
           
        // 密匙   
        $key = md5($key ? $key : Yii::$app->params['authKey']);   
           
        // 密匙a会参与加解密   
        $keya = md5(substr($key, 0, 16));   
        // 密匙b会用来做数据完整性验证   
        $keyb = md5(substr($key, 16, 16));   
        // 密匙c用于变化生成的密文   
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): 
    substr(md5(microtime()), -$ckey_length)) : '';   
        // 参与运算的密匙   
        $cryptkey = $keya.md5($keya.$keyc);   
        $key_length = strlen($cryptkey);   
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)， 
    //解密时会通过这个密匙验证数据完整性   
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确   
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :  
    sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;   
        $string_length = strlen($string);   
        $result = '';   
        $box = range(0, 255);   
        $rndkey = array();   
        // 产生密匙簿   
        for($i = 0; $i <= 255; $i++) {   
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);   
        }   
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度   
        for($j = $i = 0; $i < 256; $i++) {   
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;   
            $tmp = $box[$i];   
            $box[$i] = $box[$j];   
            $box[$j] = $tmp;   
        }   
        // 核心加解密部分   
        for($a = $j = $i = 0; $i < $string_length; $i++) {   
            $a = ($a + 1) % 256;   
            $j = ($j + $box[$a]) % 256;   
            $tmp = $box[$a];   
            $box[$a] = $box[$j];   
            $box[$j] = $tmp;   
            // 从密匙簿得出密匙进行异或，再转成字符   
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));   
        }   
        if($operation == 'DECODE') {  
            // 验证数据有效性，请看未加密明文的格式   
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&  
    substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {   
                return substr($result, 26);   
            } else {   
                return '';   
            }   
        } else {   
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因   
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码   
            return $keyc.str_replace('=', '', base64_encode($result));   
        }   
    } 

	public function checkInRange($range)
	{
		//$test = '2019/5/10 10:30-2019/5/10 18:00==2019/5/11 10:30-2019/5/11 12:00';
		//$test1 = '2019/5/10 10:30-2019/5/10 18:00';//==2019/5/11 10:30-2019/5/11 12:00';
		$arrays = strpos($range, '==') !== false ? explode('==', $range) : (array) $range;
		$cTime = time();
		$rangeStr = '有效时间段：';
		foreach ($arrays as $array) {
			list($start, $end) = explode('-', $array);
			$start = strtotime($start);
			$end = strtotime($end);
			$rangeStr .= $array . '-----';
			if ($cTime >= $start && $cTime <= $end) {
				return true;
			}
		}
		return $rangeStr;
		print_r($arrays);
			var_dump($start);var_dump($end);
		exit();
	}

    public function getPatternElems($content, $patterns = [])
    {
        $patterns = $this->getPatterns();
        $datas = []; 
        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $content, $url);
            if (is_array($url)) {
                $datas = array_merge($datas, $url['url']);
            }
        }
        $datas = array_unique($datas);
        return $datas;
    }
    
    protected function getPatterns()
    {
        $patterns = [ 
            '@data-original="(?P<url>.*)"@Us',
            '@data-source="(?P<url>.*)".*@Us',
            '@src= *"(?P<url>.*)".*@Us',
            "@src= *'(?P<url>.*)'.*@Us",
            '@<link.*type="text/css".*href="(?P<url>.*)".*>@Us',
            "@url\( *'(?P<url>.*)'.*\)@Us",
            '@url\( *"(?P<url>.*)".*\)@Us',
            '@url\((?P<url>.*)\)@Us',
            "@<link.*href='(?P<url>.*\.css)'.*>@Us",
            '@<link.*href="(?P<url>.*)".*>@Us',
        ];

        return $patterns;
    }

	public function getRandLetter($type, $length)
	{
		$chapters = 'ABCDEFGHJKLMNPQRSTWXY';
		$letters = 'abcdefghijklmnopqrstuvwxyz';
		switch ($type) {
		case 'letter':
			$str = $ltters;
			break;
		case 'chapter':
			$str = $chapters;
			break;
		default:
			$str = $letters . $chapters;
		}

		$returnStr = '';
        for($i = 0; $i < $length; $i++) {
		    $returnStr .= $str{mt_rand(0, strlen($str) - 1)}; //生成php随机数
		}
		return $returnStr;
	}

    public function _getSummary($content, $s = 0, $e = 150, $char = 'utf-8')
    {
        if (empty($content)) {
            return null;
        }
        return (mb_substr(str_replace('&nbsp;', '', strip_tags($content)), $s, $e, $char));
    }
}
