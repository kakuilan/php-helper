<?php
/**
 * Created by PhpStorm.
 * User: kakuilan@163.com
 * Date: 2019/5/9
 * Time: 18:33
 * Desc:
 */

namespace Kph\Tests\Objects;

use Kph\Objects\StrictObject;

class StrictCls extends StrictObject {

    public $name;

    protected $gender = 'man';

    protected $nick = 'boot';

    private $id = 1;


    protected function getNick() {
        return $this->nick;
    }

    protected function setNick(string $nick) {
        $this->nick = $nick;
    }

    protected function setId($id) {
        $this->id = $id;
    }

    protected function getId() {
        return $this->id;
    }




}