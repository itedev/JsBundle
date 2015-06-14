<?php

namespace ITE\JsBundle\Utils;

/**
 * Class Inflector
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Inflector
{
    /**
     * @param string $word
     * @return string
     */
    public static function headerize($word)
    {
        return preg_replace('~\\s+~', '-', ucwords(strtr($word, '_-', '  ')));
    }

    /**
     * @param string $word
     * @return string
     */
    public static function underscore($word)
    {
        return strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $word));
    }
}