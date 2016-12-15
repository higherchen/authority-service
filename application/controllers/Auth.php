<?php

class AuthController extends Yaf_Controller_Abstract
{

    /**
     * 验证登录，返回权限
     *
     * @return json string
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        $method = $request->getMethod();

        switch ($method) {
            case 'GET':
                $ret = isset($_SESSION['uid']) ? ['code' => Constant::RET_OK, 'data' => UserLogged::getUser()] : ['code' => Constant::RET_NO_LOGIN];
                break;

            case 'POST':
                $username = $request->getPost('username');
                $password = $request->getPost('password');
                $user = (new UserModel())->getByName($username);
                if (!$user) {
                    $ret = ['code' => Constant::RET_NO_USER];
                } else {
                    if ($password == md5($user['password'])) {
                        $_SESSION['uid'] = $user['id'];
                        $ret = ['code' => Constant::RET_OK, 'data' => UserLogged::getUser()];
                    } else {
                        $ret = ['code' => Constant::RET_USER_PWD_ERROR];
                    }
                }
                break;

            default:
                $ret = ['code' => Constant::RET_METHOD_ERROR];
                break;
        }

        return Common::jsonReturn($ret);
    }
}
