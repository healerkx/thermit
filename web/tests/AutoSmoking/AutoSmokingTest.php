<?php

namespace Tests\AutoSmoking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

ob_start();

define('ENV_FILE', '.env.unit');

/**
 * Created by PhpStorm.
 * User: healer.kx.yu@gmail.com
 * Date: 2017/1/5
 * Time: 下午1:40
 */
class AutoSmokingTest extends TestCase
{
    private $methodGetCount = 0;

    private $methodPostCount = 0;

    private $caseGetCount = 0;

    private $casePostCount = 0;


    private $commonHeaders = [];

    // 需要UT的列表(这个仅仅用于某个UT接口开发过程中, 只想跑这个接口的UT的时候才需要)
    // 所以对应的actions.php文件, 不要git add -f actions.php !!!
    private $actionToTest = [];

    //不需要测试的接口
    private $noTestApi = [

    ];

    public function setUp()
    {
        parent::setUp();
        // 公共的Headers(解决dev, admin, merchant的登录验证问题)
        $this->commonHeaders = [

        ];
        // 数据重置
        $this->dataResetNew();
        // $this->dataReset();
        $this->fetchActionsNeedTest();
    }

    public function fetchActionsNeedTest()
    {
        $actions = [];
        if (file_exists(dirname(__FILE__) . '/actions.php')) {
            $actions = include('actions.php');
        }

        $this->actionToTest = array_map(function($action) {
            return "App\\Http\\Controllers\\$action";
        }, $actions);
    }


    /**
     * 通过PHP array进行数据初始化
     */
    private function dataResetNew()
    {
        require_once(dirname(__FILE__) . '/auto_smoking_test_db.php');
        $dataSet = new DataSet();
        foreach ($dataSet->tables as $table) {
            $emptyTableSql = 'TRUNCATE TABLE ' . $table;
            DB::statement($emptyTableSql);
        }

        foreach ($dataSet->getInserts() as $tableName => $tableData) {
            foreach ($tableData as $record) {
                $fields = implode(',', array_keys($record));
                $valuesArray = [];
                foreach ($record as $v) {
                    if (is_string($v)) {
                        $str = '\'' . $v . '\'';
                        array_push($valuesArray, $str);
                    } else {
                        array_push($valuesArray, $v);
                    }
                }
                $values = implode(',', $valuesArray);
                $insertSql = 'INSERT INTO ' . $tableName . '(' . $fields . ') VALUES (' . $values . ');';
                DB::statement($insertSql);
            }
        }
    }

    /**
     * @return bool
     * 通过SQLs进行数据初始化
     */
    private function dataReset()
    {
        $sqlPath = dirname(__FILE__) . '/auto_smoking_test_db.sql';
        if (!file_exists($sqlPath)) {
            return false;
        }

        $sqlArray = [];
        $sqlStr = '';
        $handle = fopen($sqlPath, "r");
        if ($handle) {
            while (!feof($handle)) {
                $buffer = trim(fgets($handle));
                if (substr($buffer, 0, 2) == '--' || $buffer == '') {
                    $sqlStr = '';
                } elseif (substr($buffer, -1) == ';') {
                    array_push($sqlArray, $sqlStr . $buffer);
                    $sqlStr = '';
                } else {
                    $sqlStr .= $buffer;
                }
            }
        }
        foreach ($sqlArray as $v) {
            DB::statement($v);
        }
    }

    private function getApiRoutes()
    {
        return $this->getRoutes();
    }

    /**
     *
     */
    private function getRoutes()
    {
        $routes = Route::getRoutes()->getRoutes();

        $routes = array_map(function ($routeObject) {
            return [
                'uri'    => $routeObject->uri(),
                'method' => current($routeObject->methods()),
                'action' => $routeObject->getAction()
            ];
        }, $routes);

        $api = array_filter($routes, function($i) { return Str::startsWith($i['uri'], 'api');});
        // var_dump($api);
        return $api;
    }

