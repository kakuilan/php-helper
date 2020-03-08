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
use Kph\Exceptions\UncatchableException;
use Kph\Concurrent\Future;
use Kph\Objects\BaseObject;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use Throwable;
use TypeError;
use RangeException;


/**
 * 生成匿名函数执行体,主要是自适应处理不定个数的参数.
 * @param callable $callback 回调函数
 * @param mixed ...$params 参数
 * @return callable
 * @throws ReflectionException
 * @throws Exception
 */
function makeClosureFun($callback, ...$params): callable {
    if (is_array($callback) && count($callback) == 2) {
        $f = new ReflectionMethod($callback[0], $callback[1]);
    } elseif (is_callable($callback)) {
        $f = new ReflectionFunction($callback);
    } else {
        throw new Exception(sprintf("Un callable: %s", var_export($callback, true)));
    }
    $n = $f->getNumberOfParameters();

    $fn = function () use ($callback, $params, $n) {
        $m   = count($params);
        $res = null;
        switch ($n) {
            case ($n == 1 && $m >= 1):
                $res = call_user_func($callback, $params[0]);
                break;
            case ($n > 1 && $m >= $n):
                $newParams = array_slice($params, 0, $n);
                $res       = call_user_func($callback, ...$newParams);
                break;
            default:
                $res = call_user_func($callback);
        }

        return $res;
    };

    return $fn;
}


/**
 * 是否生成器
 * @param $var
 * @return bool
 * @throws ReflectionException
 */
function isGenerator($var): bool {
    if (is_callable($var) && !is_object($var)) {
        try {
            $var = call_user_func($var);
        } catch (Throwable $e) {
        }
    }

    if (is_object($var)) {
        if ($var instanceof Closure) {
            $fn  = Closure::bind($var, null);
            $ref = new ReflectionFunction($fn);
            //闭包中包含生成器
            if ($ref->isGenerator()) {
                return true;
            }
        } elseif ($var instanceof Generator) {
            return true;
        }
    }

    return false;
}


/**
 * 根据错误构造future
 * @param mixed $e
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
 * @param mixed $v
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
 * @param mixed $value
 * @return Future
 * @throws Exception
 */
function resolve($value): Future {
    return value($value);
}


/**
 * 构造失败的future
 * @param mixed $reason
 * @return Future
 * @throws Exception
 */
function reject($reason): Future {
    return error($reason);
}


/**
 * 是否Future
 * @param mixed $obj
 * @return bool
 */
function isFuture($obj): bool {
    return is_object($obj) && $obj instanceof Future;
}


/**
 * 是否Promise
 * @param mixed $obj
 * @return bool
 */
function isPromise($obj): bool {
    return is_object($obj) && $obj instanceof Future;
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
    if (isFuture($obj)) {
        return $obj;
    }

    if (isGenerator($obj)) {
        return co($obj);
    }

    return value($obj);
}


/**
 * 同步化yield生成器
 * @param mixed $generator 生成器
 * @param mixed ...$args
 * @return Future
 * @throws Exception
 */
function co($generator, ...$args): Future {
    if (is_callable($generator)) {
        $generator = call_user_func_array($generator, $args);
    }
    if (!($generator instanceof Generator)) {
        return toFuture($generator);
    }

    $future      = new Future();
    $onfulfilled = function ($value) use (&$onfulfilled, &$onrejected, $generator, $future) {
        try {
            $next = $generator->send($value);
            if ($generator->valid()) {
                toPromise($next)->then($onfulfilled, $onrejected);
            } else {
                if (method_exists($generator, "getReturn")) {
                    $ret = $generator->getReturn();
                    $future->resolve(($ret === null) ? $value : $ret);
                } else {
                    $future->resolve($value);
                }
            }
        } catch (Throwable $e) {
            $onrejected($e);
        }
    };
    $onrejected  = function ($err) use (&$onfulfilled, $generator, $future) {
        try {
            $onfulfilled($generator->throw($err));
        } catch (Throwable $e) {
            $future->reject($e);
        }
    };

    toPromise($generator->current())->then($onfulfilled, $onrejected);
    return $future;
}


