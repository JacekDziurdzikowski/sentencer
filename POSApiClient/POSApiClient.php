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
    private static $sentence = 'Jack is very strong';
    private static $language = 'en';

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

    //TODO: function need to be rewritten in better way
    private static function constructQuery()
    {
        $text = urlencode(trim(self::$sentence));

        $query = self::$endpoint . "?";
        $query .= "text=" . $text . "&";
        $query .= "language=" . $language;

        return $query;
    }

    private static function parseResponse(string $response){
        //unpack real message from the response string
        preg_match('/\{(?:[^{}]|(?R))*\}/', $response, $matches);
        $jsonResponse = $matches[0];
        $message = (json_decode($jsonResponse, true));
        $message = $message['taggedText'];

        return $message;
    }
}

/*function callback(data) {

    var tagMap = appData.tagMap;
    var color = appData.colors;

    $('#form').removeClass('mode-edit').addClass('mode-view');
    $('#tagTipContainer').hide();

    var words = data.taggedText.split(" ");
    var taggedHTML = "";
    var lastWord = "";
    $.each(words, function(index, taggedWord) {
        var tag = taggedWord.substring(taggedWord.lastIndexOf("_") + 1);
        var word = taggedWord.substring(0, taggedWord.lastIndexOf("_"));
        word = word.replace("\\/", "/");
        word = word.replace("-LRB-", "(");
        word = word.replace("-RRB-", ")");
        // TODO: are there other symbols?
        if (tag != '$,' && tag != '$.' && lastWord != '``' && word != '\'\'' && word != ')' && lastWord != '(') {
            taggedHTML += ' ';
        }
        lastWord = word;
        word = word.replace('``', '"');
        word = word.replace('\'\'', '"');
        if (word == '"') {
            tag = '';
        }
        // TODO: escape html
        if (tagMap[tag] != undefined && color[tagMap[tag][0]] != undefined) {
            taggedHTML += '<span class="taggedWord" style="background-color: ' + color[tagMap[tag][0]] + '">' + word + '<span>' + tag + '</span></span>';
        }
        else {
            taggedHTML += '<span class="taggedWord">' + word + '<span>' + tag + '</span></span>';
        }
    });
    $('#textTagged').html(taggedHTML);


    / \{(?:[^{}]|(?R))*\}/x*/