    public function testAll()
    {
        $routes = $this->getApiRoutes();

        echo "\nStart all APIs auto smoking test cases\n";

        foreach ($routes as $route) {
            $urlMethod = $route['method'];
            $uriPattern = $route['uri'];
            // Remove optional URL params
            $uriPattern = preg_replace('/\[.*\]/', '', $uriPattern);
            if (in_array($uriPattern, $this->noTestApi) || strpos($uriPattern, '/bo/v1/private') === 0) {
                continue;
            }
            $action = $route['action']['uses'];

            if (!empty($this->actionToTest) && !in_array($action, $this->actionToTest)) {
                continue;
            }

            if (is_string($action)) {

            } elseif (get_class($action) == 'Closure') {
                continue;
            }

            list($controllerName, $methodName) = explode('@', $action);

            try {

                $controller = new \ReflectionClass($controllerName);

                $method = $controller->getMethod($methodName);
                if ($method) {

                    $this->doApiSmokingTest($urlMethod, $uriPattern, $method->getDocComment());
                }
            } catch(\Exception $e) {
                echo "$controllerName @ $methodName\n";
                //echo $this->response->getContent();
                var_dump($e->getFile());
                var_dump($e->getLine());
                var_dump($e->getMessage());
                var_dump($e->getCode());
                exit;
            }
        }

        echo "\n";
        echo "Auto smoking test-cases report:\n";
        echo "\tGET APIs    count={$this->methodGetCount}\n";
        echo "\tGET Cases   count={$this->caseGetCount}\n";
        echo "\tPOST APIs   count={$this->methodPostCount}\n";
        echo "\tPOST Cases  count={$this->casePostCount}\n";
        $allAPIsCount = $this->methodGetCount + $this->methodPostCount;
        $allCasesCount = $this->caseGetCount + $this->casePostCount;
        echo "\tAll APIs    count={$allAPIsCount}\n";
        echo "\tAll Cases   count={$allCasesCount}\n";
    }


    private function doApiSmokingTest($urlMethod, $uriPattern, $doc)
    {
        $docLines = self::parseDocToLines($doc);
        $cases = self::parseCases($docLines);//获取请求参数


        // 关闭Redis, 如果接口使用了Redis优化, 则该接口还有额外2次使用Redis优化的数据返回并校验
        putenv('REDIS_USED_ENABLE=0');

        if ($urlMethod == 'GET') {
            $this->doGetApiSmokingTest($urlMethod, $uriPattern, $cases);
            $this->methodGetCount += 1;
        } elseif ($urlMethod == 'POST') {
            $this->doPostApiSmokingTest($urlMethod, $uriPattern, $cases);
            $this->methodPostCount += 1;
        }
    }

    private function doGetApiSmokingTest($urlMethod, $uriPattern, $cases)
    {
        foreach ($cases as $case) {

            $url = $this->printUrl($urlMethod, $uriPattern, $case);

            // 如果@case指定了header, 则不需要commonHeaders的设置
            // $headers = !empty($value['headerParams']) ? $value['headerParams'] : $this->commonHeaders;

            // 改为如果存在指定header,则在commonHeaders里追加或覆盖原有值
            $headers = $this->commonHeaders;
            foreach($case['headerParams'] as $k => $h) {
                $headers[$k] = $h;
            }

            // $this->get($url, $headers)->seeJson(['status' => intval($case['expectParams']['status'])]);

            $this->get($url, $headers)->assertJson(['status' => $case['expectParams']['status']]);

            $this->caseGetCount += 1;

            // db验证
            if (!empty($case['dbParams'])) {
                $this->validateDbValues($case['dbParams']);
            }

            // TODO: redis验证
            if (isset($case['has-redis']) && $case['has-redis']) {
                $this->validateRedis($returnData, $url, $headers, $case['expectParams']['status']);
                $this->caseGetCount += 2;
            }

            if (isset($case['has-go-api']) && $case['has-go-api']) {
                $goApiUrl = $this->printGoUrl($urlMethod, $uriPattern, $case);
                $this->validateGoApi($returnData, $goApiUrl, $headers, $case['expectParams']['status']);

                $this->caseGetCount += 1;
            }

            if(!empty($case['returnDataParams'])) {
                $returnDataToArray = json_decode($returnData, true);
                $returnDataToArray = $returnDataToArray['data'];
                new ReturnDataValidator($returnDataToArray, $case['returnDataParams']);
            }
        }
    }

