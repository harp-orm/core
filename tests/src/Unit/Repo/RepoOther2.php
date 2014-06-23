<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Repo\AbstractRepo;
use Harp\Core\Model\Models;
use BadMethodCallException;

class RepoOther2 extends RepoOther
{
    public function initialize()
    {
        parent::initialize();

        $this
            ->setModelClass(__NAMESPACE__.'\ModelOther');
            ->setRootRepo(RepoOther::get());
    }
}