/**
 * 同步执行
 * sync 函数跟 Futrue 构造方法区别在于结果上:
 * 通过 Future 构造方法的结果中如果包含生成器函数或者是生成器，则生成器函数和生成器将原样返回;
 * 而通过 sync 函数返回的生成器函数或生成器会作为协程执行之后，返回执行结果
 * @param callable $computation
 * @return Future
 * @throws Exception
 */
function sync($computation): Future {
    try {
        return toPromise(call_user_func($computation));
    } catch (UncatchableException $e) {
        $previou = $e->getPrevious();
        throw (is_object($previou) ? $previou : $e);
    } catch (Throwable $e) {
        return error($e);
    }
}


/**
 * 快速创建promise
 * @param callable $executor
 * @return Promise
 * @throws Exception
 */
function promise(callable $executor): Promise {
    $promise = new Promise($executor);
    return $promise;
}


/**
 * 把带有回调的函数$fn做promise化
 * 返回一个promise的匿名函数,它的参数为$fn的参数(不包括最后一个callback)
 * @param callable $fn 执行体,最后一参数为callback回调
 * @return callable
 */
function promisify(callable $fn): callable {
    //$args是$fn的参数
    return function (...$args) use ($fn) {
        $future = new Future();
        //新建$fn最后一个参数,是可执行的回调函数
        $args[] = function (...$params) use ($future, $args) {
            //$params是回调函数参数
            switch (count($params)) {
                case 0:
                    $future->resolve(null);
                    break;
                case 1:
                    $future->resolve($params[0]);
                    break;
                default:
                    $future->resolve($params);
                    break;
            }
        };
        try {
            call_user_func_array($fn, $args);
        } catch (Throwable $e) {
            $future->reject($e);
        }
        return $future;
    };
}

/**
 * 返回Promise
 * 当数组中所有子元素的promise全部成功fulfilled时,该Promise才为成功fulfilled状态.
 * 其值为数组参数中所有 promise 对象的最终展开值组成的数组，其数组元素与原数组元素一一对应
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
 * 类似all,但参数形式不同
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
function race(array $array): Future {
    return toFuture($array)->then(function ($array) {
        $future = new Future();
        foreach ($array as $element) {
            toFuture($element)->fill($future);
        }
        return $future;
    });
}


/**
 * race的改进版
 * 对于 race 函数,如果输入的数组为空,返回的 promise 对象将永远保持为待定（pending）状态.
 * 而对于 any 函数,如果输入的数组为空,返回的 promise 对象将被设置为失败状态,失败原因是一个 RangeException 对象.
 * @param array $array
 * @return Future
 * @throws Exception
 */
