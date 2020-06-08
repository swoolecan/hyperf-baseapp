<?php

namespace backend\models;

use Yii;
use yii\helpers\Inflector;

trait BaseElemTrait
{
	public function addMenu()
	{
		$datas = explode("\r", $this->add_menu);
		$message = '';
		foreach ($datas as $data) {
			$tmp = trim(str_replace('，', ',', $data));
			$tmp = explode('=', $tmp);
			if (empty(array_filter($tmp))) {
				continue;
			}
			$info = [];
			foreach ($this->addMenuFields as $key => $field) {
				$value = isset($tmp[$key]) ? $tmp[$key] : '';
			    $value = trim(strval($value));
				$info[$field] = $value;
			}
			$add = $this->_addMenu($info);
			if ($add === false) {
				$message .= $data;
			}
		}
		
		return empty($message) ? ['status' => 200, 'message' => 'OK'] : ['status' => '400', 'message' => $message];

	}

	public function addMul()
	{
		$datas = explode("\r", $this->add_mul);
		$message = '';
		foreach ($datas as $data) {
			$tmp = explode('=', trim($data));
			if (empty(array_filter($tmp))) {
				continue;
			}
			$info = [];
			foreach ($this->addMulFields as $key => $field) {
				$value = isset($tmp[$key]) ? $tmp[$key] : '';
			    $value = $field  == 'full_controller' || strpos($field, 'no_') !== false ? intval($value) : trim(strval($value));
				$info[$field] = $value;
			}
			$add = $this->_addInfo($info);
			if ($add === false) {
				$message .= $data;
			}
		}
		
		return empty($message) ? ['status' => 200, 'message' => 'OK'] : ['status' => '400', 'message' => $message];

	}

	public function _getClass($view, $type)
	{
		$noField = "no_{$type}";
		if ($this->$noField) {
			return '';
		}
		$classPre = !empty($this->path) ? $this->path : str_replace('/', '\\', $this->module);
		$classPre .= $type == 'controller' ? '\controllers' : '\models';
		$classBase = !empty($this->$type) ? $this->$type : $this->getFormatCode('camel');
		if ($type == 'controller') {
			$classBase .= 'Controller';
		} elseif ($type == 'search') {
			$classBase = "searchs\\{$classBase}";
		}

		$class = "{$classPre}\\{$this->path_sub}\\{$classBase}";
		$class = str_replace('\\\\', '\\', $class);
		$exist = \common\helpers\ClassHelper::checkClassExist($class);
		$exist = empty($exist) ? '不存在' : '存在';
		return empty($view) ? $class : $exist;
	}

	public function createMap()
	{
		$datas = $this->getInfos(['limit' => 2000]);
		$modelStr = $controllerFrontStr = $controllerBaseStr = $methodStr = $modelCodeStr = "<?php\nreturn [\n";
		$controllerMaps = $controllerFrontMaps = [];
		foreach ($datas as $key => $data) {
            $modelKey = '';
			if (empty($data->no_model)) {
			    $modelKey = $data->formatCode;
				$modelClass = $data->_getClass('', 'model');
			    $modelStr .= "    '{$modelKey}' => '{$modelClass}',\n";
			}

			$baseKey = $this->getFormatModule(true);
			if (empty($data['no_controller'])) {
				$baseKey = $data->getFormatModule(true);
				if (!isset($controllerMaps[$baseKey])) {
					$controllerMaps[$baseKey] = $controllerBaseStr;
				}
				$controllerKey = $data->getFormatCode('base-format');
				$controllerKeySplit = $data->full_controller ? $data->name : $controllerKey;
				$controllerClass = $data->_getClass('', 'controller');
			    $controllerMaps[$baseKey] .= "    '{$controllerKeySplit}' => '{$controllerClass}',\n";

				$methods = $data->menuMethods;
				$methodString = empty($methods) ? '' : "'" . implode("', '", $methods) . "'";
				$methodStr .= "    '{$controllerClass}' => [{$methodString}],\n";
				$modelCodeStr .= "    '{$controllerClass}' => '{$modelKey}',\n";

				$frontKey = $data->formatModule . '_' . $controllerKey;
			    $controllerFrontStr .= "    '{$frontKey}' => '{$controllerClass}',\n";
			}
		}
		$methodFile = Yii::getAlias('@baseapp/runtime/map-method.php');
		file_put_contents($methodFile, $methodStr . '];');
		$modelCodeFile = Yii::getAlias('@baseapp/runtime/map-model-code.php');
		file_put_contents($modelCodeFile, $modelCodeStr . '];');
		$modelFile = Yii::getAlias('@baseapp/runtime/map-model.php');
		file_put_contents($modelFile, $modelStr . '];');

		$controllerFrontFile = Yii::getAlias('@baseapp/runtime/map-controller-front.php');
		file_put_contents($controllerFrontFile, $controllerFrontStr . '];');

		$controllerFilePre = Yii::getAlias('@baseapp/runtime/map-controller');
		foreach ($controllerMaps as $baseKey => $cStr) {
			$controllerFile = $controllerFilePre . '-' . $baseKey . '.php';
			file_put_contents($controllerFile, $cStr . '];');
		}
		return true;
	}

