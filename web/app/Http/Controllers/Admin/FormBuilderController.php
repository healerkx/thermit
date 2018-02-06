<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/12/6
 * Time: 22:18
 */

namespace App\Http\Controllers\Admin;


use App\Services\Admin\FormBuilderService;
use Illuminate\Http\Request;

class FormBuilderController extends AdminBaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function create(Request $request)
    {
        $vars = [
            'pageName' => '',
            'xpath' => '',
            'filters' => [],
            'fields' => [],
            'url' => ''
        ];

        self::setPageTitleAndBreadcrumb('Builder Create List', []);
        return self::render('admin.builder.list-create-update', $vars);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function edit(Request $request)
    {
        $pageId = $request->input('pageId');

        $result = FormBuilderService::getPageAndConfigs($pageId);
        if ($result->hasError()) {

        }

        $data = $result->data();

        $pageName = $data['page']['page_name'];

        $vars = [
            'pageName' => $pageName,

            //'filters' => $data['filters'],
            'fields' => $data['fields'],
            'url' => $data['value']
        ];

        self::setPageTitleAndBreadcrumb('Builder Edit List', []);
        return self::render('admin.builder.form-create-update', $vars);
    }

}