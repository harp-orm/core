<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Repo\AbstractRepo;
use Harp\Core\Model\Models;
use BadMethodCallException;

class RepoOther extends AbstractRepo
{
    public function initialize()
    {
        $this
            ->setModelClass(__NAMESPACE__.'\ModelOther');
    }

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
