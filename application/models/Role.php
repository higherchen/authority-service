<?php

class RoleModel extends BaseModel
{
    const GET_BY_ID_SQL = 'SELECT id,name,description FROM role WHERE id=?';
    const INSERT_SQL = 'INSERT INTO role (name,description) VALUES (?,?)';
    const UPDATE_SQL = 'UPDATE role SET name=?,description=? WHERE id=?';
    const DELETE_BY_ID_SQL = 'DELETE FROM role WHERE id=?';

    public function getById($ids)
    {
        if (is_array($ids)) {
            $ids = implode(',', $ids);

            return $this->_db->query("SELECT id,name,description FROM role WHERE id IN ({$ids})")->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $this->getStatement(self::GET_BY_ID_SQL);
            $stmt->execute([$ids]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    public function add($name, $description = '')
    {
        $stmt = $this->getStatement(self::INSERT_SQL);
        $stmt->execute([$name, $description]);
        $count = $stmt->rowCount();

        return $count ? $this->lastInsertId() : $count;
    }

    public function update($id, $name, $description = '')
    {
        $stmt = $this->getStatement(self::UPDATE_SQL);
        $stmt->execute([$name, $description, $id]);

        return $stmt->rowCount();
    }

    public function removeById($ids)
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
