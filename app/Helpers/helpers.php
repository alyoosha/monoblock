<?php
if (!function_exists('my_ucfirst')) {
    function my_ucfirst($str){
        $array = mb_str_split(trim($str));
        $array[0] = mb_strtoupper($array[0]);

        return implode('', $array);
    }
}