<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/23
 * Time: 15:36
 */

namespace App\Services;


class Utils
{
    /**
     * @param array $queryLog
     * @return array
     */
    public static function getSQLArrayWithBindings(array $queryLog)
    {
        $sql = [];
        foreach ($queryLog as $bind) {
            $query = $bind['query'];
            $bindings = $bind['bindings'];

            $line = $query;
            while (!empty($bindings)) {
                $line = preg_replace('/\?/', "'{$bindings[0]}'", $line, 1);
                array_shift($bindings);
            }

            unset($bindings);
            $sql[] = $line;
        }

        return $sql;
    }

    /**
     * @param string $root
     * @return array
     */
    public static function readFiles($root = '.')
    {
        $files = ['files' => [], 'dirs' => []];
        $directories = [];
        $last_letter = $root[strlen($root) - 1];
        $root = ($last_letter == '\\' || $last_letter == '/') ? $root : $root . DIRECTORY_SEPARATOR;

        $directories[] = $root;

        while (sizeof($directories)) {
            $dir = array_pop($directories);
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }
                    $file = $dir . $file;
                    if (is_dir($file)) {
                        $directory_path = $file . DIRECTORY_SEPARATOR;
                        array_push($directories, $directory_path);
                        $files['dirs'][] = $directory_path;
                    } elseif (is_file($file)) {
                        $files['files'][] = $file;
                    }
                }
                closedir($handle);
            }
        }

        return $files;
    }
}