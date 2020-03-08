<?php
/**
 * Copyright (c) 2020 kakuilan@163.com All rights reserved
 * User: kakuilan
 * Date: 2020/1/27
 * Time: 14:06
 * Desc:
 */

namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Error;
use Exception;
use Faker\Factory;
use Generator;
use Kph\Concurrent;
use Kph\Exceptions\UncatchableException;
use Kph\Concurrent\Future;
use Kph\Concurrent\Promise;
use Kph\Helpers\OsHelper;
use Kph\Tests\Objects\BaseCls;
use Kph\Tests\Objects\BaseServ;
use Kph\Tests\Objects\MathCls;
use Kph\Tests\Future\MyGenerator;
use RuntimeException;
use Throwable;

class PromiseTest extends TestCase {


    /**
     * @throws \ReflectionException
     */
    public function testIsGenerator() {
        $chk = Concurrent\isGenerator('time');
        $this->assertFalse($chk);

        $chk = Concurrent\isGenerator([MyGenerator::class, 'randName']);
        $this->assertTrue($chk);

        $chk = Concurrent\isGenerator([MathCls::class, 'add']);
        $this->assertFalse($chk);
    }


    /**
     * @throws Exception
     */
    public function testIsFuture() {
        //创建待定状态的promise
        $promise = new Future();
        $chk     = $promise->isPending();
        $this->assertTrue($chk);
        $ret = $promise->getResult();
        $this->assertNull($ret);

        $chk = Concurrent\isFuture($promise);
        $this->assertTrue($chk);

        $chk = Concurrent\isPromise($promise);
        $this->assertTrue($chk);

        $chk = Concurrent\isFuture($this);
        $this->assertFalse($chk);

        //创建成功状态的promise
        $promise = new Future(function () {
            return 'hello';
        });
        $promise->then(function ($value) {
            $this->assertEquals($value, 'hello');
        });

        $ret = $promise->getResult();
        $this->assertEquals($ret, 'hello');

        $chk = $promise->isFulfilled();
        $this->assertTrue($chk);

        //创建失败状态的promise
        $promise = new Future(function () {
            throw new Exception('error');
        });
        $promise->catchError(function ($reason) {
            $chk = $reason instanceof Exception;
            $this->assertTrue($chk);
        });

        $reason = $promise->getReason();
        $this->assertNotNull($reason);

        $chk = $promise->isRejected();
        $this->assertTrue($chk);
    }


    /**
     * @throws Exception
     */
    public function testFunResolve() {
        //创建成功状态
        $promise = Concurrent\resolve('world');
        $chk1    = $promise->isFulfilled();
        $chk2    = $promise->isCompleted();

        $this->assertTrue($chk1);
        $this->assertTrue($chk2);
    }


    /**
     * @throws Exception
     */
    public function testFunReject() {
        //创建失败状态
        $e       = new Exception('error');
        $promise = Concurrent\reject($e);
        $chk1    = $promise->isRejected();
        $chk2    = $promise->isCompleted();

        $this->assertTrue($chk1);
        $this->assertTrue($chk2);
    }


    /**
     * @throws Exception
     */
    public function testFunToFutureToPromise() {
        $test    = $this;
        $future  = Concurrent\toFuture(1);
        $promise = Concurrent\toPromise($future);
        $promise->then(function ($res) {
            //不能在回调里面进行断言
        });
        $res = $promise->getResult();
        $this->assertEquals($res, 1);

        //闭包中引用生成器
        $promise = Concurrent\toPromise(function () {
            yield MyGenerator::randName();
        });
        $promise->then(function ($res) {
        });
        $res = $promise->getResult();
        $this->assertNotEmpty($res);

        //直接调用生成器
        $promise = Concurrent\toPromise(MyGenerator::randAddr());
        $promise->then(function ($res) {
        });
        $res = $promise->getResult();
        $this->assertNotEmpty($res);
    }


    /**
     * @throws Exception
     */
    public function testFunCo() {
        $promise = Concurrent\co(function () {
            yield MyGenerator::randNum();
        });
        $res     = $promise->getResult();
        $this->assertNotEmpty($res);

        $obj     = new BaseCls();
        $promise = Concurrent\co($obj);
        $this->assertTrue(Concurrent\isPromise($promise));

        //注意,结果为生成器迭代完成的最后一个结果
        //此段代码本地通过,但travis失败
        //        $promise = Concurrent\co(MyGenerator::num());
        //        $res = $promise->getResult();
        //        $this->assertEquals($res, 99);

        $fn1 = function (string $url) {
            $res = yield OsHelper::curlDownload($url, '', [], true);
            if (empty($res)) {
                throw new Exception('download fail.');
            }
            return $res;
        };
        $fn2 = function (string $url) {
            $res = yield OsHelper::curlDownload($url, '', [], true);
            if (empty($res)) {
                throw new RuntimeException('download fail.');
            }
            return $res;
        };

        $promise = Concurrent\co($fn1, 'http://test.loc/hello');
        $res     = $promise->getResult();
        $this->assertNull($res);

        $promise = Concurrent\co($fn2, 'http://test.loc/hello');
        $res     = $promise->getResult();
        $this->assertNull($res);
    }


