<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/24
 * Time: 23:04
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class AdminController extends AdminBaseController
{

    public function index()
    {
        self::setPageTitleAndBreadcrumb('Main Board', []);
        return self::render('admin.main-board', []);
    }

    public function blank()
    {
        self::setPageTitleAndBreadcrumb('Blank Page', []);
        return self::render('admin.blank', []);
    }

    /**
     * @param Request $request
     * @return string
     *
     * @cat TODO
     * @title TODO
     * @comment TODO
     */
    protected function somePage5(Request $request)
    {
        self::setPageTitleAndBreadcrumb('some-page5', []);
        return self::render('admin.tools.some-page5', []);
    }

    /*__ACTION_PLACEHOLDER__*/


}