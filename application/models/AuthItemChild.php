<?php

use Swoole\Core\Logger;

class AuthItemChild extends Model
{
    const GET_ALL_SQL = 'SELECT parent,child FROM auth_item_child';
    const GET_BY_PARENT_SQL = 'SELECT child FROM auth_item_child WHERE parent=?';
    const GET_BY_CHILD_SQL = 'SELECT parent FROM auth_item_child WHERE child=?';
    const INSERT_SQL = 'INSERT INTO auth_item_child (parent,child) VALUES (?,?)';

    public function getAll()
    {
        return $this->_db->query(self::GET_ALL_SQL)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRelations()
    {
        $all = $this->getAll();
        $parents = $children = [];
        foreach ($all as $item) {
            if (!isset($parents[$item['parent']])) {
                $parents[$item['parent']] = [];
            }
            $parents[$item['parent']][] = $item['child'];
            if (!isset($children[$item['child']])) {
                $children[$item['child']] = [];
            }
            $children[$item['child']][] = $item['parent'];
        }

        return ['parents' => $parents, 'children' => $children];
    }

    public function getChildren($id)
    {
        $stmt = $this->getStatement(self::GET_BY_PARENT_SQL);
        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getParent($id)
    {
        $stmt = $this->getStatement(self::GET_BY_CHILD_SQL);
        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function add($parent, $child)
    {
        $stmt = $this->getStatement(self::INSERT_SQL);
        $count = 0;
        if (is_array($child)) {
            foreach ($child as $item) {
                $stmt->execute([$parent, $item]);
                $error = $stmt->errorInfo();
                if ($error[0] != '00000') {
                    Logger::write(date('Y-m-d H:i:s')." AuthItemChild::add error({$error[0]}): {$error[2]}".PHP_EOL);
                }
                $count += $stmt->rowCount();
            }
        } else {
            $stmt->execute([$parent, $child]);
            $error = $stmt->errorInfo();
            if ($error[0] != '00000') {
                Logger::write(date('Y-m-d H:i:s')." AuthItemChild::add error({$error[0]}): {$error[2]}".PHP_EOL);
            }
            $count = $stmt->rowCount();
        }

        return $count;
    }

    public function remove($parent = '', $child = '', $expr = 'AND')
    {
        $where = [];
        if ($parent) {
            $where[] = "parent={$parent}";
        }
        if ($child) {
            $where[] = "child={$child}";
        }

        return $where ? $this->_db->exec('DELETE FROM auth_item_child WHERE '.implode(" {$expr} ", $where)) : false;
    }

    public function removeMulti($parents, $children, $expr = 'AND')
    {
        $where = [];
        if ($parents) {
            $where[] = 'parent IN ('.implode(',', $parents).')';
        }
        if ($children) {
            $where[] = 'child IN ('.implode(',', $children).')';
        }

        return $where ? $this->_db->exec('DELETE FROM auth_item_child WHERE '.implode(" {$expr} ", $where)) : false;
    }
}
