<?php

/**
 * AppController
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
    //         Common::jsonReturn(['code' => Constant::RET_NO_LOGIN], true);
    //     }
    // }

    /**
     * @todo Method GET 加入访问判断(app access filter)
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
            if (!$name || !preg_match("/^[a-zA-Z\x{4e00}-\x{9fa5}][\w\x{4e00}-\x{9fa5}]{1,15}$/u", $name)) {
                return Common::jsonReturn(['code' => Constant::RET_INVALID_APP_NAME]);
            }
            $app_key = $request->getPost('app_key');
            if (!$app_key || !preg_match("/^[a-zA-Z][\w\-\_]{1,14}\w$/", $app_key)) {
                return Common::jsonReturn(['code' => Constant::RET_INVALID_APP_KEY]);
            }
            $data = Authority_App::add($name, $app_key);
            $ret = $data ? ['code' => Constant::RET_OK, 'data' => $data] : ['code' => Constant::RET_DATA_CONFLICT];
            break;

        default:
            $ret = ['code' => Constant::RET_METHOD_ERROR];
            break;
        }

        return Common::jsonReturn($ret);
    }

    /**
     * @todo Method GET/POST 加入访问判断(app access filter)
     */
    public function handleAction()
    {
        $request = $this->getRequest();
        $method = $request->getMethod();
        $app_id = $request->getParam('app_id');

        switch ($method) {
        case 'GET':
            $ret = ['code' => Constant::RET_OK, 'data' => (new AppModel())->getById($app_id)];
            break;

        case 'POST':
            $name = $request->getPost('name');
            if (!$name || !preg_match("/^[a-zA-Z\x{4e00}-\x{9fa5}][\w\x{4e00}-\x{9fa5}]{1,15}$/u", $name)) {
                return Common::jsonReturn(['code' => Constant::RET_INVALID_APP_NAME]);
            }
            $count = (new AppModel())->update($app_id, $name);
            $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_UPDATE_FAIL];
            break;

        case 'DELETE':
            $count = Authority_App::remove($app_id);
            $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_NO_FOUND];
            break;

        default:
            $ret = ['code' => Constant::RET_METHOD_ERROR];
            break;
        }

        return Common::jsonReturn($ret);
    }

    /**
     * @todo GET/POST 加入访问判断(app access filter)
     */
    public function usersAction()
    {
        // 给app添加数据源角色用户
        $request = $this->getRequest();
        $method = $request->getMethod();
        $app_id = $request->getParam('app_id');

        switch ($method) {
        case 'GET':
            // 获取对当前app有权限的用户
            $resource_attr = (new ResourceAttrModel())->getById('app', $app_id);
            $user_ids = (new RoleMemberModel())->getUserIdsByRoleId($resource_attr['role_id']);
            $ret = ['code' => Constant::RET_OK, 'data' => (new UserModel())->getById($user_ids)];
            break;

        case 'POST':
            $resource_attr = (new ResourceAttrModel())->getById('app', $app_id);
            $count = (new RoleMemberModel())->add($resource_attr['role_id'], $request->getPost('user_id'));
            $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_CONFLICT];
            break;

        default:
            $ret = ['code' => Constant::RET_METHOD_ERROR];
            break;
        }

        return Common::jsonReturn($ret);
    }

    /**
     * @todo GET/POST 加入访问判断(app access filter)
     */
    public function delUserAction()
    {
        // 删除app数据源角色用户
        $request = $this->getRequest();
        if ($request->getMethod() != 'DELETE') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }

        $resource_attr = (new ResourceAttrModel())->getById('app', $request->getParam('app_id'));
        $count = (new RoleMemberModel())->remove($resource_attr['role_id'], $request->getParam('user_id'));
        $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_NO_FOUND];

        return Common::jsonReturn($ret);
    }

    // 给用户分配组
    public function groupsAction()
    {
        $request = $this->getRequest();
        $method = $request->getMethod();

        $app_id = $request->getParam('app_id');
        /**
         * @todo 检测app_id权限
         */
        // $accessed_app_ids = $user->getAccessedApps();
        // if (!in_array($app_id, $accessed_app_ids)) {
        //     return Common::jsonReturn(['code' => Constant::RET_USER_NO_ACCESS]);
        // }

        // 获取用户可以分配给他人的组
        $user = Factory::getUser($_SESSION['uid']);

        switch ($method) {
        case 'GET':
            // 获取可分配的用户组 getAssignableGroup: parameter 2 is true what means to get admin's assignable group
            $app = (new AppModel())->getById($app_id);
            $ret = ['code' => Constant::RET_OK, 'data' => $user->getAssignableGroup(Factory::getApp($app), true)];
            break;

        case 'POST':
            // 分配用户组
            $group_ids = $request->getPost('groups');
            $user->assignGroups($app_id, $group_ids, $request->getPost('uid'));
            $ret = ['code' => Constant::RET_OK];

        default:
            $ret = ['code' => Constant::RET_METHOD_ERROR];
            break;
        }

        return Common::jsonReturn($ret);
    }
}
