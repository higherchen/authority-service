<?php

use Swoole\Core\Logger;

class AuthItem extends Model
{
    const GET_ALL_SQL = 'SELECT id,name,type,description,rule_id,data FROM auth_item';
    const GET_BY_TYPE_SQL = 'SELECT id,name,type,description,rule_id,data FROM auth_item WHERE type=?';
    const GET_BY_ID_SQL = 'SELECT id,name,type,description,rule_id,data FROM auth_item WHERE id=?';
    const GET_ID_BY_RULE_SQL = 'SELECT id FROM auth_item WHERE rule_id=?';
    const GET_BY_NAME_SQL = 'SELECT id,name,type,description,rule_id,data FROM auth_item WHERE name=?';
    const INSERT_SQL = 'INSERT INTO auth_item (name,type,rule_id,description,data) VALUES (?,?,?,?,?)';
    const UPDATE_SQL = 'UPDATE auth_item SET name=?,description=?,data=? WHERE type=? AND id=?';
    const DELETE_BY_ID_SQL = 'DELETE FROM auth_item WHERE type=? AND id=?';
    const DELETE_BY_RULE_SQL = 'DELETE FROM auth_item WHERE rule_id=?';

    public function getAll()
    {
        $items = $this->_db->query(self::GET_ALL_SQL)->fetchAll(PDO::FETCH_ASSOC);

        return array_column($items, null, 'id');
    }

    public function getByType($type)
    {
        $stmt = $this->getStatement(self::GET_BY_TYPE_SQL);
        $stmt->execute([$type]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_column($items, null, 'id');
    }

    public function getById($id)
    {
        $stmt = $this->getStatement(self::GET_BY_ID_SQL);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getIdsByRule($rule_id)
    {
        $stmt = $this->getStatement(self::GET_ID_BY_RULE_SQL);
        $stmt->execute([$rule_id]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getByName($name)
    {
        $stmt = $this->getStatement(self::GET_BY_NAME_SQL);
        $stmt->execute([$name]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add($name, $type, $rule_id = 0, $description = '', $data = '')
    {
        $stmt = $this->getStatement(self::INSERT_SQL);
        $stmt->execute([$name, $type, $rule_id, $description, $data]);
        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            Logger::write(date('Y-m-d H:i:s')." AuthItem::add error({$error[0]}): {$error[2]}".PHP_EOL);
        }

        return $count ? $this->lastInsertId() : $count;
    }

    public function update($item_id, $type, $name, $description, $data = '')
    {
        $stmt = $this->getStatement(self::UPDATE_SQL);
        $stmt->execute([$name, $description, $data, $type, $item_id]);
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            Logger::write(date('Y-m-d H:i:s')." AuthItem::update error({$error[0]}): {$error[2]}".PHP_EOL);
        }

        return $stmt->rowCount();
    }

    public function remove($type, $id)
    {
        $stmt = $this->getStatement(self::DELETE_BY_ID_SQL);
        $stmt->execute([$type, $id]);

        return $stmt->rowCount();
    }

    public function removeByRuleId($rule_id)
    {
        $stmt = $this->getStatement(self::DELETE_BY_RULE_SQL);
        $stmt->execute([$rule_id]);

        return $stmt->rowCount();
    }
}
