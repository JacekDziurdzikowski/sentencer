<?php
/**
 * Created by PhpStorm.
 * User: Jakob
 * Date: 2018-03-27
 * Time: 22:13
 */

namespace jdz\Sentencer;


use jdz\POSApiClient\POSApiClient;
use jdz\Sentencer\Utils\JsonParser;

class Main
{
    public static function main()
    {
        $configuration = JsonParser::parseJson('configuration.json');

        $sentence = $_REQUEST['sentence'] ?? '';

        $response = POSApiClient::getResponse($sentence);

        var_dump($response);


    }
}