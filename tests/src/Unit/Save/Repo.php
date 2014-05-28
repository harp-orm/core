<?php

namespace CL\LunaCore\Test\Unit\Save;

use CL\LunaCore\Save\AbstractSaveRepo;
use CL\LunaCore\Model\Models;
use BadMethodCallException;

class Repo extends AbstractSaveRepo
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

    public static function set(Repo $repo)
    {
        self::$instance = $repo;
    }

    public static function clearInstance()
    {
        self::$instance = null;
    }

    public function initialize()
    {
        $this
            ->addRels([
                new RelOne('one', $this, Repo::get()),
                new RelMany('many', $this, Repo::get()),
            ]);
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
