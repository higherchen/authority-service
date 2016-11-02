<?php

/**
 * RuleController 自系统Admin角色可访问
 * 模块：1-基本信息管理
 * 
 * @access public
 */

class RuleController extends Yaf_Controller_Abstract
{
    public function init()
    {
        $user = Factory::getUser($_SESSION['uid']);
        if (!$user->isAdmin(Factory::getRule('authority'))) {
            return Common::jsonReturn(['code' => Constant::RET_NO_LOGIN], true);
        }
    }

    public function getAction()
    {
        return Common::jsonReturn(['code' => Constant::RET_OK, 'data' => (new AuthRuleModel())->getAll()]);
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $name = $request->getPost('name');

        // Filter
        if ($request->getMethod() !== 'POST') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }
        if (!$name || !preg_match("/^[a-zA-Z][\w\-\_]{1,14}\w$/", $name)) {
            return Common::jsonReturn(['code' => Constant::RET_INVALID_RULE_NAME]);
        }

        $data = Authority_Rule::add($name);
        $ret = $data ? ['code' => Constant::RET_OK, 'data' => $data] : ['code' => Constant::RET_DATA_CONFLICT];

        return Common::jsonReturn($ret);
    }

    public function updateAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $name = $request->getPost('name');

        // Filter
        if ($request->getMethod() !== 'POST') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }
        if (!$name || preg_match("/^[a-zA-Z][\w\-\_]{1,14}\w$/", $name)) {
            return Common::jsonReturn(['code' => Constant::RET_RULE_NAME_INVALID]);
        }

        $count = (new AuthRuleModel())->update($id, $name, $request->getPost('data'));
        $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_UPDATE_FAIL];

        return Common::jsonReturn($ret);
    }

    public function removeAction()
    {
        $request = $this->getRequest();

        // Filter
        if ($request->getMethod() !== 'DELETE') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }

        $id = $request->getParam('id');
        $count = Authority_Rule::remove($id);
        $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_NO_FOUND];

        return Common::jsonReturn($ret);
    }

    public function userAction()
    {
        // 给auth rule添加数据源角色用户
        $request = $this->getRequest();

        // Filter
        if ($request->getMethod() !== 'POST') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }

        $role = Authority_Rule::getRoleByRule($request->getParam('id'));
        $count = (new RoleMemberModel())->add($role['id'], $request->getPost('user_id'));
        $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_CONFLICT];

        return Common::jsonReturn($ret);
    }

    public function removeUserAction()
    {
        // 删除auth rule数据源角色用户
        $request = $this->getRequest();

        // Filter
        if ($request->getMethod() != 'DELETE') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }

        $role = Authority_Rule::getRoleByRule($request->getParam('id'));
        $count = (new RoleMemberModel())->remove($role['id'], $request->getPost('user_id'));
        $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_NO_FOUND];

        return Common::jsonReturn($ret);
    }
}
