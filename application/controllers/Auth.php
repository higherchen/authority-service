<?php

class AuthController extends Yaf_Controller_Abstract
{

    /**
     * 系统登陆.
     *
     * @return json string
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if (!isset($_SESSION['uid'])) {
            $username = $request->getCookie('username');
            $session_id = $request->getCookie('_AJSESSIONID') ? : '';
            if (empty($session_id) || empty($username)) {
                return $this->json(['code' => Constant::RET_NO_LOGIN]);
            } else {
                $ret = DashboardAPI::verifySession($username, $session_id);
                if (!$ret) {
                    session_destroy();
                    return Common::jsonReturn(['code' => Constant::RET_NO_LOGIN]);
                } else {
                    $user = (new UserModel())->getByName($ret['username']);
                    if (!$user) {
                        return Common::jsonReturn(['code' => Constant::RET_NO_PERM]);
                    }
                    $_SESSION['uid'] = $user['id'];
                }
            }
        }

        return Common::jsonReturn(UserLogged::getUser());
    }
}
