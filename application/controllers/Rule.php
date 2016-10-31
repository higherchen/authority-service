<?php

/**
 * RuleController 自系统Admin角色可访问
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

    public function get()
    {
        $rules = (new AuthRuleModel())->getAll();
        return Common::jsonReturn(['code' => Constant::RET_OK, 'data' => $rules]);
    }

    public function add()
    {
        $request = $this->getRequest();
        $name = $request->getPost('name');

        // Check method
        if ($request->getMethod() !== 'POST') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }

        // Check rule name
        if (!$name || !preg_match("/^[A-Za-z]([A-Za-z0-9]|-|_){1,15}$/", $name)) {
            return Common::jsonReturn(['code' => Constant::RET_INVALID_RULE_NAME]);
        }

        $id = (new AuthRuleModel())->add($name, $request->getPost('data'));
        $ret = $id ? ['code' => Constant::RET_OK, 'data' => ['id' => $id]] : ['code' => Constant::RET_DATA_CONFLICT];

        return Common::jsonReturn($ret);
    }

    public function update()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $name = $request->getPost('name');

        // Check method
        if ($request->getMethod() !== 'POST') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }

        // Check rule name
        if (!$name || preg_match("/^[A-Za-z]([A-Za-z0-9]|-|_){1,15}$/", $name)) {
            return Common::jsonReturn(['code' => Constant::RET_RULE_NAME_INVALID]);
        }

        $count = (new AuthRuleModel())->update($id, $name, $request->getPost('data'));
        $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_UPDATE_FAIL];

        return Common::jsonReturn($ret);
    }

    public function remove()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id');

        // Check method
        if ($request->getMethod() !== 'DELETE') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }

        $count = (new AuthRuleModel())->remove($id);
        $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_NO_FOUND];

        return Common::jsonReturn($ret);
    }
}
