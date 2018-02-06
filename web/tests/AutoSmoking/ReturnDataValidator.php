<?php
namespace Tests\AutoSmoking;
/**
 * Created by PhpStorm.
 * User: quwei
 * Date: 2017/7/3
 * Time: 下午3:20
 */
require_once(dirname(__FILE__) . '/ReturnDataFunction.php');

class ReturnDataValidator
{
    public function __construct($returnData, $params) {
        foreach ($params as $k => $value) {
            if (strpos($k, '|')) {
                self::validReturnDataByCaseFun($k, $value, $returnData);
            } else {
                self::validReturnDataByCaseString($k, $value, $returnData);
            }
        }
    }

    /**
     * 运行case中指定函数
     * @param $caseName string
     * @param $caseValue string
     * @param $returnData array
     *
     * @return boolean
     */
    private function validReturnDataByCaseFun($caseName, $caseValue, $returnData) {
        $caseArray =  explode('|', $caseName);
        $funName = $caseArray[1];
        $caseParam = $caseArray[0];

        $testFunction = new TestFunction();
        call_user_func_array(array($testFunction,$funName), array($caseParam, $caseValue, $returnData));
    }

    /**
     * 验证case中指定结果
     * @param $caseName string
     * @param $caseValue string
     * @param $returnData array
     *
     * @return boolean
     */
    private function validReturnDataByCaseString($caseName, $caseValue, $returnData) {
        $testFunction = new TestFunction();
        $result = $testFunction->getValueByCase($caseName, $returnData);

        if(preg_match("/^[0-9]*$/",$caseValue)){
            $caseValue = intval($caseValue);
        };

        foreach ($result as $r) {
            if ($r != intval($caseValue)) {
                echo "valid returnData error [{$caseName}]";
                exit;
            }
        }

    }
}