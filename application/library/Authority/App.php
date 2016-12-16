<?php

class Authority_App
{
    protected $app;
    protected $items;

    /**
     * 认证服务接入app构造函数
     *
     * @param array|string $app
     *
     */
    public function __construct($app)
    {
        $this->app = is_array($app) ? $app : (new AppModel())->getByAppKey($app);
    }

    // 获取此app下的所有auth item
    public function getItems()
    {
        if ($this->items === null) {
            $this->items = (new AuthItemModel())->getByAppId($this->app['id']);
        }

        return $this->items;
    }

    // 获取ADMIN组ID
    public function getAdmin()
    {
        foreach ($this->getItems() as $item) {
            if ($item['type'] == Constant::ADMIN) {
                return $item['id'];
            }
        }

        return false;
    }

    /**
     * 新增app
     *
     * @static
     */
    public static function add($name, $app_key)
    {
        $id = (new AppModel())->add($name, $app_key, substr(md5(time().mt_rand(0, 1000)), 16));
        if (!$id) {
            return false;
        }
        $data['id'] = $id;
        // 自动创建基于app的资源角色组
        $role_id = (new RoleModel())->add($app_key);
        // 自动创建资源权限
        (new ResourceAttrModel())->add('app', $id, $_SESSION['uid'], $role_id);

        return $data;
    }

    /**
     * 删除app - 影响很大 谨慎调用
     *
     * @static
     */
    public static function remove($id)
    {
        $count = (new AppModel())->remove($id);

        if ($count) {
            // auth item 相关清理
            $auth_item = new AuthItemModel();
            $deleted = $auth_item->getIdsByAppId($id);                          // 即将被删除的auth item
            $auth_item->removeByAppId($id);                                     // 删除auth item
            (new AuthItemChileModel())->removeMulti($deleted, $deleted, 'OR');  // 删除auth time child
            (new AuthAssignmentModel())->removeByItemIds($deleted);             // 删除auth assignment
            (new ResourceAttrModel())->remove('app', $id);                      // resource_attr role 相关清理
        }

        return $count;
    }
}
