<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/12/1
 * Time: 23:09
 */

namespace App\Console\Commands;
use App\Services\Utils;
use Illuminate\Console\Command;


class BaseCommand extends Command
{
    protected function listNamespaces($type) {
        $ns = [];
        if ($type == 'controller') {
            $controllers = $this->listControllers();
            return array_values(
                array_filter(
                    array_unique(
                        array_map(function($c) {
                            $parts = explode('\\', $c);
                            if (count($parts) > 1) {
                                return current(explode('\\', $c));
                            }
                            return null;
                        },
                            $controllers)), function($_) { return $_;}));
        } elseif ($type == 'service') {
            $services = $this->listServices();
            return array_values(
                array_filter(
                    array_unique(
                        array_map(function($c) {
                            $parts = explode('\\', $c);
                            if (count($parts) > 1) {
                                return current(explode('\\', $c));
                            }
                            return null;
                        },
                            $services)), function($_) { return $_;}));
        }

        return $ns;
    }

    protected function listControllers()
    {
        $controllerPath = base_path('app/Http/Controllers');
        $rootPathLen = strlen($controllerPath);
        $list = Utils::readFiles($controllerPath);

        $files = $list['files'];
        return array_map(function($f) use ($rootPathLen) {
            return preg_replace('/\//', '\\', substr($f, $rootPathLen + 1, -4));
        }, $files);
    }

    protected function listModels()
    {
        $modelPath = base_path('app/Models');
        $len = strlen($modelPath);
        $models = Utils::readFiles($modelPath);

        $models = $models['files'];

        $modelBasePath = base_path('app/Models/Base');

        $models = array_map(function($i) use ($len){
            return substr($i, $len + 1, -4);
        }, array_filter($models, function($i) use($modelBasePath) {
            return strstr($i, $modelBasePath . DIRECTORY_SEPARATOR) == null;
        }));
        return $models;
    }

    protected function listRoutes()
    {
        $routesPath = base_path('routes');
        $routesPathLen = strlen($routesPath);
        $list = Utils::readFiles($routesPath);

        $files = $list['files'];
        return array_map(function($f) use ($routesPathLen) { return substr($f, $routesPathLen + 1); }, $files);
    }

    protected function listServices()
    {
        $servicePath = base_path('app/Services');
        $rootPathLen = strlen($servicePath);
        $list = Utils::readFiles($servicePath);

        $files = $list['files'];
        return array_map(function($f) use ($rootPathLen) {
            return preg_replace('/\//', '\\', substr($f, $rootPathLen + 1, -4));
        }, $files);
    }

    /**
     * @param $type
     * @param bool|true $otherChoiceAllowed
     * @param bool|false $prompt
     * @return string
     */
    public function askNamespace($type, $otherChoiceAllowed=true, $prompt=false)
    {
        $otherChoice = '[Other Choice]';
        $choices = $this->listNamespaces($type);
        if ($otherChoiceAllowed) {
            $choices[] = $otherChoice;
        }
        $select = $this->choice($prompt ?: 'Please select a namespace', $choices);

        if ($select == $otherChoice) {
            $select = $this->askName('', 'Please input a namespace');
        }
        return $select;
    }

    /**
     * @param $name
     * @param bool|false $prompt
     * @return string
     */
    public function askName($name, $prompt=false)
    {
        if (!$name) {
            $name = $this->ask($prompt ?: 'Please input a name');
        }
        return $name;
    }

    /**
     * @param $select
     * @param $choices
     * @param bool|false $prompt
     * @return string
     */
    public function askSelect($select, array $choices, $prompt=false)
    {
        if (!$select) {
            $select = $this->choice($prompt ?: 'Please select a choice', $choices);
        }
        return $select;
    }

    protected function selectRoute($routeFile)
    {
        if (!$routeFile) {
            $routes = $this->listRoutes();
            $routeFile = $this->choice('Provide route file to write', $routes);
        }
        return $routeFile;
    }

    protected function selectModel($modelName)
    {
        if (!$modelName) {
            $modelNames = $this->listModels();
            $modelName = $this->choice('Please select a Model', $modelNames);
        }
        return $modelName;
    }

    protected function selectController($controller)
    {
        if (!$controller) {
            $controllers = $this->listControllers();
            $controllers = array_map(function($c) {
                return preg_replace('/\//', '\\', $c);
            }, $controllers);

            $controller = $this->choice('Select a Controller', $controllers);
        }
        return $controller;
    }

    protected function getControllerFilePath($namespace, $controllerName)
    {
        $controllerPath = base_path('app/Http/Controllers/');
        $s = DIRECTORY_SEPARATOR;
        if ($namespace) {
            return $controllerPath . $namespace . $s . $controllerName . '.php';
        } else {
            return $controllerPath . $controllerName . '.php';
        }
    }

    /**
     * @param string $templateFile
     * @param array $vars
     * @return string
     */
    protected function getContent($templateFile, array $vars)
    {
        @extract($vars);

        ob_start();
        include($templateFile);
        $actionContent = ob_get_contents();
        ob_end_clean();
        return $actionContent;
    }

}