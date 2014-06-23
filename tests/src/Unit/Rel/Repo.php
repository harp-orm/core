<?php

namespace Harp\Core\Test\Unit\Rel;

use Harp\Core\Repo\AbstractRepo;

class Repo extends AbstractRepo
{
    public $test;

    public function initialize()
    {
        $this->setModelClass(__NAMESPACE__.'\Model');
    }
}
