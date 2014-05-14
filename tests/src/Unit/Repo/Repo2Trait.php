<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\AbstractRepo;

trait Repo2Trait
{
    public function initialize2Trait()
    {
        $this->initialize2TraitCalled = true;
    }
}
