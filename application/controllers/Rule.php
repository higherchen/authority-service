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

        // Data Filter
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

        // Check method
        if ($request->getMethod() !== 'POST') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }

        // Check rule name
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
        $id = $request->getParam('id');

        // Check method
        if ($request->getMethod() !== 'DELETE') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }

        $count = Authority_Rule::remove($id);
        $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_NO_FOUND];

        return Common::jsonReturn($ret);
    }
}
