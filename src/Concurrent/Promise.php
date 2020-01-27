<?php
/**
 * Copyright (c) 2020 kakuilan@163.com All rights reserved
 * User: kakuilan
 * Date: 2019/6/26
 * Time: 19:15
 * Desc: 参考 https://github.com/hprose/hprose-php/wiki/03-Promise-%E5%BC%82%E6%AD%A5%E7%BC%96%E7%A8%8B
 */


namespace Kph\Concurrent;

use Kph\Concurrent\Exception\UncatchableException;
use Kph\Objects\BaseObject;
use Closure;
use Error;
use Exception;
use Generator;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use Throwable;
use TypeError;


/**
 * Class Promise
 * @package Kph\Concurrent
 */
class Promise extends BaseObject {


    /**
     * 等待
     */
    const PENDING = 'pending';


    /**
     * 成功
     */
    const FULFILLED = 'fulfilled';


    /**
     * 失败
     */
    const REJECTED = 'rejected';


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


    /**
     * Promise constructor.
     * @param null $computation
     * @throws Exception
     */
    public function __construct($computation = null) {
        if (is_callable($computation)) {
            try {
                $this->resolve(call_user_func($computation));
            } catch (UncatchableException $e) {
                throw $e->getPrevious();
            } catch (Exception $e) {
                $this->reject($e);
            } catch (Error $e) {
                $this->reject($e);
            }
        }

    }


    /**
     * 私有调用
     * @param callable $callback
     * @param Promise $next
     * @param mixed $params
     * @throws Exception
     */
    private function privateCall(callable $callback, Promise $next, $params) {
        try {
            $r = call_user_func($callback, $params);
            $next->resolve($r);
        } catch (UncatchableException $e) {
            throw $e->getPrevious();
        } catch (Exception $e) {
            $next->reject($e);
        } catch (Error $e) {
            $next->reject($e);
        }
    }


    /**
     * 私有解决
     * @param $onfulfill
     * @param Promise $next
     * @param mixed $params
     * @throws Exception
     */
    private function privateResolve($onfulfill, Promise $next, $params) {
        if (is_callable($onfulfill)) {
            $this->privateCall($onfulfill, $next, $params);
        } else {
            $next->resolve($params);
        }
    }


    /**
     * 私有拒绝
     * @param $onreject
     * @param Promise $next
     * @param mixed $params
     * @throws Exception
     */
    private function privateReject($onreject, Promise $next, $params) {
        if (is_callable($onreject)) {
            $this->privateCall($onreject, $next, $params);
        } else {
            $next->reject($params);
        }
    }


    /**
     * 解决.状态由等待变为成功(pending->fulfilled)
     * @param $value
     * @throws Exception
     */
    public function resolve($value) {
        if ($value === $this) {
            $this->reject(new TypeError('Self resolution'));
            return;
        }

        if ($value instanceof Promise) {
            $value->fill($this);
            return;
        }

        if ((($value !== null) && is_object($value)) || is_string($value)) {
            if (method_exists($value, 'then')) {
                $then   = [$value, 'then'];
                $notrun = true;
                $self   = $this;
                try {
                    call_user_func($then, function ($y) use (&$notrun, $self) {
                        if ($notrun) {
                            $notrun = false;
                            $self->resolve($y);
                        }
                    }, function ($r) use (&$notrun, $self) {
                        if ($notrun) {
                            $notrun = false;
                            $self->reject($r);
                        }
                    });
                } catch (UncatchableException $e) {
                    throw $e->getPrevious();
                } catch (Exception $e) {
                    if ($notrun) {
                        $notrun = false;
                        $this->reject($e);
                    }
                } catch (Error $e) {
                    if ($notrun) {
                        $notrun = false;
                        $this->reject($e);
                    }
                }
                return;
            }
        }

        if ($this->state === self::PENDING) {
            $this->state = self::FULFILLED;
            $this->value = $value;
            while (count($this->subscribers) > 0) {
                $subscriber = array_shift($this->subscribers);
                $this->privateResolve($subscriber['onfulfill'], $subscriber['next'], $value);
            }
        }
    }


    /**
     * 拒绝.状态由等待变为失败(pending->rejected)
     * @param $reason
     * @throws Exception
     */
    public function reject($reason) {
        if ($this->state === self::PENDING) {
            $this->state  = self::REJECTED;
            $this->reason = $reason;
            while (count($this->subscribers) > 0) {
                $subscriber = array_shift($this->subscribers);
                $this->privateReject($subscriber['onreject'], $subscriber['next'], $reason);
            }
        }
    }


    /**
     * 检查当前 promise 对象的状态
     * @return array
     */
    public function inspect(): array {
        return ['state' => $this->state, 'value' => $this->value,];
    }


    /**
     * 将要
     * @param mixed $onfulfill
     * @param mixed $onreject
     * @return Promise
     * @throws Exception
     */
    public function then($onfulfill, $onreject = null): Promise {
        if (!is_callable($onfulfill)) {
            $onfulfill = null;
        }
        if (!is_callable($onreject)) {
            $onreject = null;
        }

        $next = new Promise();

        if ($this->state === self::FULFILLED) {
            $this->privateResolve($onfulfill, $next, $this->value);
        } elseif ($this->state === self::REJECTED) {
            $this->privateReject($onreject, $next, $this->reason);
        } else {
            array_push($this->subscribers, ['onfulfill' => $onfulfill, 'onreject' => $onreject, 'next' => $next]);
        }

        return $next;
    }


