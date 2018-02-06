<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/12/6
 * Time: 13:49
 */

namespace App\Services;


use App\Models\KxUser;

class TalkService
{
    public static function toMySelf()
    {
        $result = self::getStoreIdArray(1);

        if (is_array($result)) {

        }

        if (is_array($result)) {

        }

        if (!empty($result)) {

        }
    }

    /**
     * @return array
     */
    public static function getStoreIdArray($merchantId)
    {
        ///
        //return [0, 'mmmm'];
        return -1;

        return [];
    }


    public static function toMySelf2()
    {

        $count = self::getStoreCount(1);

        if ($count >= 0) {

        }
    }

    public static function getStoreCount($merchantId)
    {
        return false;


        return 5;
    }





















    /**
     *
     */
    public static function toMySelf3()
    {

        $result = self::getStoreCount2(1);

        if ($result->hasError()) {

        }




        $count = $result->data();

        // TODO:
    }

    public static function getStoreCount2($merchantId)
    {
        if ($merchantId == 0) {
            return Result::error(Errors::NotFound, []);
        }

        return Result::ok(5);
    }
}