<?php

class Authority_Rule
{
    protected $rule;
    protected static $items;
    protected static $groups;

    public function __construct($rule)
    {
        if (is_array($rule)) {
            $this->rule = $rule;
        } else {
            $model = new AuthRuleModel();
            $this->rule = is_string($rule) ? $model->getByName($rule) : $model->getById($rule);
        }
    }

    // 获取此auth rule下的所有auth item
    public function getItems()
    {
        if (static::$items === null) {
            static::$items = (new AuthItemModel())->getByRule($this->rule['id']);
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
     * auth_rule新增 - 自动创建admin及role
     *
     * @static
     */
    public static function add($name)
    {
        $id = (new AuthRuleModel())->add($name, substr(md5(time().mt_rand(0, 1000)), 16));
        if (!$id) {
            return false;
        }
        $data['id'] = $id;

        // 自动创建基于此auth_rule的admin组 自动创建基于此auth_rule的role
        if ($admin_id = (new AuthItemModel())->add('Admin', Constant::ADMIN, $id, "{$name} Admin")) {
            $data['admin_id'] = $admin_id;
        }
        if ($role_id = (new RoleModel())->add($name, "{$name} Member", $id)) {
            $data['role_id'] = $role_id;
            (new ResourceAttrModel())->add('auth_rule', $id, $_SESSION['uid'], $role_id);
        }

        return $data;
    }

    /**
     * auth_rule删除 - 影响很大 谨慎调用
     *
     * @static
     */
    public static function remove($id)
    {
        $count = (new AuthRuleModel())->remove($id);

        if ($count) {
            // auth item 相关清理
            $auth_item = new AuthItemModel();
            $deleted = $auth_item->getIdsByRule($id);                           // 即将被删除的auth item
            $auth_item->removeByRuleId($id);                                    // 删除auth item
            (new AuthItemChileModel())->removeMulti($deleted, $deleted, 'OR');  // 删除auth time child  
            (new AuthAssignmentModel())->removeByItemIds($deleted);             // 删除auth assignment

            // resource_attr role 相关清理
            $role = new RoleModel();
            $deleted = $role->getIdsByRule($id);                                // 即将被删除的role
            $role->removeByRoleIds($deleted);                                   // 删除role
            (new RoleMemberModel())->removeByRole($deleted);                    // 删除role_member
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
