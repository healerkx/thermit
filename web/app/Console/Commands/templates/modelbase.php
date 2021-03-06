
namespace App\Models\Base;

/**
 * @package App\Models
 * Class <?=$modelName?>Base
 *
 * Generated by 'php artisan model:create'
 * Properties as follows
<?php
foreach ($tableInfo as $field) {
?>
 * @property $<?=$field->Field?>    <?=$field->Comment?>

<?php
}
?>
 */
class <?=$modelName?>Base extends BaseModel
{
    /**
     * Table name
     */
    protected $table = '<?=$tableName?>';

    /**
     * Table primary-key
     */
    protected $primaryKey = '<?=$primaryKey?>';

    /**
     * @return array
     */
    public static function getFields()
    {
        return [
<?php
foreach ($tableInfo as $field) {
?>            '<?=$field->Field?>',
<?php } ?>
        ];
    }
}

