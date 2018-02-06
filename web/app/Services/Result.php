<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/21
 * Time: 14:54
 */

namespace App\Services;


class Result
{
    private $errorCode = 0;

    private $data = null;

    private function __construct($errorCode, $data)
    {
        $this->errorCode = $errorCode;
        $this->data = $data;
    }

    /**
     * @param $data
     * @return Result
     */
    public static function ok($data)
    {
        return new Result(Errors::Ok, $data);
    }

    /**
     * @param $errorCode
     * @param $data
     * @return Result
     */
    public static function error($errorCode, $data)
    {
        return new Result($errorCode, $data);
    }

    /**
     * @return bool
     */
    public final function isOk()
    {
        return $this->errorCode === 0;
    }

    /**
     * @return bool
     */
    public final function hasError()
    {
        return $this->errorCode !== 0;
    }

    /**
     * @return int
     */
    public final function errorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return mixed | array
     */
    public final function data()
    {
        return $this->data;
    }
}