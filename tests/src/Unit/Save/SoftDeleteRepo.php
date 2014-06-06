<?php

namespace Harp\Core\Test\Unit\Save;

use Harp\Core\Save\AbstractSaveRepo;
use Harp\Core\Model\Models;
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
            self::$instance = self::newInstance();
        }

        return self::$instance;
    }

    public static function newInstance()
    {
        return new SoftDeleteRepo(__NAMESPACE__.'\SoftDeleteModel');;
    }

    public static function set(SoftDeleteRepo $repo)
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
