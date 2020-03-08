<?php
/**
 * Copyright (c) 2020 kakuilan@163.com All rights reserved
 * User: kakuilan
 * Date: 2020/1/27
 * Time: 15:09
 * Desc: 本库Concurrent部分参考hprose-php改写,参考 https://github.com/hprose/hprose-php/wiki
 */


namespace Kph\Concurrent;

use Closure;
use Error;
use Exception;
use Generator;
use Kph\Exceptions\UncatchableException;
use Kph\Objects\BaseObject;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use Throwable;
use TypeError;


/**
 * Class Future
 * @package Kph\Concurrent
 */
class Future extends BaseObject {

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
     * @var string
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
     * Future constructor.
     * @param mixed|null $computation
     * @throws Throwable
     */
    public function __construct($computation = null) {
        if (is_callable($computation)) {
            try {
                $this->resolve(call_user_func($computation));
            } catch (UncatchableException $e) {
                $previou = $e->getPrevious();
                throw (is_object($previou) ? $previou : $e);
            } catch (Throwable $e) {
                $this->reject($e);
            }
        }
    }


    /**
     * 私有调用
     * @param callable $callback
     * @param Future $next
     * @param mixed $params
     * @throws Throwable
     */
    private function privateCall(callable $callback, Future $next, $params) {
        try {
            $r = call_user_func($callback, $params);
            $next->resolve($r);
        } catch (UncatchableException $e) {
            $previou = $e->getPrevious();
            throw (is_object($previou) ? $previou : $e);
        } catch (Throwable $e) {
            $next->reject($e);
        }
    }


    /**
     * 私有解决
     * @param callable $onfulfill 成功事件
     * @param Future $next
     * @param mixed $params
     * @throws Throwable
     */
    private function privateResolve($onfulfill, Future $next, $params) {
        if (is_callable($onfulfill)) {
            $this->privateCall($onfulfill, $next, $params);
        } else {
            $next->resolve($params);
        }
    }


    /**
     * 私有拒绝
     * @param callable $onreject 失败事件
     * @param Future $next
     * @param mixed $params
     * @throws Throwable
     */
    private function privateReject($onreject, Future $next, $params) {
        if (is_callable($onreject)) {
            $this->privateCall($onreject, $next, $params);
        } else {
            $next->reject($params);
        }
    }


    /**
     * 解决
     * 该方法可以将状态为待定（pending）的 promise 对象变为成功（fulfilled）状态
     * @param mixed $value
     * @throws Throwable
     */
    public function resolve($value) {
        var_dump('resolve----000000000', $value);
        if ($value === $this) {
            $this->reject(new TypeError('Self resolution'));
            return;
        } elseif (isFuture($value)) {
            $value->fill($this);
            return;
        }

        $then = null;
        if (is_callable($value)) {
            $then = $value;
        } elseif (is_object($value)  && method_exists($value, 'then')) {
            $then = [$value, 'then'];
        }elseif(is_string($value) && class_exists($value) && method_exists($value, 'then')){
            $obj = new $value();
            $then = [$obj, 'then'];
        }

        if(!is_null($then)) {

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
                $previou = $e->getPrevious();
                throw (is_object($previou) ? $previou : $e);
            } catch (Throwable $e) {
                if ($notrun) {
                    $notrun = false;
                    $this->reject($e);
                }
            }
            return;
        }

