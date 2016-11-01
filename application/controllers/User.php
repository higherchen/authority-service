<?php

class UserController extends Yaf_Controller_Abstract
{
    public function init()
    {
        // if (!$_SESSION['uid']) {
        //     return Common::jsonReturn(['code' => Constant::RET_USER_NO_ACCESS], true);
        // }
    }

    // maybe unused
    public function getAction()
    {
        $request = $this->getRequest();
        $name = $request->getQuery('name');
        $page = $request->getQuery('page') ?: 1;
        $pagesize = $request->getQuery('pagesize') ?: '';
        $ret = (new UserModel())->getPagedByName($page, $pagesize, $name);
        
        return Common::jsonReturn(['code' => Constant::RET_OK, 'data' => $ret]);
    }

    // maybe only can be called by api with appname and sign
    public function addAction()
    {
        $request = $this->getRequest();
        // Check method
        if ($request->getMethod() !== 'POST') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }

        // Check username
        $username = $request->getPost('username');
        if (!$username || !preg_match("/^[A-Za-z][A-Za-z0-9]{1,15}$/", $username)) {
            return Common::jsonReturn(['code' => Constant::RET_INVALID_USERNAME]);
        }

        // Check nickname
        $nickname = $request->getPost('nickname');
        if (!$nickname || !preg_match("/^[a-zA-Z\x{4e00}-\x{9fa5}][\w\x{4e00}-\x{9fa5}]{1,15}$/u", $nickname)) {
            return Common::jsonReturn(['code' => Constant::RET_INVALID_NICKNAME]);
        }

        // Check email
        $email = $request->getPost('email');
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Common::jsonReturn(['code' => Constant::RET_INVALID_EMAIL]);
        }

        // Check telephone
        $telephone = $request->getPost('telephone');
        if ($telephone && !preg_match("/^\+?\d{3,15}$/", $telephone)) {
            return Common::jsonReturn(['code' => Constant::RET_INVALID_TELEPHONE]);
        }

        $id = (new UserModel())->add($username, $nickname, $email, $telephone);
        $ret = $id ? ['code' => Constant::RET_OK, 'data' => ['id' => $id]] : ['code' => Constant::RET_DATA_CONFLICT];

        return Common::jsonReturn($ret);
    }

    public function updateAction()
    {
        $request = $this->getRequest();

        // Check method
        if ($request->getMethod() !== 'POST') {
            return Common::jsonReturn(['code' => Constant::RET_METHOD_ERROR]);
        }

        $id = $request->getParam('id');
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

        $id = (new UserModel())->update($id, $nickname, $email, $telephone);
        $ret = $id ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_UPDATE_FAIL];

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

        $count = (new UserModel())->remove($id);
        $ret = $count ? ['code' => Constant::RET_OK] : ['code' => Constant::RET_DATA_NO_FOUND];

        return Common::jsonReturn($ret);
    }
}
