<?php

class UserController extends Yaf_Controller_Abstract
{

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
}
