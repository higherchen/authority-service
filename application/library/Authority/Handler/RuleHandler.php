<?php

namespace Authority;

class RuleHandler
{
    /**
     * 新增规则.
     *
     * @param \Authority\Rule $rule
     *
     * @return \Authority\CommonRet $ret
     */
    public static function add(Rule $rule)
    {
        $ret = new CommonRet();

        $id = (new \AuthRule())->add($rule->name, $rule->data ?: '');
        $ret->ret = \Constant::RET_OK;
        $ret->data = json_encode(['id' => $id]);

        return $ret;
    }

    /**
     * 根据名称获取规则.
     *
     * @param string $name
     *
     * @return \Authority\Rule $rule
     */
    public function getByName($name)
    {
        $rule = new Rule();

        $item = (new \AuthRule)->getByName($name);
        if ($item) {
            $rule->id = $item['id'];
            $rule->name = $item['name'];
            $rule->data = $item['data'];
        }

        return $rule;
    }

    /**
     * 编辑规则.
     *
     * @param int             $rule_id
     * @param \Authority\Rule $rule
     *
     * @return \Authority\CommonRet $ret
     */
    public static function update($rule_id, Rule $rule)
    {
        $ret = new CommonRet();

        $model = new \AuthRule();
        $item = $model->getById($rule_id);
        if ($item) {
            $name = $rule->name ?: $item['name'];
            $data = $rule->data ?: $item['data'];
            $count = $model->update($rule_id, $name, $data);
            $ret->ret = $count ? \Constant::RET_OK : \Constant::RET_DATA_CONFLICT;
        } else {
            $ret->ret = \Constant::RET_DATA_NO_FOUND;
        }

        return $ret;
    }

    /**
     * 删除规则.
     *
     * @param int $rule_id
     *
     * @return \Authority\CommonRet $ret
     */
    public static function remove($rule_id)
    {
        $ret = new CommonRet();

        $count = (new \AuthRule())->remove($rule_id);
        if ($count) {
            /*** make some clean work ***/
            $auth_item = new \AuthItem();
            $ids = $auth_item->getIdsByRule($rule_id);
            $auth_item->removeByRuleId($rule_id);                   // delete auth_item
            (new \AuthItemChild())->removeMulti($ids, $ids, 'OR');  // delete auth_item_child
            (new \AuthAssignment())->removeByItemIds($ids);         // delete auth_assignment

            $ret->ret = \Constant::RET_OK;
        } else {
            $ret->ret = \Constant::RET_DATA_NO_FOUND;
        }

        return $ret;
    }
}
