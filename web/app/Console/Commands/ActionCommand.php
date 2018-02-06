<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/22
 * Time: 08:45
 */

namespace App\Console\Commands;


class ActionCommand extends BaseCommand
{
    /**
     * 控制台命令名称
     *
     * @var string
     * example(Notice the single quote):
     *  php artisan action:create 'Admin\SomeController' actionName
     */
    protected $signature = 'action:create {type?} {controllerName?} {actionName?} {--groupName}';

    protected $description = 'Generated controller';

    /**
     * @return void
     */
    public function handle()
    {
        $type = $this->askSelect($this->argument('type'), ['api', 'admin'], 'Please select an *Action Type*');

        $controllerName = $this->argument('controllerName');
        $controllerName = $this->selectController($controllerName);

        $controllerNames = explode('\\', $controllerName);

        $namespace = '';
        if (count($controllerNames) == 2) {
            $namespace = $controllerNames[0];
            $controllerName = $controllerNames[1];
        }

        // TODO: Maybe no use for selection
        if (!strstr($controllerName, 'Controller')) {
            $controllerName .= 'Controller';
        }

        $actionName = $this->askName($this->argument('actionName'), 'Please input an *Action Name*');

        $this->createAction($type, $namespace, $controllerName, $actionName);
    }

    /**
     * Get action code lines, put them into controller file
     * @param $type
     * @param $namespace
     * @param $controllerName
     * @param $actionName
     * @return bool
     */
    private function createAction($type, $namespace, $controllerName, $actionName)
    {
        $actionContent = null;
        if ($type == 'api') {
            $vars = compact('type', 'namespace', 'controllerName', 'actionName');
            $actionContent = $this->getContent('templates/action/rest_api.php', $vars);
        } elseif ($type == 'admin') {
            $pageName = snake_case($actionName);
            $pageName = preg_replace('/_/', '-', $pageName);

            $groupName = $this->option('groupName');
            if (!$groupName) {
                $groupName = $this->askName($groupName, 'Please input a group name');
            }
            $vars = compact('type', 'namespace', 'controllerName', 'actionName', 'pageName', 'groupName');
            $actionContent = $this->getContent('templates/action/admin_blank.php', $vars);
        }

        if (!$actionContent) {

        }

        $fileName = $this->getControllerFilePath($namespace, $controllerName);
        if (!file_exists($fileName)) {
            // Models下面的Model文件只生成一次, 以后不会再被修改, 除非我改生成逻辑
            return false;
        }

        $content = file_get_contents($fileName);

        $fileContent = preg_replace('/\/\*__ACTION_PLACEHOLDER__\*\//', $actionContent, $content);

        file_put_contents($fileName, $fileContent);
        return true;
    }
}