<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/25
 * Time: 10:55
 */

namespace App\Console\Commands;

use App\Models\ThPage;
use App\Models\ThPageConfig;
use App\Services\Admin\BuilderService;
use App\Services\Admin\ListBuilderService;
use App\Services\Errors;
use App\Services\Result;


class AdminAddCURDCommand extends BaseCommand
{
    protected $signature = 'admin:add:curd {modelName?} {--pageName}';

    protected $description = 'Create admin CURD pages';


    /**
     * @return void
     */
    public function handle()
    {

        $modelName = $this->selectModel($this->argument('modelName'), 'Please input Model name');

        $pageName = $this->askName($this->option('pageName'), 'Please input Page name');

        $modelName = "App\\Models\\$modelName";
        $pageResult = $this->createListPage($pageName, $modelName);
        if ($pageResult->hasError()) {

        }

        $pageResult = $this->createEditPage($pageName, $modelName);
        if ($pageResult->hasError()) {

        }
    }

    /**
     * @param $pageName
     * @param $modelName
     * @return Result
     */
    private function createListPage($pageName, $modelName)
    {
        $page = new ThPage();
        $page->status = 1;
        $page->page_type = BuilderService::PageType_ListView;   // Type = 1 is for ListView
        $page->page_name = $pageName;

        if (!$page->save()) {
            return Result::error(Errors::SaveFailed, []);
        }

        $pageId = $page->page_id;
        $config = new ThPageConfig();
        $config->page_id = $page->page_id;
        $config->status = 1;
        $config->config_category = ListBuilderService::ConfigCategory_DataSource;
        $config->config_type = ListBuilderService::ConfigValueType_ModelName;
        $config->config_value = $modelName;

        if (!$config->save()) {
            return Result::error(Errors::SaveFailed, []);
        }

        echo "LIST: http://127.0.0.1:9090/admin-builder/listview?pageId={$pageId}\n";
        return Result::ok(['page' => $page, 'config' => $config]);
    }

    /**
     * @param $pageName
     * @param $modelName
     * @return Result
     */
    private function createEditPage($pageName, $modelName)
    {
        $page = new ThPage();
        $page->status = 1;
        $page->page_type = BuilderService::PageType_FormCreator;   // Type = 1 is for ListView
        $page->page_name = $pageName;

        if (!$page->save()) {
            return Result::error(Errors::SaveFailed, []);
        }

        $pageId = $page->page_id;
        $config = new ThPageConfig();
        $config->page_id = $page->page_id;
        $config->status = 1;
        $config->config_category = ListBuilderService::ConfigCategory_DataSource;
        $config->config_type = ListBuilderService::ConfigValueType_ModelName;
        $config->config_value = $modelName;

        if (!$config->save()) {
            return Result::error(Errors::SaveFailed, []);
        }

        echo "FORM: http://127.0.0.1:9090/admin-builder/form?pageId={$pageId}\n";
        return Result::ok(['page' => $page, 'config' => $config]);
    }


}