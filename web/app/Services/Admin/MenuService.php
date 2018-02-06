<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/24
 * Time: 22:17
 */

namespace App\Services\Admin;


use App\Models\ThMenu;

class MenuService
{

    public static function getMenu()
    {
        $menus = ThMenu::query()
            ->get()
            ->toArray();

        $rootMenus = array_filter($menus,
            function($menu) {
                return $menu['parent_id'] == 0;
            });

        foreach ($rootMenus as &$rootMenu) {
            unset($rootMenu['status']);
            unset($rootMenu['create_time']);
            unset($rootMenu['update_time']);
        }
        unset($rootMenu);

        foreach ($menus as $menu) {
            unset($menu['status']);
            unset($menu['create_time']);
            unset($menu['update_time']);
            if ($menu['parent_id'] > 0) {
                foreach ($rootMenus as &$rootMenu) {
                    if ($rootMenu['menu_id'] == $menu['parent_id']) {
                        $rootMenu['sub_menus'][] = $menu;
                        break;
                    }
                }
                unset($rootMenu);
            }
        }

        return $rootMenus;
    }
}