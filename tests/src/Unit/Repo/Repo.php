<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Repo\AbstractRepo;
use Harp\Core\Model\Models;
use BadMethodCallException;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Repo extends AbstractRepo
{
    use Repo1Trait;
    use Repo2Trait;

    public function initialize()
    {
        $this
            ->setModelClass(__NAMESPACE__.'\Model')
            ->setInherited(true);

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
