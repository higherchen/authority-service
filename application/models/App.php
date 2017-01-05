<?php

class AppModel extends BaseModel
{
    const GET_ALL_SQL = 'SELECT id,name,app_key,app_secret FROM app';
    const GET_BY_ID_SQL = 'SELECT id,name,app_key,app_secret FROM app WHERE id=?';
    const GET_BY_APP_KEY_SQL = 'SELECT id,name,app_key,app_secret FROM app WHERE app_key=?';
    const INSERT_SQL = 'INSERT INTO app (name,app_key,app_secret) VALUES (?,?,?)';
    const UPDATE_SQL = 'UPDATE app SET name=? WHERE id=?';
    const DELETE_BY_ID_SQL = 'DELETE FROM app WHERE id=?';

    public function getAll()
    {
        return $this->_db->query(self::GET_ALL_SQL)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($ids)
    {
        if (is_array($ids)) {
            $ids = implode(',', $ids);

            return $this->_db->query("SELECT id,name,app_key,app_secret FROM app WHERE id IN ({$ids})")->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $this->getStatement(self::GET_BY_ID_SQL);
            $stmt->execute([$ids]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    public function getByAppKey($app_key)
    {
        $stmt = $this->getStatement(self::GET_BY_APP_KEY_SQL);
        $stmt->execute([$app_key]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add($name, $app_key, $app_secret)
    {
        $stmt = $this->getStatement(self::INSERT_SQL);
        $stmt->execute([$name, $app_key, $app_secret]);
        $count = $stmt->rowCount();

        return $count ? $this->lastInsertId() : $count;
    }

    public function update($id, $name)
    {
        $stmt = $this->getStatement(self::UPDATE_SQL);
        $stmt->execute([$name, $id]);

        return $stmt->rowCount();
    }

    public function remove($id)
    {
        $stmt = $this->getStatement(self::DELETE_BY_ID_SQL);
        $stmt->execute([$id]);

        return $stmt->rowCount();
    }
}
