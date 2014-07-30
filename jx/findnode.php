<?php

require_once __DIR__ . "/util.php";

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

/**
 * Given a current path and a (possibly relative path),
 * determine the absolute path of the relative path.
 */

function absolute_path($cur, $rel) {
    if (startsWith($rel, "/")) {
        return $rel;
    }
    $parts = pathparts($rel);
    $cursor = $cur;
    foreach ($parts as $part) {
        if ($part === ".") {
            // no action, current path still obtains
        }
        elseif ($part == "..") {
            // step back one level, if possible
            if ($cursor !== "/") {
                $cparts = pathparts($cursor);
                array_pop($cparts);
                $cursor = "/" . join("/", $cparts);
            }
            // if $cursor is /, no change - stays at root
        }
        else {
            if ($part) {
                $cursor = ($cursor === "/") ? "/$part" : "$cursor/$part";
            }
            // else $cursor doesn't changes
        }
    }
    return $cursor;
}

// absolute_path could be simplified as simple stack-oriented approach...
// but this mixed stack/string implementation is well-tested...

?>