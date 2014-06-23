<?php

namespace Harp\Core\Test\Unit\Rel;

use Harp\Core\Model\AbstractModel;

class Model extends AbstractModel
{
    const REPO = 'Harp\Core\Test\Unit\Rel\Repo';

    public $id;
    public $name = 'test';
}
