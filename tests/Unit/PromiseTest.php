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
use Kph\Tests\Objects\MyGenerator;
use Kph\Concurrent;
use Kph\Concurrent\Future;
use Kph\Concurrent\Promise;
use Faker\Factory;
use Exception;
use Throwable;
use Generator;

class PromiseTest extends TestCase {

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
        try {
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
        } catch (Exception $e) {
        }

        //创建失败状态的promise
        try {
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
        } catch (Exception $d) {
        }

    }


    public function testFunResolve() {
        try {
            //创建成功状态
            $promise = Concurrent\resolve('world');
            $chk1    = $promise->isFulfilled();
            $chk2    = $promise->isCompleted();

            $this->assertTrue($chk1);
            $this->assertTrue($chk2);
        } catch (Exception $e) {
        }
    }


    public function testFunReject() {
        try {
            //创建失败状态
            $e       = new Exception('error');
            $promise = Concurrent\reject($e);
            $chk1    = $promise->isRejected();
            $chk2    = $promise->isCompleted();

            $this->assertTrue($chk1);
            $this->assertTrue($chk2);
        } catch (Exception $e) {
        }
    }


    public function testFunToFuture() {
        try {
            $promise = Concurrent\toFuture(1);
            $promise->then(function ($res) {
                $this->assertEquals($res, 1);
            });
        } catch (Exception $e) {
        }
    }


    public function testFunToPromise() {
        try {
            $promise = Concurrent\toPromise(function () {
                MyGenerator::num();
            });

            //TODO
            $this->assertTrue(true);
        } catch (Exception $e) {
        }
    }

    public function testFunCo() {
        try {
            $promise = Concurrent\co(function (){
                yield MyGenerator::num();
            });
            $promise->then(function ($res){
                //注意,结果为生成器迭代完成的最后一个结果
                $this->assertEquals($res, 9999);
            });

            $this->assertTrue(true);
        } catch (Exception $e) {
        }
    }


    public function testFun() {
        try {
            $this->assertTrue(true);
        } catch (Exception $e) {
        }
    }


}