<?php
namespace common\ueditor;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

class UEditorAction extends Action
{
    /**
     * @var array
     */
    public $config = [];
	public $aModel;

    public function init()
    {
        //close csrf
        Yii::$app->request->enableCsrfValidation = false;
        //默认设置
        $_config = require(__DIR__ . '/config.php');
        //load config file
        $this->config = ArrayHelper::merge($_config, $this->config);
        parent::init();
    }

    public function run()
    {
        $table = $this->controller->getInputParams('table');
        $field = $this->controller->getInputParams('field');
        $id = $this->controller->getInputParams('id');
        if (empty($table) || empty($field)) {
            return [];
        }

        //$_FILES = Yii::$app->params['uploadTest'];
        $params = [
            'info_table' => $table,
            'info_field' => $field,
            'info_id' => intval($id),
        ];
		$this->aModel = $this->controller->getAttachment($params);
		$pathBase = $this->aModel->getPath(false) . $this->aModel->getPathPre();
        FileHelper::createDirectory($pathBase);
		$this->config['fileRoot'] = $pathBase;
		$this->config['imageUrlPrefix'] = $this->aModel->getUrlBase() . $this->aModel->getPathPre();
		$this->config['imageRoot'] = $pathBase;
		//print_r($this->config);exit();

        $this->handleAction();
    }

    /**
     * 处理action
     */
    protected function handleAction()
    {
        $action = Yii::$app->request->get('action');
        switch ($action) {
            case 'config':
                $result = json_encode($this->config);
                break;

            /* 上传图片 */
            case 'uploadimage':
                /* 上传涂鸦 */
            case 'uploadscrawl':
                /* 上传视频 */
            case 'uploadvideo':
                /* 上传文件 */
            case 'uploadfile':
                $result = $this->actionUpload();
                break;

            /* 列出图片 */
            case 'listimage':
                /* 列出文件 */
            case 'listfile':
                $result = $this->actionList();
                break;

            /* 抓取远程文件 */
            case 'catchimage':
                $result = $this->actionCrawler();
                break;

            default:
                $result = json_encode(['state' => '请求地址出错']);
                break;
        }
        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(['state' => 'callback参数不合法']);
            }
        } else {
            echo $result;
        }
    }

    /**
     * 上传
     * @return string
     */
    protected function actionUpload()
    {
        $base64 = "upload";
        switch (htmlspecialchars($_GET['action'])) {
            case 'uploadimage':
                $config = [
                    "pathRoot" => ArrayHelper::getValue($this->config, "imageRoot", $_SERVER['DOCUMENT_ROOT']),
                    "urlPrefix" => ArrayHelper::getValue($this->config, "imageUrlPrefix", ''),
                    "pathFormat" => $this->config['imagePathFormat'],
                    "maxSize" => $this->config['imageMaxSize'],
                    "allowFiles" => $this->config['imageAllowFiles']
                ];
                $fieldName = $this->config['imageFieldName'];
                break;
            case 'uploadscrawl':
                $config = [
                    "pathRoot" => ArrayHelper::getValue($this->config, "scrawlRoot", $_SERVER['DOCUMENT_ROOT']),
                    "pathFormat" => $this->config['scrawlPathFormat'],
                    "maxSize" => $this->config['scrawlMaxSize'],
                    "allowFiles" => $this->config['scrawlAllowFiles'],
                    "oriName" => "scrawl.png"
                ];
                $fieldName = $this->config['scrawlFieldName'];
                $base64 = "base64";
                break;
            case 'uploadvideo':
                $config = [
                    "pathRoot" => ArrayHelper::getValue($this->config, "videoRoot", $_SERVER['DOCUMENT_ROOT']),
                    "pathFormat" => $this->config['videoPathFormat'],
                    "maxSize" => $this->config['videoMaxSize'],
                    "allowFiles" => $this->config['videoAllowFiles']
                ];
                $fieldName = $this->config['videoFieldName'];
                break;
            case 'uploadfile':
            default:
                $config = [
                    "pathRoot" => ArrayHelper::getValue($this->config, "fileRoot", $_SERVER['DOCUMENT_ROOT']),
                    "pathFormat" => $this->config['filePathFormat'],
                    "maxSize" => $this->config['fileMaxSize'],
                    "allowFiles" => $this->config['fileAllowFiles']
                ];
                $fieldName = $this->config['fileFieldName'];
                break;
        }
        /* 生成上传实例对象并完成上传 */

        $up = new Uploader($fieldName, $config, $base64);
        /**
         * 得到上传文件所对应的各个参数,数组结构
         * [
         *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
         *     "url" => "",            //返回的地址
         *     "title" => "",          //新文件名
         *     "original" => "",       //原始文件名
         *     "type" => ""            //文件类型
         *     "size" => "",           //文件大小
         * ]
         */

		$data = $up->getFileInfo();
		$this->writeFile($data);
        /* 返回数据 */
        return json_encode($data);
    }

    /**
     * 获取已上传的文件列表
     * @return string
     */
    protected function actionList()
    {
        /* 判断类型 */
        switch ($_GET['action']) {
            /* 列出文件 */
            case 'listfile':
                $allowFiles = $this->config['fileManagerAllowFiles'];
                $listSize = $this->config['fileManagerListSize'];
                $path = $this->config['fileManagerListPath'];
                break;
            /* 列出图片 */
            case 'listimage':
            default:
                $allowFiles = $this->config['imageManagerAllowFiles'];
                $listSize = $this->config['imageManagerListSize'];
                $path = $this->config['imageManagerListPath'];
        }
        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

        /* 获取参数 */
        $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
        $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
        $end = (int)$start + (int)$size;

        /* 获取文件列表 */
        $path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "" : "/") . $path;
        $files = $this->getfiles($path, $allowFiles);
        if (!count($files)) {
            return json_encode([
                "state" => "no match file",
                "list" => [],
                "start" => $start,
                "total" => count($files)
            ]);
        }

        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = []; $i < $len && $i >= 0 && $i >= $start; $i--) {
            $list[] = $files[$i];
        }
