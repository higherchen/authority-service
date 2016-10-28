<?php

class Authority_Rule
{
    protected $rule;
    protected static $items;
    protected static $groups;

    public function __construct($rule_name)
    {
        $this->rule = (new AuthRuleModel())->getByName($rule_name);
    }

    public function getItems()
    {
        if (static::$items === null) {
            static::$items = (new AuthItemModel())->getByRule($this->rule['id']);
        }

        return static::$items;
    }

    public function getAdmin()
    {
        foreach ($this->getItems() as $item) {
            if ($item['type'] == Constant::ADMIN) {
                return $item['id'];
            }
        }

        return false;
    }
}
