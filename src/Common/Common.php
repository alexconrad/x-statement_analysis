<?php

namespace Misico\Common;

use PHP_CodeSniffer\Generators\HTML;

class Common
{
    public function bootstrapFile() {
        return 'index.php';
    }

    public function hiddenParams($controller, $action = '', $extra = array()) {
        if (strpos($controller, '\\') !== false) {
            $controller = substr($controller, strrpos($controller, '\\') + 1);
            $controller = str_replace('Controller', '', $controller);
        }

        $html = <<<HTML
<input type="hidden" name="c" value="$controller">
HTML;
        if (!empty($action)) {
            $html .= <<<HTML
<input type="hidden" name="a" value="$action">
HTML;
        }

        if (!empty($extra)) {
            foreach ($extra as $key=>$value) {
            $html .= <<<HTML
<input type="hidden" name="$key" value="$value">
HTML;
            }
        }

        return $html;
    }


    public function link($controller, $action = '', $extra = array())
    {
        if (strpos($controller, '\\') !== false) {
            $controller = substr($controller, strrpos($controller, '\\') + 1);
            $controller = str_replace('Controller', '', $controller);
        }

        $url = $this->bootstrapFile().'?c=' . urlencode($controller);
        if (!empty($action)) {
            $url .= '&a=' . urlencode($action);
        }

        if (!empty($extra)) {
            $url .= '&' . http_build_query($extra);
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

    public static function formatRows($string)
    {
        return number_format($string);
    }

    public static function formatLatency($string, $decimals = 0)
    {
        return number_format($string, $decimals);
    }

    public static function formatAverage($string)
    {
        if ($string === null) {
            return '-';
        }
        return number_format((float)$string, 2);
    }

    public static function classForPercent($diff): string
    {
        $class = 'same';
        if ($diff == 0) {
            return $class;
        }

        if ($diff < 0) {
            $class = 'bad-bad';
        }
        if ($diff < 50) {
            $class = 'bad-bad';
        }
        if ($diff > 0) {
            $class = 'good';
        }
        if ($diff > 50) {
            $class = 'good-good';
        }

        return $class;
    }

 public static function classForPercentMinusGood($diff): string
    {
        $class = 'same';
        if ($diff == 0) {
            return $class;
        }

        if ($diff < 0) {
            $class = 'good';
        }
        if ($diff < 50) {
            $class = 'good-good';
        }
        if ($diff > 0) {
            $class = 'bad';
        }
        if ($diff > 50) {
            $class = 'bad-bad';
        }

        return $class;
    }

    public static function percentChange($first, $second) {

        if ($first == $second) {
            return 0;
        }

        if ($first > $second) {
            if ($second == 0) {
                return null;
            }
            $div = bcdiv($first, $second, 6);
            $div = bcsub($div, 1, 6);
            $div = bcmul($div, 100, 6);
            return bcmul($div, -1, 2);
        }

        if ($first == 0) {
            return null;
        }
        $div = bcdiv($second, $first, 6);
        $div = bcsub($div, 1, 6);
        $div = bcmul($div, 100, 2);
        return $div;

    }

    public static function formatLatencyPercentage($percent)
    {
        if ($percent === 'n/a') {
            return '<br><small>n/a</small>';
        }
        return '<br><small>' . $percent . '%</small>';
    }

    public static function formatRequestPercentage($percent)
    {
        if ($percent === 'n/a') {
            return '<br><small>n/a</small>';
        }
        return '<br><small>' . $percent . '%</small>';
    }


    public static function formatDiffPercentage($string)
    {
        if ($string === null) {
            return '<br>';
        }
        return '<br><small>' . $string . '%</small>';
    }

    public static function getTablesFromSql($query): array
    {
        if (preg_match_all('!((FROM|JOIN)\s([\S]+))!i', $query, $matches)) {
            $tables = array_unique($matches[3]);
            array_walk($tables, function (&$v) {
                $p = explode('.', $v, 2);
                $v = array_pop($p);
                $v = trim($v, '`');
            });
            $tables = array_filter($tables, function ($v) {
                return ('(' !== $v);
            });

            return $tables;
        }

        return [];
    }


}
