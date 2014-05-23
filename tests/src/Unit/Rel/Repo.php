<?php

namespace CL\LunaCore\Test\Unit\Rel;

use CL\LunaCore\Repo\AbstractRepo;

class Repo extends AbstractRepo
{
    private static $instance;

    /**
     * @return User
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new Repo(__NAMESPACE__.'\Model');
        }

        return self::$instance;
    }

    public $test;

    public function initialize()
    {

    }
}