    private function doPostApiSmokingTest($urlMethod, $uriPattern, $params)
    {
        foreach ($params as $value) {
            $url = $this->printUrl($urlMethod, $uriPattern, $value);
            $data = $value['postParams'];
            // 如果@case指定了header, 则不需要commonHeaders的设置
            // $headers = !empty($value['headerParams']) ? $value['headerParams'] : $this->commonHeaders;

            // 改为如果存在指定header,则在commonHeaders里追加或覆盖原有值
            $headers = $this->commonHeaders;
            foreach($value['headerParams'] as $k => $h) {
                $headers[$k] = $h;
            }

            $this->post($url, $data, $headers)->assertJson(['status' => $value['expectParams']['status']]);

            $this->casePostCount += 1;
            // db验证


            if (!empty($value['dbParams'])) {
                $this->validateDbValues($value['dbParams']);
            }

            // 对于Post接口来说, 暂时没有Redis的优化, 但是Post接口可能引用Redis的数据.
            // TODO:
        }
    }

    /**
     * @title 输出url
     * @param $urlMethod
     * @param $uriPattern
     * @param array $params
     * @return string
     */
    private function printUrl($urlMethod, $uriPattern, $params)
    {
        $setUrlParams = !empty($params['urlParams']) ? $params['urlParams'] : [];
        // 根据URL和参数,拼装最终的URI
        $url = self::buildUri($uriPattern, $setUrlParams);
        // 输出到终端
        echo "{$urlMethod}\t{$url}\n";

        return $url;
    }

    private function printGoUrl($urlMethod, $uriPattern, $params)
    {
        $setUrlParams = !empty($params['urlParams']) ? $params['urlParams'] : [];
        // 根据URL和参数,拼装最终的URI
        $url = self::buildUri($uriPattern, $setUrlParams);

        // 输出到终端
        echo "{$urlMethod}\t{$url}\n";

        return $url;
    }

    /**
     * @title 获取case参数
     * @param array $docLines
     * @return array
     */
    private static function parseCases(array $docLines)
    {
        // case的格式是  @case PARAM1=... @form FORM-PARAM=... @expect VALUE=...
        $results = [];

        $template = [
            'has-redis' => false,
            'has-go-api' => false
        ];

        foreach ($docLines as $line) {
            if (Str::startsWith($line, '@has-redis')) {
                $template['has-redis'] = true;
            } elseif (Str::startsWith($line, '@has-go-api')) {
                $template['has-go-api'] = true;
            }
        }

        foreach ($docLines as $line) {
            $params = [
                'has-redis' => $template['has-redis'],
                'has-go-api' => $template['has-go-api']
            ];

            if (Str::startsWith($line, '@case')) {
                // 获取get参数
                $urlParamStr = self::getCharBetween($line, '@case');
                $params['urlParams'] = self::getParamsToArray($urlParamStr);

                // 获取post参数
                $postParamStr = self::getCharBetween($line, '@form');
                $params['postParams'] = self::getParamsToArray($postParamStr);

                // 获取期望得到的结果
                $expectParamStr = self::getCharBetween($line, '@expect');
                $params['expectParams'] = self::getParamsToArray($expectParamStr);
                if (!isset($params['expectParams']['status'])) {
                    // TODO: status value should be able to config
                    $params['expectParams']['status'] = 0;
                }
                $params['expectParams'] = array_map(function($v){
                    return intval($v);
                }, $params['expectParams']);

                // 获取不期望得到的结果
                $unexpectParamStr = self::getCharBetween($line, '@unexpect');
                $params['unexpectParams'] = self::getParamsToArray($unexpectParamStr);
                $params['unexpectParams'] = array_map(function($v){
                    return intval($v);
                }, $params['unexpectParams']);

                // 获取header参数
                $headerParamStr = self::getCharBetween($line, '@header');
                $params['headerParams'] = self::getParamsToArray($headerParamStr);

                // 数据验证
                $dbParamStr = self::getCharBetween($line, '@db');
                $params['dbParams'] = self::getParamsToArray($dbParamStr);

                // 返回值验证
                $returnDataParamStr = self::getCharBetween($line, '@returnData');
                $params['returnDataParams'] = self::getParamsToArray($returnDataParamStr);

                // redis验证(旧的格式, 新格式等于说所有的case都要经过Redis的验证)
                if (strpos($line, '@has-redis') > 0) {
                    $params['has-redis'] = true;
                }

                array_push($results, $params);
            }
        }

        if (empty($results)) {
            $results[0]['urlParams'] = [];
            $results[0]['postParams'] = [];
            $results[0]['expectParams'] = ['status' => 0];
            $results[0]['unexpectParams'] = [];
            $results[0]['headerParams'] = [];
            $results[0]['dbParams'] = [];
            $results[0]['has-redis'] = [];
        }
        return $results;
    }

