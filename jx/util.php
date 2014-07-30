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



/**
 * Parse the given path into an array of path componetns
 */

function pathparts($path) {
    if (startsWith($path, "/")) {
        $path = substr($path, 1, strlen($path)-1);
    }
    $parts = split("/", $path);
    return $parts;
}

/**
 * Construct a path. If rooted is true (which it is by default),
 * make it an absolute path rooted at /.
 */

function makepath($parts, $rooted=true) {
    $parts = $parts ? $parts : array();
    $xpath = join("/", $parts);
    return $rooted ? "/$xpath" : $xpath;
}

/**
 * Return type (and if object), the class, of the given value.
 * On null, returns null rather than a class of null, which is
 * iffy in PHP.
 */


function typeof($value) {
    if ($value === null) { return null; }
    $t = gettype($value);
    return ($t === object) ? get_class($value) : $t;
}

/**
 * Like echo, but adds a newline
 */

function say() {
    echo join("", func_get_args()), "\n";
}

/**
 * Like echo, but adds a newline. Also prints file and line number.
 */

function sayl()
{
    $db = debug_backtrace();
    echo _filepos($db);

    $args = func_get_args();
    call_user_func_array('say', $args);
}

/**
 * Debugging print function. Can be called with a single
 * value, or with a label value pair.
 */

function show()
{
    $value_index = func_num_args() == 2 ? 1 : 0;
    if ($value_index == 1) {
        echo func_get_arg(0), ": ";
    }
    $value = func_get_arg($value_index);
    is_array($value) ? print_r($value) : var_dump($value);
}

/**
 * Given a backtrace, return a string indicating the line the
 * calling function was called from.
 */

function _filepos($backtrace)
{
    $filepath = $backtrace[0]['file'];
    $line = $backtrace[0]['line'];
    $last_slash = strrpos($filepath, '/');
    $filename = substr($filepath, $last_slash + 1, strlen($filepath) - $last_slash);
    return $filename . "[" . $line . "]: ";
}

/**
 * Debuging print statement, with file and line.
 */

function showl()
{
    $db = debug_backtrace();
    echo _filepos($db);
    $args = func_get_args();
    call_user_func_array('show', $args);
}


?>