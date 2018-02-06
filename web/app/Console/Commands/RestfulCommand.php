<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/28
 * Time: 15:28
 */

namespace App\Console\Commands;

use App\Services\Utils;
use \Illuminate\Console\Command;

class RestfulCommand extends BaseCommand
{
    /**
     * 控制台命令名称
     *
     * @var string
     */
    protected $signature = 'restful:add {modelName?}';

    protected $description = 'Add restful APIs for the given model';

    /**
     * @return void
     */
    public function handle()
    {
        $modelName = $this->argument('modelName');
        $modelName = $this->selectModel($modelName);

        $namespace = $this->ask('Input namespace');

        $this->addRoute('api.php', $namespace, $modelName);

        $controllerNamespacePath = base_path('app/Http/Controllers/') . $namespace;
        if (!file_exists($controllerNamespacePath)) {
            mkdir($controllerNamespacePath);
        }

        $serviceNamespacePath = base_path('app/Services/') . $namespace;
        if (!file_exists($serviceNamespacePath)) {
            mkdir($serviceNamespacePath);
        }

        $this->addController($namespace, $modelName);

        $this->addService($namespace, $modelName);
    }

    private function addRoute($routeFile, $namespace, $modelName)
    {
        $path = strtolower($modelName);
        $pathPlural = str_plural($path);

        $controllerName = $modelName . 'Controller';
        if ($namespace) {
            $controllerName = $namespace . '\\' . $controllerName;
        }

        ob_start();
        include 'templates/model_restful_api/routes.php';
        $fileContent = "\n" . ob_get_contents();
        ob_end_clean();

        $fileName = base_path('routes') . DIRECTORY_SEPARATOR . $routeFile;
        file_put_contents($fileName, $fileContent, FILE_APPEND);
    }

    /**
     * @param $namespace
     * @param $modelName
     */
    private function addController($namespace, $modelName)
    {
        $vars = compact('namespace', 'modelName');
        $content = $this->getContent('templates/model_restful_api/controller.php', $vars);

        $fileName = base_path('app/Http/Controllers') . "/$namespace/{$modelName}Controller.php";
        file_put_contents($fileName, "<?php\n" . $content);
    }

    /**
     * @param $namespace
     * @param $modelName
     */
    private function addService($namespace, $modelName)
    {
        $vars = compact('namespace', 'modelName');

        $content = $this->getContent('templates/model_restful_api/service.php', $vars);

        $fileName = base_path('app/Services') .  "/$namespace/{$modelName}Service.php";
        file_put_contents($fileName, "<?php\n" . $content);
    }
}