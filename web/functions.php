<?php

/**
 * Print an array in readable format
 * @param string|array $data
 * @param bool $terminate
 * @param bool $htmlChars
 * @param bool $return
 * @param string $wrapTag
 * @return mixed
 */
if (!function_exists('pr'))
{
    function pr($data, $terminate = true, $htmlChars = false, $return = false, $wrapTag = 'pre')
    {
        // set output (could be array, object etc.)
        $data = print_r($data, 1);
//    $data = var_export($data,1);

        // add html specialchars
        if ($htmlChars)
        {
            $data = htmlspecialchars($data);
        }

        // return the result or output it
        if ($return)
        {
            return $data;
        }
        else
        {
            if ($wrapTag && !((substr(PHP_SAPI, 0, 3) == 'cgi' || PHP_SAPI == 'cli') && empty($_SERVER['REMOTE_ADDR'])))
            {
                echo "<$wrapTag>\n$data\n</$wrapTag>";
            }
            else
            {
                echo $data . PHP_EOL;
            }
        }

        // terminate app
        if ($terminate)
        {
            exit(1);
        }

        return null;
    }
}

/**
 * Log to a file
 * @param $data
 * @param bool $terminate
 * @param string $logFile
 */
if (!function_exists('prl'))
{
    function prl($data, $terminate = true, $logFile = 'prl.log')
    {
        file_put_contents("/tmp/runtime/$logFile", print_r($data, 1), FILE_APPEND);
        if ($terminate)
        {
            exit;
        }
    }
}

/**
 * @return \yii\console\Application|\yii\web\Application
 */
function app()
{
    return Yii::$app;
}

/**
 * Translates a message to the specified language.
 *
 * This is a shortcut method of [[\yii\i18n\I18N::translate()]].
 *
 * The translation will be conducted according to the message category and the target language will be used.
 *
 * You can add parameters to a translation message that will be substituted with the corresponding value after
 * translation. The format for this is to use curly brackets around the parameter name as you can see in the following example:
 *
 * ```php
 * $username = 'Alexander';
 * echo \Yii::t('app', 'Hello, {username}!', ['username' => $username]);
 * ```
 *
 * Further formatting of message parameters is supported using the [PHP intl extensions](http://www.php.net/manual/en/intro.intl.php)
 * message formatter. See [[\yii\i18n\I18N::translate()]] for more details.
 *
 * @param string $message the message to be translated.
 * @param string $category the message category.
 * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
 * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
 * [[\yii\base\Application::language|application language]] will be used.
 * @return string the translated message.
 */
function t($message, $params = [], $category = 'app', $language = null)
{
    return Yii::t($category, $message, $params, $language);
}