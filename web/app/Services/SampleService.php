<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/27
 * Time: 20:47
 */

namespace App\Services;


use App\Models\Base\BaseModel;
use App\Models\ThSample;

class SampleService
{

    /**
     * @param $data
     * @return Result
     */
    public static function create($data)
    {
        $object = new ThSample();
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
        //var_dump($data);exit;
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
     * @return ThSample
     */
    private static function get($id)
    {
        $object = ThSample::query()->find($id);
        return $object;
    }

    /**
     * @param $filter
     * @return Result
     */
    public static function search($filter)
    {
        $query = ThSample::query();

        // TODO: Write your self's where clauses.
        $limit = isset($filter['limit']) ? $filter['limit'] : 10;
        unset($filter['limit']);
        unset($filter['page']);
        foreach ($filter as $field => $value) {
            BaseModel::tryWhere($query, $field, $value);
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