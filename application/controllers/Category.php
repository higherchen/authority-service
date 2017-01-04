<?php

/**
* CategoryController
*
* 权限点分类 判断功能权限
*
* @package controllers
*/

class CategoryController extends Yaf_Controller_Abstract
{

    public function getsAction()
    {
        $request = $this->getRequest();
        $method = $request->getMethod();

        switch ($method) {
        case 'GET':
            $app_id = $request->getParam('app_id');
            $ret = ['code' => Constant::RET_OK, 'data' => (new AuthItemModel())->getByAppIdType($app_id, Constant::CATEGORY)];
            break;

        case 'POST':
            $name = $request->getPost('name');
            if (!$name || !preg_match("/^[a-zA-Z\x{4e00}-\x{9fa5}][\w\x{4e00}-\x{9fa5}-]{1,15}$/u", $name)) {
                return Common::jsonReturn(['code' => Constant::RET_INVALID_CATE_NAME]);
            }

            $app_id = $request->getParam('app_id');
            $id = (new AuthItemModel())->add($name, Constant::CATEGORY, $app_id, $request->getPost('description') ?: '');
            $ret = $id ? ['code' => Constant::RET_OK, 'data' => ['id' => $id]] : ['code' => Constant::RET_DATA_CONFLICT];
            break;

        default:
            $ret = ['code' => Constant::RET_METHOD_ERROR];
            break;
        }

        return Common::jsonReturn($ret);
    }

    public function handleAction()
    {
        $request = $this->getRequest();
        $method = $request->getMethod();

        switch ($method) {
        case 'GET':
            break;

        case 'POST':
            $name = $request->getPost('name');
            if (!$name || !preg_match("/^[a-zA-Z\x{4e00}-\x{9fa5}][\w\x{4e00}-\x{9fa5}-]{1,15}$/u", $name)) {
                return Common::jsonReturn(['code' => Constant::RET_INVALID_CATE_NAME]);
            }
            $item_id = $request->getParam('item_id');
            $count = (new AuthItemModel())->update($item_id, Constant::CATEGORY, $name, $request->getPost('description') ?: '');
            $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_CONFLICT];
            break;

        case 'DELETE':
            $item_id = $request->getParam('item_id');
            $count = (new AuthItemModel())->remove($request->getParam('app_id'), Constant::CATEGORY, $item_id);
            $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_NO_FOUND];

            // 后续处理
            (new AuthItemChildModel())->remove($item_id, '');
            break;

        default:
            $ret = ['code' => Constant::RET_METHOD_ERROR];
            break;
        }

        return Common::jsonReturn($ret);
    }
}
