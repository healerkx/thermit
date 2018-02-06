<?php

namespace App\Http\Controllers\Mine;

use App\Http\Controllers\Admin\AdminBaseController;

/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/12/1
 * Time: 22:33
 */
class ToolsController extends AdminBaseController
{
    /**
     *
     */
    public function index()
    {
        $pageName = 'Tools';

        $vars = [];

        self::setPageTitleAndBreadcrumb($pageName, []);
        return self::render('admin.index', $vars);

    }


}