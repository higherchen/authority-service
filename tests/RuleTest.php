<?php

define('ROOT', __DIR__.'/..');
require ROOT.'/vendor/autoload.php';

class RuleTest extends PHPUnit_Framework_TestCase
{

    public function testAdd()
    {
        $rule = new \Authority\Rule(['name' => 'READ_ONLY', 'data' => 'hello world']);
        $ret = (new \Authority\Handler())->addRule($rule);
        $this->assertEquals(\Constant::RET_OK, $ret->ret);
        if ($ret->ret == \Constant::RET_OK) {
            echo PHP_EOL, 'Add rule READ_ONLY success!', PHP_EOL;
        }

        $data = json_decode($ret->data, true);
        return (int)$data['id'];
    }

    /**
     * @depends testAdd
     */
    public function testGetByName($id)
    {
        $ret = (new \Authority\Handler())->getRuleByName('READ_ONLY');
        $this->assertEquals($id, $ret->id);

        if ($ret->id == $id) {
            echo PHP_EOL, 'Get rule READ_ONLY by name success!', PHP_EOL;
        }
    }

    /**
     * @depends testAdd
     */
    public function testUpdate($id)
    {
        $rule = new \Authority\Rule(['name' => 'WRITE_ONLY']);
        $ret = (new \Authority\Handler())->updateRule($rule, $id);
        $this->assertEquals(\Constant::RET_OK, $ret->ret);
        if ($ret->ret == \Constant::RET_OK) {
            echo PHP_EOL, 'Update rule READ_ONLY to WRITE_ONLY success!', PHP_EOL;
        }
    }

    /**
     * @depends testAdd
     */
    public function testRemove($id)
    {
        $ret = (new \Authority\Handler())->rmRule($id);
        $this->assertEquals(\Constant::RET_OK, $ret->ret);

        if ($ret->ret == \Constant::RET_OK) {
            echo PHP_EOL, 'Remove rule READ_ONLY success!', PHP_EOL;
        }
    }
}
