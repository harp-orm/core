<?php

namespace Harp\Core\Test\Unit\Rel;

use Harp\Core\Model\AbstractModel;

class Model extends AbstractModel
{
    public $id;
    public $name = 'test';

    public function getRepo()
    {
        return Repo::get();
    }
}
