<?php

class Authority_Rule
{
    protected $rule;
    protected static $items;
    protected static $groups;

    public function __construct($rule_name)
    {
        $this->rule = (new AuthRuleModel())->getByName($rule_name);
    }

    public function getItems()
    {
        if (static::$items === null) {
            static::$items = (new AuthItemModel())->getByRule($this->rule['id']);
        }

        return static::$items;
    }

    public function getAdmin()
    {
        foreach ($this->getItems() as $item) {
            if ($item['type'] == Constant::ADMIN) {
                return $item['id'];
            }
        }

        return false;
    }

    public static function add($name)
    {
        $id = (new AuthRuleModel())->add($name, md5(time().mt_rand(0, 1000)));
        if (!$id) {
            return false;
        }
        $data['id'] = $id;

        // 自动创建基于此auth_rule的admin组 自动创建基于此auth_rule的role
        if ($admin_id = (new AuthItemModel())->add('Admin', Constant::ADMIN, $id)) {
            $data['admin_id'] = $admin_id;
        }
        if ($role_id = (new RoleModel())->add($name, "{$name}操作组", $id)) {
            $data['role_id'] = $role_id;
            (new ResourceAttrModel())->add('auth_rule', $id, $_SESSION['uid'], $role_id);
        }

        return $data;
    }

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
}
