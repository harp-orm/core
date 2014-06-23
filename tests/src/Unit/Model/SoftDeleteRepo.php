<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Repo\AbstractRepo;

class SoftDeleteRepo extends AbstractRepo
{
    public function initialize()
    {
        $this
            ->setModelClass(__NAMESPACE__.'\SoftDeleteModel')
            ->setSoftDelete(true);
    }
}
