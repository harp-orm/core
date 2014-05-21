<?php

namespace CL\LunaCore\Test\Unit\Save;

use CL\LunaCore\Save\AbstractSaveRepo;
use CL\LunaCore\Model\Models;
use BadMethodCallException;

class SoftDeleteRepo extends AbstractSaveRepo
{
    private static $instance;

    /**
     * @return User
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new SoftDeleteRepo(__NAMESPACE__.'\SoftDeleteModel');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->setSoftDelete(true);
    }

    public function findAll()
    {
        throw new BadMethodCallException('Test Repo: cannot call selectWithId');
    }

    public function update(Models $models)
    {
        throw new BadMethodCallException('Test Repo: cannot call update');
    }

    public function delete(Models $models)
    {
        throw new BadMethodCallException('Test Repo: cannot call delete');
    }

    public function insert(Models $models)
    {
        throw new BadMethodCallException('Test Repo: cannot call insert');
    }
}
