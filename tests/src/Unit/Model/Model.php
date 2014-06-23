<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\AbstractModel;

class Model extends AbstractModel
{
    const REPO = 'Harp\Core\Test\Unit\Model\Repo';

    public $id;
    public $name = 'test';
    public $afterConstructCalled = false;

    public function getName()
    {
        return $this->name;
    }
}
