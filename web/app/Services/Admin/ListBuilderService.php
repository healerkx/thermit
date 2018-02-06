<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/29
 * Time: 09:42
 */

namespace App\Services\Admin;


use App\Models\ThPage;
use App\Models\ThPageConfig;
use App\Services\Errors;
use App\Services\Result;


class ListBuilderService extends BuilderService
{

    /**
     * 返回列表页列表
     * @return Result
     */
    public static function listAll()
    {
        $listPages = ThPage::query()
            ->where('page_type', BuilderService::PageType_ListView)
            ->get()
            ->toArray();

        return Result::ok($listPages);
    }

    /**
     * @param $pageName
     * @return Result
     */
    public static function create($pageName)
    {
        $listPage = new ThPage();
        $listPage->page_name = $pageName;
        $listPage->page_type = BuilderService::PageType_ListView;
        $listPage->status = 1;

        if (!$listPage->save()) {
            return Result::error(Errors::SaveFailed, []);
        }
        return Result::ok($listPage);
    }

    /**
     * @param $pageId
     * @param $data
     * @return Result
     */
    public static function edit($pageId, $data)
    {
        $listPage = self::getListPage($pageId);

        if (!$listPage) {
            return Result::error(Errors::NotFound, []);
        }

        $listPage->page_name = $data['pageName'];

        if (!$listPage->save()) {
            return Result::error(Errors::SaveFailed, []);
        }
        return Result::ok($listPage);
    }

    public static function getListPage($pageId)
    {
        return ThPage::query()->find($pageId);
    }

    public static function getPageAndConfigs($pageId)
    {
        $page = ThPage::query()->find($pageId);

        $configs = ThPageConfig::query()
            ->where('page_id', $pageId)
            ->get()
            ->toArray();

        $dataSource = current(array_filter($configs, function($i) {
            return $i['config_category'] == self::ConfigCategory_DataSource;
        }));

        $xpathConfig = current(array_filter($configs, function($i) {
            return $i['config_category'] == self::ConfigCategory_XPath;
        }));

        $fields = self::getFields($configs);

        return Result::ok([
            'page' => $page,
            'configs' => $configs,
            'filters' => self::getFilters($configs),
            'fields' => $fields,
            'type' => $dataSource['config_type'],
            'value' => $dataSource['config_value'],
            'xpath' => $xpathConfig['config_value']
        ]);
    }

    private static function getFilters($configs)
    {
        $filters = array_map(function($i) {
            return json_decode($i['config_value'], true);
        }, array_filter($configs, function($i) {
            return $i['config_category'] == self::ConfigCategory_Filter;
        }));

        foreach ($filters as &$filter) {
            if (!array_key_exists('label', $filter)) {
                $field['paramName'] = '';
            } elseif (!array_key_exists('paramName', $filter)) {
                $field['paramName'] = '';
            }
        }
        unset($filter);
        return $filters;
    }

    /**
     * @param $url
     * @param $xpath
     * @return Result
     */
    public static function getListFromUrl($url, $xpath)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->get($url);

        // TODO:
        $content = $response->getBody()->getContents();

        $array = json_decode($content, true);

        $xpath = explode('.', $xpath);
        foreach ($xpath as $p) {
            $array = $array[$p];
        }

        return Result::ok($array);
    }

    /**
     * @param $modelName
     * @return Result
     */
    public static function getListFromModel($modelName)
    {
        $query = $modelName::query();
        $array = $query
            ->get()
            ->toArray();

        return Result::ok($array);
    }

    /**
     * @param $item
     * @return array
     */
    public static function getFieldsFromDataItem(array $item)
    {
        $fieldNames = array_keys($item);
        $ret = [];
        foreach ($fieldNames as $fieldName) {
            $fieldType = 'text';
            if (strstr($fieldName, 'time')) {
                $fieldType = 'datetime';
            }
            $ret[] = [
                'fieldName' => $fieldName,
                'columnName' => $fieldName,
                'fieldType' => $fieldType];
        }
        return $ret;
    }
}