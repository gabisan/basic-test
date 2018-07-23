<?php

namespace app\lib;

use Exception;
use ReflectionClass;
use yii\helpers\BaseStringHelper;

/**
 * Class StringHelper
 * @package app\lib\helpers
 * @author Angelo <angelo@sportspass.com.au>
 */
class StringHelper extends BaseStringHelper
{

    /**
     * @param $keywords
     * @return null|string
     */
    public static function getSearchSafe($keywords,$like=true)
    {
        $search = strtr(trim($keywords), array(
            //    ' ' => '%',
            "'" => "\'",
            '%' => '\%',
            '_' => '\_',
            '\\' => '\\\\',
        ));

        if ($search)
        {
            if ($like) return "%$search%";

            return $search;
        }

        return null;
    }

    /**
     * Enforce tag is wrapped
     * @param string $tag
     * @param string $text
     * @param array $checkTags if these tags are already wrapped then don't enforce
     * @return string
     */
    public static function enforceTagWrap($text, $tag, $checkTags = ['p'])
    {
        $wrap = true;
        foreach ($checkTags as $check)
        {
            // make sure tags being checked aren't already wrapped
            if (strpos($text, $check) === 0)
            {
                $wrap = false;
                break;
            }
        }

        // wrap tag
        if ($wrap)
        {
            $text = "<$tag>$text</$tag>";
        }

        return $text;
    }

    /**
     * Get shortened classname (no namespace)
     * @param string|object $className
     * @return string
     */
    public static function shortClassName($className)
    {
        if (is_object($className))
        {
            $className = get_class($className);
        }

        return trim(substr($className, strrpos($className, '\\')), '\\');
    }

    /**
     * @param $e
     * @return string
     */
    public static function formatException(Exception $e)
    {
        /*$message = $e->getMessage()
            . "\nin " . $e->getFile() . '(' . $e->getLine() . ')'
            . "\n\nStack:\n" . $e->getTraceAsString();*/
//        return $message;

        return (string)$e;
    }

    /**
     * Removes any chars that would make displaying the dir not work
     * @param string $name
     * @return string
     */
    public static function normalizeDirName($name)
    {
        return str_replace(
            ['/', '<', '>'],
            ['&#47;', '&gt;', '&lt;'],
            $name
        );
    }

    /**
     * Create Slug in a string
     * @return string
     */
    public static function createSlug($string)
    {
        $slug      = "";
        $club_name = strtolower($string);
        $slug_text = explode(' ', $club_name);

        for ($i = 0; $i <= count($slug_text) - 1; $i++) {

            $delimeter = ($i == count($slug_text) - 1) ? '' : '-';
            $slug .= $slug_text[$i].$delimeter;
        }

        return preg_replace('/[^ \w-]/', '', $slug);
    }
}