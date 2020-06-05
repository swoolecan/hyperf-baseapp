<?php

namespace common\models\traits;

use yii\helpers\FileHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

trait Curl
{
	public function getUrlBody($url)
	{
		$client = new Client(['verify' => false]); // 忽略SSL错误
		$res = $client->request('GET', $url, ['http_errors' => false]);
		return $res->getBody();
	}

	public function getUrlStatus($url)
	{
		$client = new Client(['verify' => false]); // 忽略SSL错误
		$res = $client->request('GET', $url, ['http_errors' => false]);
		return $res->getStatusCode();
	}

    public function _downFile($file, $remoteUrl, $forceDown = false)
    {
        $file = $this->getPointFile($file);
        if (file_exists($file) && empty($forceDown)) {
            return filesize($file);//true;
        }
        FileHelper::createDirectory(dirname($file), 0777);

        $filesize = $this->commandDown($file, $remoteUrl);
        //$filesize = $this->curlDown1($file, $remoteUrl);
        //$filesize = $this->simpleDown($file, $remoteUrl);
		//$filesize = $this->clientDown($file, $remoteUrl);
		$filesize = intval($filesize);
		if (empty($filesize) && file_exists($file)) {
			unlink($file);
		}
		return $filesize;
    }

    public function curlDown1($file, $remoteUrl)//baiduSpider($url$reffer){  
    {
        $ch = curl_init();  
        $userAgent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36";
		$userAgent = "Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)";
        //伪造百度蜘蛛头部
        //$ip = '111.197.112.180';//220.181.108.91';  // 百度蜘蛛
        $ip = rand(1,255).'.'.rand(1,255).'.'.rand(1,255).'.'.rand(1,255);
        $timeout = 15;
		$reffer = 'http://91jm.com';
  
        curl_setopt($ch, CURLOPT_URL, $remoteUrl);  
        curl_setopt($ch, CURLOPT_HEADER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        //curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_REFERER, $reffer);//这里写一个来源地址，可以写要抓的页面的首页       
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-FORWARDED-FOR:' . $ip, 'CLIENT-IP:' . $ip]); 
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        $temp = curl_exec($ch);  
		var_dump($temp);exit();

        //伪造百度蜘蛛IP  
        if($content === false)
        {//输出错误信息
            $no = curl_errno($ch);
            switch(trim($no))
            {
                case 28 : $error = '访问目标地址超时'; break;
                default : $error = curl_error($ch); break;
            }
            echo $error;
        }
        else
        {
            $succ = true;
            return $content;
        }
        return $temp;        
    }

    protected function commandDown($file, $remoteUrl)
    {
        $shell = "wget -O {$file} {$remoteUrl}";
        echo $shell . "\n";
        exec($shell);
		if (file_exists($file)) {
            return filesize($file);
		}
		return 0;
    }

    protected function simpleDown($file, $remoteUrl)
    {
        $content = @ file_get_contents($remoteUrl);
        if ($content) {
            file_put_contents($file, $content);
            return filesize($file);
        }
		return false;
    }

    public function clientDown($file, $remoteUrl)
    {
		$client = new Client(['verify' => false]); // 忽略SSL错误
		try {
		    $response = $client->get($remoteUrl, ['timeout' => 30, 'save_to' => $file]);
		} catch (ConnectException $e) {
			return false;
		}
		if ($response->getStatusCode() == 200) {
            var_dump($response->getHeader('Content-Length'));
            return filesize($file);
		}
        var_dump($response->getHeader('Content-Length'));;
        return false;
        //$client = new Client();
        //$r = $client->request('POST', $rUrl, $data);
        //var_dump($r->getHeader('Content-Length'));;
    }

    public function curlDown($file, $remoteUrl)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设置是否将响应结果存入变量，1是存入，0是直接echo出
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        $postData = [];
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

        //$jsonData = json_encode($postData);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length:' . strlen($jsonData)));
        //curl_setopt($ch, CURLOPT_POSTFIELDS , $data);

        $result = curl_exec($ch);
        $res = curl_error($ch);
        curl_close($ch);

    }

    protected function curlUpload()
    {
        // upload
        $data = array('name'=>'boy', "upload"=>"@boy.png");
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, "http://远程服务器地址马赛克/testRespond.php"); 
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); 
        curl_setopt($ch, CURLOPT_POSTFIELDS , $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        echo $output;
        curl_close($ch);  

        // upload php 5.6+
        $data = array('name'=>'boy', "upload"=>"");
        $ch = curl_init(); 
        $data['upload']=new CURLFile(realpath(getcwd().'/boy.png'));
        curl_setopt($ch, CURLOPT_URL, "http://115.29.247.189/test/testRespond.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); 
        curl_setopt($ch, CURLOPT_POSTFIELDS , $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        echo $output;
        curl_close($ch);  

        // download
        $ch = curl_init(); 
        $fp=fopen('./girl.jpg', 'w');
        curl_setopt($ch, CURLOPT_URL, "http://远程服务器地址马赛克/girl.jpg"); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); 
        curl_setopt($ch, CURLOPT_FILE, $fp); 
        $output = curl_exec($ch); 
        $info = curl_getinfo($ch);
        fclose($fp);
        $size = filesize("./girl.jpg");
        if ($size != $info['size_download']) {
            echo "下载的数据不完整，请重新下载";
        } else {
            echo "下载数据完整";
        }
        curl_close($ch);    
    }

	function curlAuth($url, $user, $passwd)
	{
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_USERPWD => $user.':'.$passwd,
            CURLOPT_URL     => $url,
            CURLOPT_RETURNTRANSFER => true
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    protected function curlLogined()
	{
        //$authurl = 'http://要请求HTTP认证的地址';
        //echo curlAuth($authurl,'vace','passwd');
        $post = [
            'email' => '账户', 
            'pwd' => '密码'
        ]; 
        $url = "登陆地址";  
        $cookie = dirname(__FILE__) . '/cookie.txt'; // 设置cookie保存路径  
        $url2 = "登陆后要获取信息的地址"; // 登录后要获取信息的地址  
        login_post($url, $cookie, $post); // 模拟登录 
        $content = get_content($url2, $cookie); // 获取登录页的信息  
    
        @ unlink($cookie); // 删除cookie文件 
        var_dump($content);    
	}
    
    protected function curlLogin($url, $cookie, $post)
    { 
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0);
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_exec($curl); 
        curl_close($curl);
    }
    
    protected function curlLoginedContent($url, $cookie)
    { 
        // 登录成功后获取数据  
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_HEADER, 0); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); 
        $rs = curl_exec($ch); 
        curl_close($ch); 
        return $rs; 
    }

	public function getUrlHeaders($url)
	{
		$headers = @ get_headers($url);
		return $headers;
	}


    protected function tmp()
    {
            $content = $this->getRemoteContent($this->url);
            $content = strpos($content, '302 Found') !== false ? false : $content;
            $content = strpos($content, '404 Not Found') !== false ? false : $content;
            if ($content) {
                file_put_contents($file, $content);
                $headers = @ get_headers($this->url);
                $this->status = -1;
            }
            $pathInfo = pathinfo($url);
            $extName = isset($pathInfo['extension']) ? $pathInfo['extension'] : '';
            $info->size = @ filesize($file);
            $info->type = !is_null(FileHelper::getMimeType($file)) ? FileHelper::getMimeType($file) : '';



    }

    public function getPointFile($file)
    {
		if (strpos($file, '/') === 0) {
			return $file;
		}
        return "/data/htmlwww/filesys/" . $file;
    }
}