    private static function getCharBetween($str, $start, $end = "@")
    {
        $part = explode($start, $str);
        if (isset($part[1])) {
            $results = trim(explode($end, $part[1])[0]);

            return $results;
        }

        return '';
    }

    private static function getParamsToArray($paramsStr)
    {
        $params = [];
        if (!empty($paramsStr)) {
            $paramsArray = explode('||', $paramsStr);
            foreach ($paramsArray as $k => $paramLine) {
                if (Str::startsWith(trim($paramLine), '{')) { // 若为json字符串
                    $params[$k] = json_decode(trim($paramLine), true);
                } else {
                    $assign = explode('=', $paramLine);
                    if (count($assign) > 1) {
                        if (substr(trim($assign[0]), -2) == '[]') {
                            $assign[0] = substr_replace(trim($assign[0]), '', -2);
                            $params[$assign[0]] = json_decode(trim($assign[1]), true);
                        } else {
                            $params[trim($assign[0])] = trim($assign[1]);
                        }
                    }
                }
            }
        }

        return $params;
    }

    /**
     * @title 验证数据库是否与期待一致
     * @param array $params
     */
    private function validateDbValues($params)
    {
        foreach ($params as $value) {
            if (!isset($value['field']) || !isset($value['table']) || !isset($value['condition'])) {
                echo 'field and table and condition must required in @db';
                exit;
            }

            $validParams = [];
            // case里定义的需要验证的字段值
            $validParamArray = explode('&', $value['field']);
            foreach ($validParamArray as $paramLine) {
                $assign = explode('=', $paramLine);
                if (count($assign) > 1) {
                    $validParams[trim($assign[0])] = trim($assign[1]);
                }
            }

            // 数据库实际存储的字段值
            $selectField = implode(',', array_keys($validParams));
            $sql = "select " . $selectField . " from " . $value['table'] . " where " . $value['condition'];
            $data = json_decode(json_encode(DB::select($sql)), true);

            foreach ($data as $k => $v) {
                $r = array_diff($v, $validParams);
                if (!empty($r)) {
                    echo "data test error" . "\n";
                    print_r($value);
                    exit;
                }
            }
        }
    }

    private function validateRedis($returnData, $url, $headers, $status) {

        putenv('REDIS_USED_ENABLE=1'); // redis打开

        RedisService::connect()->clear();
        $this->get($url, $headers)->seeJson(['status' => $status]);
        $redisNoData = $this->response->getContent();


        $this->get($url, $headers)->seeJson(['status' => $status]);
        $redisHaveData = $this->response->getContent();

        $same = self::compareData($returnData, $redisNoData, $redisHaveData);
        if(!$same) {
            echo 'Redis not verified';
            exit();
        } else {
            echo "\t@has-redis, got same data in 3 times\n";
        }
    }

