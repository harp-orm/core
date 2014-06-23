<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Model\AbstractModel;

class ModelOther extends AbstractModel
{
    const REPO = 'Harp\Core\Test\Unit\Repo\RepoOther';

    public $id;
    public $name = 'test';
}
