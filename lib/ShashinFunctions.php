<?php

class Lib_ShashinFunctions {
    public static function isPositiveNumber($number) {
        if (is_numeric($number) && $number > 0) {
            return true;
        }

        return false;
    }

    public static function trimCallback(&$string, $key = null) {
        $string = trim($string);
    }

    public static function strtolowerCallback(&$string, $key = null) {
        $string = strtolower($string);
    }

    public static function stripslashesCallback(&$string, $key = null) {
        $string = stripslashes($string);
    }

    public static function htmlentitiesCallback(&$string, $key = null) {
        $string = htmlentities($string);
    }

    public function sanitizeStringCallback(&$string, $key = null) {
        // this is WordPress only
        if (function_exists('sanitize_text_field')) {
            $string = sanitize_text_field($string);
        }

        else {
            $string = strip_tags(trim(iconv('UTF-8','UTF-8//IGNORE', $string)));
        }
    }

    public static function makeTimestampPhpSafe($timestamp = null) {
        // if timestamp comes in as a float, it'll be translated to, e.g. 1.30152466512E+13
        // and casting it to an int will not give us the original number
        if ($timestamp) {
            Lib_ShashinFunctions::throwExceptionIfNotString($timestamp);
        }

        switch (strlen($timestamp)) {
            case 15: // for negative times
                $timestamp = substr($timestamp,0,12);
                break;
            case 14:
                $timestamp = substr($timestamp,0,11);
                break;
            case 13:
                $timestamp = substr($timestamp,0,10);
                break;
            case 12:
                $timestamp = substr($timestamp,0,9);
                break;
            case 11:
                $timestamp = substr($timestamp,0,8);
                break;
            case 0:
                $timestamp = 0;
                break;
        }

        return $timestamp;
    }

    public static function throwExceptionIfNotString($expectedString) {
        if (!is_string($expectedString)) {
             throw new Exception(__('Not a string', 'shashin'));
        }


        return true;
    }

    public static function throwExceptionIfNotArray($expectedArray) {
        if (!is_array($expectedArray)) {
             throw new Exception(__('Not an array', 'shashin'));
        }
        return true;
    }

    public static function path() {
        return dirname(__FILE__);
    }

    public static function getFileExtension($fileName) {
        Lib_ShashinFunctions::throwExceptionIfNotString($fileName);
        $fileNameParts = explode('.', $fileName);
        $lastIndexPosition = count($fileNameParts) - 1;

        if (!$lastIndexPosition) {
            return null;
        }

        return $fileNameParts[$lastIndexPosition];
    }

    // thank you http://www.webmasterworld.com/php/3681920.htm
    public static function followRedirect($url) {
        $redirect_url = null;

        if (function_exists("curl_init")) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
        }
        else {
            $url_parts = parse_url($url);
            $sock = fsockopen($url_parts['host'], (isset($url_parts['port']) ? (int)$url_parts['port'] : 80));
            $request = "HEAD " . $url_parts['path'] . (isset($url_parts['query']) ? '?'.$url_parts['query'] : '') . " HTTP/1.1\r\n";
            $request .= 'Host: ' . $url_parts['host'] . "\r\n";
            $request .= "Connection: Close\r\n\r\n";
            fwrite($sock, $request);
            $response = fread($sock, 2048);
            fclose($sock);
        }

        $header = "Location: ";
        $pos = strpos($response, $header);
        if ($pos === false) {
            return false;
        }
        else {
            $pos += strlen($header);
            $redirect_url = substr($response, $pos, strpos($response, "\r\n", $pos)-$pos);
            return $redirect_url;
        }
    }

    // thank you http://us.php.net/manual/en/function.array-merge-recursive.php#102379
    // any subarrays must exist in $oldArray
    public static function arrayMergeRecursiveForSettings($oldArray, $newArray) {
        foreach ($newArray as $key => $value) {
            if (array_key_exists($key, $oldArray) && is_array($value)) {
                $oldArray[$key] = Lib_ShashinFunctions::arrayMergeRecursiveForSettings($oldArray[$key], $newArray[$key]);
            }

            else {
                $oldArray[$key] = $value;
            }
        }

        return $oldArray;
    }
}