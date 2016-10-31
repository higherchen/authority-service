<?php

class UserModel extends BaseModel
{
    const INSERT_SQL = 'INSERT INTO user (username,nickname,email,telephone) VALUES (?,?,?,?)';
    const GET_ALL_SQL = 'SELECT id,username,nickname,email,telephone FROM user ORDER BY id DESC';
    const GET_BY_ID_SQL = 'SELECT id,username,nickname,email,telephone FROM user WHERE id=?';
    const GET_BY_NAME_SQL = 'SELECT id,username,nickname,email,telephone FROM user WHERE username=?';
    const UPDATE_SQL = 'UPDATE user SET nickname=?,email=?,telephone=? WHERE id=?';
    const DELETE_BY_ID_SQL = 'DELETE FROM user WHERE id=?';

    public function add($username, $nickname, $email, $telephone)
    {
        $stmt = $this->getStatement(self::INSERT_SQL);
        $stmt->execute([$username, $nickname, $email, $telephone]);
        $count = $stmt->rowCount();

        return $count ? $this->lastInsertId() : $count;
    }

    public function getAll()
    {
        return $this->_db->query(self::GET_ALL_SQL)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPagedByName($page = '', $pagesize = '', $name = '')
    {
        $sql = 'SELECT id,username,nickname,email,telephone FROM user';
        $total_sql = 'SELECT COUNT(1) FROM user';
        if ($name) {
            $where = " WHERE username LIKE '%{$name}' OR nickname LIKE '%{$name}'";
            $sql .= $where;
            $total_sql .= $where;
        }

        $total = $this->_db->query($total_sql)->fetchAll(PDO::FETCH_COLUMN);

        if ($page) {
            $pagesize = $pagesize ? : 30;
            $offset = ($page - 1) * $pagesize;
            $sql .= " ORDER BY id DESC LIMIT {$offset},{$pagesize}";
        }
        $users = $this->_db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        return ['total' => $total, 'users' => $users];
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
