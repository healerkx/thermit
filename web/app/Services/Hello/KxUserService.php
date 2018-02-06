<?php

/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017-12-08
 * Time: 06:16:05 */

namespace App\Services\Hello;

use App\Models\Base\BaseModel;
use App\Models\KxUser;
use App\Services\Errors;
use App\Services\Pager;
use App\Services\Result;

class KxUserService
{

    /**
     * @param $data
     * @return Result
     */
    public static function create($data)
    {
        $object = new KxUser();
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
     * @return KxUser     */
    private static function get($id)
    {
        $object = KxUser::query()->find($id);
        return $object;
    }

    /**
     * @param $filter
     * @return Result
     */
    public static function search($filter)
    {
        $query = KxUser::query();

        // TODO: Write your self's where clauses.
        $limit = isset($filter['limit']) ? $filter['limit'] : 10;
        unset($filter['limit']);
        unset($filter['page']);
        unset($filter['__debug']);

        $fields = KxUser::getFields();
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