//倒序
//for ($i = $end, $list = []; $i < $len && $i < $end; $i++){
//    $list[] = $files[$i];
//}

        /* 返回数据 */
        $result = json_encode([
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => count($files)
        ]);

        return $result;
    }

    /**
     * 抓取远程图片
     * @return string
     */
    protected function actionCrawler()
    {
        /* 上传配置 */
        $config = [
            "pathRoot" => ArrayHelper::getValue($this->config, "fileRoot", $_SERVER['DOCUMENT_ROOT']),
            "pathFormat" => $this->config['catcherPathFormat'],
            "maxSize" => $this->config['catcherMaxSize'],
            "allowFiles" => $this->config['catcherAllowFiles'],
            "oriName" => "remote.png"
        ];
        $fieldName = $this->config['catcherFieldName'];

        /* 抓取远程图片 */
        $list = [];
        if (isset($_POST[$fieldName])) {
            $source = $_POST[$fieldName];
        } else {
            $source = $_GET[$fieldName];
		}
		//$source = ['https://y.zdmimg.com/201610/24/580dbdffc3a8c3512.jpg_g320.jpg'];
        foreach ($source as $imgUrl) {
            $pos = strpos($imgUrl, '?');
            $sourceUrl = $pos ? substr($imgUrl, 0, $pos) : $imgUrl;
			//$aInfo = $this->aModel->getInfoBySource($sourceUrl);
            $item = new Uploader($sourceUrl, $config, "remote");
            $info = $item->getFileInfo();
			$data = [
                "state" => $info["state"],
                "url" => $this->config['imageUrlPrefix'] . $info["url"],
                "size" => $info["size"],
                "type" => $info["type"],
                "title" => htmlspecialchars($info["title"]),
                "original" => htmlspecialchars($info["original"]),
                //"source" => $this->config['imageUrlPrefix'] . $info["url"],
                "source" => htmlspecialchars($imgUrl),
                "source_url" => $sourceUrl,
            ];
			$this->writeFile($data);
			$list[] = $data;
        }

        /* 返回抓取数据 */
        return json_encode([
            'state' => count($list) ? 'SUCCESS' : 'ERROR',
            'list' => $list
        ]);
    }

    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param $allowFiles
     * @param array $files
     * @return array|null
     */
    protected function getfiles($path, $allowFiles, &$files = [])
    {
        if (!is_dir($path)) return null;
        if (substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $this->getfiles($path2, $allowFiles, $files);
                } else {
                    if (preg_match("/\.(" . $allowFiles . ")$/i", $file)) {
                        $files[] = [
                            'url' => substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                            'mtime' => filemtime($path2)
                        ];
                    }
                }
            }
        }
        return $files;
    }

	public function writeFile(& $data)
	{
		$aModel = clone $this->aModel;
		$aModel->filepath = str_replace($aModel->getUrlBase(), '', $data['url']);
		$aModel->name = $data['title'];
		$aModel->info_field = "picture_{$aModel->info_field}";
		$aModel->filename = $data['original'];
		$aModel->description = $data['title'];
		//$aModel->type = $data['type'];
		$aModel->extname = str_replace('.', '', $data['type']);
		$aModel->created_at = time();
		$aModel->source_url = isset($data['source_url']) ? $data['source_url'] : '';
		$aModel->noFile = true;
		$aModel->size = intval($data['size']);
		$aModel->in_use = 1;
		$aModel->save(false);
		$data['url'] .= '?aid=' . $aModel->id;
	}
}
