<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2019/6/26
 * Time: 19:15
 * Desc:
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
    const PENDING   = 'pending';

    //成功
    const FULFILLED = 'fulfilled';

    //失败
    const REJECTED  = 'rejected';


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


    public function __construct($computation = null) {
        if(is_callable($computation)) {
            try {

            }catch (UnCatchableException $e) {

            }catch (Exception $e) {

            }catch (Error $e) {

            }
        }

    }



}