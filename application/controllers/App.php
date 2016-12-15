<?php

/**
 * AppController 自系统Admin角色可访问
 * 模块：1-基本信息管理
 * 
 * @access public
 */

class AppController extends Yaf_Controller_Abstract
{
    // public function init()
    // {
    //     $user = Factory::getUser($_SESSION['uid']);
    //     if (!$user->isAdmin(Factory::getApp('authority'))) {
    //         return Common::jsonReturn(['code' => Constant::RET_NO_LOGIN], true);
    //     }
    // }

    /**
     * @todo Method GET 加入访问判断(rule filter)
     * @todo Method POST 加入访问限制
     */
    public function getsAction()
    {
        $request = $this->getRequest();
        $method = $request->getMethod();

        switch ($method) {
            case 'GET':
                $ret = ['code' => Constant::RET_OK, 'data' => (new AppsModel())->getAll()];
                break;

            case 'POST':
                $name = $request->getPost('name');
                if (!$name || !preg_match("/^[a-zA-Z][\w\-\_]{1,14}\w$/", $name)) {
                    return Common::jsonReturn(['code' => Constant::RET_INVALID_RULE_NAME]);
                }
                $data = Authority_Rule::add($name);
                $ret = $data ? ['code' => Constant::RET_OK, 'data' => $data] : ['code' => Constant::RET_DATA_CONFLICT];
                break;

            default:
                $ret = ['code' => Constant::RET_METHOD_ERROR];
                break;
        }

        return Common::jsonReturn($ret);
    }

    /**
     * @todo Method GET/POST 加入访问判断(rule filter)
     */
    public function handleAction()
    {
        $request = $this->getRequest();
        $method = $request->getMethod();
        $rule_id = $request->getParam('rule_id');

        switch ($method) {
            case 'GET':
                $ret = ['code' => Constant::RET_OK, 'data' => (new AuthRuleModel())->getById($rule_id)];
                break;

            case 'POST':
                $name = $request->getPost('name');
                if (!$name || preg_match("/^[a-zA-Z][\w\-\_]{1,14}\w$/", $name)) {
                    return Common::jsonReturn(['code' => Constant::RET_RULE_NAME_INVALID]);
                }
                $count = (new AuthRuleModel())->update($rule_id, $name, $request->getPost('data'));
                $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_UPDATE_FAIL];
                break;

            case 'DELETE':
                $count = Authority_Rule::remove($rule_id);
                $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_NO_FOUND];
                break;

            default:
                $ret = ['code' => Constant::RET_METHOD_ERROR];
                break;
        }

        return Common::jsonReturn($ret);
    }

    /**
     * @todo GET/POST 加入访问判断(rule filter)
     */
    public function usersAction()
    {
        // 给auth rule添加数据源角色用户
        $request = $this->getRequest();
        $method = $request->getMethod();
        $rule_id = $request->getParam('rule_id');

        switch ($method) {
            case 'GET':
                // 获取当前rule的用户
                $role = Authority_Rule::getRoleByRule($rule_id);
                $user_ids = (new RoleMemberModel())->getUserIdsByRoleId($role['id']);
                $ret = ['code' => Constant::RET_OK, 'data' => (new UserModel())->getById($user_ids)];
                break;

            case 'POST':
                $role = Authority_Rule::getRoleByRule($rule_id);
                $count = (new RoleMemberModel())->add($role['id'], $request->getPost('user_id'));
                $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_CONFLICT];
                break;

            default:
                $ret = ['code' => Constant::RET_METHOD_ERROR];
                break;
        }

        return Common::jsonReturn($ret);
    }

    /**
     * @todo GET/POST 加入访问判断(rule filter)
     */
    public function handleUserAction()
    {
        // 删除auth rule数据源角色用户
        $request = $this->getRequest();
        if ($request->getMethod() != 'DELETE') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }

        $role = Authority_Rule::getRoleByRule($request->getParam('rule_id'));
        $count = (new RoleMemberModel())->remove($role['id'], $request->getParam('user_id'));
        $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_NO_FOUND];

        return Common::jsonReturn($ret);
    }
}
