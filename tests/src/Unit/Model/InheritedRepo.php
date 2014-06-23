<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Repo\AbstractRepo;

class InheritedRepo extends AbstractRepo
{
    public function initialize()
    {
        $this
            ->setModelCLass(__NAMESPACE__.'\InheritedModel')
            ->setInherited(true);
    }
}
