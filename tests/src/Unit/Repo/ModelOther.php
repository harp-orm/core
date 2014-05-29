<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Model\AbstractModel;

class ModelOther extends AbstractModel
{
    public $id;
    public $name = 'test';

    public function getRepo()
    {
        return RepoOther::get();
    }
}
