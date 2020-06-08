<?php

namespace backend\controllers;

use Yii;
use yii\rbac\Item;
use yii\web\NotFoundHttpException;
use backend\models\AuthItem;
use backend\models\Menu as MenuModel;
use backend\models\searchs\AuthItem as AuthItemSearch;

/**
 * AuthItemController implements the CRUD actions for AuthItem model.
 */
class RoleController extends Controller
{
	public $currentSort = 'backend';
    /**
     * Lists all AuthItem models.
     * @return mixed
     */
    public function actionListinfo($view = null, $dataProvider = null)
    {
        $this->showFilter = true;
        $this->searchModel = new AuthItemSearch(['type' => Item::TYPE_ROLE]);
        $dataProvider = $this->searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render($this->listinfoView, [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAdd()
    {
        $model = new AuthItem(null);
        $model->type = Item::TYPE_ROLE;
        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->name]);
        }

        return $this->render('@views/backend/common/change', [
            'model' => $model,
            'currentView' => $this->viewPrefix,
            'type' => 'add',
        ]);
    }

    /**
     * Updates an existing AuthItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param  string $id
     * @return mixed
     */
    public function actionUpdate($id = 0)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->name]);
        }

        return $this->render('@views/backend/common/change', [
            'model' => $model,
            'currentView' => $this->viewPrefix,
            'type' => 'update',
        ]);
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param  string $id
     * @return mixed
     */
    public function actionDelete($id = '')
    {
        $model = $this->findModel($id);
        Yii::$app->getAuthManager()->remove($model->item);

        return $this->redirect(['listinfo']);
    }

    public function actionView($id = null, $myInfo = false)
    {
        return $this->roleAuthority($id);
    }

    public function actionAuthority($id)
    {
        return $this->roleAuthority($id, true);
    }

    protected function roleAuthority($id, $canWrite = false)
    {
        $model = $this->findModel($id);

        $menuModel = new MenuModel();
        $menuInfos = $menuModel->getFormatedInfos();

        $manager = Yii::$app->getAuthManager();
        $role = $manager->getRole($id);
        $permissions = $manager->getPermissionsByRole($id);
        $permissionKeys = array_keys($permissions);

        if (!Yii::$app->getRequest()->isPost || !$canWrite) {
            return $this->render('authority', ['menuModel' => $menuModel, 'infos' => $menuInfos, 'permissionKeys' => $permissionKeys]);
        }

        $menuIds = Yii::$app->getRequest()->post('menu_ids');
        //$manager->removeChildren($role);

        foreach ((array) $menuIds as $permissionName) {
            if (in_array($permissionName, $permissionKeys)) {
                unset($permissionKeys[array_search($permissionName, $permissionKeys)]);
                continue ;
            }
            if (($permission = $manager->getPermission($permissionName)) === null) {
                $permission = $manager->createPermission($permissionName);
                $manager->add($permission);
            }
            $manager->addChild($role, $permission);
        }

        foreach ((array) $permissionKeys as $permissionKey) {
            $permission = $manager->getPermission($permissionKey);
            $manager->removeChild($role, $permission);
        }

        return $this->redirect(['authority', 'id' => $model->name]);
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param  string        $id
     * @return AuthItem      the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id = null, $throwException = true, $model = null)
    {
        $item = Yii::$app->getAuthManager()->getRole($id);
        if ($item) {
            return new AuthItem($item);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
