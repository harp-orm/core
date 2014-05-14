<?php

namespace CL\LunaCore\Test\Unit\Rel;

use CL\LunaCore\Repo\AbstractRepo;
use BadMethodCallException;
use SplObjectStorage;

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

    public function selectWithId($id)
    {
        throw new BadMethodCallException('Test Repo: cannot call selectWithId');
    }

    public function update(SplObjectStorage $models)
    {
        throw new BadMethodCallException('Test Repo: cannot call update');
    }

    public function delete(SplObjectStorage $models)
    {
        throw new BadMethodCallException('Test Repo: cannot call delete');
    }

    public function insert(SplObjectStorage $models)
    {
        throw new BadMethodCallException('Test Repo: cannot call insert');
    }
}
