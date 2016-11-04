<?php

class AuthRuleModel extends BaseModel
{
    const GET_ALL_SQL = 'SELECT id,name,data FROM auth_rule';
    const GET_BY_ID_SQL = 'SELECT id,name,data FROM auth_rule WHERE id=?';
    const GET_BY_NAME_SQL = 'SELECT id,name,data FROM auth_rule WHERE name=?';
    const INSERT_SQL = 'INSERT INTO auth_rule (name,data) VALUES (?,?)';
    const UPDATE_SQL = 'UPDATE auth_rule SET name=?,data=? WHERE id=?';
    const DELETE_BY_ID_SQL = 'DELETE FROM auth_rule WHERE id=?';

    public function getAll()
    {
        $items = $this->_db->query(self::GET_ALL_SQL)->fetchAll(PDO::FETCH_ASSOC);

        return array_column($items, null, 'id');
    }

    public function getById($ids)
    {
        if (is_array($ids)) {
            $ids = implode(',', $ids);

            return $this->_db->query("SELECT id,name,data FROM auth_rule WHERE id IN ({$ids})")->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $this->getStatement(self::GET_BY_ID_SQL);
            $stmt->execute([$ids]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    public function getByName($name)
    {
        $stmt = $this->getStatement(self::GET_BY_NAME_SQL);
        $stmt->execute([$name]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add($name, $data)
    {
        $stmt = $this->getStatement(self::INSERT_SQL);
        $stmt->execute([$name, $data]);
        $count = $stmt->rowCount();

        return $count ? $this->lastInsertId() : $count;
    }

    public function update($id, $name, $data)
    {
        $stmt = $this->getStatement(self::UPDATE_SQL);
        $stmt->execute([$name, $data, $id]);

        return $stmt->rowCount();
    }

    public function remove($id)
    {
        $stmt = $this->getStatement(self::DELETE_BY_ID_SQL);
        $stmt->execute([$id]);

        return $stmt->rowCount();
    }
}
