<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\AbstractRepo;
use CL\LunaCore\Model\Models;
use BadMethodCallException;
use SplObjectStorage;

class Repo extends AbstractRepo
{
    use Repo1Trait;
    use Repo2Trait;

    private static $instance;

    /**
     * @return User
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new Repo(Model::class);
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this->initializeCalled = true;

        $this->initialize1Trait();
        $this->initialize2Trait();
    }

    public function afterInitialize()
    {
        $this->afterInitializeCalled = true;
    }

    public $test;
    public $initializeCalled = false;
    public $afterInitializeCalled = false;
    public $initialize1TraitCalled = false;
    public $initialize2TraitCalled = false;

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
