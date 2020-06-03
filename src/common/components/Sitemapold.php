<?php

namespace common\components;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Component;
use yii\caching\Cache;

class Sitemap extends Component
{
    public $controllerNamespace = 'gallerycms\cmsad\controllers';

    /** @var int */
    public $cacheExpire = 86400;

    /** @var Cache|string */
    public $cacheProvider = 'cache';

    /** @var string */
    public $cacheKey = 'sitemap';

    /** @var boolean Use php's gzip compressing. */
    public $enableGzip = false;

    /** @var array */
    public $models = [];

    /** @var array */
    public $urls = [];

    public function init()
    {
        parent::init();

        /*if (is_string($this->cacheProvider)) {
            $this->cacheProvider = Yii::$app->{$this->cacheProvider};
        }*/

        /*if (!$this->cacheProvider instanceof Cache) {
            throw new InvalidConfigException('Invalid `cacheKey` parameter was specified.');
        }*/
    }

    /**
     * Build and cache a site map.
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function buildSitemap()
    {
        //print_R($this);
        $urls = $this->urls;
        foreach ($this->models as $modelName) {
            /** @var behaviors\SitemapBehavior $model */
            if (is_array($modelName)) {
                $model = new $modelName['class'];
                if (isset($modelName['behaviors'])) {
                    $model->attachBehaviors($modelName['behaviors']);
                }
            } else {
                $model = new $modelName;
            }

            $urls = array_merge($urls, $model->generateSiteMap());
        }

        //$this->cacheProvider->set($this->cacheKey, $sitemapData, $this->cacheExpire);

        return $urls;
        return $sitemapData;
    }

    public function setViewPath()
    {
    }
}
