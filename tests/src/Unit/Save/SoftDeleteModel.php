<?php

namespace CL\LunaCore\Test\Unit\Save;

use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\SoftDeleteTrait;

class SoftDeleteModel extends AbstractModel
{
    use SoftDeleteTrait;

    public $id;
    public $name = 'test';
    public $repo;

    public function getRepo()
    {
        return $this->repo ?: SoftDeleteRepo::get();
    }
}
