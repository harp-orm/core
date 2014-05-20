<?php

namespace CL\LunaCore\Test\Unit\Save;

use CL\LunaCore\Model\AbstractModel;

class Model extends AbstractModel
{
    public $id;
    public $name = 'test';
    public $repo;

    public function getRepo()
    {
        return $this->repo ?: Repo::get();
    }
}
