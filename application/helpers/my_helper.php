<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('dd')) {
    function dd($data)
    {
        echo "<pre>";
        var_dump($data);
        die();
    }
}