	public function getMenuMethods()
	{
		$menus = (array) $this->getPointModel('menu')->getInfos(['where' => ['elem_code' => $this->name], 'indexBy' => 'method']);
		return array_keys($menus);
	}

	public function _checkAction()
	{
		if ($this->no_controller) {
			return '没有控制器';
		}
		$controller = $this->_getClass('', 'controller');
		$allMethods = \common\helpers\ClassHelper::getMethods($controller);
		$methods = $this->menuMethods;
		$noVisit = $visit = [];
		foreach ($allMethods as $elems) {
			foreach ($elems as $elem) {
				if ($elem == 'actions') {
					continue;
				}
				$aStr = substr($elem, 0, 6);
				if ($aStr == 'action') {
					$action = Inflector::camel2id($elem);
					$action = substr($action, 7);
					$actions[] = $action;
					if (in_array($action, $methods)) {
						$visit[] = $action;
					} else {
						$noVisit[] = $action;
					}
				}
			}
		}
		//$cObject = Yii::createObject($controller, [$this->nameFull, Yii::$app]);
		$baseActions = [
			'add', 'add-mul', 'delete', 'import', 'export', 
			'listinfo', 'listinfo-tree', 'update', 'view',
			'list', 'mylist', 'detail', 'show', 'create', 'edit', 'remove',
		];
		foreach ($noVisit as $key => $noAction) {
			if (in_array($noAction, $baseActions)) {
				unset($noVisit[$key]);
			}
		}
		sort($noVisit);
		sort($visit);
		sort($methods);
		return implode('=', $noVisit) . '===---no-visit;<br />' . implode('=', $visit) . '===---visit;<br />' . implode('=', $methods) . '===---all-menu';
		return implode('=', $noVisit) . '===---no-visit;<br />' . implode('=', $visit) . '===---visit;';
	}

	public function updateMenu()
	{
		$menu = $this->getPointModel('menu');
		$elems = $this->elems;
		foreach ($elems as $elem) {
			if ($elem->no_controller == 1) {
				continue;
			}
			$bfCode = $elem->getFormatCode('base-format');
            $mController = $elem->full_controller ? $elem->name : $bfCode;
			$where = ['module' => $elem['module'], 'controller' => $mController];
			$rtype = $elem->no_model == 1 ? 'controller' : 'model';
			$mInfos = $menu->getInfos(['where' => $where]);
			foreach ($mInfos as $mInfo) {
			    $code = $elem->formatModule . '_' . $bfCode . '_' . $mInfo['method'];
				if ($mInfo['code'] != $code) {
					$mInfo->code = $code;
					$mInfo->update(false, ['code']);
					echo $mInfo['name'] . '==' . $mInfo['code'] . '==' . $code . "\n";
				}
				if ($mInfo['controller'] != $mController) {
					echo $mInfo['name'] . '==' . $mInfo['controller'] . '==' . $mController . "\n";
				}
			}
			$menu->updateAll(['rtype' => $rtype], $where);
			//$menu->updateAll(['elem_code' => $elem->name], $where);
		}
	}

