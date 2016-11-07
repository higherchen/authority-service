<?php

class UserController extends Yaf_Controller_Abstract
{
    public function init()
    {
        // if (!$_SESSION['uid']) {
        //     return Common::jsonReturn(['code' => Constant::RET_USER_NO_ACCESS], true);
        // }
    }

    public function getsAction()
    {
        $request = $this->getRequest();
        $method = $request->getMethod();

        switch ($method) {
            case 'GET':
                $name = $request->getQuery('name');
                $page = $request->getQuery('page') ?: 1;
                $pagesize = $request->getQuery('pagesize') ?: '';
                $ret = ['code' => Constant::RET_OK, 'data' => (new UserModel())->getPagedByName($page, $pagesize, $name)];
                break;

            case 'POST':
                $username = $request->getPost('username');
                if (!$username || !preg_match("/^[A-Za-z][A-Za-z0-9]{1,15}$/", $username)) {
                    return Common::jsonReturn(['code' => Constant::RET_INVALID_USERNAME]);
                }
                $nickname = $request->getPost('nickname');
                if (!$nickname || !preg_match("/^[a-zA-Z\x{4e00}-\x{9fa5}][\w\x{4e00}-\x{9fa5}]{1,15}$/u", $nickname)) {
                    return Common::jsonReturn(['code' => Constant::RET_INVALID_NICKNAME]);
                }
                $email = $request->getPost('email');
                if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return Common::jsonReturn(['code' => Constant::RET_INVALID_EMAIL]);
                }
                $telephone = $request->getPost('telephone');
                if ($telephone && !preg_match("/^\+?\d{3,15}$/", $telephone)) {
                    return Common::jsonReturn(['code' => Constant::RET_INVALID_TELEPHONE]);
                }

                $id = (new UserModel())->add($username, $nickname, $email, $telephone);
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
                $ret = ['code' => Constant::RET_OK, 'data' => (new UserModel())->getById($request->getParam('id'))];
                break;

            case 'POST':
                $nickname = $request->getPost('nickname');
                if (!$nickname || !preg_match("/^[a-zA-Z\x{4e00}-\x{9fa5}][\w\x{4e00}-\x{9fa5}]{1,15}$/u", $nickname)) {
                    return Common::jsonReturn(['code' => Constant::RET_INVALID_NICKNAME]);
                }
                $email = $request->getPost('email');
                if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return Common::jsonReturn(['code' => Constant::RET_INVALID_EMAIL]);
                }
                $telephone = $request->getPost('telephone');
                if ($telephone && !preg_match("/^\+?\d{3,15}$/", $telephone)) {
                    return Common::jsonReturn(['code' => Constant::RET_INVALID_TELEPHONE]);
                }

                $id = $request->getParam('id');
                $count = (new UserModel())->update($id, $nickname, $email, $telephone);
                $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_UPDATE_FAIL];
                break;

            case 'DELETE':
                $id = $request->getParam('id');
                $count = Authority_User::remove($id);
                $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_NO_FOUND];
                break;

            default:
                $ret = ['code' => Constant::RET_METHOD_ERROR];
                break;
        }


        return Common::jsonReturn($ret);
    }

    // 给用户分配组
    public function groupsAction()
    {
        $request = $this->getRequest();
        $method = $request->getMethod();

        $rule_id = $request->getParam('rule_id');
        /**
         * @todo 检测rule_id权限
         */
        // $accessed_rule_ids = $user->getAccessedRules();
        // if (!in_array($rule_id, $accessed_rule_ids)) {
        //     return Common::jsonReturn(['code' => Constant::RET_USER_NO_ACCESS]);
        // }

        // 获取用户可以分配给他人的组
        $user = Factory::getUser($_SESSION['uid']);

        switch ($method) {
            case 'GET':
                // 获取可分配的用户组 getAssignableGroup: parameter 2 is true what means to get admin's assignable group
                $rule = (new RuleModel())->getById($rule_id);
                $ret = ['code' => Constant::RET_OK, 'data' => $user->getAssignableGroup(Factory::getRule($rule), true)];
                break;

            case 'POST':
                // 分配用户组
                $group_ids = $request->getPost('groups');
                $user->assignGroups($rule_id, $group_ids, $request->getPost('uid'));
                $ret = ['code' => Constant::RET_OK];

            default:
                $ret = ['code' => Constant::RET_METHOD_ERROR];
                break;
        }

        return Common::jsonReturn($ret);
    }
}
