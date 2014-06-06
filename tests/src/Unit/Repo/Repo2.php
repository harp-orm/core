<?php

namespace Harp\Core\Test\Unit\Repo;

class Repo2 extends Repo
{
    public static $instance;

    public static function newInstance()
    {
        return self::$instance;
    }
}
