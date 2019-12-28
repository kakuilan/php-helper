<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2019/6/26
 * Time: 19:15
 * Desc: 参考 https://github.com/hprose/hprose-php/wiki/03-Promise-%E5%BC%82%E6%AD%A5%E7%BC%96%E7%A8%8B
 */


namespace Kph\Concurrent;

use Kph\Concurrent\Exception\UnCatchableException;
use Error;
use Exception;
use Generator;
use ReflectionMethod;
use Throwable;
use TypeError;

class Promise {

    //等待
    const PENDING   = 'pending';

    //成功
    const FULFILLED = 'fulfilled';

    //失败
    const REJECTED  = 'rejected';


    /**
     * 状态
     * @var int
     */
    protected $state = self::PENDING;


    /**
     * 值
     * @var
     */
    protected $value;


    /**
     * 原因
     * @var
     */
    protected $reason;


    /**
     * 订阅者
     * @var array
     */
    protected $subscribers = [];


    public function __construct($computation = null) {
        if(is_callable($computation)) {
            try {

            }catch (UnCatchableException $e) {

            }catch (Exception $e) {

            }catch (Error $e) {

            }
        }

    }


    private function privateCall($callback, $next, $params) {

    }


    private function privateResolve($onfulfill, $next, $params) {

    }


    private function privateReject($onreject, $next, $e) {

    }


    // pending->fulfilled
    public function resolve($value) {

    }

    // pending->rejected
    public function reject($reason) {

    }

    // 将当前 promise 对象的值充填到参数所表示的 promise 对象中
    protected function fill($future) {

    }

    public function then($onfulfill, $onreject = null) {

    }

    public function get(string $property) {
        return isset($this->$property) ? $this->$property : null;
    }

    public function getResult() {
        return $this->value;
    }


    public static function isPromise($obj) {
        return $obj instanceof Promise;
    }


    public static function value($v) {
        $future = new Promise();
        $future->resolve($v);
        return $future;
    }

    public static function toPromise($obj) {
        if ($obj instanceof Promise) {
            return $obj;
        }
        if ($obj instanceof Generator) {

        }
        return self::value($obj);
    }


}