    /**
     * @throws Exception
     */
    public function testFunSync() {
        $promise = Concurrent\sync('hello');
        $fail    = $promise->isRejected();
        $this->assertTrue($fail);

        try {
            $fn      = function () {
                throw new UncatchableException('none');
            };
            $promise = Concurrent\sync($fn);
            $fail    = $promise->isRejected();
            $this->assertTrue($fail);
        } catch (Error $e) {
            $this->assertNotEmpty($e->getMessage());
        }

        $fn      = function () {
            throw new Error('none');
        };
        $promise = Concurrent\sync($fn);
        $fail    = $promise->isRejected();
        $this->assertTrue($fail);

    }


    /**
     * @throws Exception
     */
    public function testFunPromise() {
        $fn = function () {
            return time();
        };

        $promise = Concurrent\promise($fn);
        $chk     = Concurrent\isPromise($promise);
        $this->assertTrue($chk);

        $promise2 = new Promise(function ($reslove, $reject) {
            $reslove();
        });
        $this->assertTrue($promise2->isFulfilled());

        $promise3 = new Promise(function ($reslove, $reject) {
            $reject();
        });
        $this->assertTrue($promise3->isRejected());
    }


    /**
     * @throws Exception
     */
    public function testFunPromisify() {
        $sum = Concurrent\promisify([MyGenerator::class, 'asyncSum']);
        $a   = $sum(1, 2);
        $b   = $a->then(function ($a) use ($sum) {
            return $sum($a, 3);
        });
        $c   = $b->then(function ($b) use ($sum) {
            return $sum($b, 4);
        });

        $promise = Concurrent\all([$a, $b, $c])->then(function ($result) {
            //需要显式地返回结果
            return $result;
        });
        $res     = $promise->getResult(); //[3,6,10]
        $num     = count($res);
        $this->assertEquals($num, 3);

        $sum1 = Concurrent\promisify([MyGenerator::class, 'asyncSumNone']);
        $sum2 = Concurrent\promisify([MyGenerator::class, 'asyncSumDoubly']);

        $d    = $sum1(1, 2);
        $e    = $sum2(1, 2);
        $res1 = $d->getResult();
        $res2 = $e->getResult();

        $this->assertEmpty($res1);
        $this->assertNotEmpty($res2);

        $sum3 = Concurrent\promisify([MyGenerator::class, 'asyncSumError']);
        $f    = $sum3();
        $this->assertTrue($f->isRejected());

        $fn   = function () {
            throw new Exception('has an error');
        };
        $sum4 = Concurrent\promisify($fn);
        $g    = $sum4();
        $this->assertTrue($g->isRejected());
    }


    /**
     * @throws Exception
     */
    public function testFunAll() {
        $promise1 = Concurrent\toPromise(MyGenerator::randName());
        $promise2 = Concurrent\toPromise(MyGenerator::randAddr());
        $promise3 = Concurrent\toPromise(MyGenerator::randNum());

        $promise4 = Concurrent\all([$promise1, $promise2, $promise3])->then(function ($ret) {
            return $ret;
        });
        $res      = $promise4->getResult();
        $num      = count($res);
        $this->assertEquals($num, 3);

        Concurrent\all([]);
    }


    /**
     * @throws Exception
     */
    public function testFunJoin() {
        $promise1 = Concurrent\toPromise(MyGenerator::randName());
        $promise2 = Concurrent\toPromise(MyGenerator::randAddr());
        $promise3 = Concurrent\toPromise(MyGenerator::randNum());

        $promise4 = Concurrent\join($promise1, $promise2, $promise3)->then(function ($ret) {
            return $ret;
        });
        $res      = $promise4->getResult();
        $num      = count($res);
        $this->assertEquals($num, 3);
    }


    /**
     * @throws Exception
     */
    public function testFunRace() {
        $promise1 = Concurrent\toPromise(MyGenerator::randName());
        $promise2 = Concurrent\toPromise(MyGenerator::randAddr());
        $promise3 = Concurrent\toPromise(MyGenerator::randNum());

        $promise4 = Concurrent\race([$promise1, $promise2, $promise3])->then(function ($ret) {
            return $ret;
        });
        $res      = $promise4->getResult();
        $this->assertNotEmpty($res);
    }


