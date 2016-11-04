<?php

class AuthItemModel extends BaseModel
{
    const GET_BY_RULE_SQL = 'SELECT id,name,type,description,rule_id,data FROM auth_item WHERE rule_id=?';
    const GET_BY_RULE_TYPE_SQL = 'SELECT id,name,type,description,rule_id,data FROM auth_item WHERE rule_id=? AND type=?';
    const GET_BY_ID_SQL = 'SELECT id,name,type,description,rule_id,data FROM auth_item WHERE id=?';
    const GET_ID_BY_RULE_SQL = 'SELECT id FROM auth_item WHERE rule_id=?';
    const INSERT_SQL = 'INSERT INTO auth_item (name,type,rule_id,description,data) VALUES (?,?,?,?,?)';
    const UPDATE_SQL = 'UPDATE auth_item SET name=?,description=?,data=? WHERE type=? AND id=?';
    const DELETE_BY_ID_SQL = 'DELETE FROM auth_item WHERE rule_id=? AND type=? AND id=?';
    const DELETE_BY_RULE_SQL = 'DELETE FROM auth_item WHERE rule_id=?';

    public function getByRule($rule_id)
    {
        $stmt = $this->getStatement(self::GET_BY_RULE_SQL);
        $stmt->execute([$type]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_column($items, null, 'id');
    }

    public function getByRuleType($rule_id, $type)
    {
        $stmt = $this->getStatement(self::GET_BY_RULE_TYPE_SQL);
        $stmt->execute([$rule_id, $type]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_column($items, null, 'id');
    }

    public function getById($ids)
    {
        if (is_array($ids)) {
            $ids = implode(',', $ids);

            return $this->_db->query("SELECT id,name,type,description,rule_id,data FROM auth_item WHERE id IN ({$ids})")->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $this->getStatement(self::GET_BY_ID_SQL);
            $stmt->execute([$ids]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    public function getIdsByRule($rule_id)
    {
        $stmt = $this->getStatement(self::GET_ID_BY_RULE_SQL);
        $stmt->execute([$rule_id]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function add($name, $type, $rule_id = 0, $description = '', $data = '')
    {
        $stmt = $this->getStatement(self::INSERT_SQL);
        $stmt->execute([$name, $type, $rule_id, $description, $data]);
        $count = $stmt->rowCount();

        return $count ? $this->lastInsertId() : $count;
    }

    public function update($item_id, $type, $name, $description, $data = '')
    {
        $stmt = $this->getStatement(self::UPDATE_SQL);
        $stmt->execute([$name, $description, $data, $type, $item_id]);

        return $stmt->rowCount();
    }

    public function remove($rule_id, $type, $id)
    {
        $stmt = $this->getStatement(self::DELETE_BY_ID_SQL);
        $stmt->execute([$rule_id, $type, $id]);

        return $stmt->rowCount();
    }

    public function removeByRuleId($rule_id)
    {
        $stmt = $this->getStatement(self::DELETE_BY_RULE_SQL);
        $stmt->execute([$rule_id]);

        return $stmt->rowCount();
    }
}
