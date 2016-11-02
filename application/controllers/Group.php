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

    public function getAction()
    {
        $request = $this->getRequest();
        $rule_id = $request->getParam('rule_id');
        $ret = (new AuthItemModel())->getByRuleType($rule_id, Constant::GROUP);
        
        return Common::jsonReturn(['code' => Constant::RET_OK, 'data' => $ret]);
    }

    public function addAction()
    {
        $request = $this->getRequest();

        // Filter
        if ($request->getMethod() !== 'POST') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }
        $name = $request->getPost('name');
        if (!$name || !preg_match("/^[a-zA-Z\x{4e00}-\x{9fa5}][\w\x{4e00}-\x{9fa5}]{1,15}$/u", $name)) {
            return Common::jsonReturn(['code' => Constant::RET_INVALID_GROUP_NAME]);
        }

        $id = (new AuthItemModel())->add($name, Constant::GROUP, $request->getParam('rule_id'), $request->getPost('description') ?: '');
        $ret = $id ? ['code' => Constant::RET_OK, 'data' => ['id' => $id]] : ['code' => Constant::RET_DATA_CONFLICT];

        return Common::jsonReturn($ret);
    }

    public function updateAction()
    {
        $request = $this->getRequest();

        // Filter
        if ($request->getMethod() !== 'POST') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }
        $name = $request->getPost('name');
        if (!$name || !preg_match("/^[a-zA-Z\x{4e00}-\x{9fa5}][\w\x{4e00}-\x{9fa5}]{1,15}$/u", $name)) {
            return Common::jsonReturn(['code' => Constant::RET_INVALID_GROUP_NAME]);
        }

        $id = (new AuthItemModel())->update($request->getParam('item_id'), Constant::GROUP, $name, $request->getPost('description') ?: '');
        $ret = $id ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_CONFLICT];

        return Common::jsonReturn($ret);
    }

    public function removeAction()
    {
        $request = $this->getRequest();

        // Filter
        if ($request->getMethod() !== 'DELETE') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }

        $id = $request->getParam('item_id');
        $count = (new AuthItemModel())->remove($request->getParam('rule_id'), Constant::CATEGORY, $id);
        $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_NO_FOUND];

        // 后续处理
        (new AuthItemChildModel())->remove($id, $id, 'OR');
        (new AuthAssignmentModel())->removeByItemIds($id);

        return Common::jsonReturn($ret);
    }
}
