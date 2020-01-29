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
        $test = $this;
        $future = Concurrent\toFuture(1);
        $promise = Concurrent\toPromise($future);
        $promise->then(function ($res) use($test){
            //不能在回调里面进行断言
        });
        $res = $promise->getResult();
        $this->assertEquals($res, 1);

        //闭包中引用生成器
        $promise = Concurrent\toPromise(function (){
            yield MyGenerator::num();
        });
        $promise->then(function ($res){
            //注意,结果为生成器迭代完成的最后一个结果
        });
        $res = $promise->getResult();
        $this->assertEquals($res, 99);

        //直接调用生成器
        $promise = Concurrent\toPromise(MyGenerator::num());
        $promise->then(function ($res){
            //注意,结果为生成器迭代完成的最后一个结果
        });
        $res = $promise->getResult();
        $this->assertEquals($res, 99);
    }


    public function testFunCo() {
        $promise = Concurrent\co(function (){
            yield MyGenerator::num();
        });
        $promise->then(function ($res){
            //注意,结果为生成器迭代完成的最后一个结果
        });
        $res = $promise->getResult();
        $this->assertEquals($res, 99);
    }




}