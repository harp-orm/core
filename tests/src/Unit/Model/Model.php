<?php

namespace CL\LunaCore\Test\Unit\Model;

use CL\LunaCore\Model\AbstractModel;

class Model extends AbstractModel
{
    public $id;
    public $name = 'test';

    private $repo;

    public function getRepo()
    {
        return $this->repo;
    }

    public function setRepo($repo)
    {
        return $this->repo = $repo;
    }
}
