<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Repo\AbstractRepo;

trait Repo2Trait
{
    public function initialize2Trait()
    {
        $this->initialize2TraitCalled = true;
    }
}
