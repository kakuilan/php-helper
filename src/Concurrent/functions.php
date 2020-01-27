<?php
/**
 * Copyright (c) 2020 kakuilan@163.com All rights reserved
 * User: kakuilan
 * Date: 2020/1/27
 * Time: 15:13
 * Desc:
 */

namespace Kph\Concurrent;

use Closure;
use Error;
use Exception;
use Generator;
use Kph\Concurrent\Exception\UncatchableException;
use Kph\Concurrent\Future;
use Kph\Objects\BaseObject;
use phpDocumentor\Reflection\Types\Mixed_;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use Throwable;
use TypeError;


/**
 * 生成匿名函数执行体,主要是自适应处理不定个数的参数.
 * @param callable $callback 回调函数
 * @param mixed $params 参数
 * @return callable
 * @throws ReflectionException
 */
function makeClosureFun(callable $callback, ...$params): callable {
    if (is_array($callback) && count($callback) == 2) {
        $f = new ReflectionMethod($callback[0], $callback[1]);
    } elseif (is_callable($callback)) {
        $f = new ReflectionFunction($callback);
    } else {
        throw new Exception(sprintf("Un callable: %s", var_export($callback, true)));
    }
    $n = $f->getNumberOfParameters();

    $fn = function () use ($callback, $params, $n) {
        $m = count($params);
        switch ($n) {
            case ($n == 1 && $m > 1):
                call_user_func($callback, $params[0]);
                break;
            case ($n > 1 && $m >= $n):
                $newParams = array_slice($params, 0, $n);
                call_user_func($callback, ...$newParams);
                break;
            default:
                call_user_func($callback);
                break;
        }
    };

    return $fn;
}


/**
 * 根据错误构造future
 * @param $e
 * @return Future
 * @throws Exception
 */
function error($e): Future {
    $future = new Future();
    $future->reject($e);
    return $future;
}


/**
 * 根据值构造future
 * @param $v
 * @return Future
 * @throws Exception
 */
function value($v): Future {
    $future = new Future();
    $future->resolve($v);
    return $future;
}


/**
 * 构造成功的future
 * @param $value
 * @return Future
 * @throws Exception
 */
function resolve($value): Future {
    return value($value);
}


/**
 * 构造失败的future
 * @param $reason
 * @return Future
 * @throws Exception
 */
function reject($reason): Future {
    return error($reason);
}


/**
 * 是否Future
 * @param object $obj
 * @return bool
 */
function isFuture(object $obj): bool {
    return $obj instanceof Future;
}


/**
 * 是否Promise
 * @param object $obj
 * @return bool
 */
function isPromise(object $obj): bool {
    return $obj instanceof Future;
}


/**
 * 将对象转为Future
 * @param mixed $obj
 * @return Future
 * @throws Exception
 */
function toFuture($obj): Future {
    return isFuture($obj) ? $obj : value($obj);
}


/**
 * 将对象转为Promise
 * @param mixed $obj
 * @return Future
 * @throws Exception
 */
function toPromise($obj): Future {
    return isPromise($obj) ? $obj : value($obj);
}


/**
 * 异步执行
 * @param callable $computation
 * @return Future
 * @throws Exception
 */
function sync(callable $computation): Future {
    try {
        return toPromise(call_user_func($computation));
    } catch (UncatchableException $e) {
        throw $e->getPrevious();
    } catch (Exception $e) {
        return error($e);
    } catch (Throwable $e) {
        return error($e);
    }
}


/**
 * 快速创建promise
 * @param callable $executor
 * @return Future
 * @throws Exception
 */
function promise(callable $executor): Future {
    $future = new Future();
    call_user_func($executor, function ($value) use ($future) {
        $future->resolve($value);
    }, function ($reason) use ($future) {
        $future->reject($reason);
    });
    return $future;
}


/**
 * 返回Promise.当数组中所有子元素的promise全部成功fulfilled时,该Promise才为成功fulfilled状态.
 * @param array $array
 * @return Future
 * @throws Exception
 */
function all(array $array): Future {
    return toFuture($array)->then(function ($array) {
        $keys   = array_keys($array);
        $n      = count($array);
        $result = [];
        if ($n === 0) {
            return value($result);
        }
        $future  = new Future();
        $resolve = function () use ($future, &$result, $keys) {
            $array = [];
            foreach ($keys as $key) {
                $array[$key] = $result[$key];
            }
            $future->resolve($array);
        };
        $reject  = [$future, "reject"];
        foreach ($array as $index => $element) {
            toFuture($element)->then(function ($value) use ($index, &$n, &$result, $resolve) {
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
 * 类似all,但参数形式不同.
 * @param mixed $args
 * @return Future
 * @throws Exception
 */
function join(...$args): Future {
    return all($args);
}


/**
 * race返回数组中最先返回的promise数据
 * @param array $array promise数组
 * @return Future
 * @throws Exception
 */
function race(array $array):Future {
    return toFuture($array)->then(
        function($array) {
            $future = new Future();
            foreach ($array as $element) {
                toFuture($element)->fill($future);
            }
            return $future;
        }
    );
}
