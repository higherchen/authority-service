<?php

namespace Authority;

class RoleHandler
{
    /**
     * 新增角色.
     *
     * @param \Authority\Role $role
     *
     * @return \Authority\CommonRet $ret
     */
    public static function add(Role $role)
    {
        $ret = new CommonRet();

        $id = (new \Role())->add($role->name, $role->description ?: '', $role->rule_id ?: 0, $role->data ?: '');
        if ($id) {
            $ret->ret = \Constant::RET_OK;
            $ret->data = json_encode(['id' => $id]);
        } else {
            $ret->ret = \Constant::RET_DATA_CONFLICT;
            $ret->data = 'Role exists!';
        }

        return $ret;
    }

    /**
     * 删除角色.
     *
     * @param int $role_id
     *
     * @return \Authority\CommonRet $ret
     */
    public static function remove($role_id)
    {
        $ret = new CommonRet();

        $count = (new \Role())->remove($role_id);
        if ($count) {
            (new \RoleMember())->removeByRole($role_id); // delete role_member
            $ret->ret = \Constant::RET_OK;
        } else {
            $ret->ret = \Constant::RET_DATA_NO_FOUND;
        }

        return $ret;
    }

    /**
     * 更新角色.
     *
     * @param int             $role_id
     * @param \Authority\Role $role
     *
     * @return \Authority\CommonRet $ret
     */
    public static function update($role_id, Role $role)
    {
        $ret = new CommonRet();

        $model = new \Role();
        $item = $model->getById($role_id);
        if ($item) {
            $model->update($role_id, $role->name ?: $item['name'], $role->description ?: $item['description'], $role->data ?: $item['data']);
            $ret->ret = \Constant::RET_OK;
        } else {
            $ret->ret = \Constant::RET_DATA_NO_FOUND;
        }

        return $ret;
    }

    /**
     * 获取角色列表.
     *
     * @param \Authority\Search $search
     *
     * @return \Authority\RoleRet $ret
     */
    public static function getList(Search $search)
    {
        $ret = new RoleRet();
        $ret->ret = \Constant::RET_OK;

        $roles = [];
        $model = new \Role();

        if (!$search->page && !$search->conditions) {
            // 无搜索条件 ^_^
            $roles = $model->getAll();
            $ret->total = count($roles);
        } else {
            // 有搜索条件 @_@
            $sql = 'SELECT id,name,description,rule_id,data FROM role';
            $total_sql = 'SELECT COUNT(1) FROM role';
            if ($search->conditions) {
                $where = [];
                foreach ($search->conditions as $condition) {
                    $expr = $condition->expr ?: '=';
                    $where[] = "{$condition->field} {$expr} '{$condition->value}'";
                }
                $where = implode(' AND ', $where);
                $sql .= " WHERE {$where}";
                $total_sql .= " WHERE {$where}";
            }
            $ret->total = $model->query($total_sql)->fetch(\PDO::FETCH_COLUMN);
            $sql .= ' ORDER BY id DESC';
            if ($page = $search->page) {
                $pagesize = $search->pagesize ?: 20;
                $offset = ($page - 1) * $pagesize;
                $sql .= " LIMIT {$offset},{$pagesize}";
            }
            $roles = $model->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        }

        if ($roles) {
            $data = [];
            foreach ($roles as $role) {
                $data[] = new Role(
                    [
                        'id' => $role['id'],
                        'name' => $role['name'],
                        'description' => $role['description'],
                        'rule_id' => $role['rule_id'],
                        'data' => $role['data'],
                    ]
                );
            }
            $ret->roles = $data;
        }

        return $ret;
    }
}