    /**
     * 将当前 promise 对象的值充填到参数所表示的 promise 对象中
     * @param $future
     * @throws Exception
     */
    protected function fill($future): void {
        $this->then([$future, 'resolve'], [$future, 'reject']);
    }


    /**
     * 获取结果
     * @return mixed
     */
    public function getResult() {
        return $this->value;
    }


    /**
     * 是否promise对象
     * @param object $obj
     * @return bool
     */
    public static function isPromise(object $obj): bool {
        return $obj instanceof Promise;
    }


    /**
     * 获取值的promise对象
     * @param mixed $v
     * @return Promise
     * @throws Exception
     */
    public static function value($v): Promise {
        $future = new Promise();
        $future->resolve($v);
        return $future;
    }


    /**
     * 将对象转换为promise
     * @param mixed $obj
     * @return Promise
     * @throws Exception
     */
    public static function toPromise($obj): Promise {
        if ($obj instanceof Promise) {
            return $obj;
        } elseif ($obj instanceof Generator) {
            return self::co($obj);
        }

        return self::value($obj);
    }


    /**
     * 返回Promise.当数组中所有子元素的promise全部成功fulfilled时,该Promise才为成功fulfilled状态.
     * @param array $array
     * @return Promise
     * @throws Exception
     */
    public static function all(array $array): Promise {
        return self::toPromise($array)->then(function ($array) {
            $keys   = array_keys($array);
            $n      = count($array);
            $result = [];
            if ($n === 0) {
                return self::value($result);
            }
            $future  = new Promise();
            $resolve = function () use ($future, &$result, $keys) {
                $array = [];
                foreach ($keys as $key) {
                    $array[$key] = $result[$key];
                }
                $future->resolve($array);
            };
            $reject  = array($future, "reject");
            foreach ($array as $index => $element) {
                self::toPromise($element)->then(function ($value) use ($index, &$n, &$result, $resolve) {
                    $result[$index] = $value;
                    if (--$n === 0) {
                        $resolve();
                    }
                }, $reject);
            }
            return $future;
        });
    }


    /**
     * 返回Promise.当数组中任一元素的promise成功或失败时,该Promise也成功或失败.
     * @param array $array
     * @return Promise
     * @throws Exception
     */
    public static function any(array $array): Promise {
        return self::toPromise($array)->then(function ($array) {
            $keys = array_keys($array);
            $n    = count($array);
            if ($n === 0) {
                throw new Exception('any(): $array must not be empty');
            }
            $reasons = [];
            $future  = new Promise();
            $resolve = array($future, "resolve");
            $reject  = function () use ($future, &$reasons, $keys) {
                $array = [];
                foreach ($keys as $key) {
                    $array[$key] = $reasons[$key];
                }
                $future->reject($array);
            };
            foreach ($array as $index => $element) {
                self::toPromise($element)->then($resolve, function ($reason) use ($index, &$reasons, &$n, $reject) {
                    $reasons[$index] = $reason;
                    if (--$n === 0) {
                        $reject();
                    }
                });
            }
            return $future;
        });
    }


    /**
     * 包装执行函数
     * @param $handler
     * @return Closure|CallableWrapper|Wrapper
     * @throws ReflectionException
     */
    public static function wrap($handler) {
        if (is_callable($handler)) {
            if (is_array($handler)) {
                $m = new ReflectionMethod($handler[0], $handler[1]);
            } else {
                $m = new ReflectionFunction($handler);
            }
            if ($m->isGenerator()) {
                return function () use ($handler) {
                    return self::all(func_get_args())->then(function ($args) use ($handler) {
                        return self::co(call_user_func_array($handler, $args));
                    });
                };
            }
        }
        if (is_object($handler)) {
            if (is_callable($handler)) {
                return new CallableWrapper($handler);
            }
            return new Wrapper($handler);
        }
        return $handler;
    }


    /**
     * 执行yield生成器
     * @param $generator
     * @return Promise
     * @throws Exception
     */
    public static function co($generator): Promise {
        if (is_callable($generator)) {
            $args      = array_slice(func_get_args(), 1);
            $generator = call_user_func_array($generator, $args);
        }
        if (!($generator instanceof Generator)) {
            return self::toPromise($generator);
        }
        $future      = new Promise();
        $onfulfilled = function ($value) use (&$onfulfilled, &$onrejected, $generator, $future) {
            try {
                $next = $generator->send($value);
                if ($generator->valid()) {
                    self::toPromise($next)->then($onfulfilled, $onrejected);
                } else {
                    if (method_exists($generator, "getReturn")) {
                        $ret = $generator->getReturn();
                        $future->resolve($ret);
                    } else {
                        $future->resolve($value);
                    }
                }
            } catch (Exception $e) {
                $future->reject($e);
            } catch (Error $e) {
                $future->reject($e);
            }
        };
        $onrejected  = function ($err) use (&$onfulfilled, $generator, $future) {
            try {
                $onfulfilled($generator->throw($err));
            } catch (Exception $e) {
                $future->reject($e);
            } catch (Error $e) {
                $future->reject($e);
            }
        };
        self::toPromise($generator->current())->then($onfulfilled, $onrejected);
        return $future;
    }

}