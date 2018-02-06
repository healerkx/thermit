<?php
/**
 * Created by PhpStorm.
 * User: quwei
 * Date: 2017/6/30
 * Time: 下午4:43
 */
namespace Tests\AutoSmoking;

class TestFunction
{
    /**
     * 验证返回值长度
     * @param $caseName string
     * @param $caseValue string
     * @param $returnData array
     *
     * @return 无
     */
    public function count($caseName, $caseValue, $returnData)
    {
        $result = $this->getValueByCase($caseName, $returnData);
        if (count($result) != $caseValue) {
            echo 'count test error';
        }

    }

    /**
     * 验证返回值不包含
     * @param $caseName string
     * @param $caseValue string
     * @param $returnData array
     *
     * @return 无
     */
    public function notContain($caseName, $caseValue, $returnData) {

        $result = $this->getValueByCase($caseName, $returnData);
        if (in_array($caseValue, $result)) {
            echo "error, $caseName contains $caseValue";
        }

    }

    /**
     * 通过case过滤返回值
     * 例如：list.abc  ====>  $data['list']['abc']
     * 例如：list.*.abc  ====>  $data['list']下所有abc组成的一维数组
     * @param $caseName string
     * @param $returnData array
     *
     * @return array
     */
    public function getValueByCase($caseName, $returnData) {
        $result = [];
        $keys = explode('.', $caseName);
        $needAll = 0; //是否需要全部数据
        foreach ($keys as $key) {
            if ($key == '*') {
                $needAll = 1;
                continue;
            }
            if ($needAll == 1 && !empty($result)) {
                foreach ($result as $v) {
                    $array[] = $v[$key];
                }
                $result = $array;
                $array = [];

            } else {
                $result = isset($returnData[$key]) ? $returnData[$key] : [];
            }
        }
        return $result;
    }
}