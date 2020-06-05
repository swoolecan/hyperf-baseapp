<?php

namespace common\helpers;

use Yii;
use yii\helpers\Inflector;

class RuleFormat
{
    public static function pointRules($app, $modules = [])
    {
        $rules = InitFormat::ruleDatas($app, $modules);
        $sites = InitFormat::runtimeParams('site-domain');

        $return = self::formatRule($rules, $sites);
        if (isset($_GET['show__router'])) {
            print_r($return);exit();
        }
        return $return;
    }

    /**
     * @param $datas array 路由列表
     * @param $siteCodes array 站点列表
     * @onlySite string 只生成指定站点的路由
     */
    public static function formatRule($datas, $siteInfos)
    {
        $ruleDatas = [];
        foreach ($datas as $data) {
            $ruleDataOrigin = $ruleData = $data['data'];
            if (isset($data['noDomain']) && $data['noDomain']) {
                $ruleDatas[] = $ruleData;
                continue;
            }

            $siteCodes = isset($data['only']) && !empty($data['only']) ? $data['only'] : array_keys($siteInfos);
            if (isset($data['noHosts'])) {
                $newSiteCodes = [];
                foreach ($siteCodes as $sCode) {
                    if (!in_array($sCode, $data['noHosts'])) {
                        $newSiteCodes[] = $sCode;
                    }
                }
                $siteCodes = $newSiteCodes;
            }

            $domainLimit = isset($data['domainLimit']) ? $data['domainLimit'] : false;
            foreach ($siteCodes as $siteCode) {
                $domains = (array) $siteInfos[$siteCode]['domains'];
                $sort = isset($siteInfos[$siteCode]['sort']) ? $siteInfos[$siteCode]['sort'] : $siteCode;
                foreach ($domains as $key => $domain) {
                    if (!empty($domainLimit) && !in_array($key, $domainLimit)) {
                        continue;
                    }
                    $ruleData['route'] = str_replace('{{SORT}}', $sort, $ruleDataOrigin['route']);
                    $ruleData['pattern'] = $domain . $ruleDataOrigin['pattern'];
                    $ruleDatas[] = $ruleData;
                }   
            }
        }
        
        //print_r($ruleDatas);exit();
        return $ruleDatas; 
    }

    public static function formatRestRule($module, $datas, $controllerPre = '')
    {
        $ruleStandards = [
            'index' => [
                'suffix' => '/',
                'verb' => ['GET', 'HEAD'],
                'pattern' => '/{{PCONTROLLER}}',
                'route'    => '{{MODULE}}/{{CONTROLLER}}/index',
            ],
            'delete' => [
                'verb' => ['DELETE'],
                'pattern' => '/{{PCONTROLLER}}/<id>',
                'route'    => '{{MODULE}}/{{CONTROLLER}}/delete',
            ],
            'update' => [
                'verb' => ['PUT', 'PATCH'],
                'pattern' => '/{{PCONTROLLER}}/<id>',
                'route'    => '{{MODULE}}/{{CONTROLLER}}/update',
            ],
            'create' => [
                'verb' => ['POST'],
                'pattern' => '/{{PCONTROLLER}}',
                'route'    => '{{MODULE}}/{{CONTROLLER}}/create',
            ],
            'view' => [
                'verb' => ['GET', 'HEAD'],
                'pattern' => '/{{PCONTROLLER}}/<id>',
                'route'    => '{{MODULE}}/{{CONTROLLER}}/view',
            ],
        ];

        $result = [];
        foreach ($datas as $controller => $actions) {
            foreach ($actions as $action) {
                if (!in_array($action, array_keys($ruleStandards))) {
                    continue;
                }
                $replaces = ['{{MODULE}}', '{{CONTROLLER}}', '{{PCONTROLLER}}'];
                $pController = Inflector::pluralize($controller);
                $rController = !empty($controllerPre) ? "{$controllerPre}/{$controller}" : $controller;
                $targets = [$module, $rController, $pController];
                $rule = $ruleStandards[$action];
                $rule['pattern'] = str_replace($replaces, $targets, $rule['pattern']);
                $rule['route'] = str_replace($replaces, $targets, $rule['route']);
                $result[$controller . '-' . $action] = ['data' => $rule];
            }
        }
        return $result;
    }
}
