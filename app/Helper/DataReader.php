<?php
/**
 * Created by PhpStorm.
 * User: cthomas
 * Date: 10/14/2017
 * Time: 8:32 PM
 */

namespace App\Helper;


class DataReader
{
    public static function openDataFile($config) {
        $file = fopen($config['file'], 'r');
        $headers = self::fullTrim(fgets($file));
        $headers = explode($config['delimiter'], $headers);
        for($i = 0; $i < count($headers); $i++) {
            $headers[$i] = self::fullTrim($headers[$i]);
        }

        return ['file_pointer' => $file, 'config' => $config, 'headers' => $headers];
    }

    public static function fullTrim(string $data) {
        $data = trim($data);
        if(substr($data, -1) == 'Â') {
            $data = substr($data, 0, strlen($data) - 1);
        }
        return $data;
    }

    public static function getNextRow($file) {
        $line = explode($file['config']['delimiter'], self::fullTrim(fgets($file['file_pointer'])));

        $array = [];

        for($i = 0; $i < count($line); $i++) {
            $array[$file['headers'][$i]] = self::fullTrim($line[$i]);
        }

        //Subarrays
        foreach($file['config']['multivalued'] as $key => $value) {
            $array[$key] = explode($value, self::fullTrim($array[$key]));
            for($i = 0; $i < count($array[$key]); $i++) {
                $array[$key][$i] = self::fullTrim($array[$key][$i]);
            }
        }
        return $array;
    }
}