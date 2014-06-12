<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\AbstractModel;

class Model extends AbstractModel
{
    public $id;
    public $name = 'test';
    public $class;

    public function getRepo()
    {
        return Repo::get();
    }

    public function getName()
    {
        return $this->name;
    }
}
