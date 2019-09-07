<?php
function path_join()
{
    $parts = [];
    foreach (func_get_args() as $arg) {
        if (is_array($arg)) {
            array_push($parts, ...$arg);
        } else {
            array_push($parts, strval($arg));
        }
    }
    return implode(DIRECTORY_SEPARATOR, $parts);
}


/**
 * Get Config var from enviroment
 * @param string $name name of env var
 * @param string $default default value
 * @param boolean $is_secret TRUE for try to get value from content of path $name . "_FILE"
 * @return unknown|string
 */
function docker_get_config($required, $name, $default=null, $is_secret=false)
{
    if ($is_secret) {
        $value = docker_get_config(FALSE, $name . '_FILE');
        if ($value and is_readable($value)) {
            return str_replace(["\r", "\n"], '', file_get_contents($value));
        }
    }
    $value = getenv($name);
    if ($value) {
        return $value;
    }
    if ($required) {
        echo "$name env var is required" . PHP_EOL;
        exit(10);
    }
    return $default;
}