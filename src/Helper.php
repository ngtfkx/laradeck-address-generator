<?php

namespace Ngtfkx\Laradeck\AddressGenerator;


class Helper
{
    public static function prepare($string)
    {
        $string = str_replace([' ', '-', '_'], '', mb_strtolower($string));

        return $string;
    }
}