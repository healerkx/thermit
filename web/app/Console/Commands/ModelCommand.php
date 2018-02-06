<?php

namespace App\Console\Commands;

use \Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Created by PhpStorm.
 * User: healer
 * Date: 16/12/5
 * Time: 下午11:11
 * Usage:
 *  php artisan model:create bo_%
 *  生成数据库中以bo_开头的表对应的Model
 */
class ModelCommand extends Command
{
    /**
     * 控制台命令名称
     *
     * @var string
     */
    protected $signature = 'model:create {tableName} {format=PHP}';

    protected $description = 'Generated models against tables';

    /**
     * @return void
     */
    public function handle()
    {
        $multiTables = false;
        $tableName = $this->argument('tableName');
        if (strstr($tableName, '%')) {
            $multiTables = true;
        }
        // 默认是PHP, 现在支持Go
        $format = $this->argument('format');

        if ($multiTables) {
            $tables = self::getTables(str_replace('%', '', $tableName));
            foreach ($tables as $tableName) {
                self::createModel($tableName, $format);
            }
        } else {
            self::createModel($tableName, $format);
        }

        // TODO: Dump file into 'Models'
        exit;
    }

    protected static function createModel($tableName, $format)
    {
        $tableInfo = self::getTableInfo($tableName);

        if (Str::upper($format) == 'PHP') {
            self::createModelForPHP($tableName, $tableInfo);
        } elseif (Str::upper($format) == 'GO') {
            self::createModelForGo($tableName, $tableInfo);
        }
    }

    private static function createModelForPHP($tableName, $tableInfo)
    {
        $modelName = ucfirst(Str::camel($tableName));

        $primaryFieldInfo = current(array_filter($tableInfo, function ($field) {
            return ($field->Key == 'PRI');
        }));

        $primaryKey = $primaryFieldInfo->Field;

        ob_start();
        include 'templates/modelbase.php';
        $fileContent = "<?php\n" . ob_get_contents();
        ob_end_clean();

        $appRootPath = dirname(dirname(dirname(__FILE__)));

        // Base models
        $fileName = $appRootPath . '/Models/Base/' . $modelName . 'Base.php';
        file_put_contents($fileName, $fileContent);

        ob_start();
        include 'templates/model.php';
        $fileContent = "<?php\n" . ob_get_contents();
        ob_end_clean();

        $fileName = $appRootPath . '/Models/' . $modelName . '.php';
        if (!file_exists($fileName)) {
            // Models下面的Model文件只生成一次, 以后不会再被修改, 除非我改生成逻辑
            file_put_contents($fileName, $fileContent);
        }
    }

    private static function createModelForGo($tableName, $tableInfo)
    {
        $modelName = ucfirst(Str::camel($tableName));

        $primaryKey = '';
        $maxLen = 0;
        foreach ($tableInfo as $field) {
            if ($field->Key == 'PRI') {
                $primaryKey = $field->Field;
            }

            $len = strlen(Str::camel($field->Field));
            if ($len > $maxLen) {
                $maxLen = $len;
            }
        }

        foreach ($tableInfo as $field) {
            $camel = Str::camel($field->Field);
            $memberName = ucfirst($camel);
            $alignSpaces = self::spaces($maxLen - strlen($memberName) + 2);
            $memberType = self::goTypeFromMySQLType($field->Type);

            $alignSpaces2 = self::spaces(10 - strlen($memberType));

            $annotation = "`json:\"{$camel}\"`";
            if ($primaryKey == $field->Field) {
                $annotation = "`orm:\"pk\" json:\"{$camel}\"`";
            }

            $fieldLine = "{$memberName}{$alignSpaces}{$memberType}{$alignSpaces2}{$annotation}";

            $field->fieldLine = $fieldLine;
        }

        $confFile = './gomodel.conf';
        $conf = '';
        if (file_exists($confFile)) {
            $conf = file_get_contents($confFile);
        }


        ob_start();
        include 'templates/gomodel.php';
        $fileContent = ob_get_contents();
        ob_end_clean();

        $appRootPath = dirname(dirname(dirname(__FILE__)));

        if ($conf) {

            $fileName = $conf . '/' . $tableName . '.go';
            echo $fileName;
        } else {
            $fileName = $appRootPath . '/Models/Base/' . $tableName . '.go';
        }

        file_put_contents($fileName, $fileContent);
    }

    private static function getTableInfo($tableName)
    {
        $results = DB::select("show full columns from $tableName");

        return $results;
    }

    private static function getTables($tableNamePattern)
    {
        $results = DB::select("show tables");

        $results = json_decode(json_encode($results), TRUE);

        $tables = array_map(function ($table) {
            $tableName = current($table);
            return $tableName;
        }, array_filter($results, function ($table) use ($tableNamePattern) {
            $tableName = current($table);
            return strstr($tableName, $tableNamePattern) != null;
        }));

        return $tables;
    }

    const ALIGN = 8;

    private static function spaces($n)
    {
        // $n = intval((($n + self::ALIGN - 1) / self::ALIGN) + 1) * self::ALIGN;
        return substr('                               ', 0, $n);
    }

    private static function goTypeFromMySQLType($type)
    {
        $type = strtolower($type);
        if (strstr($type, 'varchar') ||
            strstr($type, 'char') ||
            strstr($type, 'text')
        ) {
            return 'string';
        } elseif (strstr($type, 'int')) {
            return 'int64';
        }

    }
}