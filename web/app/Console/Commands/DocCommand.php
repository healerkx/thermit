<?php
/**
 * Created by PhpStorm.
 * User: healer.kx.yu@gmail.com
 * Date: 16/12/7
 * Time: 下午9:07
 */

namespace App\Console\Commands;

use App\Services\Errors;
use App\Services\Result;
use \Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Class DocPage
 * @package App\Console\Commands
 */
class DocPage
{
    private $apiList = [];

    public function addApi($api)
    {
        $this->apiList[] = $api;
    }

    public function save($fileName)
    {
        $file = fopen($fileName, 'wb');
        // $this->writeToc($file);  // 暂时不需要TOC
        $index = 1;
        foreach ($this->apiList as $api) {
            $this->writeApi($file, $api, $index);
            $index += 1;
        }

        fclose($file);

        return true;
    }

    /**
     * @param $file
     */
    private function writeToc($file)
    {
        ob_start();
        $host = ''; // TODO:
        include 'templates/apidoc_toc.php';
        $tocContent = ob_get_contents();
        ob_end_clean();

        fwrite($file, $tocContent);
    }

    /**
     * @param $file
     * @param $api
     * @param $index
     */
    private function writeApi($file, $api, $index)
    {
        $doc = $api;
        $doc['index'] = $index;
        ob_start();
        include 'templates/apidoc.php';
        $fileContent = ob_get_contents();
        ob_end_clean();

        fwrite($file, $fileContent);
    }
}

/**
 * Class ArrayMaker
 * @package App\Console\Commands
 */
class ArrayMaker
{
    /**
     * @var array Sorted array
     */
    private $dataFields = [];

    private $result = [];

    public function __construct($dataFields)
    {
        usort($dataFields, function ($a, $b) {
            if ($a['name'] == $b['name']) {
                return 0;
            } elseif ($a['name'] < $b['name']) {
                return -1;
            }

            return 1;
        });

        $this->dataFields = $dataFields;
    }


    public function makeArray()
    {
        foreach ($this->dataFields as $field) {
            $fieldNames = explode('.', $field['name']);
            if (count($fieldNames) == 1) {
                $fieldName = current($fieldNames);
                $this->setKeyValue($this->result, $fieldName, $field['type'], $field['value']);
            } else {
                $counter = 0;
                $array = &$this->result;
                foreach ($fieldNames as $fieldName) {
                    $counter += 1;

                    if (!array_key_exists($fieldName, $array)) {
                        if ($counter != count($fieldNames)) // Not the last one
                        {
                            $array[$fieldName] = [];
                        } else {
                            $this->setKeyValue($array, $fieldName, $field['type'], $field['value']);
                        }

                    }

                    $array = &$array[$fieldName];

                }

            }
        }

        return $this->result;
    }

    /**
     * @param array $array
     * @param $fieldName
     * @param $fieldType
     * @param string $fieldValue
     */
    private function setKeyValue(array &$array, $fieldName, $fieldType, $fieldValue = '')
    {
        if ($fieldType == 'string') {
            $array[$fieldName] = $fieldValue;
        } elseif ($fieldType == 'int') {
            $array[$fieldName] = intval($fieldValue);
        } elseif ($fieldType == 'double' || $fieldType == 'float') {
            $array[$fieldName] = floatval($fieldValue);
        } elseif ($fieldType == 'json') {
            $array[$fieldName] = json_decode($fieldValue, true);
        }
    }
}


/**
 * Class DocCommand
 * @package App\Console\Commands
 * Usage:
 *  php artisan doc:generate % > ~/a.html
 *  % 表示全部的, 但是从工程实践上, 建议使用path的一部分作为分类.
 */
class DocCommand extends Command
{
    protected $signature = 'doc:generate {path}';

    protected $description = 'doc generator';

    protected $lookup;

    private static $catDocMap = [];

    private static $map = [];

    public static function getInfoByName($word)
    {
        if (array_key_exists($word, self::$map))
        {
            return self::$map[$word];
        }
        return ['<Unknown>', '<Unknown>', '???'];
    }

    public function handle()
    {
        $allPaths = false;
        $pathPattern = $this->argument('path');
        if ($pathPattern == '%') {
            $allPaths = true;
        }

        self::$map = include 'DocDict.php';

        $routes = $this->getRoutes();
        if (!$allPaths) {
            $pathPatterns = explode(',', $pathPattern);
            $routes = array_filter($routes, function ($route) use ($pathPatterns) {
                foreach ($pathPatterns as $pathPattern) {
                    if (strstr($route['uri'], $pathPattern) != null)
                        return true;
                }

                return false;
            });
        }
        $this->generateDoc($routes);
        $scriptPath = base_path('app/Console/Commands/tools/auto_publish/') . 'auto_publish.py';
        system("python3 $scriptPath");
    }

    /**
     *
     */
    private function getRoutes()
    {
        $routes = Route::getRoutes()->getRoutes();

        return array_map(function ($routeObject) {
            return [
                'uri'    => $routeObject->uri(),
                'method' => $routeObject->methods(),
                'action' => $routeObject->getAction()
            ];
        }, $routes);
    }

