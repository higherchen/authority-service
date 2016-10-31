<?php

class UserController extends Yaf_Controller_Abstract
{
    public function init()
    {
        if (!$_SESSION['uid']) {
            return Common::jsonReturn(['code' => Constant::RET_USER_NO_ACCESS], true);
        }
    }

    public function get()
    {
        $request = $this->getRequest();
        $name = $request->getQuery('name');
        $page = $request->getQuery('page') ?: 1;
        $pagesize = $request->getQuery('pagesize') ?: '';
        $ret = (new UserModel())->getPagedByName($page, $pagesize, $name);
        
        return Common::jsonReturn(['code' => Constant::RET_OK, 'data' => $ret]);
    }

    public function add()
    {
        $request = $this->getRequest();

        // Check method
        if ($request->getMethod() !== 'POST') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }

        // Check username
        $username = $request->getQuery('username');
        if (!$username || !preg_match("/^[A-Za-z][A-Za-z0-9]{1,15}$/", $username)) {
            return Common::jsonReturn(['code' => Constant::RET_INVALID_USERNAME]);
        }

        // Check nickname
        $nickname = $request->getQuery('nickname');
        if (!$nickname || !preg_match("/^[a-zA-Z\x{4e00}-\x{9fa5}][\w\x{4e00}-\x{9fa5}]{1,15}$/u", $nickname)) {
            return Common::jsonReturn(['code' => Constant::RET_INVALID_NICKNAME]);
        }

        // Check email
        $email = $request->getQuery('email');
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Common::jsonReturn(['code' => Constant::RET_INVALID_EMAIL]);
        }

        // Check telephone
        $telephone = $request->getQuery('telephone');
        if ($telephone && !preg_match("/^\+?\d{3,15}$/", $telephone)) {
            return Common::jsonReturn(['code' => Constant::RET_INVALID_TELEPHONE]);
        }

        $id = (new UserModel())->add($username, $nickname, $email, $telephone);
        $ret = $id ? ['code' => Constant::RET_OK, 'data' => ['id' => $id]] : ['code' => Constant::RET_DATA_CONFLICT];

        return Common::jsonReturn($ret);
    }

    public function update()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id');

        $nickname = $request->getQuery('nickname');
        if (!$nickname || !preg_match("/^[a-zA-Z\x{4e00}-\x{9fa5}][\w\x{4e00}-\x{9fa5}]{1,15}$/u", $nickname)) {
            return Common::jsonReturn(['code' => Constant::RET_INVALID_NICKNAME]);
        }

        $email = $request->getQuery('email');
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Common::jsonReturn(['code' => Constant::RET_INVALID_EMAIL]);
        }

        $telephone = $request->getQuery('telephone');
        if ($telephone && !preg_match("/^\+?\d{3,15}$/", $telephone)) {
            return Common::jsonReturn(['code' => Constant::RET_INVALID_TELEPHONE]);
        }

        $id = (new UserModel())->update($id, $nickname, $email, $telephone);
        $ret = $id ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_UPDATE_FAIL];

        return Common::jsonReturn($ret);
    }

    public function remove()
    {
        // 自系统Admin角色可访问
        $user = Factory::getUser($_SESSION['uid']);
        if (!$user->isAdmin(Factory::getRule('authority'))) {
            return Common::jsonReturn(['code' => Constant::RET_NO_LOGIN], true);
        }

        $request = $this->getRequest();
        $id = $request->getParam('id');

        // Check method
        if ($request->getMethod() !== 'DELETE') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }

        $count = (new UserModel())->remove($id);
        $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_NO_FOUND];

        return Common::jsonReturn($ret);
    }
}
