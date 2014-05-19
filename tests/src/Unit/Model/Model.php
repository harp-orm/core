<?php

namespace CL\LunaCore\Test\Unit\Model;

use CL\LunaCore\Model\AbstractModel;

class Model extends AbstractModel
{
    public $id;
    public $name = 'test';
    public $class;

    public function getRepo()
    {
        return Repo::get();
    }
}
