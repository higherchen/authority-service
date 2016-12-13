<?php

class AuthItemModel extends BaseModel
{
    const GET_BY_APP_ID_SQL = 'SELECT id,name,type,description,app_id,mark FROM auth_item WHERE app_id=?';
    const GET_BY_APP_ID_TYPE_SQL = 'SELECT id,name,type,description,app_id,mark FROM auth_item WHERE app_id=? AND type=?';
    const GET_BY_ID_SQL = 'SELECT id,name,type,description,app_id,mark FROM auth_item WHERE id=?';
    const GET_IDS_BY_APP_ID_SQL = 'SELECT id FROM auth_item WHERE app_id=?';
    const INSERT_SQL = 'INSERT INTO auth_item (name,type,app_id,description,mark) VALUES (?,?,?,?,?)';
    const UPDATE_SQL = 'UPDATE auth_item SET name=?,description=?,mark=? WHERE type=? AND id=?';
    const DELETE_BY_ID_SQL = 'DELETE FROM auth_item WHERE app_id=? AND type=? AND id=?';
    const DELETE_BY_APP_ID_SQL = 'DELETE FROM auth_item WHERE app_id=?';

    public function getByAppId($app_id)
    {
        $stmt = $this->getStatement(self::GET_BY_APP_ID_SQL);
        $stmt->execute([$app_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_column($items, null, 'id');
    }

    public function getByAppIdType($app_id, $type)
    {
        $stmt = $this->getStatement(self::GET_BY_APP_ID_TYPE_SQL);
        $stmt->execute([$app_id, $type]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_column($items, null, 'id');
    }

    public function getById($ids)
    {
        if (is_array($ids)) {
            $ids = implode(',', $ids);

            return $this->_db->query("SELECT id,name,type,description,app_id,mark FROM auth_item WHERE id IN ({$ids})")->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $this->getStatement(self::GET_BY_ID_SQL);
            $stmt->execute([$ids]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    public function getIdsByAppId($app_id)
    {
        $stmt = $this->getStatement(self::GET_IDS_BY_APP_ID_SQL);
        $stmt->execute([$app_id]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function add($name, $type, $app_id, $description = '', $mark = '')
    {
        $stmt = $this->getStatement(self::INSERT_SQL);
        $stmt->execute([$name, $type, $app_id, $description, $mark]);
        $count = $stmt->rowCount();

        return $count ? $this->lastInsertId() : $count;
    }

    public function update($item_id, $type, $name, $description, $mark = '')
    {
        $stmt = $this->getStatement(self::UPDATE_SQL);
        $stmt->execute([$name, $description, $mark, $type, $item_id]);

        return $stmt->rowCount();
    }

    public function remove($app_id, $type, $id)
    {
        $stmt = $this->getStatement(self::DELETE_BY_ID_SQL);
        $stmt->execute([$app_id, $type, $id]);

        return $stmt->rowCount();
    }

    public function removeByAppId($app_id)
    {
        $stmt = $this->getStatement(self::DELETE_BY_APP_ID_SQL);
        $stmt->execute([$app_id]);

        return $stmt->rowCount();
    }
}
