<?php

class Authority_User
{
    protected $user;
    protected static $assignments;
    protected static $rules;

    public function __construct($user)
    {
        if (is_array($user)) {
            $this->user = $user;
        } else {
            $model = new UserModel();
            $this->user = is_string($user) ? $model->getByName($user) : $model->getById($user);
        }
    }

    // 获取用户信息
    public function getUser()
    {
        return $this->user;
    }

    /**
     * auth item 用户功能权限相关
     *
     * @access public
     */

    public function getAssignments()
    {
        if (static::$assignments === null) {
            static::$assignments = (new AuthAssignmentModel())->getItemIdsByUserId($this->user['id']);
        }

        return static::$assignments;
    }

    // 判断用户在指定规则下是否是admin
    public function isAdmin(Authority_Rule $rule)
    {
        $assignments = $this->getAssignments();
        $admin = $rule->getAdmin();
        return in_array($admin, $assignments, true);
    }

    // 获取用户权限相关信息
    public function getAuth(Authority_Rule $rule)
    {
        $groups = $super_points = $points = [];
        $items = $rule->getItems();

        // get user groups
        $group_ids = $this->getAssignments();
        if ($group_ids) {
            foreach ($group_ids as $group_id) {
                $item = $items[$group_id];
                $groups[] = ['id' => $item['id'], 'type' => $item['type'], 'name' => $item['name'], 'description' => $item['description']];
            }

            if ($this->isAdmin($rule)) {
                foreach ($items as $item) {
                    if ($item['type'] == Constant::POINT) {
                        $super_points[] = $item['data'];
                    }
                }
            } else {
                $auth_item_child = new AuthItemChildModel();
                foreach ($group_ids as $group_id) {
                    foreach ($auth_item_child->getChildren($group_id) as $child) {
                        if ($items[$child]['type'] == Constant::POINT) {
                            if ($items[$group_id]['type'] == Constant::ORG) {
                                $super_points[] = $items[$child]['data'];
                            }
                            if ($items[$group_id]['type'] == Constant::GROUP) {
                                $points[] = $items[$child]['data'];
                            }
                        }
                    }
                }
                $super_points = array_unique($super_points);
                $points = array_diff(array_unique($points), $super_points);
            }
        }
        return ['groups' => $groups, 'super_points' => $super_points, 'points' => $points];
    }

    public function getAssignableGroup(Authority_Rule $rule, $force_admin = false)
    {
        $groups = [];
        $items = $rule->getItems();

        if ($this->isAdmin($rule) || $force_admin) {
            foreach ($items as $item) {
                if ($item['type'] == Constant::ORG || $item['type'] == Constant::GROUP) {
                    $groups[] = [
                        'id' => $item['id'],
                        'type' => $item['type'],
                        'name' => $item['name'],
                        'description' => $item['description'],
                    ];
                }
            }
        } else {
            // 获取用户所在的权限组
            $auth_item_child = new AuthItemChildModel();
            foreach ($this->getAssignments() as $group_id) {
                if ($items[$group_id]['type'] == Constant::ORG) {
                    foreach ($auth_item_child->getChildren($group_id) as $child) {
                        if ($items[$child]['type'] == Constant::GROUP) {
                            $groups[] = [
                                'id' => $items[$child]['id'],
                                'type' => $items[$child]['type'],
                                'name' => $items[$child]['name'],
                                'description' => $items[$child]['description'],
                            ];
                        }
                    }
                }
            }
        }

        return $groups;
    }

    public function getAccessedRules()
    {
        if (static::$rules === null) {
            $role_ids = (new RoleMemberModel())->getRoleIdsByUserId($this->user['id']);
            $roles = (new RoleModel())->getById($role_ids);
            static::$rules = array_unique(array_column($roles, 'rule_id'));
        }

        return static::$rules;
    }

    /**
     * 给user分配基于auth rule的组
     *
     * @static
     */
    public static function assignGroups($rule_id, $groups_ids, $user_id)
    {
        // 构建要删除及添加的用户组
        $origin = static::getGroups($rule_id, $user_id);
        $deleted = $origin ? array_diff($origin, $group_ids) : [];
        $added = array_diff($group_ids, $origin);

        return (new AuthAssignmentModel())->updateMulti($user_id, $added, $deleted);
    }

    /**
     * 获取user基于auth rule的用户组
     *
     * @static
     */
    public static function getGroups($rule_id, $user_id)
    {
        $assignments = (new AuthAssignmentModel())->getItemIdsByUserId($user_id);
        $item_ids = (new AuthItemModel())->getIdsByRule($rule_id);

        return array_intersect($assignments, $item_ids);
    }

    /**
     * user删除 - 自动删除auth assignment
     *
     * @static
     */
    public static function remove($id)
    {
        $count = (new UserModel())->remove($id);
        if ($count) {
            (new AuthAssignmentModel())->removeByUserId($id);
        }

        return $count;
    }
}
