<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\AbstractRepo;
use CL\LunaCore\Model\Models;
use BadMethodCallException;

class RepoOther extends AbstractRepo
{
    private static $instance;

    /**
     * @return User
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new RepoOther(Model::class);
        }

        return self::$instance;
    }

    public function initialize()
    {
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
