<?php

namespace App\Http\Controllers\Example;

use App\Http\Controllers\Controller;
use App\Services\Errors;
use App\Services\Result;
use App\Services\SampleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/21
 * Time: 18:06
 */
class SampleController extends Controller
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
        $result = SampleService::create($data);

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
     * BUG: Laravel $request->input() can NOT read HTTP PUT payload ?!
     */
    protected function update(Request $request, $id)
    {
        $data = $request->input();
        $valid = $this->validate2($data, []);
        if ($valid->fails()) {
            return $this->jsonFromValid($valid);
        }

        $result = SampleService::update($id, $data);

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
     */
    protected function retrieve(Request $request, $id)
    {
        $result = SampleService::retrieve($id);
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

        $result = SampleService::search($filter);
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
    protected function hello(Request $request)
    {
        $result = Result::ok($request->input());

        if ($result->hasError()) {

        }

        return $result->data();
    }

    /*__ACTION_PLACEHOLDER__*/



}