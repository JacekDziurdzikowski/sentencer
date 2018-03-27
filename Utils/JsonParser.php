<?php
/**
 * Created by PhpStorm.
 * User: Jakob
 * Date: 2018-03-27
 * Time: 22:28
 */

namespace jdz\Sentencer\Utils;


class JsonParser
{
    public static function parseJson(string $jsonFile) {
        $jsonContent = file_exists($jsonFile) ? file_get_contents($jsonFile) : false;
        return $jsonContent ? json_decode($jsonContent) : false;
    }
}