<?php

class RoleModel extends BaseModel
{
    const GET_BY_ID_SQL = 'SELECT id,name,description,rule_id,data FROM role WHERE id=?';
    const GET_ID_BY_RULE_SQL = 'SELECT id,name,description,rule_id,data FROM role WHERE rule_id=? ORDER BY id DESC';
    const GET_BY_NAME_RULE_SQL = 'SELECT id,name,description,rule_id,data FROM role WHERE rule_id=? AND name=?';
    const INSERT_SQL = 'INSERT INTO role (name,description,rule_id,data) VALUES (?,?,?,?,?)';
    const UPDATE_SQL = 'UPDATE role SET name=?,description=?,data=? WHERE id=?';
    const DELETE_BY_ID_SQL = 'DELETE FROM role WHERE id=?';

    public function getById($ids)
    {
        if (is_array($ids)) {
            $ids = implode(',', $ids);

            return $this->_db->query("SELECT id,name,description,rule_id,data FROM role WHERE id IN ({$ids})")->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $this->getStatement(self::GET_BY_ID_SQL);
            $stmt->execute([$ids]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    public function getByNameAndRuleId($rule_id, $name)
    {
        $stmt = $this->getStatement(self::GET_BY_NAME_RULE_SQL);
        $stmt->execute([$rule_id, $name]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getIdsByRule($rule_id)
    {
        $stmt = $this->getStatement(self::GET_ID_BY_RULE_SQL);
        $stmt->execute([$rule_id]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function add($name, $description = '', $rule_id = 0, $data = '')
    {
        $stmt = $this->getStatement(self::INSERT_SQL);
        $stmt->execute([$name, $description, $rule_id, $data]);
        $count = $stmt->rowCount();

        return $count ? $this->lastInsertId() : $count;
    }

    public function update($id, $name, $description = '', $data = '')
    {
        $stmt = $this->getStatement(self::UPDATE_SQL);
        $stmt->execute([$name, $description, $data, $id]);

        return $stmt->rowCount();
    }

    public function removeByRoleIds($ids)
    {
        if (is_array($ids)) {
            $ids = implode(',', $ids);

            return $this->_db->exec("DELETE FROM role WHERE id IN ({$ids})");
        } else {
            $stmt = $this->getStatement(self::DELETE_BY_ID_SQL);
            $stmt->execute([$ids]);

            return $stmt->rowCount();
        }
    }
}
