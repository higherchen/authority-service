<?php

class ResourceAttrModel extends BaseModel
{
    const GET_BY_ID_SQL = 'SELECT id,name,src_id,owner_id,role_id FROM resource_attr WHERE name=? AND src_id=?';
    const INSERT_SQL = 'INSERT INTO resource_attr (name,src_id,owner_id,role_id) VALUES (?,?,?,?)';
    const UPDATE_SQL = 'UPDATE resource_attr SET owner_id=?,role_id=? WHERE name=? AND src_id=?';
    const DELETE_BY_ID_SQL = 'DELETE FROM resource_attr WHERE name=? AND src_id=?';

    public function getById($name, $src_id)
    {
        $stmt = $this->getStatement(self::GET_BY_ID_SQL);
        $stmt->execute([$name, $src_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAccessedResources($owner_id, $role_ids, $name)
    {
        $role_ids = implode(',', $role_ids);

        return $this->_db->query("SELECT src_id FROM resource_attr WHERE name='{$name}' AND (owner_id={$owner_id} OR role_id in ({$role_ids}))")->fetchAll(PDO::FETCH_COLUMN);
    }

    public function add($name, $src_id, $owner_id, $role_id)
    {
        $stmt = $this->getStatement(self::INSERT_SQL);
        $stmt->execute([$name, $src_id, $owner_id, $role_id]);

        return $stmt->rowCount();
    }

    public function remove($name, $src_id)
    {
        $stmt = $this->getStatement(self::DELETE_BY_ID_SQL);
        $stmt->execute([$name, $src_id]);

        return $stmt->rowCount();
    }

    public function update($name, $src_id, $owner_id, $role_id)
    {
        $stmt = $this->getStatement(self::UPDATE_SQL);
        $stmt->execute([$owner_id, $role_id, $name, $src_id]);

        return $stmt->rowCount();
    }
}
