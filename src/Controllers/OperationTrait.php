<?php

namespace Swoolecan\Baseapp\Controllers;

trait OperationTrait
{
	//use TraitAdd;
	//use TraitAddMul;
	//use TraitCommon;
	//use TraitDelete;
	//use TraitImexport;
	//use TraitListinfo;
	//use TraitListinfoTree;
	//use TraitUpdate;
	//use TraitPriv;
	//use TraitView;
    //use Rest;

    public function listinfo()
    {
        $params = $this->request->all();
        $scene = $params['point_scene'] ?? 'list';
        
        $repository = $this->getRepositoryObj();
        $repository = $this->dealCriteria($scene, $repository, $params);
        if (in_array($scene, ['list'])) {
            $perPage = $params['per_page'] ?? 25;
            $list = $repository->paginate(intval($perPage));
        } else {
            $list = $repository->all();
        }

        $collectionClass = $repository->getCollectionClass();
        $collection = new $collectionClass($list, $scene, $repository);
        return $collection->toResponse();
        //$list = $repository->all();//null, $params, (int) $pageSize);
        //$list = $repository->getByCriteria($criteria)->all();
        return $this->success($datas);
    }

    public function listinfoTree()
    {
        $resource = $this->resource->getResourceCode(get_called_class());
        $repository = $this->getRepositoryObj();
        /*$infos = $repository->cacheDatas($resource, 'tree', false);
        $collectionClass = $this->getCollectionClass();
        $collection = new $collectionClass($infos, 'tree', $repository);
        $collection->setModel($repository->getModel());
        return $collection->toResponse();*/
        return $this->success($repository->getTreeInfos());
        return $this->success($infos);
    }

    public function add()
    {
        $repository = $this->getRepositoryObj();
        $request = $this->getRequestObj('add', $repository);
        $data = $request->getInputDatas('add');
        $result = $repository->create($data);
        return $this->success($result);
    }

    public function update()
    {
        $repository = $this->getRepositoryObj();
        $request = $this->getRequestObj('update', $repository);
        $info = $this->getPointInfo($repository, $request);

        $data = $request->getInputDatas('update');
        if (empty($data)) {
            return $this->throwException(422, '没有输入参数');
        }
        $data = $request->validated();
        $result = $repository->updateInfo($info, $data);
        return $this->success([]);
    }

    public function view()
    {
        $repository = $this->getRepositoryObj();
        $request = $this->getRequestObj('', $repository);
        $info = $this->getPointInfo($repository, $request);

        //$result->permissions;
        return $this->success($info);
    }

    public function delete()
    {
        $repository = $this->getRepositoryObj();
        $request = $this->getRequestObj('', $repository);
        $info = $this->getPointInfo($repository, $request, false);

        $number = 0;
        if (empty($info)) {
            $ids = $request->input($repository->getKeyName());
            foreach ($ids as $id) {
                $info = $repository->find($id);
                if (!empty($info)) {
                    $info->delete();
                    $number++;
                }
            }
        } else {
            $result = $info->delete();
            $number = 1;
        }

        //$result->permissions;
        if ($number) {
            return $this->success(['message' => "成功删除了{$number}条信息"]);
        }
        return $this->success(['message' => '删除失败']);
    }

    protected function getPointInfo($repository, $request, $throw = true)
    {
        $repository = $this->getRepositoryObj();
        $pointKey = $request->input('point_Key', false);
        $key = $pointKey ? $pointKey : $repository->getKeyName();
        $value = $request->route($key);
        if (empty($key)) {
            return $throw ? $this->throwException(422, '参数有误') : false;
        }
        $info = $repository->find($value);
        if (empty($info)) {
            return $throw ? $this->throwException(404, '信息不存在') : false;
        }

        $limitPriv = $request->getAttribute('limitPriv');
        if ($limitPriv) {
            $priv = $info->checkLimitPriv($limitPriv);
            if (empty($priv)) {
                return $throw ? $this->throwException(403, '您没有执行该操作的权限') : false;
            }
        }
        return $info;
        //echo $this->request->path(); print_R($this->request->query()); print_R($this->request->route('id'));
    }

    public function allowMulDelete()
    {
        return true;
    }
}
