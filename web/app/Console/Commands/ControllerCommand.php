<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/22
 * Time: 08:45
 */

namespace App\Console\Commands;


/**
 * Class ControllerCommand
 * @package App\Console\Commands
 * usage:
 *  php artisan controller:create 'Admin\SomeController'
 */
class ControllerCommand extends BaseCommand
{
    /**
     * 控制台命令名称
     *
     * @var string
     * example(Notice the single quote):
     *  php artisan controller:create 'Admin\SomeController'
     */
    protected $signature = 'controller:create {type?} {controllerName?}';

    protected $description = "Create a controller, [php artisan controller:create 'Namespace\\SomeController']";

    /**
     * @return void
     */
    public function handle()
    {
        $type = $this->askSelect($this->argument('type'), ['api', 'admin'], 'Please select an *Controller Type*');

        $namespace = $this->askNamespace('controller');
        $namespace = ucfirst($namespace);

        // TODO: Create a folder for the new $namespace
        $namespacePath = base_path('app/Http/Controllers/') . $namespace;
        if (!file_exists($namespacePath)) {
            mkdir($namespacePath);
        }

        $controllerName = $this->argument('controllerName');
        $controllerName = $this->askName($controllerName, 'Please input controller name');
        $controllerName = ucfirst($controllerName);

        if (!strstr($controllerName, 'Controller')) {
            $controllerName .= 'Controller';
        }

        $this->createController($type, $namespace, $controllerName);
        echo "$namespace\\$controllerName created\n";
    }

    /**
     * @param $type
     * @param $namespace
     * @param $controllerName
     */
    private function createController($type, $namespace, $controllerName)
    {
        $vars = compact('type', 'namespace', 'controllerName');
        $fileContent = "<?php\n" . $this->getContent('templates/controller.php', $vars);

        $fileName = $this->getControllerFilePath($namespace, $controllerName);
        if (!file_exists($fileName)) {
            // Models下面的Model文件只生成一次, 以后不会再被修改, 除非我改生成逻辑
            file_put_contents($fileName, $fileContent);
        }
    }
}