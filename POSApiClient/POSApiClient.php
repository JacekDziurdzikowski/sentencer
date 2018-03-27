<?php
/**
 * Created by PhpStorm.
 * User: Jakob
 * Date: 2018-03-27
 * Time: 22:21
 */

namespace jdz\POSApiClient;

use Unirest;


class POSApiClient
{
    private static $endpoint = 'https://parts-of-speech.info/tagger/postagger';
    private static $sentence = 'Jack is very strong';
    private static $language = 'en';

    public static function getResponse($sentence)
    {
        self::$sentence = self::$sentence ?: trim($sentence);
        $headers = array('Accept' => 'application/json');
        $query = array('text' => self::$sentence, 'language' => self::$language);

        $response = Unirest\Request::get(self::$endpoint, $headers, $query);

        if ($response->code == 200) {
            //unpack real message from response
            return $response->body;
        } else {
            return false;
        }
    }

    //TODO: function need to be rewritten in better way
    private static function constructQuery()
    {
        $text = urlencode(trim(self::$sentence));

        $query = self::$endpoint . "?";
        $query .= "text=" . $text . "&";
        $query .= "language=" . $language;

        return $query;
    }

}


    
