<?php

namespace TestApp\Classes;

class Foo
{
    public static bool $init = false;

    public static function init(): void
    {
        self::$init = true;
    }

    public static function reset()
    {
        self::$init = false;
    }
}
