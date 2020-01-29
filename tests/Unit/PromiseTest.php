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
use Kph\Concurrent\Exception\UncatchableException;
use Kph\Concurrent\Future;
use Kph\Concurrent\Promise;
use Kph\Tests\Objects\MyGenerator;
use RuntimeException;
use Throwable;

class PromiseTest extends TestCase {


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
        $res = $promise->getResult();
        $this->assertNotEmpty($res);

        //注意,结果为生成器迭代完成的最后一个结果
        //此段代码本地通过,但travis失败
//        $promise = Concurrent\co(MyGenerator::num());
//        $res = $promise->getResult();
//        $this->assertEquals($res, 99);
    }


    /**
     * @throws Exception
     */
    public function testSync() {
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
    public function testPromise() {
        $fn = function () {
            return time();
        };

        $promise = Concurrent\promise($fn);
        $chk     = Concurrent\isPromise($promise);
        $this->assertTrue($chk);
    }


    /**
     * @throws Exception
     */
    public function testPromisify() {
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
    }


    /**
     * @throws Exception
     */
    public function testAll() {
        $promise1 = Concurrent\toPromise(MyGenerator::randName());
        $promise2 = Concurrent\toPromise(MyGenerator::randAddr());
        $promise3 = Concurrent\toPromise(MyGenerator::randNum());

        $promise4 = Concurrent\all([$promise1, $promise2, $promise3])->then(function ($ret) {
            return $ret;
        });
        $res      = $promise4->getResult();
        $num      = count($res);
        $this->assertEquals($num, 3);
    }


    /**
     * @throws Exception
     */
    public function testJoin() {
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



}