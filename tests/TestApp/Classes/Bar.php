<?php

namespace TestApp\Classes;

class Bar
{
    public static bool $init = false;
    public static bool $invoked = false;
    public static bool $constructed = false;
    public static bool $called = false;

    public static function init(): void
    {
        static::$init = true;
    }

    public static function reset()
    {
        static::$init = false;
        static::$invoked = false;
        static::$constructed = false;
        static::$called = false;
    }

    public function __construct()
    {
        static::$constructed = true;
    }

    public function __invoke()
    {
        static::$invoked = true;
    }

    public function call()
    {
        static::$called = true;
    }
}
