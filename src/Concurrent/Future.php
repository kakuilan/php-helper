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
use Kph\Concurrent\Exception\UncatchableException;
use Kph\Objects\BaseObject;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use Throwable;
use TypeError;

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
     * Future constructor.
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
     * @throws Exception
     */
    private function privateCall(callable $callback, Future $next, $params) {
        try {
            $r = call_user_func($callback, $params);
            $next->resolve($r);
        } catch (UncatchableException $e) {
            throw $e->getPrevious();
        } catch (Exception $e) {
            $next->reject($e);
        } catch (Throwable $e) {
            $next->reject($e);
        }
    }


    /**
     * 私有解决
     * @param callable $onfulfill 成功事件
     * @param Future $next
     * @param mixed $params
     * @throws Exception
     */
    private function privateResolve(callable $onfulfill, Future $next, $params) {
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
     * @throws Exception
     */
    private function privateReject(callable $onreject, Future $next, $params) {
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
        } elseif (isFuture($value)) {
            $value->fill($this);
            return;
        }

        if ((($value !== null) && is_object($value)) || (is_string($value) && class_exists($value, false))) {
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
                } catch (Throwable $e) {
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
     * 将要
     * @param mixed $onfulfill 当成功时的执行体
     * @param mixed $onreject 当失败时的执行体
     * @return Future
     * @throws Exception
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
     * 完成.类似then,但无返回值,不支持链式调用;用于单元测试.
     * @param $onfulfill
     * @param null $onreject
     * @throws Exception
     */
    public function done($onfulfill, $onreject = NULL): void {
        $this->then($onfulfill, $onreject)->then(NULL, function (Throwable $error) {
            throw new UncatchableException("", 0, $error);
        });
    }


    /**
     * 检查当前 promise 对象的状态
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
     * 捕获错误
     * @param $onreject
     * @param mixed $fn
     * @return Future
     * @throws Exception
     */
    public function catchError($onreject, $fn = NULL) {
        if (is_callable($fn)) {
            $self = $this;
            return $this->then(NULL, function ($e) use ($self, $onreject, $fn) {
                if (call_user_func($fn, $e)) {
                    return $self->then(NULL, $onreject);
                } else {
                    throw $e;
                }
            });
        }
        return $this->then(NULL, $onreject);
    }


    /**
     * 失败.用于单元测试.
     * @param $onreject
     * @throws Exception
     */
    public function fail($onreject): void {
        $this->done(NULL, $onreject);
    }


    /**
     * 当完成时(无论成功或失败).
     * @param callable $fn 执行体
     * @return Future
     * @throws Exception
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
     * 完成.无论成功或失败,支持链式调用.
     * @param callable $oncomplete
     * @return Future
     * @throws Exception
     */
    public function complete(callable $oncomplete = null): Future {
        $oncomplete = $oncomplete ?: function ($v) {
            return $v;
        };
        return $this->then($oncomplete, $oncomplete);
    }


    /**
     * 总是.无论成功或失败,不支持链式.
     * @param callable $oncomplete
     * @throws Exception
     */
    public function always(callable $oncomplete): void {
        $this->done($oncomplete, $oncomplete);
    }


    /**
     * 将当前 promise 对象的值充填到参数所表示的 promise 对象中
     * @param $future
     * @throws Exception
     */
    public function fill($future): void {
        $this->then([$future, 'resolve'], [$future, 'reject']);
    }


    /**
     * then成功后简写,将结果(单一值)作为回调参数.
     * @param callable $onfulfilledCallback
     * @return Future
     * @throws Exception
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
     * @throws Exception
     */
    public function spread(callable $onfulfilledCallback): Future {
        return $this->then(function ($array) use ($onfulfilledCallback) {
            return call_user_func_array($onfulfilledCallback, $array);
        });
    }


}