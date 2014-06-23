<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Repo\AbstractRepo;
use Harp\Core\Model\Models;
use BadMethodCallException;

class RepoOther3 extends AbstractRepo
{
    public function initialize()
    {
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
