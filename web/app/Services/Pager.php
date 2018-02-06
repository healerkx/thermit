<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/21
 * Time: 14:48
 */
class Pager
{
    /**
     * @param LengthAwarePaginator $pager
     * @return array
     */
    public static function formatPage(LengthAwarePaginator $pager)
    {
        return [
            'lastPage'        => $pager->lastPage(),
            'currentPage'     => $pager->currentPage(),
            'perPage'         => $pager->perPage(),
            'nextPageUrl'     => $pager->nextPageUrl(),
            'previousPageUrl' => $pager->previousPageUrl(),
            'hasMorePages'    => $pager->hasMorePages(),
            'count'           => $pager->count(),
            'totalCount'      => $pager->total(),
        ];
    }

    /**
     * @param LengthAwarePaginator $pager
     * @return array
     */
    public static function fromPager(LengthAwarePaginator $pager)
    {
        $items = $pager->items();
        $itemsArray = array_map(function($i) { return $i->toArray(); }, $items);

        $data['page'] = Pager::formatPage($pager);
        $data['list'] = $itemsArray;
        return $data;
    }
}