<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Model\AbstractModel;

class ModelWithRepo extends AbstractModel
{
    public $id;
    public $name = 'test';
    public $repo;

    public function getRepo()
    {
        return $this->repo;
    }
}