    /**
     * @throws Exception
     */
    public function testFunAny() {
        $promise = Concurrent\any([]);
        $this->assertTrue($promise->isRejected());

        $fn      = function () {
            throw new Exception('error');
        };
        $promise = Concurrent\any([1, 2, 3, $fn]);
        $res     = $promise->getResult();
        $this->assertNotEmpty($res);

        $p       = Concurrent\reject(new Exception('error'));
        $promise = Concurrent\any([$p]);
        $this->assertTrue($promise->isRejected());
    }


    /**
     * @throws Exception
     */
    public function testFunSettle() {
        $p1 = Concurrent\resolve(3);
        $p2 = Concurrent\reject(new Exception('error'));
        $p3 = new Future(); //pending状态

        $promise = Concurrent\settle([true, $p1, $p2]);
        $this->assertTrue($promise->isFulfilled());

        $promise = Concurrent\settle([true, $p1, $p2, $p3]);
        $this->assertTrue($promise->isPending());

        Concurrent\settle([]);
    }


    /**
     * @throws Exception
     */
    public function testFunRun() {
        $add = function ($a, $b) {
            return $a + $b;
        };

        $p1 = Concurrent\resolve(3);

        $promise = Concurrent\run($add, 2, $p1);
        $this->assertTrue($promise->isFulfilled());
        $this->assertEquals($promise->getResult(), 5);
    }


    /**
     * @throws Exception
     */
    public function testFunWrap() {
        $var_export = Concurrent\wrap('var_export');
        $test       = Concurrent\wrap(new MathCls());

        $promise = $var_export($test->add(1, Concurrent\value(2)), true);
        $this->assertEquals('3', $promise->getResult());

        $promise = $var_export($test->sub(Concurrent\value(1), 2), true);
        $this->assertEquals('-1', $promise->getResult());

        $promise = $var_export($test->mul(Concurrent\value(1), Concurrent\value(2)), true);
        $this->assertEquals('2', $promise->getResult());

        $promise = $var_export($test->div(1, 2), true);
        $this->assertEquals('0.5', $promise->getResult());

        $promise = Concurrent\wrap(MyGenerator::randName());
        $this->assertNotEmpty($promise->getResult());

        $obj  = new BaseCls();
        $obj2 = Concurrent\wrap($obj);
        $time = $obj2->time()->getResult();
        $this->assertGreaterThan(1, $time);

        //测试 Wrapper 魔术方法
        $pid = $obj2->pid;
        $this->assertNull($pid);
        $obj2->hehe = 'hello';
        $chk        = isset($obj2->hehe);
        $this->assertTrue($chk);
        unset($obj2->world);


        $fn   = function () {
            return time();
        };
        $obj3 = Concurrent\wrap($fn);
        $time = $obj3()->getResult();
        $this->assertGreaterThan(1, $time);

        $wrap = Concurrent\wrap(1);
        $this->assertEquals(1, $wrap);
    }


    /**
     * @throws Exception
     */
    public function testFunEach() {
        $arr1 = [1, 2, 3];
        $arr2 = ['name' => 'hello', 'age' => 20, 'lang' => 'php',];

        $p1 = Concurrent\each($arr1, [BaseServ::class, 'value']);
        $this->assertTrue($p1->isFulfilled());

        $p2 = Concurrent\each($arr1, [BaseServ::class, 'concat']);
        $this->assertTrue($p2->isFulfilled());

        $p3 = Concurrent\each($arr2, [BaseServ::class, 'join']);
        $this->assertTrue($p3->isFulfilled());

        $p4 = Concurrent\each($arr2, [BaseServ::class, 'multiParams']);
        $this->assertTrue($p4->isRejected());
    }


    /**
     * @throws Exception
     */
    public function testFunEvery() {
        $arr1 = [1, 2, 3];
        $arr2 = ['name' => 'hello', 'age' => 20, 'lang' => 'php',];

        $p1 = Concurrent\every($arr1, [BaseServ::class, 'value']);
        $this->assertTrue($p1->isFulfilled());

        $p2 = Concurrent\every($arr1, [BaseServ::class, 'concat']);
        $this->assertTrue($p2->isFulfilled());

        $p3 = Concurrent\every($arr2, [BaseServ::class, 'join']);
        $this->assertTrue($p3->isFulfilled());

        $p4 = Concurrent\every($arr2, [BaseServ::class, 'multiParams']);
        $this->assertTrue($p4->isRejected());

        $fn = function ($v) {
            return $v > 99;
        };
        $p5 = Concurrent\every($arr1, $fn);
        $this->assertFalse($p5->getResult());
    }


