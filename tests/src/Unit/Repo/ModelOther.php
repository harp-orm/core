<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Model\AbstractModel;

class ModelOther extends AbstractModel
{
    public $id;
    public $name = 'test';

    public function getRepo()
    {
        return RepoOther::get();
    }
}