        if ($this->state === self::PENDING) {
            $this->state = self::FULFILLED;
            $this->value = $value;
            while (count($this->subscribers) > 0) {
                var_dump('resolve----1111111');
                $subscriber = array_shift($this->subscribers);
                $this->privateResolve($subscriber['onfulfill'], $subscriber['next'], $value);
            }
        }
    }


    /**
     * 拒绝
     * 该方法可以将状态为待定（pending）的 promise 对象变为失败（rejected）状态
     * @param $reason
     * @throws Throwable
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
     * 将要
     * 支持链式调用.
     * @param mixed $onfulfill 当成功时的执行体
     * @param mixed $onreject 当失败时的执行体
     * @return Future
     * @throws Throwable
     */
    public function then($onfulfill, $onreject = null): Future {
        if (!is_callable($onfulfill)) {
            $onfulfill = null;
        }
        if (!is_callable($onreject)) {
            $onreject = null;
        }

        $next = new Future();

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
     * 完成
     * 类似then,但无返回值,不支持链式调用;用于单元测试.
     * @param $onfulfill
     * @param null $onreject
     * @throws Throwable
     */
    public function done($onfulfill, $onreject = null): void {
        $this->then($onfulfill, $onreject)->then(null, function (Throwable $error) {
            throw new UncatchableException("", 0, $error);
        });
    }


    /**
     * 失败
     * 该方法是 done(null, $onreject) 的简化.用于单元测试.
     * @param $onreject
     * @throws Throwable
     */
    public function fail($onreject): void {
        $this->done(null, $onreject);
    }


    /**
     * 当完成时(无论成功或失败).
     * @param callable $fn 执行体
     * @return Future
     * @throws Throwable
     */
    public function whenComplete(callable $fn): Future {
        return $this->then(function ($v) use ($fn) {
            makeClosureFun($fn, $v)();
            return $v;
        }, function ($e) use ($fn) {
            makeClosureFun($fn, $e)();
            throw $e;
        });
    }


    /**
     * 完成
     * 无论成功或失败,支持链式调用.
     * 是then(oncomplete, oncomplete)的简化
     * @param callable $oncomplete
     * @return Future
     * @throws Throwable
     */
    public function complete(callable $oncomplete = null): Future {
        $oncomplete = $oncomplete ?: function ($v) {
            return $v;
        };
        return $this->then($oncomplete, $oncomplete);
    }


    /**
     * 总是
     * 无论成功或失败,不支持链式.
     * 是done(oncomplete, oncomplete) 的简化
     * @param callable $oncomplete
     * @throws Throwable
     */
    public function always(callable $oncomplete): void {
        $this->done($oncomplete, $oncomplete);
    }


    /**
     * 将当前 promise 对象的值充填到参数所表示的 promise 对象中
     * @param $future
     * @throws Throwable
     */
    public function fill($future): void {
        $this->then([$future, 'resolve'], [$future, 'reject']);
    }


    /**
     * then成功后简写,将结果(单一值)作为回调参数.
     * @param callable $onfulfilledCallback
     * @return Future
     * @throws Throwable
     */
    public function tap(callable $onfulfilledCallback): Future {
        return $this->then(function ($result) use ($onfulfilledCallback) {
            call_user_func($onfulfilledCallback, $result);
            return $result;
        });
    }


    /**
     * then成功后简写,将结果(数组)作为回调参数.
     * @param callable $onfulfilledCallback
     * @return Future
     * @throws Throwable
     */
    public function spread(callable $onfulfilledCallback): Future {
        return $this->then(function ($array) use ($onfulfilledCallback) {
            return call_user_func_array($onfulfilledCallback, $array);
        });
    }


    /**
     * 返回当前 promise 对象的状态
     * 如果当前状态为待定（pending），返回值为：['state' => 'pending']
     * 如果当前状态为成功（fulfilled），返回值为：['state' => 'fulfilled', 'value' => $promise->value]
     * 如果当前状态为失败（rejected），返回值为：['state' => 'rejected', 'reason' => $promise->reason]
     * @return array
     */
    public function inspect(): array {
        $res = ['state' => $this->state,];

        switch ($this->state) {
            case self::PENDING:
                break;
            case self::FULFILLED:
                $res['value'] = $this->value;
                break;
            case self::REJECTED:
                $res['reason'] = $this->reason;
                break;
        }

        return $res;
    }


    /**
     * 获取结果
     * @return mixed|null
     */
    public function getResult() {
        $status = $this->inspect();
        return $status['value'] ?? null;
    }


    /**
     * 获取原因
     * @return mixed|null
     */
    public function getReason() {
        $status = $this->inspect();
        return $status['reason'] ?? null;
    }


    /**
     * 是否正待定
     * @return bool
     */
    public function isPending(): bool {
        return $this->state === self::PENDING;
    }


    /**
     * 是否已成功
     * @return bool
     */
    public function isFulfilled(): bool {
        return $this->state === self::FULFILLED;
    }


    /**
     * 是否已失败
     * @return bool
     */
    public function isRejected(): bool {
        return $this->state === self::REJECTED;
    }


    /**
     * 是否已完成
     * @return bool
     */
    public function isCompleted(): bool {
        return in_array($this->state, [self::FULFILLED, self::REJECTED]);
    }


    /**
     * 捕获错误
     * 该方法是 then(null, $onreject) 的简化.
     * @param $onreject
     * @param callable $fn
     * @return Future
     * @throws Throwable
     */
    public function catchError($onreject, callable $fn = null): Future {
        if (is_callable($fn)) {
            $self = $this;
            return $this->then(null, function ($e) use ($self, $onreject, $fn) {
                if (call_user_func($fn, $e)) {
                    return $self->then(null, $onreject);
                } else {
                    throw $e;
                }
            });
        }
        return $this->then(null, $onreject);
    }


    /**
     * 获取状态或结果key的值
     * @param string $key
     * @return string|Future
     * @throws Throwable
     */
    public function __get(string $key) {
        if ($key == 'state') {
            return $this->state;
        }
        return $this->then(function ($result) use ($key) {
            return $result->$key ?? null;
        });
    }


    /**
     * 自动调用方法
     * @param string $method 方法名
     * @param array $args 参数
     * @return Future
     * @throws Throwable
     */
    public function __call(string $method, array $args): Future {
        if ($args === null) {
            $args = [];
        }
        return $this->then(function ($result) use ($method, $args) {
            return all($args)->then(function ($args) use ($result, $method) {
                return call_user_func_array([$result, $method], $args);
            });
        });
    }


}