    private function validateGoApi($returnData, $url, $headers, $status) {
        $url = env('GO_API_HOST') . $url;

        // TODO: curl implement for remote call !
        //
        $goApiData = $this->httpGetString($url, $headers);

        $same = self::compareData($returnData, $goApiData);
        if(!$same) {
            echo 'Go Api not verified';
            exit();
        } else {
            echo "\t@has-go-api, got same data between 2 invokes\n";
        }
    }

    /**
     * TODO:
     * @param $url
     * @param $headers
     * @return string
     */
    private function httpGetString($url, $headers)
    {
        try {
            $httpClient = new \GuzzleHttp\Client();

            $option = [
                'headers' => $headers,
                'timeout' => config('database.upload_timeout'),
                'connect_timeout' => config('database.upload_connect_timeout'),
            ];

            $resultJson = $httpClient
                ->request('GET', $url, $option)
                ->getBody()
                ->getContents();

            return $resultJson;

        } catch (\HttpException $e) {
            return "";
        }
    }


    private static function parseDocToLines($docStr)
    {
        return array_map(
            function ($line) {
                return trim($line, " \t*");
            },
            explode("\n", $docStr));
    }

    /**
     * @param $uriPattern
     * @param array $urlParams
     * @return string
     */
    private static function buildUri($uriPattern, array $urlParams)
    {
        $url = preg_replace_callback('/{(\w+)}/',
            function ($m) use (&$urlParams) {
                if ($m) {
                    $paramName = $m[1];
                    if (array_key_exists($paramName, $urlParams)) {
                        $paramVal = $urlParams[$paramName];
                        unset($urlParams[$paramName]);

                        return $paramVal;
                    }
                }

                return 0;
            },
            $uriPattern);

        $first = true;
        foreach ($urlParams as $paramName => $paramVal) {
            $url .= ($first ? '?' : '&') . "{$paramName}={$paramVal}";
            $first = false;
        }

        return $url;
    }

    private static function afterSkip($line, $skip)
    {
        return trim(substr($line, strlen($skip)));
    }

    /**
     * @param $d1
     * @param $d2
     * @param $d3
     * @return boolean
     *
     * TODO: 比较两个json返回值data的部分,如果一致,说明接口在Redis的支持下,结果和没有Redis的时候是一样的.
     */
    private static function compareData($d1, $d2, $d3=false)
    {
        $array1 = \json_decode($d1, true);
        $array2 = \json_decode($d2, true);

        $ignoreFields = ['tcost', 'source'];
        $r = self::compareArray($array1, $array2, $ignoreFields);
        if ($d3) {
            $array3 = \json_decode($d3, true);
            $r = $r && self::compareArray($array2, $array3, $ignoreFields);
        }
        return $r;
    }

    /**
     * @param $arr
     * @return bool
     */
    private static function is_assoc($arr) {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * @param $array1
     * @param $array2
     * @param $ignoreFields
     * @return boolean
     * 比较两个数组, 如果是关联数组,忽略顺序
     * 如果是非关联的, 顺序影响相等的比较, 支持$ignoreFields, 例如我们的项目不比较tcost字段.
     */
    private static function compareArray($array1, $array2, $ignoreFields=[])
    {
        if (!is_array($array1) || !is_array($array2)) {
            return $array1 === $array2;
        }

        $keys1 = array_keys($array1);
        $keys2 = array_keys($array2);

        if (count($keys1) != count($keys2)) {
            return false;
        }

        $assoc1 = self::is_assoc($array1);
        $assoc2 = self::is_assoc($array2);
        if ($assoc1 != $assoc2) {
            return false;
        }

        foreach ($keys1 as $key) {
            if (in_array($key, $ignoreFields)) {
                continue;
            }

            if (!self::compareArray($array1[$key], $array2[$key], $ignoreFields)) {
                return false;
            }
        }
        return true;
    }
}