<?php

namespace App\Services\Admin;
use App\Services\Result;

/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/21
 * Time: 15:04
 */
class BuilderService
{
    const PageType_ListView = 1;

    const PageType_FormCreator = 2;

    const ConfigValueType_ModelName = 1;

    const ConfigValueType_Uri = 2;

    const ConfigCategory_DataSource = 1;

    const ConfigCategory_Filter = 2;

    const ConfigCategory_Field = 3;

    const ConfigCategory_XPath = 4;

    protected static function getFields($configs)
    {
        $fields = array_map(function($i) {
            return json_decode($i['config_value'], true);
        }, array_filter($configs, function($i) {
            return $i['config_category'] == self::ConfigCategory_Field;
        }));

        foreach ($fields as &$field) {
            if (!array_key_exists('fieldName', $field)) {
                $field['fieldName'] = '';
            } elseif (!array_key_exists('columnName', $field)) {
                $field['columnName'] = '';
            } elseif (!array_key_exists('fieldType', $field)) {
                $field['fieldType'] = 'text';
            }
        }
        unset($field);
        return $fields;
    }
}