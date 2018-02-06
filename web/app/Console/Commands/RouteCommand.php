<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/27
 * Time: 08:48
 */

namespace App\Console\Commands;

use App\Services\Utils;


class RouteCommand extends BaseCommand
{
    /**
     * 控制台命令名称
     *
     * @var string
     */
    protected $signature = 'route:add {type?} {routeFile?} {method?} {path?} {controller?} {action?} {--groupName}';

    protected $description = 'Add route entry';

    /**
     * @return void
     */
    public function handle()
    {
        $type = $this->askSelect($this->argument('type'), ['api', 'admin'], 'Please select an *Action Type*');

        $routeFile = $this->argument('routeFile');
        $routeFile = $this->selectRoute($routeFile);

        $method = $this->argument('method');
        $method = $this->askSelect($method, ['GET', 'POST', 'PUT'], 'Select a HTTP method');
        $method = strtolower($method);

        $path = $this->argument('path');
        $path = $this->askName($path, 'Please input URL path');

        $controller = $this->argument('controller');
        $controller = $this->selectController($controller);

        $action = $this->argument('action');
        $action = $this->askName($action, 'Please input action name');

        $this->addRoute($type, $routeFile, $method, $path, $controller, $action);
    }

    /**
     * @param $type
     * @param $routeFile
     * @param $method
     * @param $path
     * @param $controller
     * @param $action
     */
    private function addRoute($type, $routeFile, $method, $path, $controller, $action)
    {
        $entry = "Route::{$method}('{$path}', '{$controller}@{$action}');";

        $routeFilePath = base_path('routes') . DIRECTORY_SEPARATOR . $routeFile;
        file_put_contents($routeFilePath, "\n{$entry}\n", FILE_APPEND);

        echo "$entry added.\n";

        if ($this->confirm('Do you want to add the *action code* [y|N]')) {
            $groupName = '';
            if ($type == 'admin') {
                $groupName = $this->option('groupName');
                $groupName = $this->askName($groupName, 'Please input a group name');
            }

            $this->call('action:create', [
                'controllerName' => $controller,
                'actionName' => $action,
                'type' => $type,
                '--groupName' => $groupName
            ]);
        }

    }

}