<?php

namespace Tests\AutoSmoking;

class DataSet
{
    //用例执行前需要清空的表
    public $tables = [
        'th_sample',
        'kx_user'
    ];

    public $inserts = [
    ];

    public function getInserts() {
        return $this->inserts;


    }
}

