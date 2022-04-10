<?php

/**
 * Set up .env config
 *
 */

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();

$dotenv->load(ROOT_PATH . '/.env');

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        return isset($_ENV[$key]) ? $_ENV[$key] : $default;
    }
}