    /**
     * @throws Exception
     */
    public function testFunSome() {
        $arr1 = [1, 2, 3];
        $arr2 = ['name' => 'hello', 'age' => 20, 'lang' => 'php',];

        $p1 = Concurrent\some($arr1, [BaseServ::class, 'value']);
        $this->assertTrue($p1->isFulfilled());

        $p2 = Concurrent\some($arr1, [BaseServ::class, 'concat']);
        $this->assertTrue($p2->isFulfilled());

        $p3 = Concurrent\some($arr2, [BaseServ::class, 'join']);
        $this->assertTrue($p3->isFulfilled());

        $p4 = Concurrent\some($arr2, [BaseServ::class, 'multiParams']);
        $this->assertTrue($p4->isRejected());

        $fn = function ($v) {
            return $v > 99;
        };
        $p5 = Concurrent\some($arr1, $fn);
        $this->assertFalse($p5->getResult());
    }


    /**
     * @throws Exception
     */
    public function testFunFilter() {
        $arr = ['a' => -3, 'b' => -9, 'c' => 0, 'd' => 1, 'e' => 4, 'f' => 6,];

        $fn = function ($v) {
            return $v > 0;
        };

        $p1 = Concurrent\filter($arr, $fn);
        $this->assertEquals(3, count($p1->getResult()));

        $p2   = Concurrent\filter($arr, $fn, true);
        $res  = $p2->getResult();
        $keys = array_keys($res);
        $this->assertNotEmpty($keys);
    }


    /**
     * @throws Exception
     */
    public function testFunMap() {
        $arr = ['a' => -3, 'b' => -9, 'c' => 0, 'd' => 1, 'e' => 4, 'f' => 6,];

        $fn = function ($v) {
            return $v * 2;
        };

        $pr = Concurrent\map($arr, $fn);
        $this->assertEquals(count($arr), count($pr->getResult()));
    }


    /**
     * @throws Exception
     */
    public function testFunReduce() {
        $arr = ['a' => -3, 'b' => -9, 'c' => 0, 'd' => 1, 'e' => 4, 'f' => 6,];

        $fn = function ($carry, $item) {
            return intval($carry) + intval($item);
        };

        $pr1 = Concurrent\reduce($arr, $fn, 10);
        $res = $pr1->getResult();
        $this->assertEquals(9, $res);

        $pr2 = Concurrent\reduce($arr, $fn);
        $res = $pr2->getResult();
        $this->assertEquals(-1, $res);
    }


    /**
     * @throws Exception
     */
    public function testFunSearch() {
        $numbers = [Concurrent\value(0), 1, Concurrent\value(2), 3, Concurrent\value(4), 5,];

        $res1 = Concurrent\search($numbers, 2)->getResult();
        $res2 = Concurrent\search($numbers, Concurrent\value(3))->getResult();
        $res3 = Concurrent\search($numbers, true)->getResult();
        $res4 = Concurrent\search($numbers, true, true)->getResult();

        $this->assertEquals(2, $res1);
        $this->assertEquals(3, $res2);
        $this->assertEquals(1, $res3);
        $this->assertFalse($res4);
    }


    /**
     * @throws Exception
     */
    public function testFunIncludes() {
        $numbers = [Concurrent\value(0), 1, Concurrent\value(2), 3, Concurrent\value(4), 5,];

        $res1 = Concurrent\includes($numbers, Concurrent\value(3))->getResult();
        $res2 = Concurrent\includes($numbers, 9)->getResult();
        $res3 = Concurrent\includes($numbers, true)->getResult();
        $res4 = Concurrent\includes($numbers, true, true)->getResult();
        $this->assertTrue($res1);
        $this->assertFalse($res2);
        $this->assertTrue($res3);
        $this->assertFalse($res4);
    }


    /**
     * @throws Exception
     */
    public function testFunDiff() {
        $arr1 = [1, Concurrent\value(3), 4, Concurrent\value(2), Concurrent\value(5), Concurrent\value(true), 7,];
        $arr2 = [true, 3, 5, Concurrent\value(7), 9,];

        $res = Concurrent\diff($arr1, $arr2)->getResult();
        $this->assertEquals(2, count($res));
    }


    /**
     * @throws Exception
     */
    public function testFunUdiff() {
        $arr1 = [1, Concurrent\value(3), 4, Concurrent\value(2), Concurrent\value(5), Concurrent\value(true), 7,];
        $arr2 = [true, 3, 5, Concurrent\value(7), 9,];

        $fn = function ($a, $b) {
            if ($a < $b) {
                return -1;
            } elseif ($a > $b) {
                return 1;
            } else {
                return 0;
            }
        };

        $res = Concurrent\udiff($arr1, $arr2, $fn)->getResult();
        $this->assertEquals(2, count($res));
    }


    public function testFunMakeClosureFun() {
        try {
            Concurrent\makeClosureFun('test', 1, 2, 3);
        } catch (Exception $e) {
            $chk = strripos($e->getMessage(), 'Un callable');
            $this->assertNotEquals(-1, $chk);
        }
    }


}