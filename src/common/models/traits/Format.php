<?php

namespace common\models\traits;

use Yii;

trait Format
{

    protected function _formatData($data)
    {
        $fields = $this->_getDatasForFormat();
        $infos = [];
        foreach ($fields as $field => $fInfo) {
            $infos[$field] = isset($data[$field]) ? $data[$field] : $fInfo['default'];
        }
        return $infos;
    }

    protected function _getDatasForFormat()
    {
        return [];
    }

    public function _formatFailResult($defaultMessage, $info = null)
    {
        $errors = $this->getErrors();
        $errorFirst = $this->getFirstErrors();
        $message = !empty($errorFirst) ? current($errorFirst) : $defaultMessage;
        $eInfo = [];
        $info = is_null($info) ? $this->attributes : $info;
        foreach ($info as $field => $value) {
            $eInfo[$field]['value'] = $value;
            $error = isset($errors[$field]) ? current($errors[$field]) : '';
            $eInfo[$field]['error'] = $error;
        }

        $data = [
            'status' => '400',
			'isAjax' => true,
            'message' => $message,
            //'info' => $eInfo,
        ];
        return $data;
    }

	protected function _formatInfo($info)
	{
		return $info;
	}

	protected function _formatInfos($infos)
	{
		return $infos;
	}

    public function maskMobileView($view)
    {
        return $this->maskMobile();
    }

    public function maskMobile($mobile = null)
    {
        $mobile = is_null($mobile) && isset($this->mobile) ? $this->mobile : $mobile;
        return substr_replace($mobile, '******', 3, 6);
    }

    public function formatTimestamp($timestamp, $format = null)
    {
        if (empty($timestamp)) {
            return '';
        }
        $format = is_null($format) ? 'Y-m-d H:i:s' : $format;
        $return = date($format, $timestamp);
        return $return;
    }

    public function formatPercent($num, $num2, $haveBracket = true, $precision = 4)
    {
        //$result = $num2 == 0 ? '-' : (number_format($num / $num2, $precision) * 100) . '%';
        $result = $this->formatDivisor($num, $num2, false, $precision);
        $result = $result == '-' ? $result : ($result * 100) . '%';
        $result = $haveBracket ? " ( {$result} )" : $result;
        return $result;
    }

    public function formatDivisor($num, $num2, $haveBracket = true, $precision = 2)
    {
		$num = floatval($num);
		$num2 = floatval($num2);
        $result = $num2 == 0 ? '-' : number_format($num / $num2, $precision);
        $result = $haveBracket ? " ( {$result} )" : $result;
        return $result;
    }

    public function formatTimestampShow($timestamp)
    {
        $day = floor($timestamp / 86400);
        $hour = floor(($timestamp - ($day * 86400)) / 3600);
        $minite = ceil(($timestamp - ($day * 86400) - ($hour * 3600)) / 60);
        $str = $day ? $day . '天 ' : '';
        $str .= $hour ? $hour . '小时 ' : '';
        $str .= $minite . '分钟';
        return $str;
    }

    public function _formatDay($day)
    {
        $pattern = "@^\d{1,2}/\d{1,2}/\d{2}@";
        if (preg_match($pattern, $day)) {
            $info = explode('/', trim($day));
            $day = "20{$info[2]}-{$info[1]}-{$info[0]}";
        }
        return $day;
    }

	public function formatAtag($field, $info)
	{
		$type = isset($info['urlType']) ? $info['urlType'] : '';
		switch ($type) {
		case 'inline':
			$method = $info['urlMethod'];
			$url = $this->$method();
			break;
		default:
			$url = $this->$field;
		}
		$name = isset($info['urlName']) ? $info['urlName'] : $this->$field;
		$target = isset($info['urlTarget']) ? '' : 'target="_blank" ';
		$str = "<a href='{$url}' {$target}>{$name}</a>";
		return $str;
	}

	public function formatImgtag($field, $info)
	{
        return $this->getAttachmentImgtag($field);
	}

	public function formatImage($url, $options = [])
	{
        $optionsDefault = [
            'style' => ['width' => '120px', 'height' => '80px'],
            'onclick' => 'window.open(this.src);',
        ];
        $options = array_merge($optionsDefault, (array) $options);
        return Yii::$app->formatter->asImage($url, $options);
	}

