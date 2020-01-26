<?php
/**
 * Created by PhpStorm.
 * User: kakuilan
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
    const PENDING = 'pending';

    //成功
    const FULFILLED = 'fulfilled';

    //失败
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


    public function __construct($computation = null) {
        if (is_callable($computation)) {
            try {

            } catch (UnCatchableException $e) {

            } catch (Exception $e) {

            } catch (Error $e) {

            }
        }

    }


    private function privateCall($callback, $next, $params) {
        try {
            $r = call_user_func($callback, $params);
            $next->resolve($r);
        } catch (UnCatchableException $e) {
            throw $e->getPrevious();
        } catch (Exception $e) {
            $next->reject($e);
        } catch (Error $e) {
            $next->reject($e);
        }
    }


    private function privateResolve($onfulfill, $next, $params) {
        if (is_callable($onfulfill)) {
            $this->privateCall($onfulfill, $next, $params);
        } else {
            $next->resolve($params);
        }
    }


    private function privateReject($onreject, $next, $params) {
        if (is_callable($onreject)) {
            $this->privateCall($onreject, $next, $params);
        } else {
            $next->reject($params);
        }
    }


    // pending->fulfilled
    public function resolve($value) {
        if ($value === $this) {
            $this->reject(new TypeError('Self resolution'));
            return;
        }

        if ($value instanceof Promise) {
            $value->fill($this);
            return;
        }

        if ((($value !== NULL) && is_object($value)) || is_string($value)) {
            if (method_exists($value, 'then')) {
                $then = [$value, 'then'];
                $notrun = true;
                $self = $this;
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
                } catch (UnCatchableException $e) {
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

    // pending->rejected
    public function reject($reason) {
        if ($this->state === self::PENDING) {
            $this->state = self::REJECTED;
            $this->reason = $reason;
            while (count($this->subscribers) > 0) {
                $subscriber = array_shift($this->subscribers);
                $this->privateReject($subscriber['onreject'], $subscriber['next'], $reason);
            }
        }
    }

    // 将当前 promise 对象的值充填到参数所表示的 promise 对象中
    protected function fill($future) {
        $this->then([$future, 'resolve'], [$future, 'reject']);
    }


    public function then($onfulfill, $onreject = null) {
        if (!is_callable($onfulfill)) {
            $onfulfill = NULL;
        }
        if (!is_callable($onreject)) {
            $onreject = NULL;
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