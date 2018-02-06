<?php
namespace App\Http\Controllers\Admin;
    
use App\Http\Controllers\Controller;
use App\Models\ThPage;
use App\Services\Admin\MenuService;
use App\Services\Errors;
use App\Services\Result;
use Illuminate\Http\Request;

/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017-11-22
 * Time: 08:01:13
 */
class BuilderController extends Controller
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
    protected function action(Request $request)
    {
        $valid = $this->validate2($request->input(), [
            'a' => 'required|int',
            'a1' => 'required|int']);

        if ($valid->fails()) {
            return $this->jsonFromValid($valid);
        }

        $result = Result::ok($request->input());

        if ($result->hasError()) {

        }

        $menus = MenuService::getMenu();
        var_dump($menus);

        return $this->json(Errors::Ok, $result->data());
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
    protected function world23(Request $request)
    {
        $result = Result::ok($request->input());

        if ($result->hasError()) {

        }

        return $result->data();
    }

    /*__ACTION_PLACEHOLDER__*/


}

