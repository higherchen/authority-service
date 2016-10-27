<?php

class Bootstrap extends Yaf_Bootstrap_Abstract
{
    //全局设置 初始化
    public function _initYaf()
    {
        $arrConfig = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $arrConfig);
        Yaf_Dispatcher::getInstance()->autoRender(false)->disableView();
    }

    public function _initPlugin(Yaf_Dispatcher $dispatcher)
    {
        //注册一个插件
        $objCommonPlugin = new CommonPlugin();
        $dispatcher->registerPlugin($objCommonPlugin);
    }

    //路由注册
    public function _initRoute(Yaf_Dispatcher $dispatcher)
    {
        $router = Yaf_Dispatcher::getInstance()->getRouter();
        if ($config = Yaf_Registry::get('config')->routes) {
            $router->addConfig($config);
        }
    }
}