	public function getCountyValue($space = ' ')
	{
		$str = $this->getPointName('region', ['code' => $this->province_code]);
		$str .= $space . $this->getPointName('region', ['code' => $this->city_code]);
		$str .= $space . $this->getPointName('region', ['code' => $this->county_code]);
		return trim($str, $space);
	}

    public function regionName($info, $getStr = false)
    {
        $info['province'] = $this->getPointName('region', ['code' => $info['province_code']]);
        $info['city'] = $this->getPointName('region', ['code' => $info['city_code']]);
        $info['county'] = $this->getPointName('region', ['code' => $info['county_code']]);
		if (!empty($getStr)) {
			$info = "{$info['province']} {$info['city']} {$info['county']}";
		}
        return $info;
    }

    public function createShortUrl($longUrl)
    {
        $longUrl = urlencode($longUrl);
        $rUrl = "http://api.t.sina.com.cn/short_url/shorten.json?source=2702428363&url_long={$longUrl}";
        $return = file_get_contents($rUrl);
        $return = json_decode($return, 1);
        if (!isset($return[0])) {
            return '';
        }
        return $return[0]['url_short'];
    }

    public function createShortUrlTb($longUrl)
    {
        $longUrl = urlencode($longUrl);
        $rUrl = "https://api.taokouling.com/tkl/shorturl?apikey=BtQZVhHkKF&type=4&url={$longUrl}";
        $result = file_get_contents($rUrl);
        var_dump($result);

        var_dump($rUrl);exit();
    }

    public function createShortUrlBaidu($longUrl)
    {
        $url = 'https://dwz.cn/admin/v2/create';
        $method = 'POST';
        $content_type = 'application/json';
        
        $token = '9ebff1fe1737b7fc45b2ed78847696cd';
        $bodys = ['url' => $longUrl];
        $headers = ['Content-Type:' . $content_type, 'Token:' . $token];
        
        // 创建连接
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($bodys));
        
        // 发送请求
        $response = curl_exec($curl);
        curl_close($curl);
        $return = json_decode($response);
        print_r($return);

        
        // 读取响应
        var_dump($response);
    }

	public function createShortUrlSelf($url)
	{
        echo $url;
        $url = 'http://baidu.com';// . $url;
        $base32 = [
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 
            'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 
            '0', '1', '2', '3', '4', '5'
        ];
        $hex = md5($url);
        $hexLen = strlen($hex);
        $subHexLen = $hexLen / 8;
        $output = array();
        //把加密字符按照8位一组16进制与0x3FFFFFFF(30位1)进行位与运算 
        for ($i = 0; $i < $subHexLen; $i++) { 
            $subHex = substr ($hex, $i * 8, 8);
            $int = 0x3FFFFFFF & hexdec($subHex);
            $out = '';
            for ($j = 0; $j < 6; $j++) { //把得到的值与0x0000001F进行位与运算，取得字符数组chars索引 
                $val = 0x0000001F & $int;
                $out .= $base32[$val];
                $int = $int >> 5;
            }
            $output[] = $out;
        }
        print_r($output);exit();
        return $output;
		return '';
	}

	public function getQrcodeUrl($code, $wechatCode, $returnUrl = '')
	{
		$qrcodeInfo = $this->getPointModel('qrcode')->getInfo(['where' => ['plat_code' => $wechatCode, 'code' => $code]]);
		$url = '';
		if (empty($qrcodeInfo)) {
			return "<a href='javascript: applyQrcode(\"{$code}\", \"{$wechatCode}\", \"{$returnUrl}\", this);' >申请二维码</a>";
		}
		if (empty($qrcodeInfo['status'])) {
			return "<a href='javascript: void(0);' >管理员批准后使用</a>";
		}
		$url = Yii::getAlias('@restappurl/wechat/' . $wechatCode . '/qrcode.html?qrcode=' . $code);
		return "<a href='{$url}' target='_blank'>二维码</a>";
	}

	public function getSubStr($string, $length)
	{
		return mb_substr(urldecode($string), 0, $length, 'utf-8');
	}

	public function getUpdatedTime()
	{
		return $this->formatTimestamp($this->updated_at);
	}

	public function getSortName()
	{
		return $this->getKeyName('sort', $this->sort);
	}
}
