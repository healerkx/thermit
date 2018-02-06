<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/25
 * Time: 10:55
 */

namespace App\Console\Commands;



/**
 * Class AdminAddBlankCommand
 * @package App\Console\Commands
 *
 *
 */
class AdminAddBlankCommand extends BaseCommand
{
    protected $signature = 'admin:add:blank {pageName?} {groupName?}';

    protected $description = 'Generated admin page';

    /**
     * @return void
     */
    public function handle()
    {
        $adminPath = base_path('resources/views/admin/');

        $groupName = $this->askName($this->argument('groupName'), 'Please input a group name');
        $groupPath = $adminPath . $groupName;
        if (!file_exists($groupPath)) {
            mkdir($groupPath);
        }

        $pageName = $this->argument('pageName');

        $this->createBlankPage($groupName, $pageName);
    }

    /**
     * @param $groupName
     * @param $pageName
     */
    private function createBlankPage($groupName, $pageName)
    {
        $adminPath = base_path('resources/views/admin/');

        $fileName = $adminPath . $groupName . DIRECTORY_SEPARATOR . $pageName . '.blade.php';

        $content = $this->getContent('templates/model_admin_mvc/admin_blank_page.php', []);
        file_put_contents($fileName, $content);

        $fileName = $adminPath . $groupName . DIRECTORY_SEPARATOR . "$pageName-js" . '.blade.php';
        $content = $this->getContent('templates/model_admin_mvc/admin_blank_page.js.php', []);
        file_put_contents($fileName, $content);

        if ($this->confirm('Do you want to add route [y|N]')) {
            $snakePageName = preg_replace('/-/', '_', $pageName);

            $action = camel_case($snakePageName);

            // Add route
            $this->call('route:add', [
                'type' => 'admin',
                'routeFile' => '',
                'method' => 'GET',
                'path' => $action,
                'controller' => '',
                'action' => $action,
                '--groupName' => $groupName
            ]);
        }
    }

}