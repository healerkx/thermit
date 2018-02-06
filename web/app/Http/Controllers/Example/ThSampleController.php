<?php

namespace App\Http\Controllers\Example;

use App\Http\Controllers\Controller;
use App\Services\Errors;
use App\Services\Result;
use App\Services\ThSampleService;
use Illuminate\Http\Request;


/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/21
 * Time: 18:06
 */
class ThSampleController extends Controller
{

    /**
     * @param Request $request
     * @return string
     *
     * @cat TODO
     * @title TODO
     * @comment TODO
     * @url-param TODO
     * @form-param TODO
     * @ret-val TODO
     * @ret-val TODO
     */
    protected function create(Request $request)
    {
        $data = $request->input();
        $valid = $this->validate2($data, []);
        if ($valid->fails()) {
            return $this->jsonFromValid($valid);
        }
        $result = ThSampleService::create($data);

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
     * @cat TODO
     * @title TODO
     * @comment TODO
     * @url-param TODO
     * @form-param TODO
     * @ret-val TODO
     * @ret-val TODO
     * @case id=1
     * BUG: Laravel $request->input() can NOT read HTTP PUT payload ?!
     */
    protected function update(Request $request, $id)
    {
        $data = $request->input();
        $valid = $this->validate2($data, []);
        if ($valid->fails()) {
            return $this->jsonFromValid($valid);
        }

        $result = ThSampleService::update($id, $data);

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
     * @cat TODO
     * @title TODO
     * @comment TODO
     * @url-param TODO
     * @form-param TODO
     * @ret-val TODO
     * @ret-val TODO
     *
     * @case id=1
     */
    protected function retrieve(Request $request, $id)
    {
        $result = ThSampleService::retrieve($id);
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
     * @cat TODO
     * @title TODO
     * @comment TODO
     * @url-param TODO
     * @form-param TODO
     * @ret-val TODO
     * @ret-val TODO
     */
    protected function retrieveList(Request $request)
    {
        $filter = $request->input();

        $result = ThSampleService::search($filter);
        if ($result->hasError()) {
            return $this->jsonFromError($result);
        }
        $data = $result->data();
        return $this->json(Errors::Ok, $data);
    }

    // TODO: Remove

    /*__ACTION_PLACEHOLDER__*/

}