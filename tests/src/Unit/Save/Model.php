<?php

namespace Harp\Core\Test\Unit\Save;

use Harp\Core\Model\AbstractModel;

class Model extends AbstractModel
{
    const REPO = 'Harp\Core\Test\Unit\Save\Repo';

    public $id;
    public $name = 'test';
    public $repo;

    public function getRepo()
    {
        return $this->repo ?: parent::getRepo();
    }
}
