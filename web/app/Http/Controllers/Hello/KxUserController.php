<?php

namespace App\Http\Controllers\Hello;

use App\Http\Controllers\Controller;
use App\Services\Errors;
use App\Services\Result;
use App\Services\Hello\KxUserService;
use Illuminate\Http\Request;


/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/21
 * Time: 18:06
 */
class KxUserController extends Controller
{

    /**
     * @param Request $request
     * @return string
     *
     * @cat KxUser
     * @title 创建KxUser对象
     * @comment 创建KxUser对象
     *
     * @form-param some_data || string || 对象数据
     * @ret-val status
     */
    protected function create(Request $request)
    {
        $data = $request->input();
        $valid = $this->validate2($data, []);
        if ($valid->fails()) {
            return $this->jsonFromValid($valid);
        }
        $result = KxUserService::create($data);

        if ($result->hasError()) {
            return $this->jsonFromError($result);
        }

        $data = $result->data()->toArray();
        return $this->json(Errors::Ok, $data);
    }

    /**
     * @param Request $request
     * @param $id
     * @return string
     *
     * @cat KxUser
     * @title 编辑KxUser对象
     * @comment 编辑KxUser对象
     *
     * @form-param some_data || string || 对象数据
     * @ret-val status
     *
     * BUG: Laravel $request->input() can NOT read HTTP PUT payload ?!
     * @case id=1
     */
    protected function update(Request $request, $id)
    {
        $data = $request->input();
        $valid = $this->validate2($data, []);
        if ($valid->fails()) {
            return $this->jsonFromValid($valid);
        }

        $result = KxUserService::update($id, $data);

        if ($result->hasError()) {
            return $this->jsonFromError($result);
        }

        $data = $result->data()->toArray();
        return $this->json(Errors::Ok, $data);
    }

    /**
     * @param Request $request
     * @param $id
     * @return string
     *
     * @cat KxUser
     * @title 根据ID获取KxUser对象数据
     * @comment 根据ID获取KxUser对象数据
     * @url-param id || int || ID
     * @ret-val status
     * @ret-val create_time
     * @ret-val update_time
     *
     * @case id=1 @expect status=0
     * @case id=2 @expect status=3
     */
    protected function retrieve(Request $request, $id)
    {
        $result = KxUserService::retrieve($id);
        if ($result->hasError()) {
            return $this->jsonFromError($result);
        }
        $data = $result->data();
        return $this->json(Errors::Ok, $data);
    }

    /**
     * @param Request $request
     * @return string
     *
     * @cat KxUser
     * @title 获取KxUser列表
     * @comment 获取KxUser列表
     *
     * @ret-val list.0.status
     * @ret-val list.0.create_time
     * @ret-val list.0.update_time
     */
    protected function retrieveList(Request $request)
    {
        $filter = $request->input();

        $result = KxUserService::search($filter);
        if ($result->hasError()) {
            return $this->jsonFromError($result);
        }
        $data = $result->data();
        return $this->json(Errors::Ok, $data);
    }

    // TODO: Remove

    /*__ACTION_PLACEHOLDER__*/

}