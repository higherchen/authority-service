<?php

class Role extends Model
{
    const GET_ALL_SQL = 'SELECT id,type,name,description,rule_id,data FROM role ORDER BY id DESC';
    const GET_BY_ID_SQL = 'SELECT * FROM role WHERE id=?';
    const INSERT_SQL = 'INSERT INTO role (type,name,description,rule_id,data) VALUES (?,?,?,?,?)';
    const UPDATE_SQL = 'UPDATE role SET name=?,description=?,data=? WHERE id=?';
    const DELETE_BY_ID_SQL = 'DELETE FROM role WHERE id=?';

    public function getAll()
    {
        $roles = $this->_db->query(self::GET_ALL_SQL)->fetchAll(PDO::FETCH_ASSOC);

        return array_column($roles, null, 'id');
    }

    public function getById($id)
    {
        $stmt = $this->getStatement(self::GET_BY_ID_SQL);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add($type, $name, $description = '', $rule_id = 0, $data = '')
    {
        $stmt = $this->getStatement(self::INSERT_SQL);
        $stmt->execute([$type, $name, $description, $rule_id, $data]);
        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            Logger::write(date('Y-m-d H:i:s')." Role::add error({$error[0]}): {$error[2]}".PHP_EOL);
        }

        return $count ? $this->lastInsertId() : $count;
    }

    public function update($id, $name, $description = '', $data = '')
    {
        $stmt = $this->getStatement(self::UPDATE_SQL);
        $stmt->execute([$name, $description, $data, $id]);
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            Logger::write(date('Y-m-d H:i:s')." Role::update error({$error[0]}): {$error[2]}".PHP_EOL);
        }

        return $stmt->rowCount();
    }

    public function remove($id)
    {
        $stmt = $this->getStatement(self::DELETE_BY_ID_SQL);
        $stmt->execute([$id]);

        return $stmt->rowCount();
    }
}
