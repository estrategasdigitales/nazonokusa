<?php

/**
 * Various PHP utility functions
 */

/**
 * Is the given array an associative array?
 */

function isAssoc($arr) {
    return array_keys($arr) !== range(0, count($arr) - 1);
}

/**
 * Is the given value a scalar value?
 */

function isScalar($x) {
    $t = gettype($x);
    return ($t === string) || ($t === integer) || ($t === double) || ($t === boolean);
}

/**
 * Return the properties of the given object, by default as an array,
 * but optionally as a human-readable string.
 */

function properties($obj, $as_string=false) {

    // echo "properties "; var_dump($obj);
    $keys = array_keys(get_object_vars($obj));
    return $as_string ? join(", ", $keys) : $keys;
}


/**
 * Does the given haystack string start with the needle string?
 */

function startsWith($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}

/**
 * Does the given haystack string end with the needle string?
 */

function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

/**
 * Generate a random string of the given length
 */

function generateRandomString($length = 10) {
    $chars = range(' ', '~'); // non-control ASCII characters
    $nchars = count($chars);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $chars[rand(0, $nchars - 1)];
    }
    return $randomString;
}

?>