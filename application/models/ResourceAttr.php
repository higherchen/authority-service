<?php

class ResourceAttr extends Model
{
    const GET_BY_ID_SQL = 'SELECT id,name,src_id,owner_id,role_id,mode,data FROM resource_attr WHERE name=? AND src_id=?';
    const INSERT_SQL = 'INSERT INTO resource_attr (name,src_id,owner_id,role_id,mode,data) VALUES (?,?,?,?,?,?)';
    const UPDATE_SQL = 'UPDATE resource_attr SET owner_id=?,role_id=?,mode=?,data=? WHERE name=? AND src_id=?';
    const DELETE_BY_ID_SQL = 'DELETE FROM resource_attr WHERE name=? AND src_id=?';

    public function getById($name, $src_id)
    {
        $stmt = $this->getStatement(self::GET_BY_ID_SQL);
        $stmt->execute([$name, $src_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add($name, $src_id, $owner_id, $role_id, $mode, $data = '')
    {
        $stmt = $this->getStatement(self::INSERT_SQL);
        $stmt->execute([$name, $src_id, $owner_id, $role_id, $mode, $data]);
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            Logger::write(date('Y-m-d H:i:s')." ResourceAttr::add error({$error[0]}): {$error[2]}".PHP_EOL);
        }

        return $stmt->rowCount();
    }

    public function remove($name, $src_id)
    {
        $stmt = $this->getStatement(self::DELETE_BY_ID_SQL);
        $stmt->execute([$name, $src_id]);

        return $stmt->rowCount();
    }

    public function update($name, $src_id, $owner_id, $role_id, $mode, $data = '')
    {
        $stmt = $this->getStatement(self::UPDATE_SQL);
        $stmt->execute([$owner_id, $role_id, $mode, $data, $name, $src_id]);
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            Logger::write(date('Y-m-d H:i:s')." ResourceAttr::update error({$error[0]}): {$error[2]}".PHP_EOL);
        }

        return $stmt->rowCount();
    }
}
