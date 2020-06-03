<?php

namespace common\components;

use Yii;
use common\behaviors\SitemapBehavior;

trait SitemapTrait
{
    public function actionIndex()
    {
        $module = $this->sitemap();

        //if (!$sitemapData = $module->cacheProvider->get($module->cacheKey)) {
            $sitemapData = $module->buildSitemap();
        //}
        $this->setViewPath('@common/views/sitemap');
        $sitemapData = $this->renderPartial('@common/views/sitemap', [
            'urls' => $sitemapData,
        ]);
        file_put_contents('/tmp/sitemap', $sitemapData);

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/xml');
        if ($module->enableGzip) {
            //$sitemapData = gzencode($sitemapData);
            //$headers->add('Content-Encoding', 'gzip');
            $headers->add('Content-Length', strlen($sitemapData));
        }
        return $sitemapData;
    }

    protected function sitemap()
    {
        $sitemap = new Sitemap();
        $sitemap->models = $this->_sitemapModels();
        $sitemap->urls = $this->_sitemapUrls();
        $sitemap->enableGzip = true; // default is false
        $sitemap->cacheExpire = 1; // 1 second. Default is 24 hours
        return $sitemap;

    }
}
