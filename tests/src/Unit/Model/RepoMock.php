<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Repo\AbstractRepo;

class RepoMock extends AbstractRepo
{
    public function initialize()
    {
        $this
            ->setModelClass(__NAMESPACE__.'\ModelMock');
    }
}
