
/**
 * Created by PhpStorm.
 * User: healer
 * Date: <?=date('Y-m-d');?>

 * Time: <?=date('H:i:s');?>
 */

<?php if ($namespace):?>
namespace App\Services\<?=$namespace?>;
<?php else:?>
namespace App\Services;
<?php endif;?>

use App\Models\Base\BaseModel;
use App\Models\<?=$modelName?>;
use App\Services\Errors;
use App\Services\Pager;
use App\Services\Result;

class <?=$modelName?>Service
{

    /**
     * @param $data
     * @return Result
     */
    public static function create($data)
    {
        $object = new <?=$modelName?>();
        $object->setAttributes($data, false);
        if ($object->save()) {
            return Result::ok($object);
        }

        return Result::error(Errors::SaveFailed, $data);
    }

    /**
     * @param $id
     * @param $data
     * @return Result
     */
    public static function update($id, $data)
    {
        $object = self::get($id);
        if (!$object) {
            return Result::error(Errors::NotFound, ['id' => $id]);
        }
        $object->setRawAttributes($data, false);
        if ($object->save()) {
            return Result::ok($object);
        }

        return Result::error(Errors::SaveFailed, $data);
    }

    /**
     * @param $id
     * @return Result
     */
    public static function retrieve($id)
    {
        $object = self::get($id);
        if (!$object) {
            return Result::error(Errors::NotFound, ['id' => $id]);
        }
        return Result::ok($object);
    }

    /**
     * @param $id
     * @return <?=$modelName?>
     */
    private static function get($id)
    {
        $object = <?=$modelName?>::query()->find($id);
        return $object;
    }

    /**
     * @param $filter
     * @return Result
     */
    public static function search($filter)
    {
        $query = <?=$modelName?>::query();

        // TODO: Write your self's where clauses.
        $limit = isset($filter['limit']) ? $filter['limit'] : 10;
        unset($filter['limit']);
        unset($filter['page']);
        unset($filter['__debug']);

        $fields = <?=$modelName?>::getFields();
        foreach ($filter as $field => $value) {
            if (in_array($field, $fields)) {
                BaseModel::tryWhere($query, $field, $value);
            }
        }

        $pager = $query->paginate($limit);

        $data = Pager::fromPager($pager);

        return Result::ok($data);
    }

    /**
     * Soft delete
     * @param $id
     * @return Result
     */
    public static function delete($id)
    {
        return self::update($id, ['status' => 0]);
    }

}