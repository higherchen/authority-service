<?php

use Swoole\Core\Logger;

class AuthAssignment extends Model
{
    const GET_BY_ITEM_ID_SQL = 'SELECT user_id FROM auth_assignment WHERE item_id=?';
    const GET_BY_USER_ID_SQL = 'SELECT item_id FROM auth_assignment WHERE user_id=?';
    const INSERT_SQL = 'INSERT INTO auth_assignment (user_id,item_id) VALUES (?,?)';
    const DELETE_BY_USER_ID = 'DELETE FROM auth_assignment WHERE user_id=?';
    const DELETE_BY_ITEM_ID = 'DELETE FROM auth_assignment WHERE item_id=?';

    public function getUserIdsByItemId($item_id)
    {
        $stmt = $this->getStatement(self::GET_BY_ITEM_ID_SQL);
        $stmt->execute([$item_id]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getItemIdsByUserId($user_id)
    {
        $stmt = $this->getStatement(self::GET_BY_USER_ID_SQL);
        $stmt->execute([$user_id]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function updateMulti($user_id, $item_ids, $remove_ids)
    {
        if ($remove_ids) {
            $remove_ids = implode(',', $remove_ids);
            $this->_db->exec("DELETE FROM auth_assignment WHERE user_id={$user_id} AND item_id IN ({$remove_ids})");
        }
        if ($item_ids) {
            $stmt = $this->getStatement(self::INSERT_SQL);
            foreach ($item_ids as $item_id) {
                $stmt->execute([$user_id, $item_id]);
                $error = $stmt->errorInfo();
                if ($error[0] != '00000') {
                    Logger::write(date('Y-m-d H:i:s')." AuthAssignment::updateMulti error({$error[0]}): {$error[2]}".PHP_EOL);
                }
            }
        }

        return true;
    }

    public function removeByUserId($user_id)
    {
        $stmt = $this->getStatement(self::DELETE_BY_USER_ID);
        $stmt->execute([$user_id]);

        return $stmt->rowCount();
    }

    public function removeByItemIds($item_ids)
    {
        if (is_array($item_ids)) {
            $item_ids = implode(',', $item_ids);

            return $this->_db->exec("DELETE FROM auth_assignment WHERE item_id IN ({$item_ids})");
        } else {
            $stmt = $this->getStatement(self::DELETE_BY_ITEM_ID);
            $stmt->execute([$item_ids]);

            return $stmt->rowCount();
        }
    }
}
