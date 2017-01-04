<?php

/**
* GroupController
*
* 用户组 数据源权限 => 功能权限
*
* @package controllers
*/

class GroupController extends Yaf_Controller_Abstract
{

    public function getsAction()
    {
        $request = $this->getRequest();
        $method = $request->getMethod();

        switch ($method) {
        case 'GET':
            $ret = ['code' => Constant::RET_OK, 'data' => (new AuthItemModel())->getByAppIdType($request->getParam('app_id'), Constant::GROUP)];
            break;

        case 'POST':
            $name = $request->getPost('name');
            if (!$name || !preg_match("/^[a-zA-Z\x{4e00}-\x{9fa5}][\w\x{4e00}-\x{9fa5}-]{1,15}$/u", $name)) {
                return Common::jsonReturn(['code' => Constant::RET_INVALID_GROUP_NAME]);
            }

            $id = (new AuthItemModel())->add($name, Constant::GROUP, $request->getParam('app_id'), $request->getPost('description') ?: '');
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
            $ret = ['code' => Constant::RET_OK, 'data' => (new AuthItemModel())->getById($request->getParam('item_id'))];
            break;

        case 'POST':
            $name = $request->getPost('name');
            if (!$name || !preg_match("/^[a-zA-Z\x{4e00}-\x{9fa5}][\w\x{4e00}-\x{9fa5}-]{1,15}$/u", $name)) {
                return Common::jsonReturn(['code' => Constant::RET_INVALID_GROUP_NAME]);
            }

            $id = (new AuthItemModel())->update($request->getParam('item_id'), Constant::GROUP, $name, $request->getPost('description') ?: '');
            $ret = $id ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_CONFLICT];
            break;

        case 'DELETE':
            $id = $request->getParam('item_id');
            $count = (new AuthItemModel())->remove($request->getParam('app_id'), Constant::GROUP, $id);
            $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_NO_FOUND];

            // 后续处理
            (new AuthItemChildModel())->remove($id, $id, 'OR');
            (new AuthAssignmentModel())->removeByItemIds($id);
            break;

        default:
            $ret = ['code' => Constant::RET_METHOD_ERROR];
            break;
        }

        return Common::jsonReturn($ret);
    }
}
