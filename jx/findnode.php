<?php

require_once __DIR__ . "/util.php";


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
  * Using an XPath like path, find the given node in the given
  * PHP structure (object, array, or combination thereof).
  * Works like a poor person's JSONPath.
  */

function findnode($path, $obj) {
    $parts = pathparts($path);
    $cursor = $obj;
    foreach ($parts as $part) {
        if (startsWith($part, "[")) {
            $index = intval(trim($part, "[]"));
            $cursor = $cursor[$index];
        }
        else {
            $cursor = is_array($cursor) ? $cursor[$part] : $cursor->{$part};
        }
    }
    return $cursor;
}


?>