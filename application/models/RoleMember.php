<?php

class RoleMemberModel extends BaseModel
{
    const GET_ROLE_BY_USER_ID = 'SELECT role_id FROM role_member WHERE user_id=?';
    const GET_USER_BY_ROLE_ID = 'SELECT user_id FROM role_member WHERE role_id=?';
    const INSERT_SQL = 'INSERT INTO role_member (role_id,user_id) VALUES (?,?)';
    const DELETE_SQL = 'DELETE FROM role_member WHERE role_id=? AND user_id=?';
    const DELETE_BY_ROLE_SQL = 'DELETE FROM role_member WHERE role_id=?';

    public function getRoleIdsByUserId($user_id)
    {
        $stmt = $this->getStatement(self::GET_ROLE_BY_USER_ID);
        $stmt->execute([$user_id]);

        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function getUserIdsByRoleId($role_id)
    {
        $stmt = $this->getStatement(self::GET_USER_BY_ROLE_ID);
        $stmt->execute([$role_id]);

        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function add($role_id, $user_id)
    {
        $stmt = $this->getStatement(self::INSERT_SQL);
        $stmt->execute([$role_id, $user_id]);

        return $this->rowCount();
    }

    public function remove($role_id, $user_id)
    {
        $stmt = $this->getStatement(self::DELETE_SQL);
        $stmt->execute([$role_id, $user_id]);

        return $stmt->rowCount();
    }

    public function removeByRole($role_ids)
    {
        if (is_array($role_ids)) {
            $role_ids = implode(',', $role_ids);

            return $this->_db->exec("DELETE FROM role_member WHERE role_id IN ({$role_ids})");
        } else {
            $stmt = $this->getStatement(self::DELETE_BY_ROLE_SQL);
            $stmt->execute([$role_ids]);

            return $stmt->rowCount();
        }
    }
}
