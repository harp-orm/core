<?php

namespace Harp\Core\Test\Unit\Save;

use Harp\Core\Model\AbstractModel;

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
