<?php
namespace common\helpers;

use \DirectoryIterator;

class DirectoryHelper extends DirectoryIterator
{
    public static function unzipToPath($zipFile, $path)
    {
        $zip = new \ZipArchive; 
        $res = $zip->open($zipFile); 
        if ($res === true) { 
            $zip->extractTo($path); 
            $zip->close(); 
			return ['status' => 200, 'message' => 'OK'];
        } else { 
			return ['status' => 400, 'message' => 'ZIP文件打开失败'];
        } 
    }

    public static function pathFiles($path)
    {
        static $datas = [], $basePath;
        $basePath = is_null($basePath) ? $path : $basePath;
        $dir = self::pathObj($path);
        foreach ($dir as $file){
            if ($file->isDot()) {
                continue;
            }
            $name = $file->getFilename();
            if($file->isFile()){
                if (strpos($name, '.html') === false && strpos($name, '.php' === false)) {
                    continue;
                }
                if ($name[0] == '_') {
                    //continue;
                }
                $path = $file->getPath();
                $path = str_replace($basePath, '', $path);
                $datas[$path][] = $name;
            } else {
                //echo $file->getPathname() . '<br />';
                self::pathFiles($file->getPathname());
            }
        }
        return $datas;
    }

    protected static function pathObj($path)
    {
        return new DirectoryIterator($path);
    }
}
