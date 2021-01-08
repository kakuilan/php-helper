<?php
/**
 * Copyright (c) 2019 LKK All rights reserved
 * User: kakuilan
 * Date: 2019/4/12
 * Time: 11:21
 * Desc:
 */


namespace Kph\Tests\Objects;

use Kph\Objects\BaseObject;

class BaseCls extends BaseObject {

    public $name;

    protected $nick;

    private $id;


    public function time() {
        return time();
    }


    public function __call($name, $args) {
        return call_user_func_array([$this, $name], $args);
    }



}