    /**
     * @param $routes array
     */
    private function generateDoc(array $routes)
    {
        // 生成APIs部分
        $counter = 1;
        foreach ($routes as $route) {
            $urlMethod = $route['method'];
            $uriPattern = $route['uri'];

            if (!array_key_exists('uses', $route['action'])) {
                continue;
            }
            $action = $route['action']['uses'];

            if (is_string($action)) {

            } elseif (get_class($action) == 'Closure') {
                continue;
            }

            list($controllerName, $methodName) = explode('@', $action);

            try {
                $counter += 1;
                $controller = new \ReflectionClass($controllerName);
                // var_dump($controller);

                $method = $controller->getMethod($methodName);
                if ($method) {
                    $this->convertDocToWiki($urlMethod, $uriPattern, $method->getDocComment());

                }
            } catch(\Exception $e) {
                $errorCode = $e->getCode();
                echo "{$controllerName}@{$methodName} has doc error [$errorCode]\n";
                echo $e->getMessage();
                echo "\n";
            }
        }

        echo "Count=$counter\n";

        foreach (self::$catDocMap as $cat => $page) {
            $page->save("storage/docs/$cat.html");
        }
    }

    /**
     * @param $httpMethod
     * @param $uriPattern
     * @param $docStr
     * @return string doc
     *
     */
    private function convertDocToWiki($httpMethod, $uriPattern, $docStr)
    {
        $docResult = self::parseDocStr($docStr);
        if ($docResult->hasError()) {
            return $docResult;
        }
        $doc = $docResult->data();

        $doc['httpMethod'] = implode(',', $httpMethod);
        $doc['url'] = $uriPattern;
        $doc['examples'] = [];// TODO:

        // TODO: support multi category!
        $cat = $doc['cat']; //Category!
        if (array_key_exists($cat, self::$catDocMap)) {
            $page = self::$catDocMap[$cat];
            $page->addApi($doc);
        } else {
            $page = new DocPage();
            $page->addApi($doc);
            self::$catDocMap[$cat] = $page;
        }

        return $doc;
    }

    private function parseDocStr($docStr)
    {
        if (!trim($docStr)) {
            return Result::error(Errors::Unknown, []);
        }
        $docLines = array_map(
            function ($line) {
                return trim($line, " \t*");
            },
            explode("\n", $docStr));

        $doc = [
            'title'      => "",
            'httpMethod' => '',
            'comment'    => "",
            'url'        => '',
            'urlParams'  => [],
            'formParams' => [],
            'retFields'  => [],
            'statusEnum' => [],
            'retVal'     => self::convertToHtml([])
        ];

        foreach ($docLines as $line) {
            if (Str::startsWith($line, '@title')) {
                $doc['title'] = self::afterSkip($line, '@title');
            } elseif (Str::startsWith($line, '@comment')) {
                $doc['comment'] = self::afterSkip($line, '@comment');
            } elseif (Str::startsWith($line, '@cat')) {
                $doc['cat'] = self::afterSkip($line, '@cat');
            } elseif (Str::startsWith($line, '@url-param')) {
                $urlParamLine = self::afterSkip($line, '@url-param');
                $parts = explode('||', $urlParamLine);
                try {
                    $urlParam = [
                        'name'     => trim($parts[0]),
                        'type'     => trim($parts[1]),
                        'comment'  => trim($parts[2]),
                        'required' => count($parts) > 3 ? 'No' : 'Yes'
                    ];
                } catch (\Exception $e) {
                    throw new \Exception("Bad @url-param in line $urlParamLine");
                }

                $doc['urlParams'][] = $urlParam;
            } elseif (Str::startsWith($line, '@form-param')) {
                $formParamLine = self::afterSkip($line, '@form-param');
                $parts = explode('||', $formParamLine);
                $formParam = [
                    'name'     => trim($parts[0]),
                    'type'     => trim($parts[1]),
                    'comment'  => trim($parts[2]),
                    'required' => count($parts) > 3 ? 'No' : 'Yes'
                ];

                $doc['formParams'][] = $formParam;
            } elseif (Str::startsWith($line, '@ret-val')) {
                $retValLine = self::afterSkip($line, '@ret-val');
                $parts = explode('||', $retValLine);

                if (count($parts) == 1) {
                    $retName = $parts[0];
                    $nameParts = explode('.', $retName);

                    $info = self::getInfoByName(end($nameParts));
                    if ($info) {
                        $parts[1] = $info[0];
                        $parts[2] = $info[1];
                        $parts[3] = $info[2];
                    }
                }

                $retVal = [
                    'name'    => trim($parts[0]),
                    'type'    => trim($parts[1]),
                    'comment' => trim($parts[2]),
                    'value'   => isset($parts[3]) ? trim($parts[3]) : "None",
                ];

                $doc['retFields'][] = $retVal;
            } elseif (Str::startsWith($line, '@status')) {
                $retValLine = self::afterSkip($line, '@status');
                $parts = explode('||', $retValLine);
                $status = [
                    'value'   => trim($parts[0]),
                    'comment' => trim($parts[1]),
                ];

                $doc['statusEnum'][] = $status;
            }
        }

        $arrayMaker = new ArrayMaker($doc['retFields']);
        $array = $arrayMaker->makeArray();

        $doc['retVal'] = self::convertToHtml($array);

        return Result::ok($doc);
    }

    private static function afterSkip($line, $skip)
    {
        return trim(substr($line, strlen($skip)));
    }

    private static function convertToHtml($data)
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $json = preg_replace("/\n/", '<br>', $json);
        $json = preg_replace("/ /", '&nbsp;', $json);

        // 此处不能使用/\\s/, 而是 / /, 注意此处的空格, 否则UTF8写入有问题

        return $json;
    }

}