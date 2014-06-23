<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Model\AbstractModel;

class Model extends AbstractModel
{
    const REPO = 'Harp\Core\Test\Unit\Repo\Repo';

    public $id;
    public $name = 'test';
}