	public function checkMenu()
	{
		//$menus = $this->getPointModel('menu')->getControllerMethods();
		$menus = $this->getPointModel('menu')->getInfos(['limit' => 2000]);
		$elems = $this->elems;
		//print_r(array_keys($elems));
		//print_r(array_keys($menus));exit();
		/*$j = 0;
		foreach ($elems as $eKey => $elem) {
			if (!in_array($eKey, array_keys($menus))) {
				echo 'eee-' . $eKey . "\n<br />";
				$j++;
			}
        }*/

		$i = 0;
        $noModule = $noElem = $noController = $noCode = '';
		foreach ($menus as $mKey => $mInfo) {
            if (empty($mInfo['controller'])) {
                continue;
            }
            $detail = "{$mInfo['module']}-{$mInfo['name']}-{$mInfo['code']}-{$mInfo['controller']}-{$mInfo['method']}\n<br />";
            if (empty($mInfo['elem_code'])) {
				$noElem .= 'noelem---' . $detail;
				$i++;
                continue;
            }
            $eCodes[] = $mInfo['elem_code'];
            $baseElem = $elems[$mInfo['elem_code']];
            if ($baseElem['module'] != $mInfo['module']) {
				$noModule .= 'nomodule---' . $baseElem['module'] . '==' . $mInfo['module'] . '===' . $detail;
				$i++;
                continue;
            }
            $controller = $baseElem['full_controller'] ? $baseElem['name'] : $baseElem->getFormatCode('base-format');
            if ($controller != $mInfo['controller']) {
				$noController .= 'nocontroller---' . $controller . '===' . $mInfo['controller'] . '===' . $detail;
				$i++;
                continue;
            }
            $code = $baseElem->formatModule . '_' . $baseElem->getFormatCode('base-format') . '_' . $mInfo['method'];
			if ($code != $mInfo['code']) {
				$noCode .= 'nocode-' . $code . '===' . $mInfo['code'] . '===' . $detail;
				$i++;
                continue;
			}
		}
		echo '<br />' . $i;// . '====' . $j;
        echo $noElem . $noModule . $noController . $noCode;
        foreach ($elems as $eCode => $elem) {
            if (!in_array($eCode, $eCodes)) {
				echo 'nomenu-' . $eCode . "<br />\n";
            }
        }
        exit();
	}

	protected function getElems()
	{
		$datas = [];
		$elems = $this->getInfos(['limit' => 1000]);
		foreach ($elems as $elem) {
			if (empty($elem['no_controller'])) {
			    $datas[$elem['name']] = $elem;
			}
		}
		return $datas;
	}

	public function _addMenu($info)
	{
		if (empty($info['module']) || empty($info['code']) || empty($info['method'])) {
			return false;
		}
		$exist = $this->getInfo(['where' => ['module' => $info['module'], 'code' => $info['code']]]);
		if (empty($exist)) {
			echo '信息有误';exit();
			return false;
		}
		$methods = (array) explode(',', $info['method']);
		$methods = array_filter($methods);
		$message = '';
		foreach ($methods as $method) {
			$tmp = explode('|', $method);
			$code = isset($tmp[0]) ? $tmp[0] : $method;
			$name = isset($tmp[1]) ? $tmp[1] : $code;
			$data = [
				'name' => $name,
				'code' => $exist->formatModule . '_' . $exist->getFormatCode('base-format') . '_' . $code,
				'elem_code' => $exist->name,
				'method' => $code,
				'parent_code' => $info['parent_code'],
				'module' => $info['module'],
				'rtype' => $exist->rtype,
				'sort' => $info['sort'],
				'controller' => $exist->full_controller ? $exist->name : $exist->getFormatCode('base-format'),
			];
			$result = $this->getPointModel('menu')->_addInfo($data);
			if (empty($result)) {
				$message .= implode('=', $data) . '创建失败';
			}
		}
		return $message;
	}

	public function _addInfo($info)
	{
		if (empty($info['module']) || empty($info['code'])) {
			return false;
		}
		$exist = $this->getInfo(['where' => ['module' => $info['module'], 'code' => $info['code']]]);
		if (!empty($exist)) {
			return false;
		}
		return $this->addInfo($info);
	}

	public function getNameFull()
	{
		return $this->formatModule . '_' . $this->name;
	}

	public function getFormatModule($base = false)
	{
		$module = $this->module;
		if ($base) {
		    return strpos($module, '/') !== false ? substr($module, 0, strpos($module, '/')) : $module;
		}
		return str_replace('/', '-', $module);
	}

	public function getFormatCode($type = '')
	{
		$code = $this->code;
		$baseCode = strpos($code, '-') !== false ? substr($code, 0, strpos($code, '-')) : $code;
		if ($type == 'camel') {
            return Inflector::id2camel($baseCode, '_');
		} elseif ($type == 'base') {
			return $baseCode;
		} elseif ($type == 'base-format') {
			return str_replace('_', '-', $baseCode);
		}

		return str_replace('_', '-', $code);
	}

	protected function _someSql()
	{
		//UPDATE `wp_auth_menu` SET `code` = CONCAT(REPLACE(`module`, '/', '-'), '_', `controller`, '_', `method`) WHERE `method` != '';
		//UPDATE `wp_auth_menu` SET `elem_code` = `controller` WHERE `method` != '' AND `module` = 'backend';
	}
}
