<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/12/6
 * Time: 13:16
 */

namespace App\Console\Commands;


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableCommand extends BaseCommand
{
    /**
     * 控制台命令名称
     *
     * @var string
     */
    protected $signature = 'table:create {tableName?}';

    protected $description = 'Create a database table';

    /**
     * @return void
     */
    public function handle()
    {
        $tableName = $this->askName($this->argument('tableName'), 'Please input a table name');

        $tableName = "{$tableName}";

        $pkName = substr($tableName, strripos($tableName, '_') + 1);

        Schema::create($tableName, function(Blueprint $table) use ($pkName) {
            $table->engine = 'InnoDB';
            $table->increments("{$pkName}_id");

            // list($fieldType, $fieldName, $fieldDefaultValue) = $this->askFieldInfo();

            $table->tinyInteger('status')->default(1);
            $table->integer('create_time')->default(0);
            $table->integer('update_time')->default(0);

        });
    }

    private function askFieldInfo()
    {
        $fieldTypeMethodMapping = [
            'int' => 'integer',
            'tinyint' => 'tinyInteger',
            'varchar' => 'string'
        ];

        $fieldName = $this->askName('', 'Please input field name and type');

        $fieldType = $this->askName('', 'Please input field type');

        $fieldDefaultValue = $this->askName('', 'Please input field default value');

        return [
            $fieldTypeMethodMapping[$fieldType],
            $fieldName,
            $fieldDefaultValue];
    }
}