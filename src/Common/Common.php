<?php

namespace Misico\Common;

class Common
{

    public function link($controller, $action = '', $extra = array())
    {
        if (strpos($controller,'\\') !== false) {
            $controller = substr($controller, strrpos($controller, '\\')+1);
            $controller = str_replace('Controller', '', $controller);
        }

        $url = 'index.php?c='.urlencode($controller);
        if (!empty($action)) {
            $url .= '&a='.urlencode($action);
        }

        if (!empty($extra)) {
            $url .= '&'.http_build_query($extra);
        }

        return $url;
    }

    public static function makeClickableLinks($s)
    {
        /** @noinspection HtmlUnknownTarget */
        return preg_replace(
            '@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@',
            '<a href="$1" target="_blank">$1</a>',
            $s
        );
    }


    public static function safeString($string)
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $string);
    }

    public static function safeFileName($string)
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $string);
    }

    /**
     * Converts strings like image_the_url ==> imageTheUrl
     *
     * @param $string
     * @param bool $capitalizeFirstCharacter
     * @param array $wordSeparators
     * @return mixed
     */
    public static function toCamelCase($string, $capitalizeFirstCharacter = false, $wordSeparators = array('_'))
    {

        $str = str_replace(' ', '', ucwords(str_replace($wordSeparators, ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }

    public static function removeNullValueKeysRecursively(array $array)
    {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $array[$k] = self::removeNullValueKeysRecursively($v);
                $allElementsAreNull = true;
                foreach ($array[$k] as $value) {
                    if ($value !== null) {
                        $allElementsAreNull = false;
                        break;
                    }
                }
                if ($allElementsAreNull) {
                    unset($array[$k]);
                }
            } elseif ($v === null) {
                unset($array[$k]);
            }
        }
        return $array;
    }
}
