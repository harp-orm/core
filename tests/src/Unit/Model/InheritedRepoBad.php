<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Repo\AbstractRepo;

class InheritedRepoBad extends AbstractRepo
{
    public function initialize()
    {
        $this
            ->setModelCLass(__NAMESPACE__.'\InheritedModelBad')
            ->setInherited(false);
    }
}