function any(array $array): Future {
    return toFuture($array)->then(function ($array) {
        $keys = array_keys($array);
        $n    = count($array);
        if ($n === 0) {
            throw new RangeException('any(): $array must not be empty');
        }
        $reasons = [];
        $future  = new Future();
        $resolve = [$future, "resolve"];
        $reject  = function () use ($future, &$reasons, $keys) {
            $array = [];
            foreach ($keys as $key) {
                $array[$key] = $reasons[$key];
            }
            $future->reject($array);
        };
        foreach ($array as $index => $element) {
            toFuture($element)->then($resolve, function ($reason) use ($index, &$reasons, &$n, $reject) {
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
 * 返回Promise
 * 当数组中所有子元素的promise全部完成(成功或失败)时,该Promise才为成功fulfilled状态.
 * 其值为数组参数中所有 promise 对象的 inspect 方法返回值，其数组元素与原数组元素一一对应
 * @param array $array
 * @return Future
 * @throws Exception
 */
function settle(array $array): Future {
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
        foreach ($array as $index => $element) {
            $f = toFuture($element);
            $f->whenComplete(function () use ($index, $f, &$result, &$n, $resolve) {
                $result[$index] = $f->inspect();
                if (--$n === 0) {
                    $resolve();
                }
            });
        }
        return $future;
    });
}


/**
 * 执行 $handler 函数并返回一个包含执行结果的 promise 对象
 * @param callable $handler
 * @param mixed ...$args $handler的参数,可以是promise
 * @return Future
 * @throws Exception
 */
function run(callable $handler, ...$args): Future {
    return all($args)->then(function ($args) use ($handler) {
        return call_user_func_array($handler, $args);
    });
}


/**
 * 包装执行函数
 * @param $handler
 * @return Closure|CallableWrapper|Future|Wrapper
 * @throws Exception
 */
function wrap($handler) {
    if (is_object($handler)) {
        if (isGenerator($handler)) {
            return co($handler);
        } elseif (is_callable($handler)) {
            return new CallableWrapper($handler);
        }
        return new Wrapper($handler);
    }
    if (is_callable($handler)) {
        return function () use ($handler) {
            return all(func_get_args())->then(function ($args) use ($handler) {
                return co(call_user_func_array($handler, $args));
            });
        };
    }

    return $handler;
}


/**
 * 对参数数组中的每个元素的展开值进行遍历
 * 回调方法的格式如 function callback(mixed $value[, mixed $key[, array $array]])
 * @param array $array
 * @param callable $callback
 * @return Future
 * @throws Exception
 */
function each(array $array, callable $callback): Future {
    return all($array)->then(function ($array) use ($callback) {
        foreach ($array as $key => $value) {
            makeClosureFun($callback, $value, $key, $array)();
        }
    });
}


/**
 * 遍历数组中的每一个元素并执行回调 $callback，当所有 $callback 的返回值都为 true 时，结果为 true，否则为 false
 * 回调方法的格式如 bool callback(mixed $value[, mixed $key[, array $array]])
 * @param array $array
 * @param callable $callback
 * @return Future
 * @throws Exception
 */
function every(array $array, callable $callback): Future {
    return all($array)->then(function ($array) use ($callback) {
        foreach ($array as $key => $value) {
            $ret = makeClosureFun($callback, $value, $key, $array)();
            if (!$ret) {
                return false;
            }
        }

        return true;
    });
}


/**
 * 遍历数组中的每一个元素并执行回调 $callback，当任意一个 $callback 的返回值为 true 时，结果为 true，否则为 false
 * 回调方法的格式如 bool callback(mixed $value[, mixed $key[, array $array]])
 * @param array $array
 * @param callable $callback
 * @return Future
 * @throws Exception
 */
function some(array $array, callable $callback): Future {
    return all($array)->then(function ($array) use ($callback) {
        foreach ($array as $key => $value) {
            $ret = makeClosureFun($callback, $value, $key, $array)();
            if ($ret) {
                return true;
            }
        }
        return false;
    });
}


/**
 * 遍历数组中的每一个元素并执行回调 $callback，$callback 的返回值为 true 的元素所组成的数组将作为 filter 返回结果的 promise 对象所包含的值
 * 回调方法的格式如 bool callback(mixed $value[, mixed $key[, array $array]])
 * @param array $array
 * @param callable $callback
 * @param bool $preserveKeys 是否保持键
 * @return Future
 * @throws Exception
 */
function filter(array $array, callable $callback, $preserveKeys = false): Future {
    return all($array)->then(function ($array) use ($callback, $preserveKeys) {
        $result    = [];
        $setResult = function ($key, $value) use (&$result, $preserveKeys) {
            if ($preserveKeys) {
                $result[$key] = $value;
            } else {
                $result[] = $value;
            }
        };
        foreach ($array as $key => $value) {
            $ret = makeClosureFun($callback, $value, $key, $array)();
            if ($ret) {
                $setResult($key, $value);
            }
        }
        return $result;
    });
}


/**
 * 遍历数组中的每一个元素并执行回调 $callback，$callback 的返回值所组成的数组将作为 map 返回结果的 promise 对象所包含的值
 * 回调方法的格式如 mixed callback(mixed $value[, mixed $key[, array $array]])
 * @param array $array
 * @param callable $callback
 * @return Future
 * @throws Exception
 */
function map(array $array, callable $callback): Future {
    return all($array)->then(function ($array) use ($callback) {
        $result = [];
        foreach ($array as $key => $value) {
            $result[$key] = makeClosureFun($callback, $value, $key, $array)();
        }
        return $result;
    });
}


/**
 * 遍历数组中的每一个元素并执行回调 $callback,并累计为一个值
 * $callback 的第一个参数为 $initial 的值或者上一次调用的返回值
 * 最后一次 $callback 的返回结果作为 promise 对象所包含的值
 * 回调方法的格式如 mixed callback(mixed $carry, mixed $item);
 * @param array $array
 * @param callable $callback
 * @param null $initial 结果的初始值
 * @return Future
 * @throws Exception
 */
function reduce(array $array, callable $callback, $initial = null): Future {
    if ($initial !== null) {
        return all($array)->then(function ($array) use ($callback, $initial) {
            $initial = toFuture($initial);
            return $initial->then(function ($initial) use ($array, $callback) {
                return array_reduce($array, $callback, $initial);
            });
        });
    }

    return all($array)->then(function ($array) use ($callback) {
        return array_reduce($array, $callback);
    });
}


/**
 * 在 promise 对象所包含的数组中查找 $searchElement 元素
 * 返回值以 promise 对象形式返回，如果找到，返回的 promise 对象中将包含该元素对应的 key，否则为 false
 * @param array $array 可以是一个包含数组的 promise 对象，也可以是一个包含有 promise 对象的数组
 * @param mixed $searchElement 要查找的元素
 * @param bool $strict 为 true 时，使用 === 运算符进行相等测试
 * @return Future
 * @throws Exception
 */
function search(array $array, $searchElement, $strict = false): Future {
    return all($array)->then(function ($array) use ($searchElement, $strict) {
        $searchElement = toFuture($searchElement);
        return $searchElement->then(function ($searchElement) use ($array, $strict) {
            return array_search($searchElement, $array, $strict);
        });
    });
}


/**
 * 同 search 类似，只是在找到的情况下，仅仅返回包含 true 的 promise 对象
 * @param array $array
 * @param $searchElement
 * @param bool $strict
 * @return Future
 * @throws Exception
 */
function includes(array $array, $searchElement, $strict = false): Future {
    return all($array)->then(function ($array) use ($searchElement, $strict) {
        $searchElement = toFuture($searchElement);
        return $searchElement->then(function ($searchElement) use ($array, $strict) {
            return in_array($searchElement, $array, $strict);
        });
    });
}


/**
 * 计算数组的差集
 * 对比 array1 和其他一个或者多个数组，返回在 array1 中但是不在其他数组里的值
 * @param array ...$params
 * @return Future
 * @throws Exception
 */
function diff(array ...$params): Future {
    $args = [];
    foreach ($params as $i => $param) {
        $args[$i] = all($param);
    }

    return all($args)->then(function ($array) {
        return call_user_func_array("array_diff", $array);
    });
}


/**
 * 用回调函数比较数据来计算数组的差集
 * @param mixed ...$params 不定参数.注意,最后一个参数为回调函数
 * @return Future
 * @throws Exception
 */
function udiff(...$params): Future {
    $callback = array_pop($params); //最后一个参数为回调函数
    $args     = [];
    foreach ($params as $i => $param) {
        $args[$i] = all($param);
    }

    return all($args)->then(function ($array) use ($callback) {
        array_push($array, $callback);
        return call_user_func_array("array_udiff", $array);
    });
}