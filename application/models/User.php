<?php

class User extends Model
{
    const INSERT_SQL = 'INSERT INTO user (username,nickname,email,telephone) VALUES (?,?,?,?)';
    const GET_ALL_SQL = 'SELECT * FROM user ORDER BY id DESC';
    const GET_BY_ID_SQL = 'SELECT * FROM user WHERE id=?';
    const GET_BY_NAME_SQL = 'SELECT * FROM user WHERE username=?';
    const UPDATE_SQL = 'UPDATE user SET nickname=?,email=?,telephone=? WHERE id=?';
    const DELETE_BY_ID_SQL = 'DELETE FROM user WHERE id=?';

    public function add($username, $nickname, $email, $telephone)
    {
        $stmt = $this->getStatement(self::INSERT_SQL);
        $stmt->execute([$username, $nickname, $email, $telephone]);
        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            Logger::write(date('Y-m-d H:i:s')." User::add error({$error[0]}): {$error[2]}".PHP_EOL);
        }

        return $count ? $this->lastInsertId() : $count;
    }

    public function getAll()
    {
        return $this->_db->query(self::GET_ALL_SQL)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->getStatement(self::GET_BY_ID_SQL);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByName($username)
    {
        $stmt = $this->getStatement(self::GET_BY_NAME_SQL);
        $stmt->execute([$username]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $nickname, $email, $telephone)
    {
        $stmt = $this->getStatement(self::UPDATE_SQL);
        $stmt->execute([$nickname, $email, $telephone, $id]);
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            Logger::write(date('Y-m-d H:i:s')." User::update error({$error[0]}): {$error[2]}".PHP_EOL);
        }

        return $stmt->rowCount();
    }

    public function remove($id)
    {
        $stmt = $this->getStatement(self::DELETE_BY_ID_SQL);
        $stmt->execute([$id]);

        return $stmt->rowCount();
    }

    public function getMulti($ids)
    {
        $ids = implode(',', $ids);

        return $this->_db->query("SELECT * FROM user WHERE id IN ({$ids})")->fetchAll(PDO::FETCH_ASSOC);
    }
}
