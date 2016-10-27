<?php

class IndexController extends Yaf_Controller_Abstract
{

    public function authAction()
    {
        var_dump((new UserModel())->getById(1));
    }
}
