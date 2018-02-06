<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/29
 * Time: 09:03
 */

namespace App\Http\Controllers\Admin;

use App\Services\Admin\ListBuilderService;
use App\Services\Errors;
use App\Services\Result;
use Illuminate\Http\Request;

class ListBuilderController extends AdminBaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function listAll(Request $request)
    {
        self::setPageTitleAndBreadcrumb('Builder List', []);
        return self::render('admin.builder.list-index', []);
    }

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

        $result = ListBuilderService::getPageAndConfigs($pageId);
        if ($result->hasError()) {

        }

        $data = $result->data();

        $pageName = $data['page']['page_name'];

        $vars = [
            'pageName' => $pageName,
            'xpath' => $data['xpath'],
            'filters' => $data['filters'],
            'fields' => $data['fields'],
            'url' => $data['value']
        ];

        self::setPageTitleAndBreadcrumb('Builder Edit List', []);
        return self::render('admin.builder.list-create-update', $vars);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function show(Request $request)
    {
        $pageId = $request->input('pageId');

        $result = ListBuilderService::getPageAndConfigs($pageId);
        if ($result->hasError()) {
            return $this->jsonFromError($result);
        }

        $data = $result->data();

        $pageName = $data['page']['page_name'];


        $itemsResult = Result::error(Errors::Unknown, []);
        if ($data['type'] == ListBuilderService::ConfigValueType_ModelName) {
            $itemsResult = ListBuilderService::getListFromModel($data['value']);
        } elseif ($data['type'] == ListBuilderService::ConfigValueType_Uri) {
            $itemsResult = ListBuilderService::getListFromUrl($data['value'], $data['xpath']);
        } else {
            return $this->jsonFromError($itemsResult);
        }

        $items = $itemsResult->data();

        // Fields!
        $fields = $data['fields'];
        if (empty($fields)) {
            $fields = ListBuilderService::getFieldsFromDataItem(current($items));
        }

        $vars = [
            'pageName' => $pageName,
            'xpath' => $data['xpath'],
            'filters' => $data['filters'],
            'fields' => $fields,
            'url' => $data['value'],
            'items' => $items
        ];

        self::setPageTitleAndBreadcrumb($pageName, []);
        // TODO: We can make the template name as parameter for render a subclass List view.
        return self::render('admin.builder.list-show', $vars);
    }
}