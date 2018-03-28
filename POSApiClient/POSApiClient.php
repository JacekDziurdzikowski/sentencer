<?php
/**
 * Created by PhpStorm.
 * User: Jakob
 * Date: 2018-03-27
 * Time: 22:21
 */

namespace jdz\POSApiClient;

use jdz\Sentencer\Utils\JsonParser;
use Unirest;


class POSApiClient
{
    private static $endpoint = 'https://parts-of-speech.info/tagger/postagger';
    private static $sentence = 'Jack is very handsome';
    private static $language = 'en';
    private static $configurationFile = __DIR__. '/posApiConfiguration.json';

    public static function getResponse(string $sentence)
    {
        self::$sentence = trim($sentence) ?: self::$sentence;
        $headers = array('Accept' => 'application/json');
        $query = array('text' => self::$sentence, 'language' => self::$language);

        $response = Unirest\Request::get(self::$endpoint, $headers, $query);

        if ($response->code == 200) {
            //unpack real message from response
            $response = self::parseResponse($response->raw_body);
            return $response;
        } else {
            return false;
        }
    }

    /**
     * This function returns useful associative array of type 'word' => 'partOfSpeech'
     * made from raw output from parts-of-speech API
     *
     * @param string $response
     * @return array
     */
    private static function parseResponse(string $response)
    {
        //unpack real message from the response string
        preg_match('/\{(?:[^{}]|(?R))*\}/', $response, $matches);
        $jsonResponse = $matches[0];
        $message = (json_decode($jsonResponse, true));
        $message = $message['taggedText'];

        //parse configuration
        $tagMap = JsonParser::parseJson(self::$configurationFile)['tagMap'];

        //parse the obtained string to useful associative array
        $words = explode(' ', $message);


        /**
         * Assoc Array containing word under key and 'pos-tag' and 'pos-name' describing part of speech in value array
         * under accordingly keys
         * @var $wordTagArr array
         */
        $wordPosArr = [];
        /**
         * temp var helper to store last word in loop
         * @var $lastWordTemp string
         */
        $lastWordTemp = "";

        // loop which result is complete $wordTagArrs
        foreach ($words as $index => $value) {
            $tag = substr($value, strrpos($value, '_') + 1);
            $word = substr($value, 0, strrpos($value, '_'));

            $word = str_replace("\\/", "/", $word);
            $word = str_replace("-LRB-", "(", $word);
            $word = str_replace("-RRB-", ")", $word);

            if ($tag != '$,' && $tag != '$.' && $lastWordTemp != '``' && $word != '\'\'' && $word != ')' && $lastWordTemp != '(') {
                //$taggedHTML += ' ';
            }

            $lastWordTemp = $word;
            $word = str_replace('``', '"', $word);
            $word = str_replace('\'\'', '"', $word);
            if ($word == '"') {
                $tag = '';
            }

            $wordPosArr[$word]['pos-tag'] = $tag ?: '';
            $wordPosArr[$word]['pos-name'] = $tagMap[$tag][0] ?: '';
        }

        return $wordPosArr;
    }
}