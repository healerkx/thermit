<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/12/6
 * Time: 22:25
 */

namespace App\Services\Admin;


use App\Models\ThPage;
use App\Models\ThPageConfig;
use App\Services\Result;

class FormBuilderService extends BuilderService
{
    /**
     * 返回FormCreator的列表
     * @return Result
     */
    public static function listAll()
    {
        $listPages = ThPage::query()
            ->where('page_type', BuilderService::PageType_FormCreator)
            ->get()
            ->toArray();

        return Result::ok($listPages);
    }
    /**
     * @param $pageId
     * @return Result
     */
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

        $fields = self::getFields($configs);

        return Result::ok([
            'page' => $page,
            'configs' => $configs,
            'fields' => $fields,
            'type' => $dataSource['config_type'],
            'value' => $dataSource['config_value'],
        ]);
    }
}