<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2019/12/23
 * Time: 18:44
 * Desc:
 */


namespace Kph\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Kph\Objects\BaseObject;
use Kph\Tests\Objects\BaseServ;
use Error;
use Exception;

class ServicesTest extends TestCase {

    /**
     * 基础服务类测试
     */
    public function testBase() {
        $serv = new BaseServ([]);

        // 获取最终父类的实例化
        $serv2 = BaseServ::getFinalInstance();
        $servName2 = $serv2->getClassShortName();
        $this->assertEquals($servName2, 'BaseObject');
        $this->assertTrue(BaseServ::hasFinalInstance());
        BaseServ::destroyFinalInstance();
        $this->assertFalse(BaseServ::hasFinalInstance());

        // 获取当前类的实例化
        $serv3 = BaseServ::getSelfInstance();
        $servName3 = $serv3->getClassShortName();
        $this->assertEquals($servName3, 'BaseServ');
        $this->assertTrue(BaseServ::hasSelfInstance());
        BaseServ::destroySelfInstance();
        $this->assertFalse(BaseServ::hasSelfInstance());

        $serv->setErrorInfo(123, '找不到资源');
        $errArr = $serv->getErrorInfo();
        $errno = $serv->getErrno();
        $error = $serv->getError();
        $this->assertEquals($errArr['errno'], $errno);
        $this->assertEquals($errArr['error'], $error);

    }


}