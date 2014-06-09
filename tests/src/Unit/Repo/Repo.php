<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Repo\AbstractRepo;
use Harp\Core\Model\Models;
use BadMethodCallException;

class Repo extends AbstractRepo
{
    use Repo1Trait;
    use Repo2Trait;

    public static function newInstance()
    {
        return new Repo(__NAMESPACE__.'\Model');
    }

    public function initialize()
    {
        $this->setInherited(true);

        $this->initializeCalled = true;

        $this->initialize1Trait();
        $this->initialize2Trait();
    }

    public $test;
    public $initializeCalled = false;
    public $initialize1TraitCalled = false;
    public $initialize2TraitCalled = false;

    public function findAll()
    {
        throw new BadMethodCallException('Test Repo: cannot call findAll');
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
