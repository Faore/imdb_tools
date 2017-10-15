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
        $headers = fgetcsv($file, 0, $config['delimiter'], $config['enclosure'], $config['escape']);
        for($i = 0; $i < count($headers); $i++) {
            $headers[$i] = self::fullTrim($headers[$i]);
        }

        return ['file_pointer' => $file, 'config' => $config, 'headers' => $headers];
    }

    public static function fullTrim(string $data) {
        $data = trim($data, " \t\n\r\0\x0BÂ ");
        if($data == "\\N" || $data == '') {
            $data = null;
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
            if($array[$key] != null) {
                $array[$key] = explode($value, self::fullTrim($array[$key]));
            } else {
                $array[$key] = [];
            }
            for($i = 0; $i < count($array[$key]); $i++) {
                $array[$key][$i] = self::fullTrim($array[$key][$i]);
            }
        }
        return $array;
    }

    public static function endOfFile($file) {
        return feof($file['file_pointer']);
    }
}