<?php

namespace App\Http\Controllers;

use App\Http\Middleware\Debug;
use App\Services\Errors;
use App\Services\Result;
use App\Services\Utils;
use App\Services\Validate;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs;
    use ValidatesRequests;

    private function __debug()
    {
        $request = Request::capture();
        $debug = $request->input('__debug');
        if ($debug) {
            $ret = [];
            if ($debug & 0x1) {
                $ret['cost'] = round(microtime(1) - Debug::$beginTime, 4);
            }

            if ($debug & 0x2) {
                $db = DB::connection();
                $queryLog = $db->getQueryLog();

                $ret['queries'] = Utils::getSQLArrayWithBindings($queryLog);
            }

            return $ret;
        }

        return false;
    }

    /**
     * @param $errorCode
     * @param $data
     * @param $msg
     * @return string
     */
    public function json($errorCode, $data, $msg='')
    {
        $response = [
            'status' => $errorCode,
            'msg' => $msg ?: Errors::getErrorMsg($errorCode),
            'data' => $data,
        ];

        $debug = $this->__debug();
        if ($debug) {
            $response['debug'] = $debug;
        }

        return response($response);
    }

    /**
     * @param \App\Services\Result $result
     * @return string
     */
    public function jsonFromError($result)
    {
        return $this->json($result->errorCode(), $result->data());
    }

    /**
     * @param \Illuminate\Validation\Validator $valid
     * @return string
     */
    public function jsonFromValid($valid)
    {
        return $this->json(Errors::BadArguments, $valid->getMessageBag());
    }

    /**
     * @param $input
     * @param $rules
     * @param $messages
     * @return \Illuminate\Validation\Validator
     * 提供一个不抛出异常, 并且可以轻易支持Customer Validator的校验办法,
     * 并且不需要指定Message (整个工程使用统一的Message, 除非想重新定义(覆盖)Message)
     */
    public function validate2(array $input, array $rules, array $messages=[])
    {
        $factory = $this->getValidationFactory();
        Validate::addExtendValidators($factory, $rules);
        $valid = $factory->make($input, $rules, $messages);
        return $valid;
    }
}
