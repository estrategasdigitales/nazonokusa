#!/usr/bin/env php
<?php

/**
 *  jxrun.php provides the mechanism to run filters from the command line,
 *  and thus to integrate with Unix cron jobs / crontab execution.
 */


$recipes = array();

class BadRecipe extends Exception {};


if (!defined('endsWith')) {
    function endsWith($haystack, $needle) {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }
}

function getSubclasses($parentClassName)
{
    $classes = array();
    foreach (get_declared_classes() as $className)
    {
    	if (is_subclass_of($className, $parentClassName))
    		$classes[] = $className;
    }

    return $classes;
}


/**
 * Read through the JX recipes directory, importing each file found there
 */

function import_recipes() {
    global $recipes;

    $basedir = __DIR__ . '/recipes';

    foreach (scandir($basedir) as $file) {
        $path = "$basedir/$file";
        if (endsWith($file, '.php')) {
            require_once($path);
        }
    }

    // register all know recipe subclasses
    foreach (getSubClasses('JX_Recipe') as $recipe) {
        // currently using full name, so mapping is slight overkill
        $recipes[$recipe] = $recipe;
    }

}

import_recipes();


/**
 * Run the given recipe on the given contents
 */

function run_recipe($name, $content) {
    global $recipes;

    if (array_key_exists($name, $recipes)) {
        $recipe_class = $recipes[$name];
        $instance = new $recipe_class();
        $instance->process_string($content);
        echo $instance->emit();
    }
    else {
        throw new BadRecipe("recipe '$name' not found\n");
    }
}

/**
 * Run the input processor as though from a command line interface (cli).
 */

function as_script() {
    if (php_sapi_name() == "cli") {
        // In cli-mode
        $options = getopt("i:r:");
        $inpath = $options['i'];
        $recipe = $options['r'];
        if ($inpath) {
            $contents = file_get_contents($inpath);
            // echo $contents, "\n";
            if ($recipe) {
                run_recipe($recipe, $contents);
            }
            else {
                echo "no recipe specified, so no conversion attempted\n";
            }
        }
        else {
            echo "no inpath given, so no file grab\n";
        }
    } else {
        // Not in cli-mode
        echo "not in CLI mode; no CLI options parsed\n";
    }
}

as_script();


?>