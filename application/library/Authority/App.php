<?php

class Authority_App
{
    protected $app;
    protected static $items;
    protected static $groups;

    public function __construct($app)
    {
        if (is_array($app)) {
            $this->app = $app;
        } else {
            $model = new AppModel();
            $this->app = is_string($app) ? $model->getByAppKey($app) : $model->getById($app);
        }
    }

    // 获取此app下的所有auth item
    public function getItems()
    {
        if (static::$items === null) {
            static::$items = (new AuthItemModel())->getByAppId($this->app['id']);
        }

        return static::$items;
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
     * 新增app - 自动创建资源权限
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
        (new ResourceAttrModel())->add('app', $id, $_SESSION['uid'], 0);

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

    /**
     * 根据rule_id获取基于此auth_rule的基本role
     *
     * @static
     */
    public static function getRoleByRule($rule_id)
    {
        $rule = (new AuthRuleModel())->getById($rule_id);

        return (new RoleModel())->getByNameAndRuleId($rule['id'], $rule['name']);
    }
}
