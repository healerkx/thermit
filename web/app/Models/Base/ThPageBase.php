<?php

namespace App\Models\Base;

/**
 * @package App\Models
 * Class ThPageBase
 *
 * Generated by 'php artisan model:create'
 * Properties as follows
 * @property $page_id    
 * @property $page_name    
 * @property $page_type    
 * @property $page_status    
 * @property $status    
 * @property $create_time    
 * @property $update_time    
 */
class ThPageBase extends BaseModel
{
    /**
     * Table name
     */
    protected $table = 'th_page';

    /**
     * Table primary-key
     */
    protected $primaryKey = 'page_id';

    /**
     * @return array
     */
    public static function getFields()
    {
        return [
            'page_id',
            'page_name',
            'page_type',
            'page_status',
            'status',
            'create_time',
            'update_time',
        ];
    }
}
